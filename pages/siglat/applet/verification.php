<style>
/* Nord Theme Variables */
:root {
    --nord0: #2e3440;
    --nord1: #3b4252;
    --nord2: #434c5e;
    --nord3: #4c566a;
    --nord4: #d8dee9;
    --nord5: #e5e9f0;
    --nord6: #eceff4;
    --nord7: #8fbcbb;
    --nord8: #88c0d0;
    --nord9: #81a1c1;
    --nord10: #5e81ac;
    --nord11: #bf616a;
    --nord12: #d08770;
    --nord13: #ebcb8b;
    --nord14: #a3be8c;
    --nord15: #b48ead;
}

#verification-container {
    max-width: 1200px;
    margin: 0 auto;
}

.filter-container {
    background-color: var(--nord1);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.filter-container h2 {
    margin: 0 0 16px 0;
    color: var(--nord6);
    font-size: 1.5rem;
    font-weight: 600;
}

#filterSelect {
    background-color: var(--nord2);
    color: var(--nord4);
    border: 2px solid var(--nord3);
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 14px;
    font-weight: 500;
    min-width: 160px;
    cursor: pointer;
    transition: all 0.2s ease;
}

#filterSelect:hover {
    border-color: var(--nord8);
    background-color: var(--nord3);
}

#filterSelect:focus {
    outline: none;
    border-color: var(--nord10);
    box-shadow: 0 0 0 3px rgba(94, 129, 172, 0.2);
}

#loading {
    text-align: center;
    padding: 40px;
    color: var(--nord4);
    font-size: 16px;
    background-color: var(--nord1);
    border-radius: 12px;
    margin-bottom: 24px;
}

#verification-list {
    display: grid;
    gap: 24px;
}

.verification-card {
    background-color: var(--nord1);
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 24px;
    align-items: start;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.verification-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.verification-image {
    width: 120px;
    height: 120px;
    border-radius: 8px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.2s ease;
    border: 2px solid var(--nord3);
}

.verification-image:hover {
    transform: scale(1.05);
}

.no-image {
    width: 120px;
    height: 120px;
    border-radius: 8px;
    background-color: var(--nord2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--nord4);
    font-size: 14px;
    border: 2px dashed var(--nord3);
}

.verification-details h3 {
    margin: 0 0 16px 0;
    color: var(--nord6);
    font-size: 1.25rem;
    font-weight: 600;
}

.verification-details p {
    margin: 8px 0;
    font-size: 14px;
    line-height: 1.5;
}

.verification-details strong {
    color: var(--nord5);
    font-weight: 600;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background-color: rgba(235, 203, 139, 0.2);
    color: var(--nord13);
}

.status-approved {
    background-color: rgba(163, 190, 140, 0.2);
    color: var(--nord14);
}

.status-rejected {
    background-color: rgba(191, 97, 106, 0.2);
    color: var(--nord11);
}

.verification-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.btn {
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    min-width: 120px;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.btn:active {
    transform: translateY(0);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.btn-approve {
    background-color: var(--nord14);
    color: var(--nord0);
}

.btn-approve:hover {
    background-color: #b3d196;
}

.btn-reject {
    background-color: var(--nord11);
    color: var(--nord6);
}

.btn-reject:hover {
    background-color: #d67b84;
}

.btn-cancel {
    background-color: var(--nord3);
    color: var(--nord4);
}

.btn-cancel:hover {
    background-color: var(--nord2);
}

/* Modal Styles */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

.modal-backdrop.show {
    opacity: 1;
}

.modal-content {
    transform: scale(0.7) translateY(-50px);
    opacity: 0;
    transition: all 0.3s ease-in-out;
    background-color: var(--nord1);
    border: border-[#434c5e];
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
}

.modal-content.show {
    transform: scale(1) translateY(0);
    opacity: 1;
}

.modal-close {
    position: absolute;
    top: 16px;
    right: 20px;
    color: var(--nord4);
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1001;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background-color: var(--nord11);
    color: var(--nord6);
}

#modalImage {
    max-width: 80vw;
    max-height: 80vh;
    border-radius: 8px;
    display: block;
}

.remarks-modal-content {
    padding: 32px;
    width: 500px;
    max-width: 90vw;
}

.remarks-modal-content h3 {
    margin: 0 0 24px 0;
    color: var(--nord6);
    font-size: 1.5rem;
    font-weight: 600;
}

#remarksText {
    width: 100%;
    min-height: 120px;
    padding: 16px;
    border: 2px solid var(--nord3);
    border-radius: 8px;
    background-color: var(--nord2);
    color: var(--nord4);
    font-family: inherit;
    font-size: 14px;
    line-height: 1.5;
    resize: vertical;
    margin-bottom: 24px;
    transition: border-color 0.2s ease;
}

#remarksText:focus {
    outline: none;
    border-color: var(--nord10);
    box-shadow: 0 0 0 3px rgba(94, 129, 172, 0.2);
}

#remarksText::placeholder {
    color: var(--nord3);
}

