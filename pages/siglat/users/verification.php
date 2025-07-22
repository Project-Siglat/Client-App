<rewrite_this>
<?php
include "./config/env.php";
$API = $_ENV["API"];
?>
<style>
    body {
        background-color: #1a1a1a;
        color: #fff;
        font-family: Arial, sans-serif;
    }

    #verification-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    #loading {
        color: #ffeb3b;
        text-align: center;
        font-size: 18px;
    }

    .tabs-container {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #333;
    }

    .tab {
        padding: 12px 24px;
        cursor: pointer;
        border: none;
        background: none;
        color: #ccc;
        font-size: 16px;
        font-weight: bold;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }

    .tab:hover {
        color: #fff;
    }

    .tab.active {
        color: #fff;
    }

    .tab.pending.active {
        border-bottom-color: #ffeb3b;
        color: #ffeb3b;
    }

    .tab.approved.active {
        border-bottom-color: #4caf50;
        color: #4caf50;
    }

    .tab.rejected.active {
        border-bottom-color: #f44336;
        color: #f44336;
    }

    .verification-card {
        background: #2a2a2a;
        border: 2px solid #ffeb3b;
        border-radius: 8px;
        margin: 15px 0;
        padding: 15px;
        display: grid;
        grid-template-columns: 100px 1fr auto;
        gap: 15px;
        align-items: center;
    }

    .verification-card.card-pending {
        border-color: #ffeb3b;
    }

    .verification-card.card-approved {
        border-color: #4caf50;
    }

    .verification-card.card-rejected {
        border-color: #f44336;
    }

    .verification-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
        cursor: pointer;
        border: 1px solid #ffeb3b;
    }

    .verification-details {
        font-size: 14px;
        line-height: 1.4;
    }

    .verification-details h3 {
        color: #ffeb3b;
        margin: 0 0 8px 0;
        font-size: 16px;
    }

    .verification-details p {
        margin: 4px 0;
        color: #ccc;
    }

    .verification-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s;
    }

    .btn-approve {
        background-color: #4caf50;
        color: white;
    }

    .btn-approve:hover {
        background-color: #45a049;
    }

    .btn-reject {
        background-color: #f44336;
        color: white;
    }

    .btn-reject:hover {
        background-color: #da190b;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.8);
    }

    .modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #2a2a2a;
        padding: 20px;
        border-radius: 8px;
        border: 2px solid #ffeb3b;
        max-width: 90%;
        max-height: 90%;
    }

    .modal-image {
        max-width: 100%;
        max-height: 70vh;
        object-fit: contain;
    }

    .remarks-modal .modal-content {
        max-width: 500px;
        width: 90%;
    }

    .remarks-modal h3 {
        color: #ffeb3b;
        margin-top: 0;
        margin-bottom: 15px;
    }

    .remarks-modal textarea {
        width: 100%;
        min-height: 100px;
        background: #1a1a1a;
        border: 1px solid #ffeb3b;
        color: #fff;
        padding: 10px;
        border-radius: 5px;
        font-family: Arial, sans-serif;
        resize: vertical;
    }

    .remarks-modal .modal-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 15px;
    }

    .btn-cancel {
        background-color: #666;
        color: white;
    }

    .btn-cancel:hover {
        background-color: #555;
    }

    .close {
        color: #ffeb3b;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover {
        color: #f44336;
    }

    .status {
        padding: 2px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }

    .status-pending {
        background-color: #ffeb3b;
        color: #1a1a1a;
    }

    .status-approved {
        background-color: #4caf50;
        color: white;
    }

    .status-rejected {
        background-color: #f44336;
        color: white;
    }

    .hidden {
        display: none !important;
    }
</style>

<div id="verification-container">
    <div class="tabs-container">
        <button class="tab pending active" data-filter="pending">Pending</button>
        <button class="tab approved" data-filter="approved">Approved</button>
        <button class="tab rejected" data-filter="disapproved">Disapproved</button>
        <button class="tab" data-filter="all">All</button>
    </div>
    <div id="loading">Loading verifications...</div>
    <div id="verification-list"></div>
</div>

<div id="imageModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <img id="modalImage" class="modal-image" alt="Verification Image">
    </div>
</div>

<div id="remarksModal" class="modal remarks-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="remarksTitle">Add Remarks</h3>
        <textarea id="remarksText" placeholder="Enter remarks for this verification decision..."></textarea>
        <div class="modal-actions">
            <button class="btn btn-cancel" onclick="closeRemarksModal()">Cancel</button>
            <button class="btn" id="remarksSubmitBtn" onclick="submitRemarks()">Submit</button>
        </div>
    </div>
</div>

<script>
let allVerifications = [];
let currentFilter = 'pending';
let currentVerificationId = null;
let currentAction = null;

// Get API base URL from PHP
const API_BASE = "<?php echo $API; ?>";

async function loadVerifications() {
    try {
        const response = await fetch(API_BASE + '/api/v1/Admin/verify');
        const verifications = await response.json();

        allVerifications = verifications;

        const loadingDiv = document.getElementById('loading');
        loadingDiv.style.display = 'none';

        displayVerifications();

    } catch (error) {
        document.getElementById('loading').innerHTML = `<p style="color: #f44336;">Error loading verifications: ${error.message}</p>`;
    }
}

