<div
    id="deliverystatus"
    style="
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #ef4444;
        z-index: 2147483647;
        position: fixed;
        bottom: 20px;
        right: 85px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-weight: bold;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    "
    onclick="openStatusModal()"
>
    <svg
        width="22"
        height="22"
        viewBox="0 0 24 24"
        fill="currentColor"
        style="transition: transform 0.2s ease"
    >
        <path d="M3 13h8V3H9v6H3v4zm6 8h2v-8h-2v8zM13 21h8v-4h-6V3h-2v18z" />
    </svg>
</div>

<!-- Status Modal -->
<div
    id="statusModal"
    style="
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 2147483648;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(2px);
        animation: fadeIn 0.2s ease-out;
    "
    onclick="closeStatusModal(event)"
>
    <div
        style="
            background: white;
            border-radius: 16px;
            padding: 32px;
            min-width: 400px;
            max-width: 90vw;
            max-height: 90vh;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.08);
            position: relative;
            transform: scale(1);
            animation: modalSlideIn 0.3s ease-out;
        "
        onclick="event.stopPropagation()"
    >
        <button
            onclick="closeStatusModal()"
            style="
                position: absolute;
                top: 16px;
                right: 16px;
                background: none;
                border: none;
                font-size: 24px;
                color: #9ca3af;
                cursor: pointer;
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: all 0.2s ease;
            "
            onmouseover="this.style.background='#f3f4f6'; this.style.color='#374151'"
            onmouseout="this.style.background='none'; this.style.color='#9ca3af'"
        >×</button>

        <div style="display: flex; align-items: center; margin-bottom: 24px;">
            <div style="
                width: 48px;
                height: 48px;
                background: linear-gradient(135deg, #ef4444, #dc2626);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 16px;
            ">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                    <path d="M19.5 3.5L18 2l-6.5 6.5-6.5-6.5L3.5 3.5 10 10l-6.5 6.5L5 18l6.5-6.5L18 18l1.5-1.5L13 10l6.5-6.5z"/>
                </svg>
            </div>
            <div>
                <h3 style="margin: 0; color: #1f2937; font-size: 22px; font-weight: 700;">Ambulance Status</h3>
                <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 14px;">Track and update ambulance delivery</p>
            </div>
        </div>

        <div style="margin-bottom: 32px; text-align: center;">
            <label style="display: block; margin-bottom: 8px; color: #374151; font-weight: 600; font-size: 14px;">Current Status</label>
            <span id="currentStatus" style="
                display: inline-flex;
                align-items: center;
                padding: 12px 20px;
                background: #fef3c7;
                color: #d97706;
                border-radius: 12px;
                font-weight: 600;
                font-size: 16px;
                border: 2px solid #fde68a;
            ">
                <span style="
                    width: 8px;
                    height: 8px;
                    background: currentColor;
                    border-radius: 50%;
                    margin-right: 8px;
                    animation: pulse 2s infinite;
                "></span>
                Loading...
            </span>
        </div>

        <div style="display: flex; gap: 12px; justify-content: center; align-items: center;">
            <button
                onclick="closeStatusModal()"
                style="
                    padding: 12px 24px;
                    border: 2px solid #e5e7eb;
                    background: white;
                    color: #6b7280;
                    border-radius: 10px;
                    cursor: pointer;
                    font-weight: 600;
                    font-size: 14px;
                    transition: all 0.2s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                "
                onmouseover="this.style.borderColor='#d1d5db'; this.style.color='#374151'"
                onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='#6b7280'"
            >Cancel</button>
            <button
                id="doneButton"
                onclick="openConfirmModal()"
                style="
                    padding: 12px 24px;
                    border: none;
                    background: linear-gradient(135deg, #10b981, #059669);
                    color: white;
                    border-radius: 10px;
                    cursor: pointer;
                    font-weight: 600;
                    font-size: 14px;
                    transition: all 0.2s ease;
                    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                "
                onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(16, 185, 129, 0.4)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'"
            >Mark as Done</button>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div
    id="confirmModal"
    style="
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 2147483649;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(2px);
        animation: fadeIn 0.2s ease-out;
    "
    onclick="closeConfirmModal(event)"
>
    <div
        style="
            background: white;
            border-radius: 16px;
            padding: 32px;
            min-width: 450px;
            max-width: 90vw;
            max-height: 90vh;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.08);
            position: relative;
            transform: scale(1);
            animation: modalSlideIn 0.3s ease-out;
        "
        onclick="event.stopPropagation()"
    >
        <button
            onclick="closeConfirmModal()"
            style="
                position: absolute;
                top: 16px;
                right: 16px;
                background: none;
                border: none;
                font-size: 24px;
                color: #9ca3af;
                cursor: pointer;
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: all 0.2s ease;
            "
            onmouseover="this.style.background='#f3f4f6'; this.style.color='#374151'"
            onmouseout="this.style.background='none'; this.style.color='#9ca3af'"
        >×</button>

        <div style="display: flex; align-items: center; margin-bottom: 24px;">
            <div style="
                width: 48px;
                height: 48px;
                background: linear-gradient(135deg, #f59e0b, #d97706);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 16px;
            ">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 style="margin: 0; color: #1f2937; font-size: 22px; font-weight: 700;">Confirm Action</h3>
                <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 14px;">Please confirm your decision</p>
            </div>
        </div>

        <div style="margin-bottom: 32px; text-align: center;">
            <p style="
                color: #374151;
                font-size: 16px;
                line-height: 1.5;
                margin: 0;
            ">Are you sure you want to mark this ambulance status as done?</p>
            <p style="
                color: #6b7280;
                font-size: 14px;
                margin: 8px 0 0 0;
            ">This action cannot be undone.</p>
        </div>

        <div style="display: flex; gap: 12px; justify-content: center; align-items: center;">
            <button
                onclick="closeConfirmModal()"
                style="
                    padding: 12px 24px;
                    border: 2px solid #e5e7eb;
                    background: white;
                    color: #6b7280;
                    border-radius: 10px;
                    cursor: pointer;
                    font-weight: 600;
                    font-size: 14px;
                    transition: all 0.2s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                "
                onmouseover="this.style.borderColor='#d1d5db'; this.style.color='#374151'"
                onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='#6b7280'"
            >Cancel</button>
            <button
                onclick="confirmMarkAsDone()"
                style="
                    padding: 12px 24px;
                    border: none;
                    background: linear-gradient(135deg, #10b981, #059669);
                    color: white;
                    border-radius: 10px;
                    cursor: pointer;
                    font-weight: 600;
                    font-size: 14px;
                    transition: all 0.2s ease;
                    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                "
                onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(16, 185, 129, 0.4)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'"
            >Yes, Mark as Done</button>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>

<script>
let currentAlertData = null;
let sirenAudio = null;
let lastStatus = null;
let hasPendingPlayedOnce = false;

// Initialize audio element
function initializeSiren() {
    if (!sirenAudio) {
        sirenAudio = new Audio('./assets/sounds/siren.mp3');
    }
}

// Play siren sound once for pending status
function playSirenForPending() {
    initializeSiren();
    sirenAudio.currentTime = 0;
    sirenAudio.play().catch(e => console.log('Error playing siren:', e));
}

// Fetch current alert status from API
async function fetchAlertStatus() {
    try {
      const response = await fetch(API() + '/api/v1/Ambulance/alert/current/amb', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJhbWJ1bGFuY2VAY29tLmNvbSIsImp0aSI6IjU3YjMwZTVlLWY1NWEtNGFlNS1hM2QxLTM4MmIxZmUyYTE1MiIsInJvbGUiOiJBbWJ1bGFuY2UiLCJuYmYiOjE3NTQyNDA0ODIsImV4cCI6MTc1NjkxODg4MiwiaWF0IjoxNzU0MjQwNDgyLCJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjUwNjkiLCJhdWQiOiJodHRwOi8vbG9jYWxob3N0OjUwNTAifQ.VOWPLL3OSv4EWvp0_fJTBSWecZ2qI6h8BO_xXW5ZGSQ',
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            currentAlertData = data;
            updateStatusDisplay(data.status);
        } else {
            console.error('Failed to fetch alert status:', response.status);
            updateStatusDisplay('error');
        }
    } catch (error) {
        console.error('Error fetching alert status:', error);
        updateStatusDisplay('error');
    }
}

// Update the status to done via API
async function updateStatusToDone() {
    try {
        const response = await fetch(`http://localhost:5069/api/v1/Ambulance/alert/current/amb/57b30e5e-f55a-4ae5-a3d1-382b1fe2a152`, {
            method: 'GET',
            headers: {
                'accept': '*/*'
            }
        });

        if (response.ok) {
            const data = await response.json();
            console.log('Status updated successfully:', data);
            // Refresh the status display
            fetchAlertStatus();
        } else {
            console.error('Failed to update status:', response.status);
            showErrorModal('Failed to update status. Please try again.');
        }
    } catch (error) {
        console.error('Error updating status:', error);
        showErrorModal('Error updating status. Please try again.');
    }
}

// Show error modal instead of alert
function showErrorModal(message) {
    // Create error modal if it doesn't exist
    let errorModal = document.getElementById('errorModal');
    if (!errorModal) {
        errorModal = document.createElement('div');
        errorModal.id = 'errorModal';
        errorModal.innerHTML = `
            <div style="
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                z-index: 2147483650;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(2px);
                animation: fadeIn 0.2s ease-out;
            " onclick="closeErrorModal(event)">
                <div style="
                    background: white;
                    border-radius: 16px;
                    padding: 32px;
                    min-width: 400px;
                    max-width: 90vw;
                    max-height: 90vh;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
                    border: 1px solid rgba(0, 0, 0, 0.08);
                    position: relative;
                    transform: scale(1);
                    animation: modalSlideIn 0.3s ease-out;
                " onclick="event.stopPropagation()">
                    <div style="display: flex; align-items: center; margin-bottom: 24px;">
                        <div style="
                            width: 48px;
                            height: 48px;
                            background: linear-gradient(135deg, #ef4444, #dc2626);
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-right: 16px;
                        ">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 style="margin: 0; color: #1f2937; font-size: 22px; font-weight: 700;">Error</h3>
                            <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 14px;">Something went wrong</p>
                        </div>
                    </div>
                    <div style="margin-bottom: 32px; text-align: center;">
                        <p id="errorMessage" style="
                            color: #374151;
                            font-size: 16px;
                            line-height: 1.5;
                            margin: 0;
                        "></p>
                    </div>
                    <div style="display: flex; justify-content: center;">
                        <button onclick="closeErrorModal()" style="
                            padding: 12px 24px;
                            border: none;
                            background: linear-gradient(135deg, #ef4444, #dc2626);
                            color: white;
                            border-radius: 10px;
                            cursor: pointer;
                            font-weight: 600;
                            font-size: 14px;
                            transition: all 0.2s ease;
                            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
                        ">OK</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(errorModal);
    }

    document.getElementById('errorMessage').textContent = message;
    errorModal.firstElementChild.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeErrorModal(event) {
    if (event && event.target !== event.currentTarget) return;
    const errorModal = document.getElementById('errorModal');
    if (errorModal) {
        errorModal.firstElementChild.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Update the status display based on API response
function updateStatusDisplay(status) {
    const statusElement = document.getElementById('currentStatus');
    const deliveryButton = document.getElementById('deliverystatus');
    const doneButton = document.getElementById('doneButton');

    switch (status.toLowerCase()) {
        case 'pending':
            statusElement.innerHTML = `
                <span style="
                    width: 8px;
                    height: 8px;
                    background: currentColor;
                    border-radius: 50%;
                    margin-right: 8px;
                    animation: pulse 2s infinite;
                "></span>
                Pending
            `;
            statusElement.style.background = '#fef3c7';
            statusElement.style.color = '#d97706';
            statusElement.style.borderColor = '#fde68a';
            deliveryButton.style.background = '#ef4444';
            deliveryButton.style.boxShadow = '0 2px 8px rgba(239, 68, 68, 0.2)';
            deliveryButton.style.cursor = 'pointer';
            deliveryButton.style.pointerEvents = 'auto';
            deliveryButton.style.opacity = '1';
            doneButton.style.display = 'flex';

            // Play siren sound only once when status first becomes pending
            if (lastStatus !== 'pending' && !hasPendingPlayedOnce) {
                playSirenForPending();
                hasPendingPlayedOnce = true;
            }
            break;

        case 'done':
        case 'completed':
            statusElement.innerHTML = `
                <span style="
                    width: 8px;
                    height: 8px;
                    background: currentColor;
                    border-radius: 50%;
                    margin-right: 8px;
                "></span>
                Done
            `;
            statusElement.style.background = '#dcfce7';
            statusElement.style.color = '#16a34a';
            statusElement.style.borderColor = '#bbf7d0';
            deliveryButton.style.background = '#10b981';
            deliveryButton.style.boxShadow = '0 2px 8px rgba(16, 185, 129, 0.2)';
            deliveryButton.style.cursor = 'pointer';
            deliveryButton.style.pointerEvents = 'auto';
            deliveryButton.style.opacity = '1';
            doneButton.style.display = 'none';
            break;

        case 'error':
            statusElement.innerHTML = `
                <span style="
                    width: 8px;
                    height: 8px;
                    background: currentColor;
                    border-radius: 50%;
                    margin-right: 8px;
                "></span>
                Error
            `;
            statusElement.style.background = '#fee2e2';
            statusElement.style.color = '#dc2626';
            statusElement.style.borderColor = '#fecaca';
            deliveryButton.style.background = '#9ca3af';
            deliveryButton.style.boxShadow = '0 2px 8px rgba(156, 163, 175, 0.2)';
            deliveryButton.style.cursor = 'not-allowed';
            deliveryButton.style.pointerEvents = 'none';
            deliveryButton.style.opacity = '0.6';
            doneButton.style.display = 'flex';
            break;

        default:
            statusElement.innerHTML = `
                <span style="
                    width: 8px;
                    height: 8px;
                    background: currentColor;
                    border-radius: 50%;
                    margin-right: 8px;
                    animation: pulse 2s infinite;
                "></span>
                ${status.charAt(0).toUpperCase() + status.slice(1)}
            `;
            statusElement.style.background = '#fef3c7';
            statusElement.style.color = '#d97706';
            statusElement.style.borderColor = '#fde68a';
            deliveryButton.style.background = '#ef4444';
            deliveryButton.style.boxShadow = '0 2px 8px rgba(239, 68, 68, 0.2)';
            deliveryButton.style.cursor = 'pointer';
            deliveryButton.style.pointerEvents = 'auto';
            deliveryButton.style.opacity = '1';
            doneButton.style.display = 'flex';
    }

    // Update last status
    lastStatus = status.toLowerCase();
}

function openStatusModal() {
    const deliveryButton = document.getElementById('deliverystatus');
    if (deliveryButton.style.pointerEvents === 'none') {
        return;
    }
    document.getElementById('statusModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    // Refresh status when modal opens
    fetchAlertStatus();
}

function closeStatusModal(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('statusModal').style.display = 'none';
    document.body.style.overflow = '';
}

function openConfirmModal() {
    document.getElementById('confirmModal').style.display = 'flex';
}

function closeConfirmModal(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('confirmModal').style.display = 'none';
}

async function confirmMarkAsDone() {
    await updateStatusToDone();
    closeConfirmModal();
    closeStatusModal();
}

// Initialize status on page load
document.addEventListener('DOMContentLoaded', function() {
    fetchAlertStatus();
});

// Refresh status every 30 seconds
setInterval(fetchAlertStatus, 30000);

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const confirmModal = document.getElementById('confirmModal');
        const statusModal = document.getElementById('statusModal');
        const errorModal = document.getElementById('errorModal');

        if (confirmModal && confirmModal.style.display === 'flex') {
            closeConfirmModal();
        } else if (errorModal && errorModal.firstElementChild && errorModal.firstElementChild.style.display === 'flex') {
            closeErrorModal();
        } else if (statusModal && statusModal.style.display === 'flex') {
            closeStatusModal();
        }
    }
});
</script>
