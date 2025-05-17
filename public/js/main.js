// Global Variables
let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Form Validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});

// Image Upload Preview
function previewImages(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            let preview = input.closest('.form-group').querySelector('.img-preview');
            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'img-preview';
                input.closest('.form-group').appendChild(preview);
            }
            preview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" alt="Preview">';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Real-time Search
function liveSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    searchInput.addEventListener('input', function(e) {
        const query = e.target.value;
        if (query.length >= 3) {
            fetch('/api/search?q=' + query)
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data);
                });
        }
    });
}

// Display Search Results
function displaySearchResults(items) {
    const resultsContainer = document.getElementById('searchResults');
    if (!resultsContainer) return;

    if (items.length === 0) {
        resultsContainer.innerHTML = '<p>No results found.</p>';
        return;
    }

    let html = '';
    items.forEach(item => {
        html += `
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title">${item.title}</h5>
                    <p class="card-text">${item.description}</p>
                    <p class="card-text"><small class="text-muted">${item.location}</small></p>
                    <a href="/items/${item.id}" class="btn btn-primary">View Details</a>
                </div>
            </div>
        `;
    });
    resultsContainer.innerHTML = html;
}

// QR Code Scanner
async function scanQR() {
    try {
        const video = document.createElement('video');
        const canvasElement = document.createElement('canvas');
        const canvas = canvasElement.getContext('2d');
        const qrResult = document.getElementById('qrResult');
        let scanning = true;

        video.setAttribute('playsinline', '');
        video.style.width = '100%';
        video.style.maxWidth = '600px';

        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        video.srcObject = stream;
        video.play();

        canvasElement.width = video.videoWidth;
        canvasElement.height = video.videoHeight;

        function tick() {
            if (!scanning) {
                video.srcObject.getTracks().forEach(track => track.stop());
                return;
            }
            canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
            const imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            if (code) {
                scanning = false;
                qrResult.textContent = code.data;
                // Process QR code data
                processQRCode(code.data);
            }
            requestAnimationFrame(tick);
        }
        tick();
    } catch (err) {
        console.error(err);
        alert('Error accessing camera: ' + err.message);
    }
}

// Process QR Code
function processQRCode(data) {
    fetch('/api/qr/scan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ qr_data: data })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = `/items/${data.item.id}`;
        } else {
            alert(data.error || 'Error processing QR code');
        }
    });
}

// Chat Interface
function initializeChat() {
    const chatContainer = document.getElementById('chat-container');
    if (!chatContainer) return;

    // WebSocket connection
    const socket = new WebSocket('ws://' + window.location.host + '/ws/chat');

    socket.onmessage = function(event) {
        const message = JSON.parse(event.data);
        displayMessage(message);
    };

    // Send message
    document.getElementById('send-message').addEventListener('click', function() {
        const message = document.getElementById('message-input').value;
        if (message.trim()) {
            socket.send(JSON.stringify({
                type: 'message',
                content: message,
                timestamp: new Date().toISOString()
            }));
            document.getElementById('message-input').value = '';
        }
    });
}

// Display Message
function displayMessage(message) {
    const chatMessages = document.getElementById('chat-messages');
    const messageElement = document.createElement('div');
    messageElement.className = 'chat-message';
    messageElement.innerHTML = `
        <div class="message-content">
            <p>${message.content}</p>
            <small class="message-timestamp">${new Date(message.timestamp).toLocaleString()}</small>
        </div>
    `;
    chatMessages.appendChild(messageElement);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Notification System
function initializeNotifications() {
    const notificationBell = document.getElementById('notification-bell');
    const notificationCount = document.getElementById('notification-count');
    const notificationList = document.getElementById('notification-list');

    if (!notificationBell) return;

    // Fetch notifications
    fetch('/api/notifications')
        .then(response => response.json())
        .then(data => {
            notificationCount.textContent = data.unread_count;
            updateNotificationList(data.notifications);
        });

    // Mark as read
    notificationBell.addEventListener('click', function() {
        fetch('/api/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            notificationCount.textContent = '0';
        });
    });
}

// Update Notification List
function updateNotificationList(notifications) {
    const notificationList = document.getElementById('notification-list');
    if (!notificationList) return;

    let html = '';
    notifications.forEach(notification => {
        html += `
            <div class="notification-item ${notification.read ? '' : 'unread'}">
                <div class="notification-content">
                    <p>${notification.message}</p>
                    <small>${new Date(notification.created_at).toLocaleString()}</small>
                </div>
                <button class="mark-read" data-id="${notification.id}">✓</button>
            </div>
        `;
    });
    notificationList.innerHTML = html;

    // Add click handlers for mark as read
    document.querySelectorAll('.mark-read').forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closest('.notification-item').classList.remove('unread');
                }
            });
        });
    });
}
