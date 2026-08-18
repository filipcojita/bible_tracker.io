// Sermon Upload and View Modals JavaScript

// Get modal elements when they are created
function getUploadModal() {
    let modal = document.getElementById('sermon-upload-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'sermon-upload-modal';
        modal.className = 'sermon-modal';
        modal.innerHTML = `
            <div class="sermon-modal-content">
                <div class="sermon-modal-header">
                    <h2 id="sermon-upload-title">📤 Încarcă Notițe Predică</h2>
                    <button class="sermon-modal-close" onclick="closeSermonUploadModal()">&times;</button>
                </div>
                <div class="sermon-modal-body">
                    <div id="sermon-upload-message" class="sermon-message"></div>
                    <form id="sermon-upload-form" onsubmit="submitSermonForm(event)">
                        <input type="hidden" id="sermon-date-input" name="sermon_date">
                        
                        <div class="sermon-form-group">
                            <label for="sermon-file">Selectează Fișier Notițe (txt, pdf, doc, docx, odt):</label>
                            <input type="file" id="sermon-file" name="sermon_file" accept=".txt,.pdf,.doc,.docx,.odt,.pages" required>
                            <small style="color: #666; margin-top: 5px; display: block;">Maxim 10MB. Acceptate: txt, pdf, doc, docx, odt</small>
                        </div>
                        
                        <div class="sermon-form-group">
                            <label for="sermon-notes">Note Aditionale (opțional):</label>
                            <textarea id="sermon-notes" name="sermon_notes" placeholder="Adaugă orice note suplimentare..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="sermon-modal-footer">
                    <button class="sermon-btn sermon-btn-secondary" onclick="closeSermonUploadModal()">Anulează</button>
                    <button class="sermon-btn sermon-btn-primary" onclick="submitSermonForm()">📤 Încarcă</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    return modal;
}

function getViewModal() {
    let modal = document.getElementById('sermon-view-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'sermon-view-modal';
        modal.className = 'sermon-modal sermon-view-modal';
        modal.innerHTML = `
            <div class="sermon-modal-content">
                <div class="sermon-modal-header">
                    <h2>📖 Vizualizează Submission</h2>
                    <button class="sermon-modal-close" onclick="closeSermonViewModal()">&times;</button>
                </div>
                <div class="sermon-modal-body">
                    <div id="sermon-view-message" class="sermon-message"></div>
                    <div id="sermon-view-details" class="sermon-view-details"></div>
                </div>
                <div class="sermon-modal-footer">
                    <a id="sermon-download-btn" class="sermon-btn sermon-btn-primary" target="_blank">📥 Descarcă Fișierul</a>
                    <button class="sermon-btn sermon-btn-secondary" onclick="closeSermonViewModal()">Închide</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    return modal;
}

function showSermonUploadModal(date) {
    const modal = getUploadModal();
    document.getElementById('sermon-date-input').value = date;
    
    // Format date for display
    const dateObj = new Date(date + 'T00:00:00');
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = dateObj.toLocaleDateString('ro-RO', options);
    document.getElementById('sermon-upload-title').textContent =
    `📤 Încarcă Notițe Predică - ${formattedDate}`;
    
    // Reset form
    document.getElementById('sermon-upload-form').reset();
    clearSermonMessage('upload');
    
    modal.classList.add('show');
}

function closeSermonUploadModal() {
    const modal = document.getElementById('sermon-upload-modal');
    if (modal) {
        modal.classList.remove('show');
    }
}

function showSermonViewModal(date, submissionId) {
    const modal = getViewModal();
    clearSermonMessage('view');
    
    // Fetch submission details
    fetch(`sermon_upload.php?id=${submissionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showSermonMessage('view', data.error, 'error');
                return;
            }
            
            // Format date for display
            const dateObj = new Date(data.sermon_date + 'T00:00:00');
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = dateObj.toLocaleDateString('ro-RO', options);
            
            const detailsHtml = `
                <dl>
                    <dt>📅 Data Sermon:</dt>
                    <dd>${formattedDate}</dd>
                    <dt>📄 Fișier:</dt>
                    <dd>${escapeHtml(data.file_name)}</dd>
                </dl>
            `;
            
            document.getElementById('sermon-view-details').innerHTML = detailsHtml;
            
            // Set download link
            const downloadBtn = document.getElementById('sermon-download-btn');
            downloadBtn.href = `sermon_download.php?id=${submissionId}`;
            downloadBtn.download = data.file_name;
        })
        .catch(error => {
            console.error('Error:', error);
            showSermonMessage('view', 'Eroare la încărcarea detaliilor.', 'error');
        });
    
    modal.classList.add('show');
}

function closeSermonViewModal() {
    const modal = document.getElementById('sermon-view-modal');
    if (modal) {
        modal.classList.remove('show');
    }
}

function submitSermonForm(event) {
    if (event) {
        event.preventDefault();
    }
    
    const fileInput = document.getElementById('sermon-file');
    const dateInput = document.getElementById('sermon-date-input');
    const messageDiv = document.getElementById('sermon-upload-message');
    const submitBtn = event ? event.target.closest('button') : document.querySelector('.sermon-btn-primary');
    
    if (!fileInput.files.length) {
        showSermonMessage('upload', '❌ Te rog selectează un fișier.', 'error');
        return;
    }
    
    const file = fileInput.files[0];
    
    // Check file size
    if (file.size > 10 * 1024 * 1024) {
        showSermonMessage('upload', '❌ Fișierul este prea mare. Maximum 10MB.', 'error');
        return;
    }
    
    // Check file type
    const allowedExtensions = ['txt', 'pdf', 'doc', 'docx', 'odt', 'pages'];
    const fileExtension = file.name.split('.').pop().toLowerCase();
    if (!allowedExtensions.includes(fileExtension)) {
        showSermonMessage('upload', '❌ Tip de fișier necunoscut.', 'error');
        return;
    }
    
    // Submit form
    const formData = new FormData();
    formData.append('sermon_date', dateInput.value);
    formData.append('sermon_file', file);
    
    // Show loading state
    const originalButtonText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="sermon-loading"></span>Se încarcă...';
    
    fetch('sermon_upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSermonMessage('upload', data.message, 'success');
            setTimeout(() => {
                closeSermonUploadModal();
                location.reload(); // Refresh to update calendar
            }, 2000);
        } else {
            showSermonMessage('upload', data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = originalButtonText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showSermonMessage('upload', '❌ Eroare la comunicare cu serverul.', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = originalButtonText;
    });
}

function showSermonMessage(modalType, message, type) {
    const messageDiv = document.getElementById(`sermon-${modalType}-message`);
    if (!messageDiv) return;
    
    messageDiv.textContent = message;
    messageDiv.className = `sermon-message show ${type}`;
}

function clearSermonMessage(modalType) {
    const messageDiv = document.getElementById(`sermon-${modalType}-message`);
    if (messageDiv) {
        messageDiv.textContent = '';
        messageDiv.className = 'sermon-message';
    }
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const uploadModal = document.getElementById('sermon-upload-modal');
    const viewModal = document.getElementById('sermon-view-modal');
    
    if (event.target === uploadModal) {
        closeSermonUploadModal();
    }
    if (event.target === viewModal) {
        closeSermonViewModal();
    }
}
