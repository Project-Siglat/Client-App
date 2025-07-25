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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Montserrat:wght@700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div class="dashboard-container">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>

    <!-- Modern Glassmorphism Header -->
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
            <!-- Weather Card (Modal) -->
            <div class="panel-item weather-card">
                <div class="weather-header">
                    <span class="weather-icon animated-icon">🌀</span>
                    <h3 class="panel-title">Hourly Weather Forecast</h3>
                    <button class="btn btn-gradient btn-small weather-modal-btn" onclick="openWeatherModal()" title="Show Weather">
                        <i class="bi bi-cloud-sun"></i>
                    </button>
                </div>
            </div>

            <!-- Contact Management (Modal) -->
            <div class="panel-item contact-card">
                <div class="contact-header">
                    <span class="contact-icon animated-icon">📞</span>
                    <h3 class="panel-title">Contact Management</h3>
                    <button class="btn btn-gradient btn-small contact-modal-btn" onclick="openContactListModal()" title="Show Contacts">
                        <i class="bi bi-people"></i>
                    </button>
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

    <!-- Weather Modal -->
    <div id="weatherModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3><i class="bi bi-cloud-sun"></i> Hourly Weather Forecast</h3>
                <span class="close" onclick="closeWeatherModal()">&times;</span>
            </div>
            <div class="weather-iframe-container" style="height: 220px;">
                <iframe
                    src="https://www.accuweather.com/en/ph/villaverde/265132/hourly-weather-forecast/265132"
                    class="weather-iframe"
                    frameborder="0"
                    allowfullscreen
                    style="height:200px;">
                </iframe>
            </div>
        </div>
    </div>

    <!-- Contact List Modal -->
    <div id="contactListModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="bi bi-people"></i> Contact Management</h3>
                <span class="close" onclick="closeContactListModal()">&times;</span>
            </div>
            <div class="contact-crud-controls" id="contactCrudControls">
                <button class="btn btn-gradient btn-small" onclick="openContactModal()">
                    <i class="bi bi-plus-circle"></i> Add Contact
                </button>
            </div>
            <div class="contacts-container" id="contactsContainer">
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
            font-family: 'Roboto', 'Inter', 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            min-height: 100vh;
            height: 100vh;
            overflow: hidden;
        }

        .ultra-glass {
            background: rgba(30, 30, 40, 0.85);
            border-radius: 24px;
            box-shadow: 0 12px 48px rgba(0,0,0,0.18), 0 2px 8px rgba(220,38,38,0.10);
            backdrop-filter: blur(18px) saturate(200%);
            border: 2px solid #222;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 32px;
            margin-bottom: 18px;
            background: linear-gradient(90deg, rgba(40,40,50,0.95) 0%, rgba(60,60,70,0.95) 100%);
        }

        .dashboard-title {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .dashboard-logo {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.18);
            background: #222;
            border: 2px solid #dc2626;
            transition: transform 0.2s;
        }
        .dashboard-logo:hover {
            transform: scale(1.12) rotate(-8deg);
        }

        .dashboard-header h1 {
            color: #fff;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 1.5px;
            text-shadow: 0 2px 12px #dc2626;
        }

        .gradient-text {
            background: linear-gradient(90deg, #dc2626 0%, #facc15 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .dashboard-actions {
            display: flex;
            gap: 16px;
        }

        .btn-gradient {
            background: linear-gradient(90deg, #dc2626 0%, #facc15 100%);
            color: #111;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(255,0,0,0.10);
            transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-gradient:hover, .btn-gradient:focus {
            transform: translateY(-2px) scale(1.08);
            box-shadow: 0 12px 32px rgba(255,0,0,0.18);
            background: linear-gradient(90deg, #facc15 0%, #dc2626 100%);
            color: #fff;
        }
        .btn-action {
            padding: 12px 14px;
            font-size: 1.5rem;
            border-radius: 50%;
            min-width: 48px;
            min-height: 48px;
            justify-content: center;
            box-shadow: 0 2px 12px rgba(255,0,0,0.10);
        }

        .main-content {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 24px;
            max-width: 100vw;
            margin: 0 auto;
            height: calc(100vh - 100px);
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .sidebar::-webkit-scrollbar {
            width: 8px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #222;
            border-radius: 6px;
        }

        .panel-item {
            background: linear-gradient(135deg, #232526 80%, #414345 100%);
            padding: 18px 16px;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(255,0,0,0.10);
            border: 1px solid #222;
            position: relative;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .panel-item:hover {
            box-shadow: 0 16px 32px #dc2626;
            transform: translateY(-2px) scale(1.04);
        }

        .panel-title {
            font-family: 'Montserrat', 'Inter', 'Roboto', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: #facc15;
            margin-bottom: 12px;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .weather-card .weather-header,
        .contact-card .contact-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        .weather-modal-btn,
        .contact-modal-btn {
            margin-left: auto;
            padding: 8px 16px;
            font-size: 1.2rem;
            border-radius: 10px;
            background: linear-gradient(90deg, #dc2626 0%, #facc15 100%);
            color: #111;
            border: none;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .weather-modal-btn:hover,
        .contact-modal-btn:hover {
            background: linear-gradient(90deg, #facc15 0%, #dc2626 100%);
            color: #fff;
        }
        .weather-icon, .contact-icon, .animated-icon {
            font-size: 1.2rem;
            background: linear-gradient(135deg, #dc2626 0%, #facc15 100%);
            color: #111;
            border-radius: 50%;
            padding: 4px;
            box-shadow: 0 2px 8px #dc2626;
            animation: bounce 1.8s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0);}
            50% { transform: translateY(-4px);}
        }

        .weather-iframe-container {
            height: 120px;
            border-radius: 6px;
            overflow: hidden;
            background: #222;
            box-shadow: 0 2px 8px #dc2626;
        }

        .weather-iframe {
            width: 100%;
            height: 100px;
            border: none;
            border-radius: 6px;
            background: #222;
        }

        .contact-crud-controls {
            margin-bottom: 8px;
            display: flex;
            justify-content: flex-end;
        }

        .btn-small {
            padding: 4px 8px;
            font-size: 0.9rem;
            font-weight: 700;
            border-radius: 6px;
        }

        .contacts-container {
            height: 80px;
            overflow-y: auto;
            border: 1px solid #dc2626;
            border-radius: 6px;
            background: #222;
            box-shadow: 0 2px 8px #dc2626;
        }
        .contacts-container::-webkit-scrollbar {
            width: 4px;
        }
        .contacts-container::-webkit-scrollbar-thumb {
            background: #dc2626;
            border-radius: 2px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .contacts-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        .contacts-table th {
            padding: 4px 6px;
            background: linear-gradient(135deg, #222, #181818);
            color: #facc15;
            font-weight: 700;
            text-align: left;
            border-bottom: 1px solid #dc2626;
            font-size: 0.9rem;
        }
        .contacts-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #222;
            color: #fff;
            font-size: 0.9rem;
        }
        .contacts-table th:last-child,
        .contacts-table td:last-child {
            width: 60px;
            text-align: center;
        }

        .action-buttons {
            display: flex;
            gap: 4px;
            justify-content: center;
        }

        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            padding: 2px;
            border-radius: 4px;
            font-size: 1rem;
            transition: all 0.2s ease;
            color: #facc15;
        }
        .btn-edit:hover {
            background: #facc15;
            color: #dc2626;
            transform: scale(1.12);
        }
        .btn-delete:hover {
            background: #dc2626;
            color: #fff;
            transform: scale(1.12);
        }

        /* Map Section */
        .main-area {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .map-section {
            background: #181818;
            border-radius: 8px;
            padding: 8px 6px;
            box-shadow: 0 2px 8px #dc2626;
            height: 100%;
            min-height: 0;
            border: 1px solid #dc2626;
            backdrop-filter: blur(6px);
            position: relative;
        }
        .map-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .map-section h2 {
            margin: 0;
            color: #facc15;
            font-size: 1rem;
            font-weight: 700;
        }
        .map-legend {
            display: flex;
            gap: 8px;
            background: #222;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 0.8rem;
            color: #facc15;
            font-weight: 600;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 2px;
        }
        .legend-icon {
            font-size: 1rem;
        }
        .map-container {
            height: calc(100% - 30px);
            width: 100%;
            border-radius: 6px;
            overflow: hidden;
            background: #222;
            border: none;
            box-shadow: 0 2px 8px #dc2626;
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
            gap: 4px;
        }
        .loader-spinner {
            width: 20px;
            height: 20px;
            border: 3px solid #222;
            border-top: 3px solid #dc2626;
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
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(6px);
        }
        .modal-content {
            background: linear-gradient(135deg, #181818 80%, #222 100%);
            margin: 10% auto;
            padding: 0;
            border-radius: 8px;
            width: 99%;
            max-width: 320px;
            box-shadow: 0 8px 32px #dc2626;
            border: 1px solid #dc2626;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            border-bottom: 1px solid #dc2626;
        }
        .modal-header h3 {
            margin: 0;
            color: #facc15;
            font-size: 1rem;
            font-weight: 700;
        }
        .close {
            color: #dc2626;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
            transition: color 0.2s ease;
        }
        .close:hover {
            color: #facc15;
        }
        .form-group {
            margin-bottom: 8px;
            padding: 0 12px;
        }
        .form-group:first-of-type {
            margin-top: 8px;
        }
        .form-group label {
            display: block;
            margin-bottom: 4px;
            font-weight: 700;
            color: #facc15;
            font-size: 0.9rem;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 6px 8px;
            border: 1px solid #dc2626;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: border-color 0.2s;
            background: #222;
            color: #fff;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #facc15;
            box-shadow: 0 0 0 2px #facc15;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 6px;
            padding: 8px 12px;
            border-top: 1px solid #dc2626;
        }
        .btn-secondary {
            background: #222;
            color: #facc15;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .btn-secondary:hover {
            background: #facc15;
            color: #dc2626;
        }

        /* Responsive Design */
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
                height: 120px;
            }
            .panel-item {
                padding: 6px;
            }
        }
        @media (max-width: 700px) {
            .dashboard-header h1 {
                font-size: 1rem;
            }
            .dashboard-logo {
                width: 20px;
                height: 20px;
            }
            .main-content {
                gap: 4px;
            }
            .map-container {
                height: 80px;
            }
            .contacts-container {
                height: 40px;
            }
            .contacts-table th,
            .contacts-table td {
                padding: 2px 2px;
            }
            .modal-content {
                margin: 2% auto;
                width: 99%;
            }
        }
        @media (max-width: 500px) {
            .dashboard-header {
                padding: 4px 2px;
            }
            .dashboard-header h1 {
                font-size: 0.8rem;
            }
            .dashboard-logo {
                width: 12px;
                height: 12px;
            }
            .main-content {
                gap: 2px;
            }
            .map-container {
                height: 40px;
            }
            .contacts-container {
                height: 20px;
            }
            .contacts-table th,
            .contacts-table td {
                padding: 1px 1px;
            }
            .modal-content {
                margin: 1% auto;
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

        // Modal open/close logic for weather
        function openWeatherModal() {
            document.getElementById('weatherModal').style.display = 'block';
        }
        function closeWeatherModal() {
            document.getElementById('weatherModal').style.display = 'none';
        }

        // Modal open/close logic for contact list
        function openContactListModal() {
            document.getElementById('contactListModal').style.display = 'block';
        }
        function closeContactListModal() {
            document.getElementById('contactListModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const weatherModal = document.getElementById('weatherModal');
            const contactListModal = document.getElementById('contactListModal');
            const contactModal = document.getElementById('contactModal');
            if (event.target === weatherModal) {
                closeWeatherModal();
            }
            if (event.target === contactListModal) {
                closeContactListModal();
            }
            if (event.target === contactModal) {
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
                iconSize: [24, 24],
                className: 'emergency-marker'
            });

            var ambulanceIcon = L.divIcon({
                html: '🚑',
                iconSize: [24, 24],
                className: 'ambulance-marker'
            });

            var floodIcon = L.divIcon({
                html: '🌊',
                iconSize: [24, 24],
                className: 'flood-marker'
            });

            // Add sample markers
            L.marker([14.5995, 120.9842], {icon: emergencyIcon})
                .addTo(map)
                .bindPopup('<b>🚨 Emergency Incident #001</b><br>Status: <span style="color:#dc2626;font-weight:600;">Active</span>');

            L.marker([14.6042, 120.9822], {icon: ambulanceIcon})
                .addTo(map)
                .bindPopup('<b>🚑 Ambulance Unit A-01</b><br>Status: <span style="color:#facc15;font-weight:600;">Available</span>');

            L.marker([14.5955, 120.9862], {icon: floodIcon})
                .addTo(map)
                .bindPopup('<b>🌊 Flood Alert Area</b><br>Water Level: <span style="color:#facc15;font-weight:600;">2.3m</span>');

            L.marker([14.5935, 120.9802], {icon: ambulanceIcon})
                .addTo(map)
                .bindPopup('<b>🚑 Ambulance Unit A-02</b><br>Status: <span style="color:#dc2626;font-weight:600;">En Route</span>');

            // No dropdowns to collapse by default anymore
        });
    </script>
    <style>
        /* Toast styles */
        .toast {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%) scale(0.97);
            background: linear-gradient(90deg, #dc2626 0%, #facc15 100%);
            color: #111;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 700;
            box-shadow: 0 4px 16px #dc2626;
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
            background: linear-gradient(90deg, #facc15 0%, #dc2626 100%);
            color: #111;
        }
        .toast.error {
            background: linear-gradient(90deg, #dc2626 0%, #facc15 100%);
            color: #fff;
        }
    </style>
</div>

<?php }
?>
