document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const videoFeed = document.getElementById('videoFeed');
    const currentPrediction = document.getElementById('currentPrediction');
    const sentenceDisplay = document.getElementById('sentenceDisplay');
    const clearBtn = document.getElementById('clearBtn');
    const speakBtn = document.getElementById('speakBtn');
    const downloadBtn = document.getElementById('downloadBtn');
    const translateBtn = document.getElementById('translateBtn');
    const languageSelect = document.getElementById('languageSelect');
    const translationResult = document.getElementById('translationResult');
    const audioPlayer = document.getElementById('audioPlayer');
    
    // State variables
    let currentSentence = '';
    let audioUrl = '';
    
    // Check prediction every second
    setInterval(checkPrediction, 1000);
    
    // Event listeners
    clearBtn.addEventListener('click', clearSentence);
    speakBtn.addEventListener('click', speakSentence);
    downloadBtn.addEventListener('click', downloadAudio);
    translateBtn.addEventListener('click', translateSentence);
    
    // Keyboard shortcuts
    document.addEventListener('keydown', handleKeyPress);
    
    // Check for new predictions
    function checkPrediction() {
        fetch('/get_prediction')
            .then(response => response.json())
            .then(data => {
                if (data.prediction) {
                    // Update current prediction display
                    currentPrediction.textContent = data.prediction;
                    
                    // Add pulse animation
                    currentPrediction.classList.add('pulse');
                    setTimeout(() => {
                        currentPrediction.classList.remove('pulse');
                    }, 300);
                    
                    // Handle special commands
                    if (data.prediction === 'ENTER') {
                        // Just show the sentence without adding
                        currentSentence = data.sentence;
                    } else {
                        // Update sentence
                        currentSentence = data.sentence;
                    }
                    
                    // Update sentence display
                    sentenceDisplay.textContent = currentSentence || 'Show a sign to begin';
                    
                    // Enable/disable buttons based on sentence
                    if (currentSentence.trim().length > 0) {
                        speakBtn.disabled = false;
                        downloadBtn.disabled = false;
                        translateBtn.disabled = false;
                    } else {
                        speakBtn.disabled = true;
                        downloadBtn.disabled = true;
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching prediction:', error);
            });
    }
    
    // Clear the current sentence
    function clearSentence() {
        fetch('/clear_sentence')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'cleared') {
                    currentSentence = '';
                    sentenceDisplay.textContent = 'Show a sign to begin';
                    currentPrediction.textContent = '?';
                    speakBtn.disabled = true;
                    downloadBtn.disabled = true;
                    translationResult.textContent = 'Translation will appear here';
                }
            });
    }
    
    // Convert text to speech
    function speakSentence() {
        if (!currentSentence) return;
        
        const lang = languageSelect.value;
        
        fetch('/text_to_speech', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                text: currentSentence,
                lang: lang
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                audioUrl = data.audio_url;
                audioPlayer.src = audioUrl;
                audioPlayer.play();
                
                // Enable download button
                downloadBtn.disabled = false;
            } else {
                alert('Error generating speech: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to generate speech');
        });
    }
    
    // Download audio file
    function downloadAudio() {
        if (!audioUrl) return;
        
        const link = document.createElement('a');
        link.href = audioUrl;
        link.download = 'sign-language-speech.mp3';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    // Translate sentence
    function translateSentence() {
        if (!currentSentence) return;
        
        const lang = languageSelect.value;
        
        fetch('/translate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                text: currentSentence,
                lang: lang
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                translationResult.textContent = data.translation;
            } else {
                translationResult.textContent = 'Error: ' + (data.error || 'Translation failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            translationResult.textContent = 'Translation error occurred';
        });
    }
    
    // Handle keyboard shortcuts
    function handleKeyPress(e) {
        // Space bar (add space)
        if (e.key === ' ') {
            e.preventDefault();
            addToSentence(' ');
        }
        // Enter key (finish sentence)
        else if (e.key === 'Enter') {
            e.preventDefault();
            addToSentence('\n'); // Or handle differently if needed
        }
    }
    
    // Add to sentence (for keyboard input)
    function addToSentence(text) {
        currentSentence += text;
        sentenceDisplay.textContent = currentSentence;
        
        // Enable buttons
        if (currentSentence.trim().length > 0) {
            speakBtn.disabled = false;
            downloadBtn.disabled = false;
        }
    }
});

// Add pulse animation
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    .pulse {
        animation: pulse 0.3s ease;
    }
`;
document.head.appendChild(style);