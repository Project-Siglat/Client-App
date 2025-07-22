<?php
include "./pages/siglat/topbar.php";
include "./config/env.php";
$API = $_ENV["API"];

// Check current page/route to determine what to display
$current_page = $_GET["where"] ?? "dashboard";

if ($current_page === "verification") {
    include "./pages/siglat/users/verification.php";
    exit();
}

if ($current_page === "user-list") {
    include "./pages/siglat/users/user.php";
    exit();
}

// Only display dashboard if not on verification or users page
if ($current_page === "dashboard") { ?>

<!-- Google Fonts for modern look -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Montserrat:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div class="dashboard-container">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>

    <!-- Ultra Glassmorphism Header -->
    <div class="dashboard-header ultra-glass">
        <div class="dashboard-title">
            <img src="https://cdn-icons-png.flaticon.com/512/484/484167.png" alt="Logo" class="dashboard-logo">
            <h1>
                <span class="gradient-text">Disaster Response Dashboard</span>
            </h1>
        </div>
        <div class="dashboard-actions">
            <button class="btn btn-gradient btn-action" onclick="window.location.reload()" title="Refresh">
                <i class="bi bi-arrow-repeat"></i>
            </button>
            <button class="btn btn-gradient btn-action" onclick="window.location.href='?where=user-list'" title="Users">
                <i class="bi bi-people"></i>
            </button>
            <button class="btn btn-gradient btn-action" onclick="window.location.href='?where=verification'" title="Verification">
                <i class="bi bi-shield-check"></i>
            </button>
        </div>
    </div>

    <div class="main-content">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Weather Card -->
            <div class="panel-item weather-card">
                <div class="weather-header">
                    <span class="weather-icon animated-icon">🌀</span>
                    <h3 class="panel-title">Hourly Weather Forecast</h3>
                </div>
                <div class="weather-iframe-container">
                    <iframe
                        src="https://www.accuweather.com/en/ph/villaverde/265132/hourly-weather-forecast/265132"
                        class="weather-iframe"
                        frameborder="0"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>

            <!-- Incident Statistics -->
            <div class="panel-item stats-card">
                <h3 class="panel-title"><i class="bi bi-bar-chart-line-fill"></i> Incident Statistics</h3>
                <div class="stats-grid compact">
                    <div class="stat-box stat-total">
                        <span class="stat-value incident-stats"><i class="bi bi-calendar-day"></i> 12</span>
                        <label>Total Today</label>
                    </div>
                    <div class="stat-box stat-success">
                        <span class="stat-value success"><i class="bi bi-check-circle-fill"></i> 8</span>
                        <label>Resolved</label>
                    </div>
                    <div class="stat-box stat-warning">
                        <span class="stat-value warning"><i class="bi bi-hourglass-split"></i> 4</span>
                        <label>Pending</label>
                    </div>
                    <div class="stat-box stat-danger">
                        <span class="stat-value danger"><i class="bi bi-exclamation-triangle-fill"></i> 2</span>
                        <label>Critical</label>
                    </div>
                </div>
                <div class="stats-progress">
                    <div class="progress-bar">
                        <div class="progress-resolved" style="width: 66%"></div>
                        <div class="progress-pending" style="width: 22%"></div>
                        <div class="progress-critical" style="width: 12%"></div>
                    </div>
                </div>
            </div>

            <!-- Contact Management -->
            <div class="panel-item contact-card">
                <div class="contact-header">
                    <span class="contact-icon animated-icon">📞</span>
                    <h3 class="panel-title">Contact Management</h3>
                </div>
                <div class="contact-crud-controls">
                    <button class="btn btn-gradient btn-small" onclick="openContactModal()">
                        <i class="bi bi-plus-circle"></i> Add Contact
                    </button>
                </div>
                <div class="contacts-container">
                    <div class="table-responsive">
                        <table class="contacts-table">
                            <thead>
                                <tr>
                                    <th><i class="bi bi-tag"></i> Label</th>
                                    <th><i class="bi bi-telephone"></i> Type</th>
                                    <th><i class="bi bi-info-circle"></i> Information</th>
                                    <th><i class="bi bi-tools"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody id="contactsTableBody">
                                <!-- Contacts will be loaded from API -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-area">
            <!-- Map Section -->
            <div class="map-section">
                <div class="map-header">
                    <h2><span class="animated-icon">🗺️</span> <span class="gradient-text">Map View</span></h2>
                    <div class="map-legend">
                        <span class="legend-item"><span class="legend-icon">🚨</span> Emergency</span>
                        <span class="legend-item"><span class="legend-icon">🚑</span> Ambulance</span>
                        <span class="legend-item"><span class="legend-icon">🌊</span> Flood</span>
                    </div>
                </div>
                <div id="map" class="map-container">
                    <!-- Leaflet map will be initialized here -->
                    <div class="map-loader" id="mapLoader">
                        <div class="loader-spinner"></div>
                        <span>Loading Map...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle"><i class="bi bi-person-plus"></i> Add Contact</h3>
                <span class="close" onclick="closeContactModal()">&times;</span>
            </div>
            <form id="contactForm" onsubmit="saveContact(event)">
                <div class="form-group" style="display: none;">
                    <label for="contactId">ID:</label>
                    <input type="text" id="contactId" name="contactId" required>
                </div>
                <div class="form-group">
                    <label for="contactLabel"><i class="bi bi-tag"></i> Contact Label:</label>
                    <input type="text" id="contactLabel" name="contactLabel" required placeholder="e.g. Police Station">
                </div>
                <div class="form-group">
                    <label for="contactType"><i class="bi bi-telephone"></i> Contact Type:</label>
                    <select id="contactType" name="contactType" required>
                        <option value="">Select Contact Type</option>
                        <option value="phone">Phone</option>
                        <option value="email">Email</option>
                        <option value="facebook">Facebook</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="contactInformation"><i class="bi bi-info-circle"></i> Contact Information:</label>
                    <input type="text" id="contactInformation" name="contactInformation" required placeholder="e.g. 0917-123-4567">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeContactModal()"><i class="bi bi-x-circle"></i> Cancel</button>
                    <button type="submit" class="btn btn-gradient"><i class="bi bi-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

    <!-- Enhanced UI Styles -->
    <style>
        * {
            box-sizing: border-box;
        }

        body, .dashboard-container {
            font-family: 'Inter', 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: radial-gradient(circle at 60% 40%, #a1c4fd 0%, #c2e9fb 40%, #667eea 100%);
            min-height: 100vh;
            height: 100vh;
            overflow: hidden;
        }

        .ultra-glass {
            background: rgba(255,255,255,0.22);
            border-radius: 32px;
            box-shadow: 0 16px 64px rgba(76,130,255,0.22), 0 2px 8px rgba(0,0,0,0.08);
            backdrop-filter: blur(24px) saturate(200%);
            border: 2px solid rgba(255,255,255,0.28);
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 40px 60px;
            margin-bottom: 40px;
            background: linear-gradient(90deg, rgba(76,130,255,0.08) 0%, rgba(124,58,237,0.08) 100%);
        }

        .dashboard-title {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .dashboard-logo {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.12);
            background: white;
            border: 3px solid #7c3aed;
            transition: transform 0.2s;
        }
        .dashboard-logo:hover {
            transform: scale(1.08) rotate(-8deg);
        }

        .dashboard-header h1 {
            color: #fff;
            font-size: 3.2rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: 2px;
            text-shadow: 0 4px 24px rgba(76,130,255,0.28);
        }

        .gradient-text {
            background: linear-gradient(90deg, #4f8cff 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .dashboard-actions {
            display: flex;
            gap: 24px;
        }

        .btn-gradient {
            background: linear-gradient(90deg, #4f8cff 0%, #7c3aed 100%);
            color: #fff;
            border: none;
            border-radius: 16px;
            padding: 16px 36px;
            font-size: 1.2rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 4px 24px rgba(76, 130, 255, 0.22);
            transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-gradient:hover, .btn-gradient:focus {
            transform: translateY(-2px) scale(1.08);
            box-shadow: 0 12px 48px rgba(76, 130, 255, 0.32);
            background: linear-gradient(90deg, #7c3aed 0%, #4f8cff 100%);
        }
        .btn-action {
            padding: 16px 20px;
            font-size: 1.5rem;
            border-radius: 50%;
            min-width: 56px;
            min-height: 56px;
            justify-content: center;
            box-shadow: 0 2px 12px rgba(76,130,255,0.18);
        }

        .main-content {
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 44px;
            max-width: 1920px;
            margin: 0 auto;
            height: calc(100vh - 260px);
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 40px;
            overflow-y: auto;
            padding-right: 32px;
        }

        .sidebar::-webkit-scrollbar {
            width: 12px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(120,120,255,0.28);
            border-radius: 8px;
        }

        .panel-item {
            background: rgba(255,255,255,0.32);
            padding: 40px 32px;
            border-radius: 28px;
            box-shadow: 0 12px 48px rgba(76,130,255,0.18);
            backdrop-filter: blur(24px) saturate(180%);
            border: 2px solid rgba(255,255,255,0.28);
            position: relative;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .panel-item:hover {
            box-shadow: 0 24px 72px rgba(76,130,255,0.32);
            transform: translateY(-2px) scale(1.04);
        }

        .panel-title {
            font-family: 'Montserrat', 'Inter', sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            color: #7c3aed;
            margin-bottom: 16px;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .weather-card .weather-header,
        .contact-card .contact-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
        }
        .weather-icon, .contact-icon, .animated-icon {
            font-size: 3rem;
            background: linear-gradient(135deg, #4f8cff 0%, #7c3aed 100%);
            color: #fff;
            border-radius: 50%;
            padding: 14px;
            box-shadow: 0 4px 24px rgba(76, 130, 255, 0.22);
            animation: bounce 1.8s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0);}
            50% { transform: translateY(-10px);}
        }

        .weather-iframe-container {
            height: 460px;
            border-radius: 18px;
            overflow: hidden;
            background: #f9fafb;
            box-shadow: 0 4px 24px rgba(76,130,255,0.14);
        }

        .weather-iframe {
            width: 100%;
            height: 400px;
            border: none;
            border-radius: 18px;
            background: #f9fafb;
        }

        .stats-card h3 {
            margin-bottom: 22px;
            font-size: 1.4rem;
            color: #4f8cff;
            font-weight: 800;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }
        .stats-grid.compact {
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }
        .stat-box {
            padding: 28px 18px;
            background: linear-gradient(135deg, #f9fafb 60%, #e0e7ff 100%);
            border-radius: 18px;
            text-align: center;
            border: 2px solid #e0e7ff;
            box-shadow: 0 4px 24px rgba(76, 130, 255, 0.14);
            transition: box-shadow 0.2s, transform 0.2s;
            position: relative;
        }
        .stat-box:hover {
            box-shadow: 0 12px 36px rgba(76,130,255,0.22);
            transform: scale(1.06);
        }
        .stat-box label {
            display: block;
            font-size: 1rem;
            color: #6b7280;
            margin-top: 10px;
            font-weight: 600;
        }
        .stat-value {
            font-weight: 800;
            display: block;
            font-size: 2rem;
            margin-bottom: 2px;
            letter-spacing: 1.5px;
        }
        .incident-stats { color: #4f8cff; }
        .stat-value.success { color: #059669; }
        .stat-value.warning { color: #d97706; }
        .stat-value.danger { color: #dc2626; }

        .stat-total { border-left: 7px solid #4f8cff; }
        .stat-success { border-left: 7px solid #059669; }
        .stat-warning { border-left: 7px solid #d97706; }
        .stat-danger { border-left: 7px solid #dc2626; }

        .stats-progress {
            margin-top: 22px;
            margin-bottom: 10px;
        }
        .progress-bar {
            width: 100%;
            height: 18px;
            background: #e0e7ff;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }
        .progress-resolved {
            background: linear-gradient(90deg, #059669 0%, #4f8cff 100%);
            height: 100%;
            border-radius: 10px 0 0 10px;
            position: absolute;
            left: 0;
            top: 0;
        }
        .progress-pending {
            background: linear-gradient(90deg, #d97706 0%, #fbbf24 100%);
            height: 100%;
            position: absolute;
            left: 66%;
            top: 0;
        }
        .progress-critical {
            background: linear-gradient(90deg, #dc2626 0%, #f87171 100%);
            height: 100%;
            border-radius: 0 10px 10px 0;
            position: absolute;
            left: 88%;
            top: 0;
        }

        .contact-crud-controls {
            margin-bottom: 22px;
            display: flex;
            justify-content: flex-end;
        }

        .btn-small {
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 10px;
        }

        .contacts-container {
            height: 260px;
            overflow-y: auto;
            border: 2px solid #e0e7ff;
            border-radius: 14px;
            background: #f9fafb;
            box-shadow: 0 4px 16px rgba(76, 130, 255, 0.10);
        }
        .contacts-container::-webkit-scrollbar {
            width: 8px;
        }
        .contacts-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .contacts-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 1.05rem;
        }
        .contacts-table th {
            padding: 18px 22px;
            background: linear-gradient(135deg, #f9fafb, #e0e7ff);
            color: #4f8cff;
            font-weight: 800;
            text-align: left;
            border-bottom: 2px solid #e0e7ff;
            font-size: 1.1rem;
        }
        .contacts-table td {
            padding: 18px 22px;
            border-bottom: 1px solid #e0e7ff;
            color: #374151;
            font-size: 1.05rem;
        }
        .contacts-table th:last-child,
        .contacts-table td:last-child {
            width: 140px;
            text-align: center;
        }

        .action-buttons {
            display: flex;
            gap: 14px;
            justify-content: center;
        }

        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px;
            border-radius: 10px;
            font-size: 1.3rem;
            transition: all 0.2s ease;
        }
        .btn-edit:hover {
            background: #e0f2fe;
            transform: scale(1.16);
        }
        .btn-delete:hover {
            background: #fee2e2;
            transform: scale(1.16);
        }

        /* Map Section */
        .main-area {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .map-section {
            background: rgba(255,255,255,0.28);
            border-radius: 24px;
            padding: 40px 36px;
            box-shadow: 0 12px 48px rgba(0,0,0,0.12);
            height: 100%;
            min-height: 0;
            border: 2px solid rgba(255,255,255,0.22);
            backdrop-filter: blur(18px);
            position: relative;
        }
        .map-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .map-section h2 {
            margin: 0;
            color: #4f8cff;
            font-size: 2rem;
            font-weight: 800;
        }
        .map-legend {
            display: flex;
            gap: 24px;
            background: rgba(76,130,255,0.12);
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 1.05rem;
            color: #374151;
            font-weight: 600;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .legend-icon {
            font-size: 1.4rem;
        }
        .map-container {
            height: calc(100% - 70px);
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            background: #f3f4f6;
            border: none;
            box-shadow: 0 6px 24px rgba(76,130,255,0.14);
            position: relative;
        }
        .map-loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .loader-spinner {
            width: 40px;
            height: 40px;
            border: 5px solid #e0e7ff;
            border-top: 5px solid #4f8cff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(76,130,255,0.22);
            backdrop-filter: blur(12px);
        }
        .modal-content {
            background: linear-gradient(135deg, #fff 80%, #e0e7ff 100%);
            margin: 6% auto;
            padding: 0;
            border-radius: 24px;
            width: 97%;
            max-width: 560px;
            box-shadow: 0 24px 72px rgba(76,130,255,0.28);
            border: 2px solid #e0e7ff;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 32px 36px;
            border-bottom: 2px solid #e0e7ff;
        }
        .modal-header h3 {
            margin: 0;
            color: #4f8cff;
            font-size: 1.5rem;
            font-weight: 800;
        }
        .close {
            color: #7c3aed;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
            transition: color 0.2s ease;
        }
        .close:hover {
            color: #4f8cff;
        }
        .form-group {
            margin-bottom: 28px;
            padding: 0 36px;
        }
        .form-group:first-of-type {
            margin-top: 28px;
        }
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 700;
            color: #374151;
            font-size: 1.05rem;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 18px;
            border: 2px solid #e0e7ff;
            border-radius: 10px;
            font-size: 1.1rem;
            transition: border-color 0.2s;
            background: #f9fafb;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #4f8cff;
            box-shadow: 0 0 0 4px rgba(76,130,255,0.12);
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 18px;
            padding: 24px 36px;
            border-top: 2px solid #e0e7ff;
        }
        .btn-secondary {
            background: #e0e7ff;
            color: #4f8cff;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .btn-secondary:hover {
            background: #4f8cff;
            color: #fff;
        }

        /* Responsive Design */
        @media (max-width: 1400px) {
            .main-content {
                grid-template-columns: 320px 1fr;
            }
            .dashboard-header {
                flex-direction: column;
                gap: 24px;
                padding: 24px 16px;
            }
        }
        @media (max-width: 1000px) {
            .main-content {
                grid-template-columns: 1fr;
                height: auto;
            }
            .sidebar {
                order: 2;
                overflow-y: visible;
                padding-right: 0;
            }
            .main-area {
                order: 1;
            }
            .map-container {
                height: 360px;
            }
            .panel-item {
                padding: 18px;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 700px) {
            .dashboard-header h1 {
                font-size: 1.7rem;
            }
            .dashboard-logo {
                width: 44px;
                height: 44px;
            }
            .main-content {
                gap: 16px;
            }
            .map-container {
                height: 240px;
            }
            .contacts-container {
                height: 140px;
            }
            .contacts-table th,
            .contacts-table td {
                padding: 10px 8px;
            }
            .modal-content {
                margin: 5% auto;
                width: 99%;
            }
        }
        @media (max-width: 500px) {
            .dashboard-header {
                padding: 10px 4px;
            }
            .dashboard-header h1 {
                font-size: 1.1rem;
            }
            .dashboard-logo {
                width: 28px;
                height: 28px;
            }
            .main-content {
                gap: 8px;
            }
            .map-container {
                height: 120px;
            }
            .contacts-container {
                height: 80px;
            }
            .contacts-table th,
            .contacts-table td {
                padding: 4px 2px;
            }
            .modal-content {
                margin: 2% auto;
                width: 100%;
            }
        }
    </style>

    <script>
        // Contact CRUD operations
        let isEditMode = false;
        let editingContactId = null;
        let contactIdCounter = 6; // Start from 6 since we have 5 sample contacts
        const API_BASE = '<?php echo $API; ?>';

        function generateGuid() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        // Load contacts from API
        async function loadContacts() {
            try {
                const response = await fetch(`${API_BASE}/api/v1/Admin/contact`, {
                    method: 'GET',
                    headers: {
                        'accept': '*/*',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const contacts = await response.json();
                    const tbody = document.getElementById('contactsTableBody');
                    tbody.innerHTML = '';

                    contacts.forEach(contact => {
                        addContactToTable(contact.id, contact.label, contact.contactType, contact.contactInformation);
                    });
                } else {
                    console.error('Failed to load contacts');
                }
            } catch (error) {
                console.error('Error loading contacts:', error);
            }
        }

        function openContactModal(id = null, label = '', type = '', information = '') {
            const modal = document.getElementById('contactModal');
            const title = document.getElementById('modalTitle');
            const form = document.getElementById('contactForm');

            if (id) {
                // Edit mode
                isEditMode = true;
                editingContactId = id;
                title.textContent = 'Edit Contact';
                document.getElementById('contactId').value = id;
                document.getElementById('contactLabel').value = label;
                document.getElementById('contactType').value = type;
                document.getElementById('contactInformation').value = information;
                document.getElementById('contactId').disabled = true;
            } else {
                // Add mode
                isEditMode = false;
                editingContactId = null;
                title.textContent = 'Add Contact';
                form.reset();
                document.getElementById('contactId').value = generateGuid();
                document.getElementById('contactLabel').value = '';
                document.getElementById('contactId').disabled = true;
            }

            modal.style.display = 'block';
        }

        function closeContactModal() {
            const modal = document.getElementById('contactModal');
            modal.style.display = 'none';
            document.getElementById('contactForm').reset();
            isEditMode = false;
            editingContactId = null;
        }

        async function saveContact(event) {
            event.preventDefault();

            const id = document.getElementById('contactId').value;
            const label = document.getElementById('contactLabel').value;
            const type = document.getElementById('contactType').value;
            const information = document.getElementById('contactInformation').value;

            try {
                const contactData = {
                    id: id,
                    label: label,
                    contactType: type,
                    contactInformation: information,
                    createdAt: new Date().toISOString(),
                    updatedAt: new Date().toISOString()
                };

                const response = await fetch(`${API_BASE}/api/v1/Admin/contact`, {
                    method: 'POST',
                    headers: {
                        'accept': '*/*',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(contactData)
                });

                if (response.ok) {
                    if (isEditMode) {
                        updateContactInTable(id, label, type, information);
                        showToast('Contact updated successfully!', 'success');
                    } else {
                        addContactToTable(id, label, type, information);
                        contactIdCounter++;
                        showToast('Contact added successfully!', 'success');
                    }
                } else {
                    showToast('Failed to save contact. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Error saving contact:', error);
                showToast('Error saving contact. Please try again.', 'error');
            }

            closeContactModal();
        }

        function addContactToTable(id, label, type, information) {
            const tbody = document.getElementById('contactsTableBody');
            const row = document.createElement('tr');
            row.setAttribute('data-id', id);

            row.innerHTML = `
                <td>${label}</td>
                <td>${type}</td>
                <td>${information}</td>
                <td class="action-buttons">
                    <button class="btn-icon btn-edit" title="Edit" onclick="editContact('${id}', '${label}', '${type}', '${information}')">✏️</button>
                    <button class="btn-icon btn-delete" title="Delete" onclick="deleteContact('${id}')">🗑️</button>
                </td>
            `;

            tbody.appendChild(row);
        }

        function updateContactInTable(id, label, type, information) {
            const tbody = document.getElementById('contactsTableBody');
            const rows = tbody.getElementsByTagName('tr');

            for (let row of rows) {
                if (row.getAttribute('data-id') === id) {
                    row.cells[0].textContent = label;
                    row.cells[1].textContent = type;
                    row.cells[2].textContent = information;
                    row.cells[3].innerHTML = `
                        <button class="btn-icon btn-edit" title="Edit" onclick="editContact('${id}', '${label}', '${type}', '${information}')">✏️</button>
                        <button class="btn-icon btn-delete" title="Delete" onclick="deleteContact('${id}')">🗑️</button>
                    `;
                    break;
                }
            }
        }

        function editContact(id, label, type, information) {
            openContactModal(id, label, type, information);
        }

        async function deleteContact(id) {
            if (confirm('Are you sure you want to delete this contact?')) {
                try {
                    const response = await fetch(`${API_BASE}/api/v1/Admin/contact?Id=${id}`, {
                        method: 'DELETE',
                        headers: {
                            'accept': '*/*'
                        }
                    });

                    if (response.ok) {
                        const tbody = document.getElementById('contactsTableBody');
                        const rows = tbody.getElementsByTagName('tr');

                        for (let i = 0; i < rows.length; i++) {
                            if (rows[i].getAttribute('data-id') === id) {
                                tbody.removeChild(rows[i]);
                                break;
                            }
                        }
                        showToast('Contact deleted successfully!', 'success');
                    } else {
                        showToast('Failed to delete contact. Please try again.', 'error');
                    }
                } catch (error) {
                    console.error('Error deleting contact:', error);
                    showToast('Error deleting contact. Please try again.', 'error');
                }
            }
        }

        // Toast notification
        function showToast(message, type = 'info') {
            let toast = document.createElement('div');
            toast.className = 'toast ' + type;
            toast.innerHTML = `<span>${message}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => document.body.removeChild(toast), 400);
            }, 2200);
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('contactModal');
            if (event.target === modal) {
                closeContactModal();
            }
        }

        // Initialize Leaflet map
        document.addEventListener('DOMContentLoaded', function() {
            // Load contacts on page load
            loadContacts();

            // Initialize the map
            var map = L.map('map').setView([16.606254918019598, 121.18314743041994], 100);

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add emergency markers
            var emergencyIcon = L.divIcon({
                html: '🚨',
                iconSize: [40, 40],
                className: 'emergency-marker'
            });

            var ambulanceIcon = L.divIcon({
                html: '🚑',
                iconSize: [40, 40],
                className: 'ambulance-marker'
            });

            var floodIcon = L.divIcon({
                html: '🌊',
                iconSize: [40, 40],
                className: 'flood-marker'
            });

            // Add sample markers
            L.marker([14.5995, 120.9842], {icon: emergencyIcon})
                .addTo(map)
                .bindPopup('<b>🚨 Emergency Incident #001</b><br>Status: <span style="color:#dc2626;font-weight:600;">Active</span>');

            L.marker([14.6042, 120.9822], {icon: ambulanceIcon})
                .addTo(map)
                .bindPopup('<b>🚑 Ambulance Unit A-01</b><br>Status: <span style="color:#059669;font-weight:600;">Available</span>');

            L.marker([14.5955, 120.9862], {icon: floodIcon})
                .addTo(map)
                .bindPopup('<b>🌊 Flood Alert Area</b><br>Water Level: <span style="color:#0891b2;font-weight:600;">2.3m</span>');

            L.marker([14.5935, 120.9802], {icon: ambulanceIcon})
                .addTo(map)
                .bindPopup('<b>🚑 Ambulance Unit A-02</b><br>Status: <span style="color:#d97706;font-weight:600;">En Route</span>');

            // Handle map resize
            setTimeout(function() {
                map.invalidateSize();
            }, 100);
        });
    </script>
    <style>
        /* Toast styles */
        .toast {
            position: fixed;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%) scale(0.97);
            background: linear-gradient(90deg, #4f8cff 0%, #7c3aed 100%);
            color: #fff;
            padding: 20px 40px;
            border-radius: 16px;
            font-size: 1.15rem;
            font-weight: 700;
            box-shadow: 0 8px 32px rgba(76,130,255,0.22);
            opacity: 0;
            pointer-events: none;
            z-index: 9999;
            transition: opacity 0.3s, transform 0.3s;
        }
        .toast.show {
            opacity: 1;
            transform: translateX(-50%) scale(1.04);
            pointer-events: auto;
        }
        .toast.success {
            background: linear-gradient(90deg, #059669 0%, #4f8cff 100%);
        }
        .toast.error {
            background: linear-gradient(90deg, #dc2626 0%, #7c3aed 100%);
        }
    </style>
</div>

<?php }
?>
