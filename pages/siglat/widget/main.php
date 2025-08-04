<!-- Drawer Button -->
<button id="drawerBtn" style="position: fixed; bottom: 20px; right: 20px; z-index: 2147483647; background: #88c0d0; color: #2e3440; border: none; border-radius: 50%; width: 60px; height: 60px; font-size: 24px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.3); transition: transform 0.2s ease;">
    ☰
</button>

<!-- Drawer -->
<div id="drawer" style="position: fixed; bottom: -100%; left: 0; right: 0; height: 95%; background: #2e3440; z-index: 2147483647; transition: bottom 0.3s ease; border-top: 1px solid #434c5e; border-radius: 15px 15px 0 0; box-shadow: 0 -4px 20px rgba(0,0,0,0.2);">
    <!-- Drawer Header -->
    <div style="padding: 15px 20px; border-bottom: 1px solid #434c5e; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; color: #eceff4; font-size: clamp(1.1rem, 4vw, 1.3rem);">Dashboard</h3>
        <button id="closeDrawer" style="background: none; border: none; font-size: 28px; cursor: pointer; color: #d8dee9; padding: 5px; border-radius: 50%; transition: background-color 0.2s ease;" onmouseover="this.style.backgroundColor='#434c5e'" onmouseout="this.style.backgroundColor='transparent'">×</button>
    </div>

    <!-- Tab Navigation -->
    <div id="tabNavigation" style="display: flex; border-bottom: 1px solid #434c5e; overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <button class="tab-btn active" data-tab="weather" data-tab-index="0" data-requires-auth="false" style="flex: 1; min-width: 80px; padding: 12px 8px; border: none; background: none; cursor: pointer; border-bottom: 2px solid #5e81ac; color: #5e81ac; font-size: clamp(0.8rem, 3vw, 1rem); white-space: nowrap; transition: all 0.2s ease;">
        <span class="tab-icon">🌤️</span>
        <span class="tab-text">Weather</span>
        </button>
        <button class="tab-btn" data-tab="statistics" data-tab-index="1" data-requires-auth="false" style="flex: 1; min-width: 80px; padding: 12px 8px; border: none; background: none; cursor: pointer; border-bottom: 2px solid transparent; color: #d8dee9; font-size: clamp(0.8rem, 3vw, 1rem); white-space: nowrap; transition: all 0.2s ease;">
        <span class="tab-icon">📊</span>
        <span class="tab-text">Stats</span>
        </button>
        <button class="tab-btn" data-tab="users" data-tab-index="2" data-requires-auth="false" style="flex: 1; min-width: 80px; padding: 12px 8px; border: none; background: none; cursor: pointer; border-bottom: 2px solid transparent; color: #d8dee9; font-size: clamp(0.8rem, 3vw, 1rem); white-space: nowrap; transition: all 0.2s ease;">
        <span class="tab-icon">👥</span>
        <span class="tab-text">Users</span>
        </button>
        <button class="tab-btn" data-tab="contact" data-tab-index="3" data-requires-auth="false" style="flex: 1; min-width: 80px; padding: 12px 8px; border: none; background: none; cursor: pointer; border-bottom: 2px solid transparent; color: #d8dee9; font-size: clamp(0.8rem, 3vw, 1rem); white-space: nowrap; transition: all 0.2s ease;">
        <span class="tab-icon">📞</span>
        <span class="tab-text">Contact</span>
        </button>
    </div>

    <!-- Loading Indicator -->
    <div id="tabLoading" style="display: none; padding: 20px; text-align: center; color: #d8dee9;">
        <div style="display: inline-block; width: 20px; height: 20px; border: 2px solid #434c5e; border-radius: 50%; border-top-color: #5e81ac; animation: spin 1s ease-in-out infinite;"></div>
        <p style="margin: 10px 0 0 0;">Loading...</p>
    </div>

    <!-- Tab Content Container -->
    <div style="padding: 15px; height: calc(100% - 140px); overflow: auto;">
        <div id="weather-tab" class="tab-content" style="display: block; height: 100%; margin: -15px 0; padding: 15px 15px 15px 0;">
            <iframe src="https://www.accuweather.com/en/ph/villaverde/265132/hourly-weather-forecast/265132" style="width: 100%; height: 100%; border: none; border-radius: 0;"></iframe>
        </div>

        <div id="statistics-tab" class="tab-content" style="display: none; height: 100%; color: #eceff4; padding: 0 15px 0 0;">
            <?php include "./pages/siglat/applet/stats.php"; ?>
        </div>

        <div id="users-tab" class="tab-content" style="display: none; height: 100%; color: #eceff4; padding: 0 15px 0 0;">
            <?php include "./pages/siglat/applet/tab.php"; ?>
        </div>

        <div id="contact-tab" class="tab-content" style="display: none; height: 100%; color: #eceff4; padding: 0; margin: 0;">
            <?php include "./pages/siglat/applet/contact.php"; ?>
        </div>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 480px) {
    .tab-text {
        display: none;
    }
    .tab-icon {
        font-size: 1.2em;
    }
    #drawer {
        height: 98% !important;
    }
    .tab-btn {
        min-width: 60px !important;
    }
}

