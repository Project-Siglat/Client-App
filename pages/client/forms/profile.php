<?php
include "./config/env.php";
$API = $_ENV["API"];
?>

<style>
:root {
    --profile-modal-bg: linear-gradient(to bottom right, #18181b, #27272a 60%, #18181b 100%);
    --profile-modal-border: rgba(75, 85, 99, 0.5);
    --profile-modal-radius: 1.5rem;
    --profile-modal-shadow: 0 10px 40px 0 rgba(0,0,0,0.5);
    --profile-modal-width: 95vw;
    --profile-modal-max-width: 700px;
    --profile-modal-max-height: 95vh;

    --verify-form-bg: rgba(88, 28, 135, 0.20);
    --verify-form-border: rgba(168, 85, 247, 0.5);
    --verify-form-radius: 1rem;
    --verify-form-shadow: 0 8px 32px 0 rgba(0,0,0,0.5);

    --input-bg: rgba(55, 65, 81, 0.5);
    --input-border: rgba(75, 85, 99, 0.5);
    --input-radius: 0.375rem;
    --input-color: #fff;

    --label-color: #d1d5db;
    --label-size: 0.75rem;
    --label-weight: 500;

    --btn-radius: 0.5rem;
    --btn-font-weight: 500;
    --btn-shadow: 0 2px 8px 0 rgba(0,0,0,0.15);

    --toast-z: 60;
    --modal-z: 50;
    --verify-form-z: 70;
}

#profileModal {
    position: fixed !important;
    inset: 0;
    z-index: var(--modal-z);
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(8px);
    display: none;
    align-items: center;
    justify-content: center;
    place-items: center;
    place-content: center;
}

#profileModal.flex {
    display: flex !important;
}

#profileModal > .profile-modal-inner {
    background: var(--profile-modal-bg);
    border: 1px solid var(--profile-modal-border);
    border-radius: var(--profile-modal-radius);
    box-shadow: var(--profile-modal-shadow);
    width: var(--profile-modal-width);
    max-width: var(--profile-modal-max-width);
    max-height: var(--profile-modal-max-height);
    margin: 1rem auto;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    backdrop-filter: blur(4px);
    position: relative;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
}

#verificationForm {
    position: fixed !important;
    inset: 0;
    z-index: var(--verify-form-z);
    background: rgba(0,0,0,0.35);
    display: none;
    align-items: center;
    justify-content: center;
}

#verificationForm.flex {
    display: flex !important;
}

#verificationForm .verify-form-inner {
    background: var(--verify-form-bg);
    border: 1px solid var(--verify-form-border);
    border-radius: var(--verify-form-radius);
    box-shadow: var(--verify-form-shadow);
    width: 100%;
    max-width: 400px;
    margin: 0 1rem;
    position: relative;
}

#verificationForm label {
    color: var(--label-color);
    font-size: var(--label-size);
    font-weight: var(--label-weight);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

#verificationForm input[type="file"],
#verificationForm select,
#verificationForm input[type="text"],
#verificationForm input[type="password"] {
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    border-radius: var(--input-radius);
    color: var(--input-color);
    font-size: 0.95rem;
    width: 100%;
    padding: 0.5rem;
    margin-top: 0.25rem;
    margin-bottom: 0.5rem;
}

#verificationForm .flex.gap-2 > button {
    border-radius: var(--btn-radius);
    font-weight: var(--btn-font-weight);
    box-shadow: var(--btn-shadow);
}

@media (max-width: 640px) {
    #profileModal > .profile-modal-inner {
        max-width: 98vw;
        width: 98vw;
    }
    #verificationForm .verify-form-inner {
        max-width: 98vw;
    }
}
</style>

<button class="w-full text-left py-3 px-4 rounded-lg hover:bg-gray-800 text-yellow-400 text-base flex items-center gap-3 transition-all duration-300 hover:shadow-lg hover:scale-105 active:scale-95" onclick="fetchUserProfile()">
    <span class="text-xl">👤</span>
    <span class="font-medium">View Profile</span>
