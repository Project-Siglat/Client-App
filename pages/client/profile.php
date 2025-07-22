<?php
include "./config/env.php";
$API = $_ENV["API"];
?>

<button class="w-full text-left py-3 px-4 rounded-lg hover:bg-gray-800 text-yellow-400 text-base flex items-center gap-3 transition-all duration-300 hover:shadow-lg hover:scale-105 active:scale-95" onclick="fetchUserProfile()">
    <span class="text-xl">👤</span>
    <span class="font-medium">View Profile</span>
</button>

<!-- Toast Notification Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-[60] space-y-2"></div>

<!-- Enhanced Floating Profile Modal -->
<div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 backdrop-blur-md">
    <div class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-2xl shadow-2xl w-[95%] sm:w-[90%] md:w-[85%] lg:w-[80%] xl:w-[75%] 2xl:w-[70%] mx-4 border border-gray-600/50 max-h-[95vh] overflow-hidden backdrop-blur-sm flex flex-col">
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

                <!-- Verification Form -->
                <div id="verificationForm" class="mb-4 p-4 rounded-lg border border-purple-500/50 bg-purple-900/20 hidden">
                    <h3 class="text-lg font-bold text-purple-400 mb-4">Account Verification</h3>
                    <div id="verificationError" class="mb-4 p-3 rounded-lg border border-red-500/50 bg-red-900/20 text-red-400 text-sm hidden"></div>

                    <!-- Document Type Selection -->
                    <div class="mb-4">
                        <label class="text-gray-300 text-xs font-medium mb-2 block uppercase tracking-wide">Document Type</label>
                        <select id="documentType" class="text-white bg-gray-700/50 p-2 rounded-md border border-gray-600/50 text-sm block w-full hover:bg-gray-700/70 transition-colors duration-200 focus:border-purple-400 focus:ring-2 focus:ring-purple-400/20">
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
                        <label class="text-gray-300 text-xs font-medium mb-2 block uppercase tracking-wide">Upload Document Photo</label>
                        <input type="file" id="documentPhoto" accept="image/*" class="text-white bg-gray-700/50 p-2 rounded-md border border-gray-600/50 text-sm block w-full hover:bg-gray-700/70 transition-colors duration-200 focus:border-purple-400 focus:ring-2 focus:ring-purple-400/20 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
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
        showToast(errorMessage, 'warning');
        return;
    }

    verificationError.classList.add('hidden');
    verificationForm.classList.remove('hidden');
}

function hideVerificationForm() {
    const verificationForm = document.getElementById('verificationForm');
    verificationForm.classList.add('hidden');

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
        showToast(successMessage, 'success');

        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

    } catch (error) {
        console.error('Error submitting verification:', error);
        const errorMessage = 'Error submitting verification. Please try again.';
        verificationError.textContent = errorMessage;
        verificationError.classList.remove('hidden');
        showToast(errorMessage, 'error');

        // Reset button
        const submitBtn = document.getElementById('submitVerificationBtn');
        submitBtn.innerHTML = '<span>📤</span> <span>Submit Verification</span>';
        submitBtn.disabled = false;
    }
}

