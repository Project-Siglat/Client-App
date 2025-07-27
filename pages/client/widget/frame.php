<style>
@keyframes buttonPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.8);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes fadeOutDown {
    from {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
    to {
        opacity: 0;
        transform: translateY(20px) scale(0.8);
    }
}

.button-animate {
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.button-animate:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 20px rgba(0,0,0,0.3) !important;
}

.button-animate:active {
    transform: scale(0.95);
    transition: all 0.1s ease;
}

.option-animate {
    animation: fadeInUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

.option-animate:nth-child(1) { animation-delay: 0.1s; }
.option-animate:nth-child(2) { animation-delay: 0.15s; }
.option-animate:nth-child(3) { animation-delay: 0.2s; }
.option-animate:nth-child(4) { animation-delay: 0.25s; }

.options-container-show {
    animation: fadeInUp 0.3s ease forwards;
}

.options-container-hide {
    animation: fadeOutDown 0.3s ease forwards;
}

/* Mobile styles */
@media (max-width: 768px) {
    #recenterButton, #chatButton, #sirenButton, #mainButton {
        width: 45px !important;
        height: 45px !important;
    }

    #recenterButton svg, #chatButton svg, #sirenButton svg, #mainButtonIcon {
        width: 18px !important;
        height: 18px !important;
    }

    #chatButton {
        right: 125px !important;
    }

    #sirenButton {
        right: 70px !important;
    }

    #optionsContainer {
        bottom: 70px !important;
        right: 20px !important;
    }

    .option-button {
        width: 35px !important;
        height: 35px !important;
    }

    .option-button svg {
        width: 16px !important;
        height: 16px !important;
    }
}
</style>

<div onclick="reCenter()" id="recenterButton" class="button-animate" style="width: 60px; height: 60px; border-radius: 50%; background: #6366F1; z-index: 2147483647; position: fixed; bottom: 20px; left: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-weight: bold; box-shadow: 0 2px 8px rgba(99,102,241,0.2); transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);" onclick="handleRecenter()">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="transition: transform 0.2s ease;">
        <path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0 0 13 3.06V1h-2v2.06A8.994 8.994 0 0 0 3.06 11H1v2h2.06A8.994 8.994 0 0 0 11 20.94V23h2v-2.06A8.994 8.994 0 0 0 20.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
    </svg>
</div>

<div id="chatButton" class="button-animate" style="width: 60px; height: 60px; border-radius: 50%; background: #10B981; z-index: 2147483647; position: fixed; bottom: 20px; right: 155px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-weight: bold; box-shadow: 0 2px 8px rgba(16,185,129,0.2); transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);" onclick="handleChat()">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="transition: transform 0.2s ease;">
        <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
    </svg>
</div>

<div id="sirenButton" class="button-animate" style="width: 60px; height: 60px; border-radius: 50%; background: #EF4444; z-index: 2147483647; position: fixed; bottom: 20px; right: 85px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-weight: bold; box-shadow: 0 2px 8px rgba(239,68,68,0.2); transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);" onclick="handleSiren()">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="transition: transform 0.2s ease;">
        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
    </svg>
</div>

<div id="mainButton" class="button-animate" style="width: 60px; height: 60px; border-radius: 50%; background: #1F2937; z-index: 2147483647; position: fixed; bottom: 20px; right: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-weight: bold; box-shadow: 0 2px 8px rgba(31,41,55,0.2); transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);" onclick="toggleOptions()">
    <svg id="mainButtonIcon" width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="transition: transform 0.3s ease;">
        <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
    </svg>
</div>

<!-- here -->
<?php include "./pages/client/widget/weather.html"; ?>
<?php include "./pages/client/widget/user.html"; ?>
<?php include "./pages/client/widget/contactlist.html"; ?>

<div id="optionsContainer" style="position: fixed; bottom: 90px; right: 25px; z-index: 2147483640; opacity: 0; transform: translateY(20px) scale(0.8); pointer-events: none; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
    <?php include "./pages/client/widget/account.html"; ?>
    <?php include "./pages/client/widget/contact.html"; ?>

    <!-- <div class="option-button" style="width: 45px; height: 45px; border-radius: 50%; background: #F59E0B; margin-bottom: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #FFFFFF; box-shadow: 0 2px 6px rgba(245,158,11,0.2); transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); opacity: 0; transform: translateY(10px);" onclick="handleOption('settings')" onmouseenter="this.style.transform='scale(1.1) translateY(0)'; this.style.boxShadow='0 4px 15px rgba(245,158,11,0.4)'" onmouseleave="this.style.transform='scale(1) translateY(0)'; this.style.boxShadow='0 2px 6px rgba(245,158,11,0.2)'" onmousedown="this.style.transform='scale(0.95) translateY(0)'" onmouseup="this.style.transform='scale(1.1) translateY(0)'">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.82,11.69,4.82,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/>
        </svg>
    </div> -->

    <?php include "./pages/client/widget/logout.html"; ?>
</div>





<script>
let optionsVisible = false;

function toggleOptions() {
    const optionsContainer = document.getElementById('optionsContainer');
    const mainButtonIcon = document.getElementById('mainButtonIcon');
    const optionButtons = optionsContainer.querySelectorAll('.option-button');

    if (!optionsVisible) {
        // Show options
        optionsContainer.style.opacity = '1';
        optionsContainer.style.transform = 'translateY(0) scale(1)';
        optionsContainer.style.pointerEvents = 'auto';

        // Rotate main button icon
        mainButtonIcon.style.transform = 'rotate(90deg)';

        // Animate option buttons with stagger
        optionButtons.forEach((button, index) => {
            setTimeout(() => {
                button.style.opacity = '1';
                button.style.transform = 'translateY(0)';
            }, index * 80);
        });

        optionsVisible = true;
    } else {
        // Hide options
        mainButtonIcon.style.transform = 'rotate(0deg)';

        // Animate option buttons out with reverse stagger
        optionButtons.forEach((button, index) => {
            setTimeout(() => {
                button.style.opacity = '0';
                button.style.transform = 'translateY(10px)';
            }, index * 50);
        });

        // Hide container after all buttons are animated out
        setTimeout(() => {
            optionsContainer.style.opacity = '0';
            optionsContainer.style.transform = 'translateY(20px) scale(0.8)';
            optionsContainer.style.pointerEvents = 'none';
        }, optionButtons.length * 50 + 100);

        optionsVisible = false;
    }
}

function handleOption(option) {
    alert(option + ' clicked');
    // Add your specific logic for each option here
    toggleOptions(); // Hide options after selection
}

function handleChat() {
    // Add click animation
    const button = document.getElementById('chatButton');
    button.style.animation = 'buttonPulse 0.3s ease';
    setTimeout(() => button.style.animation = '', 300);

    alert('chat button clicked');
    // Add your chat logic here
}

function handleSiren() {
    // Add click animation
    const button = document.getElementById('sirenButton');
    button.style.animation = 'buttonPulse 0.3s ease';
    setTimeout(() => button.style.animation = '', 300);

    alert('siren button clicked');
    // Add your siren logic here
}

function handleRecenter() {
    // Add click animation
    const button = document.getElementById('recenterButton');
    button.style.animation = 'buttonPulse 0.3s ease';
    setTimeout(() => button.style.animation = '', 300);

    alert('recenter button clicked');
    // Add your recenter logic here
}
</script>
