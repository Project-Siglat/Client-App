<?php
include "./config/env.php";
$API = $_ENV["API"];
?>

<!-- Sidebar Component -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="Sidebar.toggle()"></div>
<aside class="sidebar" id="sidebar">
    <div class="p-4 border-b border-red-600">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-yellow-400">Profile</h2>
            <button class="text-gray-400 hover:text-yellow-400" onclick="Sidebar.close()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center">
                <span id="sidebarUserInitials" class="text-lg font-bold text-yellow-400">--</span>
            </div>
            <div>
                <div id="sidebarUserName" class="text-sm font-semibold text-yellow-400">Loading...</div>
                <div id="sidebarUserEmail" class="text-xs text-gray-400">Loading...</div>
                <div class="text-xs text-yellow-400">●  <span id="sidebarStatus">Online</span></div>
                <div class="flex items-center gap-1 mt-1">
                    <span id="accountVerificationBadge" class="text-xs">⏳</span>
                    <span id="accountValidationText" class="text-xs text-gray-400">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div class="p-4 border-b border-red-600">
        <div class="space-y-2">
            <?php include "./pages/client/profile.php"; ?>
            <!-- <button class="w-full text-left py-2 px-3 rounded-lg hover:bg-gray-800 text-yellow-400 text-sm flex items-center gap-2" onclick="Sidebar.showSettings()">
                ⚙️ Settings
            </button>
            <button class="w-full text-left py-2 px-3 rounded-lg hover:bg-gray-800 text-yellow-400 text-sm flex items-center gap-2" onclick="Sidebar.showHelp()">
                ❓ Help & Support
            </button> -->
        </div>
    </div>

    <!-- Emergency Contacts -->
    <div class="p-4 border-b border-red-600">
        <h3 class="text-sm font-bold text-yellow-400 mb-3">Emergency Contacts</h3>
        <div class="space-y-2">
            <div class="text-xs text-gray-400">
                <div class="flex justify-between">
                    <span>Police:</span>
                    <span class="text-yellow-400">117</span>
                </div>
            </div>
            <div class="text-xs text-gray-400">
                <div class="flex justify-between">
                    <span>Fire Dept:</span>
                    <span class="text-yellow-400">116</span>
                </div>
            </div>
            <div class="text-xs text-gray-400">
                <div class="flex justify-between">
                    <span>Medical:</span>
                    <span class="text-yellow-400">911</span>
                </div>
            </div>
        </div>
    </div>

    <!-- User Session Info -->
    <div class="p-4 border-b border-red-600">
        <div class="text-xs text-gray-400 space-y-1">
            <div>Session: <span id="sessionTime" class="text-yellow-400">--:--</span></div>
            <div>Location: <span id="locationStatus" class="text-yellow-400">Getting location...</span></div>
            <div>Last Update: <span id="lastUpdate" class="text-yellow-400">--:--:--</span></div>
        </div>
    </div>

    <!-- Logout Button -->
    <div class="p-4">
        <button class="logout-btn w-full py-2 px-3 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-yellow-400" onclick="Sidebar.logout()">
            🚪 Logout
        </button>
    </div>
</aside>