.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.no-results {
    text-align: center;
    padding: 60px 20px;
    color: var(--nord3);
    font-size: 16px;
    background-color: var(--nord1);
    border-radius: 12px;
    border: 2px dashed var(--nord3);
}

/* Toast Container */
.toast {
    min-width: 300px;
    padding: 12px 16px;
    border-radius: 8px;
    color: white;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.3s ease-in-out;
    z-index: 99999;
}

.toast.show {
    transform: translateX(0);
    opacity: 1;
}

.toast.success {
    background-color: #a3be8c;
    border-left: 4px solid #8fbcbb;
}

.toast.error {
    background-color: #bf616a;
    border-left: 4px solid #d08770;
}

.toast.info {
    background-color: #5e81ac;
    border-left: 4px solid #81a1c1;
}

@media (max-width: 768px) {
    .verification-card {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .verification-actions {
        flex-direction: row;
        justify-content: center;
    }

    .remarks-modal-content {
        width: 90vw;
        padding: 24px;
    }

    .modal-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}
</style>

<div id="verification-container">
    <div class="filter-container">
        <h2>Verification Management</h2>
        <select id="filterSelect">
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="disapproved">Disapproved</option>
            <option value="all">All</option>
        </select>
    </div>
    <div id="loading">Loading verifications...</div>
    <div id="verification-list"></div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="fixed right-4 space-y-2" style="top: 65px; z-index: 2147483647;"></div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 hidden z-50">
    <div class="modal-backdrop fixed inset-0 flex items-center justify-center p-4">
        <div class="modal-content mx-auto shadow-2xl">
            <span class="modal-close" onclick="closeImageModal()">&times;</span>
            <img id="modalImage" alt="Verification Image">
        </div>
    </div>
</div>

<!-- Remarks Modal -->
<div id="remarksModal" class="fixed inset-0 hidden z-50">
    <div class="modal-backdrop fixed inset-0 flex items-center justify-center p-4">
        <div class="modal-content remarks-modal-content mx-auto shadow-2xl">
            <span class="modal-close" onclick="closeRemarksModal()">&times;</span>
            <h3 id="remarksTitle">Add Remarks</h3>
            <textarea id="remarksText" placeholder="Enter remarks for this verification decision..."></textarea>
            <div class="modal-actions">
                <button class="btn btn-cancel" onclick="closeRemarksModal()">Cancel</button>
                <button id="remarksSubmitBtn" class="btn" onclick="submitRemarks()">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
let verificationsData = [];
let activeFilter = 'pending';
let selectedVerificationId = null;
let selectedAction = null;

// Get API base URL from JavaScript function
const apiBaseUrl = API();

// Utility functions
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;

    toastContainer.appendChild(toast);

    // Trigger animation
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    // Auto remove after 4 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 4000);
}

