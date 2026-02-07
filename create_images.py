import os
import cv2

# Path to store image data
DATA_DIR = './data'
if not os.path.exists(DATA_DIR):
    os.makedirs(DATA_DIR)

number_of_classes = 26  # For A to Z
dataset_size = 100      # Images per class

# Open the webcam (try 0 or 1 or 2 depending on your system)
cap = cv2.VideoCapture(0)

if not cap.isOpened():
    print("Error: Could not open webcam.")
    exit()

for j in range(number_of_classes):
    class_dir = os.path.join(DATA_DIR, str(j))
    if not os.path.exists(class_dir):
        os.makedirs(class_dir)

    print(f'\nCollecting data for class {j}')
    
    # Step 1: Show prompt to get ready
    while True:
        ret, frame = cap.read()
        if not ret:
            continue

        cv2.putText(frame, 'Get Ready - Press "Q" to start capturing', (50, 50),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.8, (0, 255, 0), 2)
        cv2.imshow('frame', frame)

        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    # Step 2: Start capturing images
    counter = 0
    while counter < dataset_size:
        ret, frame = cap.read()
        if not ret:
            continue

        cv2.imshow('frame', frame)
        img_path = os.path.join(class_dir, f'{counter}.jpg')
        cv2.imwrite(img_path, frame)
        counter += 1

        key = cv2.waitKey(1)
        if key & 0xFF == ord('q'):  # Optionally break mid-way
            print("Early exit from image capture.")
            break

    print(f"Finished collecting {counter} images for class {j}")

cap.release()
cv2.destroyAllWindows()
