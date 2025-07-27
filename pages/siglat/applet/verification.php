<?php
include "./config/env.php";
$API = $_ENV["API"];
?>
<style>
    body {
        background-color: #2e3440;
        color: #eceff4;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        height: 100vh;
        width: 100vw;
    }

    #verification-container {
        width: 100%;
        height: 100vh;
        padding: 20px;
        box-sizing: border-box;
        overflow-y: auto;
    }

    #loading {
        color: #ebcb8b;
        text-align: center;
        font-size: 18px;
    }

    .tabs-container {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #4c566a;
    }

    .tab {
        padding: 12px 24px;
        cursor: pointer;
        border: none;
        background: none;
        color: #d8dee9;
        font-size: 16px;
        font-weight: bold;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }

    .tab:hover {
        color: #eceff4;
    }

    .tab.active {
        color: #eceff4;
    }

    .tab.pending.active {
        border-bottom-color: #ebcb8b;
        color: #ebcb8b;
    }

    .tab.approved.active {
        border-bottom-color: #a3be8c;
        color: #a3be8c;
    }

    .tab.rejected.active {
        border-bottom-color: #bf616a;
        color: #bf616a;
    }

    .verification-card {
        background: #3b4252;
        border: 2px solid #ebcb8b;
        border-radius: 8px;
        margin: 15px 0;
        padding: 15px;
        display: grid;
        grid-template-columns: 100px 1fr auto;
        gap: 15px;
        align-items: center;
    }

    .verification-card.card-pending {
        border-color: #ebcb8b;
    }

    .verification-card.card-approved {
        border-color: #a3be8c;
    }

    .verification-card.card-rejected {
        border-color: #bf616a;
    }

    .verification-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
        cursor: pointer;
        border: 1px solid #88c0d0;
    }

    .verification-details {
        font-size: 14px;
        line-height: 1.4;
    }

    .verification-details h3 {
        color: #88c0d0;
        margin: 0 0 8px 0;
        font-size: 16px;
    }

    .verification-details p {
        margin: 4px 0;
        color: #d8dee9;
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
        background-color: #a3be8c;
        color: #2e3440;
    }

    .btn-approve:hover {
        background-color: #8fbaa0;
    }

    .btn-reject {
        background-color: #bf616a;
        color: #eceff4;
    }

    .btn-reject:hover {
        background-color: #a54e56;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(46, 52, 64, 0.8);
    }

    .modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #3b4252;
        padding: 20px;
        border-radius: 8px;
        border: 2px solid #88c0d0;
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
        color: #88c0d0;
        margin-top: 0;
        margin-bottom: 15px;
    }

    .remarks-modal textarea {
        width: 100%;
        min-height: 100px;
        background: #2e3440;
        border: 1px solid #4c566a;
        color: #eceff4;
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
        background-color: #4c566a;
        color: #eceff4;
    }

    .btn-cancel:hover {
        background-color: #434c5e;
    }

    .close {
        color: #88c0d0;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover {
        color: #bf616a;
    }

    .status {
        padding: 2px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }

    .status-pending {
        background-color: #ebcb8b;
        color: #2e3440;
    }

    .status-approved {
        background-color: #a3be8c;
        color: #2e3440;
    }

    .status-rejected {
        background-color: #bf616a;
        color: #eceff4;
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
        document.getElementById('loading').innerHTML = `<p style="color: #bf616a;">Error loading verifications: ${error.message}</p>`;
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
        listDiv.innerHTML = '<p style="color: #ebcb8b; text-align: center;">No verifications found.</p>';
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
                        '<div style="width: 80px; height: 80px; background: #4c566a; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #d8dee9;">No Image</div>'
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