</button>

<!-- Toast Notification Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-[60] space-y-2" style="position:fixed;"></div>

<!-- Enhanced Floating Profile Modal -->
<div id="profileModal" class="hidden">
    <div class="profile-modal-inner">
        <!-- Header -->
        <div class="flex justify-between items-center p-4 sm:p-6 border-b border-gray-700/50 bg-gradient-to-r from-gray-900/80 to-gray-800/80 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center text-gray-900 text-lg sm:text-xl font-bold shadow-lg">
                    👤
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-yellow-400">User Profile</h2>
                    <p class="text-gray-400 text-xs sm:text-sm">Manage your account information</p>
                </div>
            </div>
            <button onclick="closeProfileModal()" class="text-gray-400 hover:text-white text-xl sm:text-2xl w-8 h-8 sm:w-10 sm:h-10 rounded-full hover:bg-gray-700 transition-all duration-300 flex items-center justify-center hover:scale-110 active:scale-95">&times;</button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-4 sm:p-6">
                <!-- Verification Status -->
                <div class="mb-4 p-3 rounded-lg border border-gray-700/50 hover:bg-gray-800/70 transition-all duration-300" id="verificationStatus">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span id="verificationIcon" class="text-xl">❓</span>
                            <div>
                                <label class="text-gray-300 text-xs font-medium block uppercase tracking-wide">Account Verification</label>
                                <span id="verificationText" class="text-sm font-medium">Not yet verified</span>
                            </div>
                        </div>
                        <button onclick="showVerificationForm()" id="verifyBtn" class="px-3 py-1.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:scale-95 flex items-center gap-2 text-sm">
                            <span>✅</span> <span>Verify</span>
                        </button>
                    </div>
                </div>

                <!-- Verification Form (floating, centered) -->
                <div id="verificationForm" class="hidden">
                    <div class="verify-form-inner mb-4 p-4">
                        <button onclick="hideVerificationForm()" class="absolute top-2 right-2 text-gray-400 hover:text-white text-2xl w-8 h-8 rounded-full hover:bg-gray-700 transition-all duration-300 flex items-center justify-center hover:scale-110 active:scale-95">&times;</button>
                        <h3 class="text-lg font-bold text-purple-400 mb-4">Account Verification</h3>
                        <div id="verificationError" class="mb-4 p-3 rounded-lg border border-red-500/50 bg-red-900/20 text-red-400 text-sm hidden"></div>

                        <!-- Document Type Selection -->
                        <div class="mb-4">
                            <label class="mb-2 block">Document Type</label>
                            <select id="documentType">
                                <option value="">Select a document type</option>
                                <option value="national_id">National ID</option>
                                <option value="postal_id">Postal ID</option>
                                <option value="school_id">School ID</option>
                                <option value="barangay_clearance">Barangay Clearance</option>
                                <option value="passport">Passport</option>
                                <option value="others">Others</option>
                            </select>
                        </div>

                        <!-- Photo Upload -->
                        <div class="mb-4">
                            <label class="mb-2 block">Upload Document Photo</label>
                            <input type="file" id="documentPhoto" accept="image/*" class="file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                        </div>

                        <!-- Form Actions -->
                        <div class="flex gap-2">
                            <button onclick="submitVerification()" id="submitVerificationBtn" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:scale-95 flex items-center gap-2 text-sm">
                                <span>📤</span> <span>Submit Verification</span>
                            </button>
                            <button onclick="hideVerificationForm()" class="px-4 py-2 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:scale-95 flex items-center gap-2 text-sm">
                                <span>❌</span> <span>Cancel</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="profileContent" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                    <!-- Profile content will be loaded here -->
                </div>

                <!-- Change Password Field -->
                <div class="mt-4 pt-4 border-t border-gray-700/50">
                    <div class="bg-gray-800/50 p-3 rounded-lg border border-gray-700/50 hover:bg-gray-800/70 transition-all duration-300">
                        <label class="text-gray-300 text-xs font-medium mb-1 block uppercase tracking-wide">Change Password</label>
                        <input id="newPasswordInput" type="password" placeholder="Enter new password" class="text-white bg-gray-700/50 p-2 rounded-md border border-gray-600/50 text-sm block w-full hover:bg-gray-700/70 transition-colors duration-200 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20" />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row justify-center gap-2 mt-4 pt-4 border-t border-gray-700/50">
                    <button onclick="enableEdit()" id="editBtn" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2 text-sm">
                        <span>✏️</span> <span>Edit Profile</span>
                    </button>
                    <button onclick="saveProfile()" id="saveBtn" class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:scale-95 hidden items-center justify-center gap-2 text-sm">
                        <span>💾</span> <span>Save Changes</span>
                    </button>
                    <button onclick="cancelEdit()" id="cancelBtn" class="px-4 py-2 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:scale-95 hidden items-center justify-center gap-2 text-sm">
                        <span>❌</span> <span>Cancel</span>
                    </button>
                    <button onclick="changePassword()" id="changePasswordBtn" class="px-4 py-2 bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-700 hover:to-yellow-800 text-white rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2 text-sm">
                        <span>🔒</span> <span>Change Password</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let originalProfileData = {};
