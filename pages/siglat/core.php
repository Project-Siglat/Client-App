<div class="dashboard-container">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>

    <h1>Emergency Management Dashboard</h1>

    <div class="main-content">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Ambulance Management -->
            <div class="panel-item">
                <h3>🚑 Ambulance Management</h3>
                <div class="button-group">
                    <button class="btn btn-primary">
                        Add Ambulance
                    </button>
                    <button class="btn btn-danger">
                        Remove Ambulance
                    </button>
                </div>
            </div>

            <!-- Weather Forecast -->
            <div class="panel-item">
                <h3>🌤️ Weather Forecast</h3>
                <div class="weather-forecast">
                    <div class="weather-today">
                        <div class="weather-icon">☀️</div>
                        <div class="weather-temp">24°C</div>
                        <div class="weather-condition">Sunny</div>
                    </div>
                    <div class="weather-details">
                        <div class="weather-detail">
                            <span class="detail-label">Humidity:</span>
                            <span class="detail-value">65%</span>
                        </div>
                        <div class="weather-detail">
                            <span class="detail-label">Wind:</span>
                            <span class="detail-value">12 km/h</span>
                        </div>
                        <div class="weather-detail">
                            <span class="detail-label">Pressure:</span>
                            <span class="detail-value">1013 hPa</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Grid -->
            <div class="panel-item">
                <h3>🌊 Flood Statistics</h3>
                <div class="stats-grid compact">
                    <div class="stat-box">
                        <span class="stat-value flood-stats">3</span>
                        <label>Active Alerts</label>
                    </div>
                    <div class="stat-box">
                        <span class="stat-value flood-stats">7</span>
                        <label>Areas Affected</label>
                    </div>
                    <div class="stat-box">
                        <span class="stat-value flood-stats">2.3m</span>
                        <label>Water Level</label>
                    </div>
                    <div class="stat-box">
                        <span class="stat-value risk-high">High</span>
                        <label>Risk Level</label>
                    </div>
                </div>
            </div>

            <div class="panel-item">
                <h3>🌀 Typhoon Statistics</h3>
                <div class="stats-grid compact">
                    <div class="stat-box">
                        <span class="stat-value typhoon-stats">1</span>
                        <label>Active Alerts</label>
                    </div>
                    <div class="stat-box">
                        <span class="stat-value typhoon-stats">185 km/h</span>
                        <label>Wind Speed</label>
                    </div>
                    <div class="stat-box">
                        <span class="stat-value typhoon-stats">5</span>
                        <label>Provinces</label>
                    </div>
                    <div class="stat-box">
                        <span class="stat-value risk-critical">Critical</span>
                        <label>Severity</label>
                    </div>
                </div>
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

            <!-- Bottom Content -->
            <div class="bottom-content">
                <!-- User Accounts -->
                <div class="panel-item">
                    <h3>👥 User Accounts</h3>
                    <div class="user-list">
                        <div class="table-responsive">
                            <table class="user-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>001</td>
                                        <td>admin</td>
                                        <td>Administrator</td>
                                        <td><span class="status-active">Active</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Logs -->
                <div class="panel-item">
                    <h3>📋 System Logs</h3>
                    <div class="logs-container">
                        <div class="log-entry">10:00:00 - User login: admin</div>
                        <div class="log-entry">09:45:00 - Ambulance dispatched to location</div>
                        <div class="log-entry">09:30:00 - Flood alert activated</div>
                        <div class="log-entry">09:15:00 - Typhoon warning issued</div>
                    </div>
                </div>
            </div>
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
            padding: 16px;
            background: #f8fafc;
            min-height: 100vh;
            height: 100vh;
            overflow: hidden;
        }

        .dashboard-container h1 {
            color: #1f2937;
            text-align: center;
            margin-bottom: 24px;
            font-size: 1.875rem;
            font-weight: 600;
            margin-top: 0;
        }

        .main-content {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 16px;
            max-width: 1400px;
            margin: 0 auto;
            height: calc(100vh - 120px);
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .main-area {
            display: flex;
            flex-direction: column;
            gap: 16px;
            overflow: hidden;
        }

        .map-section {
            background: white;
            border-radius: 8px;
            padding: 16px;
            border: 1px solid #e5e7eb;
            flex: 1;
            min-height: 0;
        }

        .map-section h2 {
            margin-top: 0;
            color: #374151;
            font-size: 1.25rem;
            margin-bottom: 16px;
            font-weight: 500;
        }

        .map-container {
            height: 100%;
            width: 100%;
            border-radius: 6px;
            overflow: hidden;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
        }

        .bottom-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            flex-shrink: 0;
        }

        .panel-item {
            background: white;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .panel-item h3 {
            margin-top: 0;
            margin-bottom: 12px;
            color: #374151;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .button-group {
            display: flex;
            gap: 8px;
            flex-direction: column;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.875rem;
            transition: background-color 0.2s;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .weather-forecast {
            text-align: center;
        }

        .weather-today {
            margin-bottom: 12px;
        }

        .weather-icon {
            font-size: 2rem;
            margin-bottom: 4px;
        }

        .weather-temp {
            font-size: 1.5rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 2px;
        }

        .weather-condition {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .weather-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .weather-detail {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            padding: 2px 0;
        }

        .detail-label {
            color: #6b7280;
        }

        .detail-value {
            color: #374151;
            font-weight: 500;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .user-table th {
            padding: 8px 12px;
            background: #f9fafb;
            color: #374151;
            font-weight: 500;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .user-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .status-active {
            background: #10b981;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
        }

        .logs-container {
            height: 120px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            background: #f9fafb;
        }

        .log-entry {
            margin-bottom: 6px;
            font-size: 0.875rem;
            color: #6b7280;
            padding: 6px;
            background: white;
            border-radius: 4px;
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

            .bottom-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 12px;
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

            .logs-container {
                height: 100px;
            }

            .user-table th,
            .user-table td {
                padding: 6px 8px;
            }
        }
    </style>

    <script>
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