async function loadVerifications() {
    try {
        console.log('Loading verifications from:', apiBaseUrl + '/api/v1/Admin/verify');

        const response = await fetch(apiBaseUrl + '/api/v1/Admin/verify');

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const verifications = await response.json();
        console.log('Loaded verifications:', verifications);

        verificationsData = verifications || [];

        const loadingElement = document.getElementById('loading');
        loadingElement.style.display = 'none';

        displayVerifications();

    } catch (error) {
        console.error('Error loading verifications:', error);
        document.getElementById('loading').innerHTML = `<p style="color: #bf616a;">Error loading verifications: ${error.message}</p>`;
    }
}

function displayVerifications() {
    const listElement = document.getElementById('verification-list');

    let filteredVerifications = verificationsData;
    if (activeFilter !== 'all') {
        filteredVerifications = verificationsData.filter(v =>
            v.status && v.status.toLowerCase() === activeFilter
        );
    }

    if (filteredVerifications.length === 0) {
        listElement.innerHTML = '<div class="no-results">No verifications found for the selected filter.</div>';
        return;
    }

    let htmlContent = '';
    filteredVerifications.forEach(verification => {
        // Map status to correct class and label
        let statusClass = verification.status ? verification.status.toLowerCase() : 'pending';
        let statusLabel = verification.status || 'Pending';

        if (statusClass === 'disapproved') {
            statusClass = 'rejected';
            statusLabel = 'Disapproved';
        }

        const imageContent = verification.b64Image
            ? `<img class="verification-image" src="data:image/jpeg;base64,${verification.b64Image}"
                    alt="Verification Image"
                    onclick="openImageModal('data:image/jpeg;base64,${verification.b64Image}')">`
            : '<div class="no-image">No Image</div>';

        htmlContent += `
            <div class="verification-card">
                <div>${imageContent}</div>
                <div class="verification-details">
                    <h3>ID: ${verification.id || 'N/A'}</h3>
                    <p><strong>Type:</strong> ${verification.verificationType || 'N/A'}</p>
                    <p><strong>Status:</strong> <span class="status-badge status-${statusClass}">${statusLabel}</span></p>
                    <p><strong>Created:</strong> ${verification.createdAt ? new Date(verification.createdAt).toLocaleDateString() : 'N/A'}</p>
                    <p><strong>Updated:</strong> ${verification.updatedAt ? new Date(verification.updatedAt).toLocaleDateString() : 'N/A'}</p>
                    ${verification.remarks ? `<p><strong>Remarks:</strong> ${verification.remarks}</p>` : ''}
                </div>
                <div class="verification-actions">
                    ${statusClass === 'pending' ? `
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

    listElement.innerHTML = htmlContent;
}

function switchFilter(filter) {
    activeFilter = filter;
    displayVerifications();
}

function openImageModal(imageSrc) {
    console.log('Opening image modal with src:', imageSrc);

    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const backdrop = modal.querySelector('.modal-backdrop');
    const content = modal.querySelector('.modal-content');

    if (!modal || !modalImage) {
        console.error('Modal elements not found');
        return;
    }

    modalImage.src = imageSrc;

    modal.classList.remove('hidden');
    setTimeout(() => {
        backdrop.classList.add('show');
        content.classList.add('show');
    }, 10);

    console.log('Image modal opened');
}

function closeImageModal() {
    console.log('Closing image modal');

    const modal = document.getElementById('imageModal');
    const backdrop = modal.querySelector('.modal-backdrop');
    const content = modal.querySelector('.modal-content');

    if (modal) {
        backdrop.classList.remove('show');
        content.classList.remove('show');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
}

function openRemarksModal(verificationId, action) {
    console.log('Opening remarks modal for verification:', verificationId, 'action:', action);

    selectedVerificationId = verificationId;
    selectedAction = action;

    const modal = document.getElementById('remarksModal');
    const modalTitle = document.getElementById('remarksTitle');
    const submitButton = document.getElementById('remarksSubmitBtn');
    const remarksText = document.getElementById('remarksText');
    const backdrop = modal.querySelector('.modal-backdrop');
    const content = modal.querySelector('.modal-content');

    if (!modal || !modalTitle || !submitButton || !remarksText) {
        console.error('Remarks modal elements not found');
        return;
    }

    if (action === 'approved') {
        modalTitle.textContent = 'Approve Verification';
        submitButton.className = 'btn btn-approve';
        remarksText.placeholder = 'Optional remarks for approval...';
    } else {
        modalTitle.textContent = 'Disapprove Verification';
        submitButton.className = 'btn btn-reject';
        remarksText.placeholder = 'Please provide a reason for disapproval...';
    }

    remarksText.value = '';

    modal.classList.remove('hidden');
    setTimeout(() => {
        backdrop.classList.add('show');
        content.classList.add('show');
    }, 10);

    // Focus on textarea
    setTimeout(() => remarksText.focus(), 100);

    console.log('Remarks modal opened');
}

function closeRemarksModal() {
    console.log('Closing remarks modal');

    const modal = document.getElementById('remarksModal');
    const backdrop = modal.querySelector('.modal-backdrop');
    const content = modal.querySelector('.modal-content');

    if (modal) {
        backdrop.classList.remove('show');
        content.classList.remove('show');

        setTimeout(() => {
            modal.classList.add('hidden');
            selectedVerificationId = null;
            selectedAction = null;
        }, 300);
    }
}

async function submitRemarks() {
    const remarksText = document.getElementById('remarksText').value.trim();

    if (selectedAction === 'disapproved' && !remarksText) {
        showToast('Please enter a reason for disapproval.', 'error');
        return;
    }

    // Disable submit button to prevent double submission
    const submitButton = document.getElementById('remarksSubmitBtn');
    const originalText = submitButton.textContent;
    submitButton.disabled = true;
    submitButton.textContent = 'Processing...';

    try {
        await updateVerificationStatus(selectedVerificationId, selectedAction, remarksText);
        closeRemarksModal();
    } catch (error) {
        console.error('Error submitting remarks:', error);
    } finally {
        // Re-enable button
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
}

async function updateVerificationStatus(id, status, remarks) {
    try {
        // Find the verification object
        const verification = verificationsData.find(v => v.id === id);
        if (!verification) {
            throw new Error('Verification not found.');
        }

        const endpoint = apiBaseUrl + '/api/v1/Admin/verification-action';

        // Create payload
        const requestPayload = {
            Id: verification.id,
            VerificationType: verification.verificationType,
            B64Image: verification.b64Image || '',
            Remarks: remarks,
            Status: status,
            CreatedAt: verification.createdAt,
            UpdatedAt: new Date().toISOString()
        };

        console.log('Updating verification status:', requestPayload);

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 30000);

        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestPayload),
            signal: controller.signal
        });

        clearTimeout(timeoutId);

        if (response.ok) {
            console.log('Verification status updated successfully');

            // Update local data
            const verificationIndex = verificationsData.findIndex(v => v.id === id);
            if (verificationIndex !== -1) {
                verificationsData[verificationIndex].status = status;
                verificationsData[verificationIndex].remarks = remarks;
                verificationsData[verificationIndex].updatedAt = new Date().toISOString();
            }

            // Refresh display
            displayVerifications();

            // Show success message
            showToast(`Verification ${status} successfully!`, 'success');
        } else {
            const errorText = await response.text();
            console.error('Server response error:', errorText);
            throw new Error(`Server error: ${response.status} - ${errorText}`);
        }
    } catch (error) {
        console.error('Error updating verification:', error);
        if (error.name === 'AbortError') {
            showToast('Request timed out. Please try again.', 'error');
        } else {
            showToast('Error updating verification: ' + error.message, 'error');
        }
        throw error;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing verification system...');

    // Prevent modal content clicks from closing the modal
    const modalContents = document.querySelectorAll('.modal-content');
    modalContents.forEach(content => {
        content.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });

    // Filter select event listener
    const filterSelect = document.getElementById('filterSelect');
    filterSelect.addEventListener('change', function() {
        switchFilter(this.value);
    });

    // Load verifications
    loadVerifications();
});
</script>
