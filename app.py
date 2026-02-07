from flask import Flask, render_template, Response, request, jsonify, send_from_directory
import cv2
import mediapipe as mp
import numpy as np
import pickle
from gtts import gTTS
import os
from googletrans import Translator
from datetime import datetime
import threading
import time

app = Flask(__name__)
app.config['STATIC_FOLDER'] = 'static'
app.config['AUDIO_FOLDER'] = os.path.join(app.config['STATIC_FOLDER'], 'audio')

# Create audio directory if not exists
os.makedirs(app.config['AUDIO_FOLDER'], exist_ok=True)

# Load trained model
try:
    model_dict = pickle.load(open('./model.p', 'rb'))
    model = model_dict['model']
except Exception as e:
    print(f"Error loading model: {e}")
    model = None

# Extended labels (A-Z + space + enter)
labels_dict = {i: chr(65 + i) for i in range(26)}  # A-Z
labels_dict[26] = ' '  # Space
labels_dict[27] = 'ENTER'  # Enter

# Setup MediaPipe Hands
mp_hands = mp.solutions.hands
hands = mp_hands.Hands(
    static_image_mode=False,
    max_num_hands=1,
    min_detection_confidence=0.7,
    min_tracking_confidence=0.7
) if model else None

# Global variables
current_prediction = ""
last_prediction_time = 0
prediction_lock = threading.Lock()
translator = Translator()
camera_active = True  # Camera state flag

def generate_frames():
    global current_prediction, last_prediction_time, camera_active
    
    cap = cv2.VideoCapture(0)
    if not cap.isOpened():
        print("Error: Camera not accessible")
        yield b'--frame\r\nContent-Type: image/jpeg\r\n\r\n' + cv2.imencode('.jpg', np.zeros((480, 640, 3), dtype=np.uint8))[1].tobytes() + b'\r\n'
        return

    while True:
        if not camera_active:
            # Generate blank frame when camera is off
            blank_frame = np.zeros((480, 640, 3), dtype=np.uint8)
            cv2.putText(blank_frame, "Camera is OFF", (150, 240), 
                        cv2.FONT_HERSHEY_SIMPLEX, 1, (255, 255, 255), 2)
            ret, buffer = cv2.imencode('.jpg', blank_frame)
            frame = buffer.tobytes()
            yield (b'--frame\r\nContent-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')
            time.sleep(0.1)
            continue

        ret, frame = cap.read()
        if not ret:
            continue

        data_aux = []
        x_ = []
        y_ = []
        prediction_made = False

        H, W, _ = frame.shape
        frame_rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        results = hands.process(frame_rgb)

        if results.multi_hand_landmarks:
            for hand_landmarks in results.multi_hand_landmarks:
                # Draw hand landmarks
                mp.solutions.drawing_utils.draw_landmarks(
                    frame, hand_landmarks, mp_hands.HAND_CONNECTIONS,
                    mp.solutions.drawing_styles.get_default_hand_landmarks_style(),
                    mp.solutions.drawing_styles.get_default_hand_connections_style()
                )

                # Collect landmarks for bounding box
                for landmark in hand_landmarks.landmark:
                    x = landmark.x
                    y = landmark.y
                    x_.append(x)
                    y_.append(y)

                # Draw bounding box
                x1 = int(min(x_) * W) - 20
                y1 = int(min(y_) * H) - 20
                x2 = int(max(x_) * W) + 20
                y2 = int(max(y_) * H) + 20
                cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 3)

                # Prepare data for prediction
                for landmark in hand_landmarks.landmark:
                    data_aux.append(landmark.x - min(x_))
                    data_aux.append(landmark.y - min(y_))

                # Make prediction continuously but update sentence every 4 seconds
                current_time = time.time()
                if len(data_aux) == 42:
                    try:
                        prediction = model.predict([np.asarray(data_aux)])
                        predicted_label = labels_dict[int(prediction[0])]
                        
                        # Always show current prediction above hand
                        cv2.putText(frame, predicted_label, (x1, y1 - 10),
                                    cv2.FONT_HERSHEY_SIMPLEX, 1.5, (0, 255, 0), 3)
                        
                        # Update the shared prediction every 4 seconds
                        if current_time - last_prediction_time > 4:
                            with prediction_lock:
                                current_prediction = predicted_label
                                last_prediction_time = current_time
                                prediction_made = True
                    except Exception as e:
                        print(f"Prediction error: {e}")
        else:
            cv2.putText(frame, "Show your hand", (50, 50),
                        cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 2)

        ret, buffer = cv2.imencode('.jpg', frame)
        frame = buffer.tobytes()
        yield (b'--frame\r\nContent-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')

@app.route('/')
def index():
    return render_template('interface.html')

@app.route('/video_feed')
def video_feed():
    return Response(generate_frames(),
                   mimetype='multipart/x-mixed-replace; boundary=frame')

@app.route('/get_prediction')
def get_prediction():
    global current_prediction
    with prediction_lock:
        prediction = current_prediction if current_prediction else ""
        current_prediction = ""  # Reset after reading
    return jsonify({'prediction': prediction})

@app.route('/toggle_camera', methods=['POST'])
def toggle_camera():
    global camera_active
    camera_active = not camera_active
    return jsonify({'status': 'success', 'camera_active': camera_active})

@app.route('/text_to_speech', methods=['POST'])
def text_to_speech():
    text = request.json.get('text', '')
    lang = request.json.get('lang', 'en')
    
    if not text:
        return jsonify({'error': 'No text provided'}), 400
    
    try:
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        filename = f"speech_{timestamp}.mp3"
        filepath = os.path.join(app.config['AUDIO_FOLDER'], filename)
        
        tts = gTTS(text=text, lang=lang, slow=False)
        tts.save(filepath)
        
        return jsonify({
            'status': 'success',
            'audio_url': f"/static/audio/{filename}"
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/translate', methods=['POST'])
def translate_text():
    text = request.json.get('text', '')
    dest_lang = request.json.get('lang', 'en')
    
    if not text:
        return jsonify({'error': 'No text provided'}), 400
    
    try:
        translation = translator.translate(text, dest=dest_lang)
        return jsonify({
            'status': 'success',
            'translation': translation.text,
            'src_lang': translation.src,
            'dest_lang': translation.dest
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/static/audio/<filename>')
def serve_audio(filename):
    return send_from_directory(app.config['AUDIO_FOLDER'], filename)

if __name__ == '__main__':
    app.run(debug=True, threaded=True)