@media (hover: hover) {
    .tab-btn:hover {
        background-color: #3b4252 !important;
    }
    #drawerBtn:hover {
        transform: scale(1.05);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const drawerBtn = document.getElementById('drawerBtn');
    const drawer = document.getElementById('drawer');
    const closeDrawer = document.getElementById('closeDrawer');
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    const tabLoading = document.getElementById('tabLoading');

    // User authentication state (simulate)
    let isAuthenticated = false; // Change this based on actual auth state
    let currentTabIndex = 0;

    // Swipe detection variables
    let touchStartY = 0;
    let touchStartTime = 0;
    let isSwiping = false;
    let isDragging = false;
    let currentTranslate = 0;
    let startTranslate = 0;

    // Verification functions
    function canAccessTab(tabBtn) {
        return true; // Always allow access
    }

    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.style.cssText = 'position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: #bf616a; color: white; padding: 10px 20px; border-radius: 5px; z-index: 10000; animation: slideDown 0.3s ease;';
        errorDiv.textContent = message;
        document.body.appendChild(errorDiv);

        setTimeout(() => {
            errorDiv.remove();
        }, 3000);
    }

    function validateTabSwitch(targetTab) {
        const targetElement = document.getElementById(targetTab + '-tab');
        if (!targetElement) {
            showError('Tab content not found');
            return false;
        }
        return true;
    }

    function showLoadingState() {
        tabLoading.style.display = 'block';
        tabContents.forEach(content => {
            content.style.opacity = '0.3';
        });
    }

    function hideLoadingState() {
        tabLoading.style.display = 'none';
        tabContents.forEach(content => {
            content.style.opacity = '1';
        });
    }

    function showTab(targetIndex) {
        // Hide all tabs
        tabContents.forEach(content => {
            content.style.display = 'none';
        });

        // Show target tab
        const targetTab = tabContents[targetIndex];
        if (targetTab) {
            targetTab.style.display = 'block';
        }

        currentTabIndex = targetIndex;
    }

    // Toggle drawer
    drawerBtn.addEventListener('click', function() {
        drawer.style.bottom = drawer.style.bottom === '0%' ? '-100%' : '0%';
        drawer.style.transition = 'bottom 0.3s ease';
    });

    // Close drawer
    closeDrawer.addEventListener('click', function() {
        drawer.style.bottom = '-100%';
        drawer.style.transition = 'bottom 0.3s ease';
    });

    // Touch events for swipe detection and dragging
    drawer.addEventListener('touchstart', function(e) {
        touchStartY = e.touches[0].clientY;
        touchStartTime = Date.now();
        isSwiping = false;
        isDragging = false;

        // Get current position
        const rect = drawer.getBoundingClientRect();
        startTranslate = rect.bottom - window.innerHeight;
        currentTranslate = startTranslate;

        // Disable transition for smooth dragging
        drawer.style.transition = 'none';
    });

    drawer.addEventListener('touchmove', function(e) {
        const touchCurrentY = e.touches[0].clientY;
        const deltaY = touchCurrentY - touchStartY;

        if (!isSwiping && Math.abs(deltaY) > 10) {
            isSwiping = true;
            isDragging = true;
        }

        if (isDragging && deltaY > 0) {
            // Calculate new position (only allow downward movement)
            const movePercent = (deltaY / window.innerHeight) * 100;
            const newBottom = Math.max(-100, startTranslate - movePercent);

            drawer.style.bottom = newBottom + '%';
            currentTranslate = newBottom;

            // Prevent scrolling
            e.preventDefault();
        }
    });

    drawer.addEventListener('touchend', function(e) {
        if (isDragging) {
            const touchEndY = e.changedTouches[0].clientY;
            const touchEndTime = Date.now();
            const deltaY = touchEndY - touchStartY;
            const deltaTime = touchEndTime - touchStartTime;
            const velocity = deltaY / deltaTime; // pixels per millisecond

            // Re-enable transition
            drawer.style.transition = 'bottom 0.3s ease';

            // Close drawer if fast downward swipe (velocity > 0.3 pixels/ms) or dragged more than 30%
            if ((velocity > 0.3 && deltaY > 30) || currentTranslate < -30) {
                drawer.style.bottom = '-100%';
            } else {
                // Snap back to open position
                drawer.style.bottom = '0%';
            }
        }

        isSwiping = false;
        isDragging = false;
    });

    // Enhanced tab switching with verification
    tabBtns.forEach(function(btn, index) {
        btn.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            const targetIndex = parseInt(this.getAttribute('data-tab-index'));

            if (!validateTabSwitch(targetTab)) {
                return;
            }

            // Show loading state
            showLoadingState();

            // Simulate loading delay for better UX
            setTimeout(() => {
                // Remove active class from all tabs
                tabBtns.forEach(function(b) {
                    b.classList.remove('active');
                    b.style.borderBottomColor = 'transparent';
                    b.style.color = '#d8dee9';
                    b.style.backgroundColor = 'transparent';
                });

                // Hide loading state
                hideLoadingState();

                // Activate clicked tab
                this.classList.add('active');
                this.style.borderBottomColor = '#5e81ac';
                this.style.color = '#5e81ac';

                // Show the target tab
                showTab(targetIndex);

                // Scroll tab into view if needed
                this.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });

            }, 50);
        });

        // Add hover effects for touch devices
        btn.addEventListener('touchstart', function() {
            if (!this.classList.contains('active')) {
                this.style.backgroundColor = '#3b4252';
            }
        });

        btn.addEventListener('touchend', function() {
            if (!this.classList.contains('active')) {
                this.style.backgroundColor = 'transparent';
            }
        });
    });

    // Close drawer when clicking outside
    document.addEventListener('click', function(e) {
        if (!drawer.contains(e.target) && !drawerBtn.contains(e.target) && drawer.style.bottom === '0%') {
            drawer.style.bottom = '-100%';
            drawer.style.transition = 'bottom 0.3s ease';
        }
    });

    // Handle orientation changes
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            if (drawer.style.bottom === '0%') {
                drawer.style.bottom = '0%'; // Force redraw
                // Ensure current tab position is maintained
                showTab(currentTabIndex);
            }
        }, 100);
    });

    // Keyboard navigation support
    document.addEventListener('keydown', function(e) {
        if (drawer.style.bottom === '0%') {
            if (e.key === 'Escape') {
                drawer.style.bottom = '-100%';
                drawer.style.transition = 'bottom 0.3s ease';
            }
            // Add left/right arrow key navigation
            else if (e.key === 'ArrowLeft' && currentTabIndex > 0) {
                tabBtns[currentTabIndex - 1].click();
            }
            else if (e.key === 'ArrowRight' && currentTabIndex < tabBtns.length - 1) {
                tabBtns[currentTabIndex + 1].click();
            }
        }
    });
});
</script>
