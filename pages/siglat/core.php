<?php
include "./pages/siglat/topbar.php"; ?>

<div class="dashboard-container">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>


    <div class="main-content">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="panel-item weather-iframe-container">
                <h3>🌀 Hourly Weather Forecast</h3>
                <iframe
                    src="https://www.accuweather.com/en/ph/villaverde/265132/hourly-weather-forecast/265132"
                    class="weather-iframe"
                    frameborder="0"
                    allowfullscreen>
                </iframe>
            </div>

            <div class="panel-item">
                <h3>📊 Incident Statistics</h3>
                <div class="stats-grid compact">
                    <div class="stat-box">
                        <span class="stat-value incident-stats">12</span>
                        <label>Total Today</label>
                    </div>
                    <div class="stat-box">
                        <span class="stat-value success">8</span>
                        <label>Resolved</label>
                    </div>
                    <div class="stat-box">
                        <span class="stat-value warning">4</span>
                        <label>Pending</label>
                    </div>
                    <div class="stat-box">
                        <span class="stat-value danger">2</span>
                        <label>Critical</label>
                    </div>
                </div>
            </div>

            <!-- Contact Management -->
            <div class="panel-item">
                <h3>📞 Contact Management</h3>
                <div class="contact-crud-controls">
                    <button class="btn btn-primary btn-small" onclick="openContactModal()">
                        Add Contact
                    </button>
                </div>
                <div class="contacts-container">
                    <div class="table-responsive">
                        <table class="contacts-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Label</th>
                                    <th>Type</th>
                                    <th>Information</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="contactsTableBody">
                                <tr>
                                    <td>001</td>
                                    <td>Fire Department</td>
                                    <td>phone</td>
                                    <td>911</td>
                                    <td class="action-buttons">
                                        <button class="btn-icon btn-edit" onclick="editContact('001', 'Fire Department', 'phone', '911')">✏️</button>
                                        <button class="btn-icon btn-delete" onclick="deleteContact('001')">🗑️</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>002</td>
                                    <td>Police Station</td>
                                    <td>phone</td>
                                    <td>117</td>
                                    <td class="action-buttons">
                                        <button class="btn-icon btn-edit" onclick="editContact('002', 'Police Station', 'phone', '117')">✏️</button>
                                        <button class="btn-icon btn-delete" onclick="deleteContact('002')">🗑️</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>003</td>
                                    <td>Medical Emergency</td>
                                    <td>phone</td>
                                    <td>911</td>
                                    <td class="action-buttons">
                                        <button class="btn-icon btn-edit" onclick="editContact('003', 'Medical Emergency', 'phone', '911')">✏️</button>
                                        <button class="btn-icon btn-delete" onclick="deleteContact('003')">🗑️</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>004</td>
                                    <td>Disaster Response</td>
                                    <td>phone</td>
                                    <td>(02) 8911-1406</td>
                                    <td class="action-buttons">
                                        <button class="btn-icon btn-edit" onclick="editContact('004', 'Disaster Response', 'phone', '(02) 8911-1406')">✏️</button>
                                        <button class="btn-icon btn-delete" onclick="deleteContact('004')">🗑️</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>005</td>
                                    <td>Red Cross</td>
                                    <td>email</td>
                                    <td>contact@redcross.ph</td>
                                    <td class="action-buttons">
                                        <button class="btn-icon btn-edit" onclick="editContact('005', 'Red Cross', 'email', 'contact@redcross.ph')">✏️</button>
                                        <button class="btn-icon btn-delete" onclick="deleteContact('005')">🗑️</button>
                                    </td>
                                </tr>
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
                <h2>Map View</h2>
                <div id="map" class="map-container">
                    <!-- Leaflet map will be initialized here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add Contact</h3>
                <span class="close" onclick="closeContactModal()">&times;</span>
            </div>
            <form id="contactForm" onsubmit="saveContact(event)">
                <div class="form-group">
                    <label for="contactId">ID:</label>
                    <input type="text" id="contactId" name="contactId" required>
                </div>
                <div class="form-group">
                    <label for="contactLabel">Contact Label:</label>
                    <input type="text" id="contactLabel" name="contactLabel" required>
                </div>
                <div class="form-group">
                    <label for="contactType">Contact Type:</label>
                    <select id="contactType" name="contactType" required>
                        <option value="">Select Contact Type</option>
                        <option value="phone">Phone</option>
                        <option value="email">Email</option>
                        <option value="facebook">Facebook</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="contactInformation">Contact Information:</label>
                    <input type="text" id="contactInformation" name="contactInformation" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeContactModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

    <style>
        * {
            box-sizing: border-box;
        }

        .dashboard-container {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            padding-top: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            height: 100vh;
            overflow: hidden;
        }

        .dashboard-container h1 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.25rem;
            font-weight: 700;
            margin-top: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .main-content {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 20px;
            max-width: 1600px;
            margin: 0 auto;
            height: calc(100vh - 190px);
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 16px;
            overflow-y: auto;
            padding-right: 12px;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }

        .main-area {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .map-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: none;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            height: 100%;
            min-height: 0;
        }

        .map-section h2 {
            margin-top: 0;
            color: #374151;
            font-size: 1.5rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .map-container {
            height: calc(100% - 60px);
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            background: #f3f4f6;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .panel-item {
            background: white;
            padding: 16px;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }

        .panel-item h3 {
            margin-top: 0;
            margin-bottom: 16px;
            color: #374151;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .weather-iframe-container {
            height: 400px;
        }

        .weather-iframe {
            width: 100%;
            height: 350px;
            border: none;
            border-radius: 8px;
            background: #f9fafb;
        }

        .button-group {
            display: flex;
            gap: 10px;
            flex-direction: column;
        }

        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(107, 114, 128, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(239, 68, 68, 0.4);
        }

        .btn-small {
            padding: 8px 14px;
            font-size: 0.8rem;
        }

        .weather-forecast {
            text-align: center;
        }

        .weather-today {
            margin-bottom: 16px;
        }

        .weather-icon {
            font-size: 2.5rem;
            margin-bottom: 8px;
        }

        .weather-temp {
            font-size: 1.75rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 4px;
        }

        .weather-condition {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .weather-details {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .weather-detail {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            padding: 4px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .detail-label {
            color: #6b7280;
            font-weight: 500;
        }

        .detail-value {
            color: #374151;
            font-weight: 600;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .contacts-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .contacts-table th {
            padding: 12px 16px;
            background: linear-gradient(135deg, #f9fafb, #f3f4f6);
            color: #374151;
            font-weight: 600;
            text-align: left;
            border-bottom: 2px solid #e5e7eb;
        }

        .contacts-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .contacts-table th:last-child,
        .contacts-table td:last-child {
            width: 100px;
            text-align: center;
        }

        .contact-crud-controls {
            margin-bottom: 16px;
        }

        .contacts-container {
            height: 180px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #f9fafb;
        }

        .contacts-container::-webkit-scrollbar {
            width: 6px;
        }

        .contacts-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .contacts-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .action-buttons {
            display: flex;
            gap: 6px;
            justify-content: center;
        }

        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .btn-edit:hover {
            background: #e0f2fe;
            transform: scale(1.1);
        }

        .btn-delete:hover {
            background: #fee2e2;
            transform: scale(1.1);
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
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background-color: white;
            margin: 8% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-header h3 {
            margin: 0;
            color: #374151;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .close {
            color: #6b7280;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
            transition: color 0.2s ease;
        }

        .close:hover {
            color: #374151;
        }

        .form-group {
            margin-bottom: 20px;
            padding: 0 20px;
        }

        .form-group:first-of-type {
            margin-top: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #374151;
            font-size: 0.875rem;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 16px 20px;
            border-top: 1px solid #e5e7eb;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .stats-grid.compact {
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }

        .stat-box {
            padding: 8px;
            background: #f9fafb;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #f3f4f6;
        }

        .stat-box label {
            display: block;
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 4px;
            font-weight: 500;
        }

        .stat-value {
            font-weight: 600;
            display: block;
            font-size: 1rem;
        }

        .flood-stats { color: #0891b2; }
        .typhoon-stats { color: #7c3aed; }
        .incident-stats { color: #3b82f6; }

        .stat-value.success { color: #059669; }
        .stat-value.warning { color: #d97706; }
        .stat-value.danger { color: #dc2626; }
        .stat-value.risk-high { color: #ea580c; }
        .stat-value.risk-critical { color: #dc2626; }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 250px 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 12px;
                padding-top: 70px;
                height: auto;
                overflow: visible;
            }

            .dashboard-container h1 {
                font-size: 1.5rem;
            }

            .main-content {
                grid-template-columns: 1fr;
                height: auto;
            }

            .sidebar {
                order: 2;
                overflow-y: visible;
            }

            .main-area {
                order: 1;
            }

            .map-container {
                height: 300px;
            }

            .panel-item {
                padding: 12px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .dashboard-container h1 {
                font-size: 1.25rem;
            }

            .map-container {
                height: 250px;
            }

            .contacts-container {
                height: 120px;
            }

            .contacts-table th,
            .contacts-table td {
                padding: 6px 8px;
            }

            .modal-content {
                margin: 5% auto;
                width: 95%;
            }
        }
    </style>

    <script>
        // Contact CRUD operations
        let isEditMode = false;
        let editingContactId = null;
        let contactIdCounter = 6; // Start from 6 since we have 5 sample contacts

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
                document.getElementById('contactId').value = String(contactIdCounter).padStart(3, '0');
                document.getElementById('contactId').disabled = false;
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

        function saveContact(event) {
            event.preventDefault();

            const id = document.getElementById('contactId').value;
            const label = document.getElementById('contactLabel').value;
            const type = document.getElementById('contactType').value;
            const information = document.getElementById('contactInformation').value;

            if (isEditMode) {
                // Update existing contact
                updateContactInTable(id, label, type, information);
            } else {
                // Add new contact
                addContactToTable(id, label, type, information);
                contactIdCounter++;
            }

            closeContactModal();
        }

        function addContactToTable(id, label, type, information) {
            const tbody = document.getElementById('contactsTableBody');
            const row = document.createElement('tr');

            row.innerHTML = `
                <td>${id}</td>
                <td>${label}</td>
                <td>${type}</td>
                <td>${information}</td>
                <td class="action-buttons">
                    <button class="btn-icon btn-edit" onclick="editContact('${id}', '${label}', '${type}', '${information}')">✏️</button>
                    <button class="btn-icon btn-delete" onclick="deleteContact('${id}')">🗑️</button>
                </td>
            `;

            tbody.appendChild(row);
        }

        function updateContactInTable(id, label, type, information) {
            const tbody = document.getElementById('contactsTableBody');
            const rows = tbody.getElementsByTagName('tr');

            for (let row of rows) {
                if (row.cells[0].textContent === id) {
                    row.cells[1].textContent = label;
                    row.cells[2].textContent = type;
                    row.cells[3].textContent = information;
                    row.cells[4].innerHTML = `
                        <button class="btn-icon btn-edit" onclick="editContact('${id}', '${label}', '${type}', '${information}')">✏️</button>
                        <button class="btn-icon btn-delete" onclick="deleteContact('${id}')">🗑️</button>
                    `;
                    break;
                }
            }
        }

        function editContact(id, label, type, information) {
            openContactModal(id, label, type, information);
        }

        function deleteContact(id) {
            if (confirm('Are you sure you want to delete this contact?')) {
                const tbody = document.getElementById('contactsTableBody');
                const rows = tbody.getElementsByTagName('tr');

                for (let i = 0; i < rows.length; i++) {
                    if (rows[i].cells[0].textContent === id) {
                        tbody.removeChild(rows[i]);
                        break;
                    }
                }
            }
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
            // Initialize the map
            var map = L.map('map').setView([14.5995, 120.9842], 100); // Manila, Philippines coordinates

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add emergency markers
            var emergencyIcon = L.divIcon({
                html: '🚨',
                iconSize: [30, 30],
                className: 'emergency-marker'
            });

            var ambulanceIcon = L.divIcon({
                html: '🚑',
                iconSize: [30, 30],
                className: 'ambulance-marker'
            });

            var floodIcon = L.divIcon({
                html: '🌊',
                iconSize: [30, 30],
                className: 'flood-marker'
            });

            // Add sample markers
            L.marker([14.5995, 120.9842], {icon: emergencyIcon})
                .addTo(map)
                .bindPopup('Emergency Incident #001<br>Status: Active');

            L.marker([14.6042, 120.9822], {icon: ambulanceIcon})
                .addTo(map)
                .bindPopup('Ambulance Unit A-01<br>Status: Available');

            L.marker([14.5955, 120.9862], {icon: floodIcon})
                .addTo(map)
                .bindPopup('Flood Alert Area<br>Water Level: 2.3m');

            L.marker([14.5935, 120.9802], {icon: ambulanceIcon})
                .addTo(map)
                .bindPopup('Ambulance Unit A-02<br>Status: En Route');

            // Handle map resize
            setTimeout(function() {
                map.invalidateSize();
            }, 100);
        });
    </script>
</div>
