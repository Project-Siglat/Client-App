<style>
.tabs {
    display: flex;
    background-color: #3B4252;
    border-radius: 8px 8px 0 0;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.tabs a {
    flex: 1;
    padding: 16px 24px;
    text-decoration: none;
    color: #D8DEE9;
    background-color: #3B4252;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    font-size: 14px;
    text-align: center;
    position: relative;
}

.tabs a:hover {
    background-color: #434C5E;
    color: #ECEFF4;
}

.tabs a.active {
    background-color: #5E81AC;
    color: #ECEFF4;
    box-shadow: inset 0 -3px 0 #88C0D0;
}

.tabs a.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background-color: #88C0D0;
}

#tab-content {
    background-color: #2E3440;
    border-radius: 0 0 8px 8px;
    min-height: 400px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

#tab-content > div {
    padding: 24px;
    color: #ECEFF4;
}

@media (max-width: 768px) {
    .tabs a {
        padding: 12px 16px;
        font-size: 13px;
    }

    #tab-content > div {
        padding: 16px;
    }
}
</style>

<div class="tabs">
    <a id="user-tab" class="active" onclick="switchTab('user')">User</a>
    <a id="verify-tab" class="" onclick="switchTab('verify')">Verify</a>
</div>

<div id="tab-content">
    <div id="user-content" style="display: block;">
        <?php include "./pages/siglat/applet/users.php"; ?>
    </div>
    <div id="verify-content" style="display: none;">
        <?php include "verification.php"; ?>
    </div>
</div>

<script>
function switchTab(tab) {
    // Remove active class from all tabs
    document.getElementById('user-tab').classList.remove('active');
    document.getElementById('verify-tab').classList.remove('active');

    // Hide all content
    document.getElementById('user-content').style.display = 'none';
    document.getElementById('verify-content').style.display = 'none';

    // Show selected tab and content
    if (tab === 'user') {
        document.getElementById('user-tab').classList.add('active');
        document.getElementById('user-content').style.display = 'block';
    } else if (tab === 'verify') {
        document.getElementById('verify-tab').classList.add('active');
        document.getElementById('verify-content').style.display = 'block';
    }
}
</script>
