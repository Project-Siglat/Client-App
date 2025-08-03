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
                Pending
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
                onclick="markAsDone()"
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
let currentStatus = 'Pending';

function openStatusModal() {
    document.getElementById('statusModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeStatusModal(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('statusModal').style.display = 'none';
    document.body.style.overflow = '';
}

function markAsDone() {
    if (confirm('Are you sure you want to mark this ambulance status as done? This action cannot be undone.')) {
        currentStatus = 'Done';
        const statusElement = document.getElementById('currentStatus');
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

        // Update the main button color to green
        document.getElementById('deliverystatus').style.background = '#10b981';
        document.getElementById('deliverystatus').style.boxShadow = '0 2px 8px rgba(16, 185, 129, 0.2)';

        // Hide the done button
        document.getElementById('doneButton').style.display = 'none';

        closeStatusModal();
    }
}

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeStatusModal();
    }
});
</script>
