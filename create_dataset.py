import os
import pickle
import cv2
import mediapipe as mp

# Initialize MediaPipe Hands
mp_hands = mp.solutions.hands
hands = mp_hands.Hands(static_image_mode=True, min_detection_confidence=0.3)

# Path to the dataset directory
DATA_DIR = './data'

# Containers for features and labels
data = []
labels = []

# Loop through each class folder (e.g., '0', '1', ..., '25')
for dir_ in os.listdir(DATA_DIR):
    dir_path = os.path.join(DATA_DIR, dir_)
    if not os.path.isdir(dir_path):
        continue  # Skip if not a directory

    # Loop through each image in the class folder
    for img_name in os.listdir(dir_path):
        img_path = os.path.join(dir_path, img_name)

        img = cv2.imread(img_path)
        if img is None:
            print(f"Warning: Could not read image {img_path}")
            continue

        img_rgb = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)

        # Process the image to get hand landmarks
        results = hands.process(img_rgb)

        # If hand landmarks are detected
        if results.multi_hand_landmarks:
            data_aux = []
            x_ = []
            y_ = []

            for hand_landmarks in results.multi_hand_landmarks:
                for lm in hand_landmarks.landmark:
                    x_.append(lm.x)
                    y_.append(lm.y)

                for lm in hand_landmarks.landmark:
                    data_aux.append(lm.x - min(x_))
                    data_aux.append(lm.y - min(y_))

            data.append(data_aux)
            labels.append(dir_)

# Save the extracted features and labels
with open('data.pickle', 'wb') as f:
    pickle.dump({'data': data, 'labels': labels}, f)

print(f"✅ Processed and saved {len(data)} samples to 'data.pickle'")
