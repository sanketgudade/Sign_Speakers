import numpy as np
import pickle
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score

# Load data
data_dict = pickle.load(open('data.pickle', 'rb'))
raw_data = data_dict['data']
raw_labels = data_dict['labels']

# Clean data: only keep vectors with 42 elements
clean_data = [d for d in raw_data if len(d) == 42]
clean_labels = [raw_labels[i] for i, d in enumerate(raw_data) if len(d) == 42]

data = np.array(clean_data)
labels = np.array(clean_labels)

# Train/test split
x_train, x_test, y_train, y_test = train_test_split(
    data, labels, test_size=0.2, shuffle=True, stratify=labels, random_state=42
)

# Train model
model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(x_train, y_train)

# Evaluate
y_predict = model.predict(x_test)
score = accuracy_score(y_predict, y_test)
print(f"✅ {score*100:.2f}% of samples classified correctly.")

# Save model
with open('model.p', 'wb') as f:
    pickle.dump({'model': model}, f)

print("✅ Trained model saved to 'model.p'")