function displayVerifications() {
    const listDiv = document.getElementById('verification-list');

    let filteredVerifications = allVerifications;
    if (currentFilter !== 'all') {
        filteredVerifications = allVerifications.filter(v =>
            v.status.toLowerCase() === currentFilter
        );
    }

    if (filteredVerifications.length === 0) {
        listDiv.innerHTML = '<p style="color: #ffeb3b; text-align: center;">No verifications found.</p>';
        return;
    }

    let html = '';
    filteredVerifications.forEach(verification => {
        // Map status to correct class and label
        let statusClass = verification.status.toLowerCase();
        let statusLabel = verification.status;
        if (statusClass === 'disapproved') {
            statusClass = 'rejected';
            statusLabel = 'Disapproved';
        }
        html += `
            <div class="verification-card card-${statusClass}">
                <div>
                    ${verification.b64Image ?
                        `<img src="data:image/jpeg;base64,${verification.b64Image}"
                              class="verification-image"
                              alt="Verification Image"
                              onclick="openModal('data:image/jpeg;base64,${verification.b64Image}')">` :
                        '<div style="width: 80px; height: 80px; background: #444; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #888;">No Image</div>'
                    }
                </div>
                <div class="verification-details">
                    <h3>ID: ${verification.id}</h3>
                    <p><strong>Type:</strong> ${verification.verificationType}</p>
                    <p><strong>Status:</strong> <span class="status status-${statusClass}">${statusLabel}</span></p>
                    <p><strong>Created:</strong> ${new Date(verification.createdAt).toLocaleDateString()}</p>
                    <p><strong>Updated:</strong> ${new Date(verification.updatedAt).toLocaleDateString()}</p>
                    ${verification.remarks ? `<p><strong>Remarks:</strong> ${verification.remarks}</p>` : ''}
                </div>
                <div class="verification-actions">
                    ${verification.status.toLowerCase() === 'pending' ? `
                        <button class="btn btn-approve" onclick="openRemarksModal('${verification.id}', 'approved')">
                            Approve
                        </button>
                        <button class="btn btn-reject" onclick="openRemarksModal('${verification.id}', 'disapproved')">
                            Disapprove
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    });

    listDiv.innerHTML = html;
}

function switchTab(filter) {
    currentFilter = filter;

    // Update tab styles
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });

    document.querySelector(`.tab[data-filter="${filter}"]`).classList.add('active');

    displayVerifications();
}

function openModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');

    modal.style.display = 'block';
    modalImage.src = imageSrc;
}

function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
}

function openRemarksModal(verificationId, action) {
    currentVerificationId = verificationId;
    currentAction = action;

    const modal = document.getElementById('remarksModal');
    const title = document.getElementById('remarksTitle');
    const submitBtn = document.getElementById('remarksSubmitBtn');

    if (action === 'approved') {
        title.textContent = 'Approve Verification';
        submitBtn.className = 'btn btn-approve';
        document.getElementById('remarksText').placeholder = 'Optional remarks for approval...';
    } else {
        title.textContent = 'Disapprove Verification';
        submitBtn.className = 'btn btn-reject';
        document.getElementById('remarksText').placeholder = 'Please provide a reason for disapproval...';
    }

    document.getElementById('remarksText').value = '';
    modal.style.display = 'block';
}

function closeRemarksModal() {
    document.getElementById('remarksModal').style.display = 'none';
    currentVerificationId = null;
    currentAction = null;
}

async function submitRemarks() {
    const remarks = document.getElementById('remarksText').value.trim();

    if (currentAction === 'disapproved' && !remarks) {
        alert('Please enter a reason for disapproval.');
        return;
    }

    await updateVerificationStatus(currentVerificationId, currentAction, remarks);
    closeRemarksModal();
}

async function updateVerificationStatus(id, status, remarks) {
    try {
        // Find the verification object to send full DTO
        const verification = allVerifications.find(v => v.id === id);
        if (!verification) {
            alert('Verification not found.');
            return;
        }

        // Only status values: approved, disapproved, pending
        const payload = {
            Id: verification.id,
            B64Image: verification.b64Image,
            VerificationType: verification.verificationType,
            Remarks: remarks,
            Status: status,
            CreatedAt: verification.createdAt,
            UpdatedAt: new Date().toISOString()
        };

        const response = await fetch(API_BASE + '/api/v1/Admin/verification-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
        });

        if (response.ok) {
            loadVerifications(); // Reload the list
        } else {
            alert('Failed to update verification status');
        }
    } catch (error) {
        alert('Error updating verification: ' + error.message);
    }
}

// Modal and tab event listeners
document.addEventListener('DOMContentLoaded', function() {
    const imageModal = document.getElementById('imageModal');
    const remarksModal = document.getElementById('remarksModal');
    const closeBtns = document.querySelectorAll('.close');

    closeBtns.forEach(btn => {
        btn.onclick = function() {
            imageModal.style.display = 'none';
            remarksModal.style.display = 'none';
        };
    });

    window.onclick = function(event) {
        if (event.target == imageModal) {
            closeModal();
        }
        if (event.target == remarksModal) {
            closeRemarksModal();
        }
    }

    // Tab event listeners
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', function() {
            switchTab(this.dataset.filter);
        });
    });

    loadVerifications();
});
</script>
</rewrite_this>