let isEditing = false;
let verificationStatus = 'none';

// Toast notification function
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer');
    const toast = document.createElement('div');

    // Set toast styles based on type
    let bgColor, borderColor, icon;
    switch (type) {
        case 'success':
            bgColor = 'bg-green-900/90';
            borderColor = 'border-green-500/50';
            icon = '✅';
            break;
        case 'error':
            bgColor = 'bg-red-900/90';
            borderColor = 'border-red-500/50';
            icon = '❌';
            break;
        case 'warning':
            bgColor = 'bg-yellow-900/90';
            borderColor = 'border-yellow-500/50';
            icon = '⚠️';
            break;
        default:
            bgColor = 'bg-blue-900/90';
            borderColor = 'border-blue-500/50';
            icon = 'ℹ️';
    }

    toast.className = `${bgColor} ${borderColor} border rounded-lg p-4 shadow-lg backdrop-blur-sm transform transition-all duration-300 max-w-sm`;
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <span class="text-lg">${icon}</span>
            <div class="flex-1">
                <p class="text-white text-sm font-medium">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-white text-xl leading-none">&times;</button>
        </div>
    `;

    // Add toast to container
    toastContainer.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
        toast.style.opacity = '1';
    }, 100);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.style.transform = 'translateX(100%)';
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Helper function to format date as MM/DD/YYYY
function formatDateForDisplay(dateValue) {
    const date = new Date(dateValue);
    if (!isNaN(date.getTime())) {
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const year = date.getFullYear();
        return `${month}/${day}/${year}`;
    }
    return dateValue;
}

async function fetchUserProfile() {
    try {
        const token = sessionStorage.getItem('token');
        if (!token) {
            showToast('No authentication token found. Please log in again.', 'error');
            return;
        }

        const response = await fetch('<?php echo $API; ?>/api/v1/IAM', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'accept': '*/*'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const profileData = await response.json();
        originalProfileData = profileData;

        // Populate profile content
        populateProfileContent(profileData);

        // Fetch and update verification status
        const verStatus = await fetchVerificationStatus();
        updateVerificationStatus(verStatus);

        // Show the modal
        showProfileModal();

    } catch (error) {
        console.error('Error fetching user profile:', error);
        showToast('Error loading profile data. Please try again.', 'error');
    }
}

function populateProfileContent(data) {
    const profileContent = document.getElementById('profileContent');
    profileContent.innerHTML = '';

    // Fields to exclude or handle specially
    const excludeFields = ['id', 'password', 'token'];

    for (const [key, value] of Object.entries(data)) {
        if (excludeFields.includes(key.toLowerCase()) || value === null || value === undefined) {
            continue;
        }

        const fieldDiv = document.createElement('div');
        fieldDiv.className = 'bg-gray-800/50 p-3 rounded-lg border border-gray-700/50 hover:bg-gray-800/70 transition-all duration-300';

        const label = document.createElement('label');
        label.className = 'text-gray-300 text-xs font-medium mb-1 block uppercase tracking-wide';
        label.textContent = key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());

        const input = document.createElement('input');
        input.type = 'text';
        input.id = `profile_${key}`;
        input.value = value || '';
        input.disabled = true;
        input.className = 'text-white bg-gray-700/50 p-2 rounded-md border border-gray-600/50 text-sm block w-full hover:bg-gray-700/70 transition-colors duration-200 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 disabled:opacity-60';

        fieldDiv.appendChild(label);
        fieldDiv.appendChild(input);
        profileContent.appendChild(fieldDiv);
    }
}

function updateVerificationStatus(status) {
    verificationStatus = status;
    console.log('Verification statusxxx:', status);

    const verificationIcon = document.getElementById('verificationIcon');
    const verificationText = document.getElementById('verificationText');
    const verificationStatusDiv = document.getElementById('verificationStatus');
    const verifyBtn = document.getElementById('verifyBtn');

    switch (status) {
        case 'accepted':
            verificationIcon.textContent = '✅';
            verificationText.textContent = 'Verified';
            verificationText.className = 'text-sm font-medium text-green-400';
            verificationStatusDiv.className = 'mb-4 p-3 rounded-lg border border-green-500/50 bg-green-900/20 hover:bg-green-900/30 transition-all duration-300';
            verifyBtn.classList.add('hidden');
            break;
        case 'pending':
            verificationIcon.textContent = '⏳';
            verificationText.textContent = 'Verification Pending';
            verificationText.className = 'text-sm font-medium text-yellow-400';
            verificationStatusDiv.className = 'mb-4 p-3 rounded-lg border border-yellow-500/50 bg-yellow-900/20 hover:bg-yellow-900/30 transition-all duration-300';
            // Change button to show pending status instead of hiding
            verifyBtn.innerHTML = '<span>⏳</span> <span>Pending</span>';
            verifyBtn.className = 'px-3 py-1.5 bg-gradient-to-r from-yellow-600 to-yellow-700 text-white rounded-lg font-medium transition-all duration-300 shadow-lg flex items-center gap-2 text-sm cursor-not-allowed opacity-70';
            verifyBtn.disabled = true;
            verifyBtn.onclick = null;
            break;
        case 'rejected':
            verificationIcon.textContent = '❌';
            verificationText.textContent = 'Verification Rejected';
            verificationText.className = 'text-sm font-medium text-red-400';
            verificationStatusDiv.className = 'mb-4 p-3 rounded-lg border border-red-500/50 bg-red-900/20 hover:bg-red-900/30 transition-all duration-300';
            verifyBtn.innerHTML = '<span>🔄</span> <span>Retry Verification</span>';
            verifyBtn.className = 'px-3 py-1.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:scale-95 flex items-center gap-2 text-sm';
            verifyBtn.disabled = false;
            verifyBtn.onclick = showVerificationForm;
            verifyBtn.classList.remove('hidden');
            break;
        case 'none':
        default:
            verificationIcon.textContent = '❓';
            verificationText.textContent = 'Not yet verified';
            verificationText.className = 'text-sm font-medium text-red-400';
            verificationStatusDiv.className = 'mb-4 p-3 rounded-lg border border-red-500/50 bg-red-900/20 hover:bg-red-900/30 transition-all duration-300';
            verifyBtn.innerHTML = '<span>✅</span> <span>Verify</span>';
            verifyBtn.className = 'px-3 py-1.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:scale-95 flex items-center gap-2 text-sm';
            verifyBtn.disabled = false;
            verifyBtn.onclick = showVerificationForm;
            verifyBtn.classList.remove('hidden');
            break;
    }
}

async function fetchVerificationStatus() {
    try {
        const token = sessionStorage.getItem('token');
        if (!token) {
            return 'none';
        }

        const response = await fetch('<?php echo $API; ?>/api/v1/IAM/verified', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const status = await response.text();
        console.log('Fetched verification status:', status);
        return status;
    } catch (error) {
        console.error('Error fetching verification status:', error);
        return 'none';
    }
}

function checkProfileCompleteness(profile) {
    const requiredFields = ['email', 'phone', 'address'];
    const missingFields = [];

    for (const field of requiredFields) {
        let hasField = false;
        for (const [key, value] of Object.entries(profile)) {
            if (key.toLowerCase().includes(field) && value && value.toString().trim() !== '') {
                hasField = true;
                break;
            }
        }
        if (!hasField) {
            missingFields.push(field);
        }
    }

    return {
        isComplete: missingFields.length === 0,
        missingFields: missingFields
    };
}

function showVerificationForm() {
    // Check verification status first
    if (verificationStatus === 'pending') {
        showToast('Verification is already pending. Please wait for review.', 'warning');
        return;
    }

    if (verificationStatus === 'accepted') {
        showToast('Account is already verified!', 'success');
        return;
    }

    const profileCheck = checkProfileCompleteness(originalProfileData);
    const verificationForm = document.getElementById('verificationForm');
    const verificationError = document.getElementById('verificationError');

    if (!profileCheck.isComplete) {
        const errorMessage = `Please complete your profile first. Missing: ${profileCheck.missingFields.join(', ')}`;
        verificationError.textContent = errorMessage;
        verificationError.classList.remove('hidden');
        verificationForm.classList.remove('hidden');
        verificationForm.classList.add('flex');
        return;
    }

    verificationError.classList.add('hidden');
    verificationForm.classList.remove('hidden');
    verificationForm.classList.add('flex');
}

function hideVerificationForm() {
    const verificationForm = document.getElementById('verificationForm');
    verificationForm.classList.add('hidden');
    verificationForm.classList.remove('flex');

    // Reset form
    document.getElementById('documentType').value = '';
    document.getElementById('documentPhoto').value = '';
    document.getElementById('verificationError').classList.add('hidden');
}

async function submitVerification() {
    const documentType = document.getElementById('documentType').value;
    const documentPhoto = document.getElementById('documentPhoto').files[0];
    // remarks is always set to empty string
    const remarks = "";
    const verificationError = document.getElementById('verificationError');

    // Validate form
    if (!documentType) {
        const errorMessage = 'Please select a document type';
        verificationError.textContent = errorMessage;
        verificationError.classList.remove('hidden');
        showToast(errorMessage, 'error');
        return;
    }

    if (!documentPhoto) {
        const errorMessage = 'Please upload a document photo';
        verificationError.textContent = errorMessage;
        verificationError.classList.remove('hidden');
        showToast(errorMessage, 'error');
        return;
    }

    // Check profile completeness again
    const profileCheck = checkProfileCompleteness(originalProfileData);
    if (!profileCheck.isComplete) {
        const errorMessage = `Please complete your profile first. Missing: ${profileCheck.missingFields.join(', ')}`;
        verificationError.textContent = errorMessage;
        verificationError.classList.remove('hidden');
        showToast(errorMessage, 'warning');
        return;
    }

    try {
        const token = sessionStorage.getItem('token');
        if (!token) {
            const errorMessage = 'No authentication token found';
            verificationError.textContent = errorMessage;
            verificationError.classList.remove('hidden');
            showToast(errorMessage, 'error');
            return;
        }

        const formData = new FormData();
        formData.append('image', documentPhoto);
        // remarks is not appended to formData

        // Update button to show loading state
        const submitBtn = document.getElementById('submitVerificationBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span>⏳</span> <span>Submitting...</span>';
        submitBtn.disabled = true;

        // Make the actual API call to submit verification
        const response = await fetch(`<?php echo $API; ?>/api/v1/IAM/verify?DocuType=${encodeURIComponent(documentType)}`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'accept': '*/*'
            },
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Reset form and hide it
        hideVerificationForm();

        // Update verification status to pending
        updateVerificationStatus('pending');

        // Show success message
        const successMessage = 'Verification request submitted successfully! We will review your documents and notify you once verified.';
        showToast(successMessage,