<script>
// Independent Sidebar Module
const Sidebar = (function() {
    let isVisible = false;
    let userData = null;
    let sessionStartTime = new Date();
    let updateInterval = null;
    let locationWatcher = null;

    // Private methods
    function updateSessionTime() {
        const now = new Date();
        const diff = now - sessionStartTime;
        const minutes = Math.floor(diff / 60000);
        const seconds = Math.floor((diff % 60000) / 1000);
        const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        const sessionTimeElement = document.getElementById('sessionTime');
        if (sessionTimeElement) {
            sessionTimeElement.textContent = timeString;
        }
    }

    function updateLastUpdateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();

        const lastUpdateElement = document.getElementById('lastUpdate');
        if (lastUpdateElement) {
            lastUpdateElement.textContent = timeString;
        }
    }

    function watchLocation() {
        if (navigator.geolocation) {
            locationWatcher = navigator.geolocation.watchPosition(
                (position) => {
                    const locationElement = document.getElementById('locationStatus');
                    if (locationElement) {
                        locationElement.textContent = `${position.coords.latitude.toFixed(4)}, ${position.coords.longitude.toFixed(4)}`;
                    }
                    updateLastUpdateTime();
                },
                (error) => {
                    const locationElement = document.getElementById('locationStatus');
                    if (locationElement) {
                        locationElement.textContent = 'Location unavailable';
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 30000
                }
            );
        }
    }

    function stopLocationWatch() {
        if (locationWatcher) {
            navigator.geolocation.clearWatch(locationWatcher);
            locationWatcher = null;
        }
    }

    function startTimers() {
        // Update session time every second
        updateInterval = setInterval(() => {
            updateSessionTime();
        }, 1000);

        // Start location watching
        watchLocation();
    }

    function stopTimers() {
        if (updateInterval) {
            clearInterval(updateInterval);
            updateInterval = null;
        }
        stopLocationWatch();
    }

    async function loadUserData() {
        // Initialize with loading state
        updateAccountValidationUI(null);

        try {
            const token = sessionStorage.getItem('token');
            if (!token) {
                throw new Error('No token found');
            }

            const response = await fetch(`<?php echo $API; ?>/api/v1/IAM`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            userData = await response.json();
            updateUserInterface();

            // Fetch verification status separately
            loadVerificationStatus();
        } catch (error) {
            console.error('Sidebar: Failed to load user data:', error);
            // Fallback to default values
            userData = {
                firstName: 'User',
                lastName: '',
                email: 'user@example.com',
                verified: false
            };
            updateUserInterface();
        }
    }

    async function loadVerificationStatus() {
        try {
            const token = sessionStorage.getItem('token');
            if (!token) {
                throw new Error('No token found');
            }

            const response = await fetch(`<?php echo $API; ?>/api/v1/IAM/verified`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Get the response as text since it's a raw string, not JSON
            const verificationData = await response.text();
            console.log('Full verification response:', verificationData);

            // The response is already a string, so use it directly
            let verificationStatus = verificationData.trim() || 'none';

            console.log('Extracted verification status:', verificationStatus);
            updateAccountValidationUI(verificationStatus);
        } catch (error) {
            console.error('Sidebar: Failed to load verification status:', error);
            // Fallback to none status
            updateAccountValidationUI('none');
        }
    }

    function updateAccountValidationUI(verificationStatus) {
        console.log('Updating account validation UI with status:', verificationStatus);
        const accountBadge = document.getElementById('accountVerificationBadge');
        const accountText = document.getElementById('accountValidationText');

        if (accountBadge && accountText) {
            if (verificationStatus === null) {
                // Loading state
                accountBadge.textContent = '⏳';
                accountText.textContent = 'Loading...';
                accountText.className = 'text-xs text-gray-400';
            } else if (verificationStatus === 'accepted') {
                // Accepted state
                accountBadge.textContent = '✅';
                accountText.textContent = 'Accepted';
                accountText.className = 'text-xs text-green-400';
            } else if (verificationStatus === 'rejected') {
                // Rejected state
                accountBadge.textContent = '❌';
                accountText.textContent = 'Rejected';
                accountText.className = 'text-xs text-red-400';
            } else if (verificationStatus === 'pending') {
                // Pending state
                accountBadge.textContent = '⏳';
                accountText.textContent = 'Pending';
                accountText.className = 'text-xs text-orange-400';
            } else if (verificationStatus === 'none') {
                // None state
                accountBadge.textContent = '⚪';
                accountText.textContent = 'Not Started';
                accountText.className = 'text-xs text-gray-400';
            } else {
                // Default fallback to none
                accountBadge.textContent = '⚪';
                accountText.textContent = 'Not Started';
                accountText.className = 'text-xs text-gray-400';
            }
        }
    }

    function updateUserInterface() {
        if (!userData) return;

        const fullName = `${userData.firstName} ${userData.lastName}`.trim();
        const initials = userData.firstName && userData.lastName
            ? `${userData.firstName.charAt(0)}${userData.lastName.charAt(0)}`.toUpperCase()
            : userData.firstName
                ? userData.firstName.charAt(0).toUpperCase()
                : 'U';

        // Update sidebar elements
        const sidebarInitials = document.getElementById('sidebarUserInitials');
        const sidebarName = document.getElementById('sidebarUserName');
        const sidebarEmail = document.getElementById('sidebarUserEmail');

        if (sidebarInitials) {
            sidebarInitials.textContent = initials;
        }
        if (sidebarName) {
            sidebarName.textContent = fullName || 'User';
        }
        if (sidebarEmail) {
            sidebarEmail.textContent = userData.email || 'user@example.com';
        }

        // Also update header profile icon if it exists
        const headerInitials = document.getElementById('userInitials');
        if (headerInitials) {
            headerInitials.textContent = initials;
        }
    }

    function handleOutsideClick(event) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const profileButton = event.target.closest('[onclick*="toggleSidebar"], [onclick*="Sidebar.toggle"]');

        if (isVisible && !sidebar.contains(event.target) && !profileButton) {
            Sidebar.close();
        }
    }

    // Public API
    return {
        init: function() {
            console.log('Sidebar: Initializing...');
            loadUserData();
            startTimers();

            // Add outside click listener
            document.addEventListener('click', handleOutsideClick);

            console.log('Sidebar: Initialized successfully');
        },

        destroy: function() {
            console.log('Sidebar: Destroying...');
            stopTimers();
            document.removeEventListener('click', handleOutsideClick);
        },

        toggle: function() {
            if (isVisible) {
                this.close();
            } else {
                this.open();
            }
        },

        open: function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (sidebar && overlay) {
                sidebar.classList.add('active');
                overlay.classList.add('active');
                isVisible = true;

                // Refresh data when opening
                updateLastUpdateTime();
                console.log('Sidebar: Opened');
            }
        },

        close: function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (sidebar && overlay) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                isVisible = false;
                console.log('Sidebar: Closed');
            }
        },

        isOpen: function() {
            return isVisible;
        },



        // showSettings: function() {
        //     alert('Settings would open here');
        //     // You can implement settings modal or navigation here
        // },

        // showHelp: function() {
        //     alert('Help & Support would open here');
        //     // You can implement help modal or navigation here
        // },

        logout: function() {
            console.log('Sidebar: Logging out...');

            // Stop all sidebar timers and watchers
            this.destroy();

            // Notify other components about logout (if they're listening)
            if (window.App && typeof window.App.onLogout === 'function') {
                window.App.onLogout();
            }

            // Clear all session storage
            sessionStorage.clear();

            // Redirect to home
            window.location.href = '/';
        },

        refreshUserData: function() {
            console.log('Sidebar: Refreshing user data...');
            loadUserData();
        },

        updateStatus: function(status) {
            console.log('Updating status:', status);
            const statusElement = document.getElementById('sidebarStatus');
            if (statusElement) {
                statusElement.textContent = status;
            }
        },

        getUserData: function() {
            return userData;
        }
    };
})();

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    Sidebar.init();
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    Sidebar.destroy();
});
</script>