async function fetchUserProfile() {
    try {
        const token = sessionStorage.getItem('token');
        if (!token) {
            console.error('No token found in session storage');
            showToast('No authentication token found', 'error');
            return;
        }

        // Show loading state
        showProfileModal();
        const profileContent = document.getElementById('profileContent');
        profileContent.innerHTML = '<div class="col-span-full flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-400"></div></div>';

        // Fetch verification status
        const status = await fetchVerificationStatus();
        updateVerificationStatus(status);

        // Make actual API call to fetch profile data
        const response = await fetch('<?php echo $API; ?>/api/v1/IAM', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const profile = await response.json();
        originalProfileData = profile;

        displayProfile(profile);
    } catch (error) {
        console.error('Error fetching profile:', error);
        const profileContent = document.getElementById('profileContent');
        profileContent.innerHTML = '<div class="col-span-full text-center text-red-400 py-8">Error loading profile data</div>';
        showToast('Failed to load profile data', 'error');
    }
}

function displayProfile(profile) {
    const profileContent = document.getElementById('profileContent');
    profileContent.innerHTML = '';

    let addressKey = null;
    let emailKey = null;
    let phoneKey = null;
    let birthDateKey = null;
    let middleNameKey = null;
    let genderKey = null;
    let createdAtKey = null;

    // Find special fields first
    for (const [key, value] of Object.entries(profile)) {
        if (key.toLowerCase().includes('address')) {
            addressKey = key;
        }
        if (key.toLowerCase().includes('email')) {
            emailKey = key;
        }
        if (key.toLowerCase().includes('phone')) {
            phoneKey = key;
        }
        if (key.toLowerCase().includes('birth') || key.toLowerCase().includes('date')) {
            birthDateKey = key;
        }
        if (key.toLowerCase().includes('middle')) {
            middleNameKey = key;
        }
        if (key.toLowerCase().includes('gender')) {
            genderKey = key;
        }
        if (key.toLowerCase().includes('createdat') || key.toLowerCase().includes('created_at')) {
            createdAtKey = key;
        }
    }

    // Display address first if found with half width
    if (addressKey) {
        const div = document.createElement('div');
        div.className = 'bg-gray-800/50 p-3 rounded-lg border border-gray-700/50 hover:bg-gray-800/70 transition-all duration-300';

        let displayValue = profile[addressKey];

        div.innerHTML = `
            <label class="text-gray-300 text-xs font-medium mb-1 block uppercase tracking-wide">${addressKey.charAt(0).toUpperCase() + addressKey.slice(1)}</label>
            <span class="profile-value text-white bg-gray-700/50 p-2 rounded-md border border-gray-600/50 text-sm block w-full hover:bg-gray-700/70 transition-colors duration-200" data-key="${addressKey}">${displayValue}</span>
        `;
        profileContent.appendChild(div);
    }

    // Display other fields (except special fields and excluded ones)
    for (const [key, value] of Object.entries(profile)) {
        // Skip fields we don't want to display
        if (key.toLowerCase().includes('address') ||
            key.toLowerCase().includes('email') ||
            key.toLowerCase().includes('phone') ||
            key.toLowerCase().includes('hashpass') ||
            key.toLowerCase().includes('updatedat') ||
            key.toLowerCase().includes('createdat') ||
            key.toLowerCase().includes('updated_at') ||
            key.toLowerCase().includes('created_at') ||
            key.toLowerCase().includes('role') ||
            key.toLowerCase().includes('id')) {
            continue;
        }

        const div = document.createElement('div');
        div.className = 'bg-gray-800/50 p-3 rounded-lg border border-gray-700/50 hover:bg-gray-800/70 transition-all duration-300';

        // Format the display value for birthdate fields
        let displayValue = value;
        if (key.toLowerCase().includes('birth') || key.toLowerCase().includes('date')) {
            displayValue = formatDateForDisplay(value);
        }

        // Special handling for gender field
        if (key.toLowerCase().includes('gender')) {
            div.innerHTML = `
                <label class="text-gray-300 text-xs font-medium mb-1 block uppercase tracking-wide">${key.charAt(0).toUpperCase() + key.slice(1)}</label>
                <select class="profile-value text-white bg-gray-700/50 p-2 rounded-md border border-gray-600/50 text-sm block w-full hover:bg-gray-700/70 transition-colors duration-200" data-key="${key}" disabled>
                    <option value="Male" ${value === 'Male' ? 'selected' : ''}>Male</option>
                    <option value="Female" ${value === 'Female' ? 'selected' : ''}>Female</option>
                </select>
            `;
        } else {
            div.innerHTML = `
                <label class="text-gray-300 text-xs font-medium mb-1 block uppercase tracking-wide">${key.charAt(0).toUpperCase() + key.slice(1)}</label>
                <span class="profile-value text-white bg-gray-700/50 p-2 rounded-md border border-gray-600/50 text-sm block w-full hover:bg-gray-700/70 transition-colors duration-200" data-key="${key}">${displayValue}</span>
            `;
        }

        profileContent.appendChild(div);
    }

    // Display CreatedAt after birth date if found
    if (createdAtKey) {
        const div = document.createElement('div');
        div.className = 'bg-gray-800/50 p-3 rounded-lg border border-gray-700/50 hover:bg-gray-800/70 transition-all duration-300';

        let displayValue = formatDateForDisplay(profile[createdAtKey]);

        div.innerHTML = `
            <label class="text-gray-300 text-xs font-medium mb-1 block uppercase tracking-wide">Created At</label>
            <span class="profile-value text-white bg-gray-700/50 p-2 rounded-md border border-gray-600/50 text-sm block w-full hover:bg-gray-700/70 transition-colors duration-200 non-editable" data-key="${createdAtKey}">${displayValue}</span>
        `;
        profileContent.appendChild(div);
    }

    // Display phone number after birth date with full width
    if (phoneKey) {
        const div = document.createElement('div');
        div.className = 'bg-gray-800/50 p-3 rounded-lg border border-gray-700/50 hover:bg-gray-800/70 transition-all duration-300 col-span-full w-full';

        let displayValue = profile[phoneKey];

        div.innerHTML = `
            <label class="text-gray-300 text-xs font-medium mb-1 block uppercase tracking-wide">${phoneKey.charAt(0).toUpperCase() + phoneKey.slice(1)}</label>
            <span class="profile-value text-white bg-gray-700/50 p-2 rounded-md border border-gray-600/50 text-sm block w-full hover:bg-gray-700/70 transition-colors duration-200" data-key="${phoneKey}">${displayValue}</span>
        `;
        profileContent.appendChild(div);
    }

    // Display email last if found with full width
    if (emailKey) {
        const div = document.createElement('div');
        div.className = 'bg-gray-800/50 p-3 rounded-lg border border-gray-700/50 hover:bg-gray-800/70 transition-all duration-300 col-span-full w-full';

        let displayValue = profile[emailKey];

        div.innerHTML = `
            <label class="text-gray-300 text-xs font-medium mb-1 block uppercase tracking-wide">${emailKey.charAt(0).toUpperCase() + emailKey.slice(1)}</label>
            <span class="profile-value text-white bg-gray-700/50 p-2 rounded-md border border-gray-600/50 text-sm block w-full hover:bg-gray-700/70 transition-colors duration-200" data-key="${emailKey}">${displayValue}</span>
        `;
        profileContent.appendChild(div);
    }
}

function showProfileModal() {
    const modal = document.getElementById('profileModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeProfileModal() {
    const modal = document.getElementById('profileModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    if (isEditing) {
        cancelEdit();
    }
    // Hide verification form if open
    hideVerificationForm();
}

function enableEdit() {
    isEditing = true;
    const values = document.querySelectorAll('.profile-value');
    values.forEach(element => {
        const key = element.dataset.key.toLowerCase();

        // Skip non-editable fields
        if (element.classList.contains('non-editable')) {
            return;
        }

        // Skip gender field as it's already a select
        if (key.includes('gender')) {
            element.disabled = false;
            return;
        }

        const input = document.createElement('input');

        // Check if the field is a date field
        if (key.includes('date') || key.includes('birth')) {
            input.type = 'date';
            // Convert the value to date format if needed
            const originalValue = originalProfileData[element.dataset.key];
            const dateValue = new Date(originalValue);
            if (!isNaN(dateValue.getTime())) {
                input.value = dateValue.toISOString().split('T')[0];
            } else {
                input.value = originalValue;
            }
        } else {
            input.type = 'text';
            input.value = originalProfileData[element.dataset.key];
        }

        input.className = 'text-white bg-gray-600/50 p-2 rounded-md border border-gray-500/50 w-full text-sm focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20 transition-all duration-200';
        input.dataset.key = element.dataset.key;
        element.parentNode.replaceChild(input, element);
    });

    document.getElementById('editBtn').classList.add('hidden');
    document.getElementById('saveBtn').classList.remove('hidden');
    document.getElementById('saveBtn').classList.add('flex');
    document.getElementById('cancelBtn').classList.remove('hidden');
    document.getElementById('cancelBtn').classList.add('flex');

    showToast('Edit mode enabled', 'info');
}

function cancelEdit() {
    isEditing = false;
    displayProfile(originalProfileData);
    document.getElementById('editBtn').classList.remove('hidden');
    document.getElementById('saveBtn').classList.add('hidden');
    document.getElementById('saveBtn').classList.remove('flex');
    document.getElementById('cancelBtn').classList.add('hidden');
    document.getElementById('cancelBtn').classList.remove('flex');

    showToast('Changes cancelled', 'info');
}

async function saveProfile() {
    try {
        const inputs = document.querySelectorAll('#profileContent input, #profileContent select');
        const updatedProfile = {...originalProfileData};

        inputs.forEach(input => {
            updatedProfile[input.dataset.key] = input.value;
        });

        // Ensure required fields are included
        if (!updatedProfile.Role) {
            updatedProfile.Role = originalProfileData.Role || 'User';
        }
        if (!updatedProfile.HashPass) {
            updatedProfile.HashPass = originalProfileData.HashPass || '';
        }
        if (!updatedProfile.MiddleName) {
            updatedProfile.MiddleName = originalProfileData.MiddleName || '';
        }

        const token = sessionStorage.getItem('token');
        const response = await fetch('<?php echo $API; ?>/api/v1/IAM', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updatedProfile)
        });

        if (!response.ok) {
            const errorData = await response.json();
            console.error('Error response:', errorData);
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        originalProfileData = updatedProfile;
        isEditing = false;
        displayProfile(updatedProfile);

        document.getElementById('editBtn').classList.remove('hidden');
        document.getElementById('saveBtn').classList.add('hidden');
        document.getElementById('saveBtn').classList.remove('flex');
        document.getElementById('cancelBtn').classList.add('hidden');
        document.getElementById('cancelBtn').classList.remove('flex');

        console.log('Profile updated successfully');
        showToast('Profile updated successfully', 'success');
    } catch (error) {
        console.error('Error saving profile:', error);
        showToast('Error saving profile. Please try again.', 'error');
    }
}

async function changePassword() {
    try {
        const newPassword = document.getElementById('newPasswordInput').value;

        if (!newPassword) {
            showToast('Please enter a new password', 'warning');
            return;
        }

        const token = sessionStorage.getItem('token');
        if (!token) {
            showToast('No authentication token found', 'error');
            return;
        }

        const response = await fetch(`<?php echo $API; ?>/api/v1/IAM/change-pass?pass=${encodeURIComponent(newPassword)}`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'accept': '*/*'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Clear the password input
        document.getElementById('newPasswordInput').value = '';
        showToast('Password changed successfully', 'success');
    } catch (error) {
        console.error('Error changing password:', error);
        showToast('Error changing password. Please try again.', 'error');
    }
}

async function verifyAccount() {
    try {
        // Redirect to /verify when verify is clicked
        window.location.href = '/verify';
    } catch (error) {
        console.error('Error verifying account:', error);
        showToast('Error verifying account. Please try again.', 'error');
    }
}
</script>
