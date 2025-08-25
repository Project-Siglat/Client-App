<script>
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';
	import { auth } from '$lib/stores/auth.js';
	import { ambulanceApi, userApi } from '$lib/api/client.js';

	let map;
	let userLocation = null;
	let ambulanceData = [];
	let currentAlertData = null;
	let currentAlertStatus = null;
	let isLocationLoading = true;
	let showEmergencyModal = false;
	let emergencyType = 'accident';
	let patientCount = 1;
	let countdown = 10;
	let countdownTimer = null;
	let showStatusMessage = false;
	let ambulanceMarkers = [];
	let userLocationMarker = null;
	let routingControl = null;

	$: buttonDisabled = currentAlertStatus && currentAlertStatus !== 'done';

	onMount(async () => {
		if (browser) {
			auth.initialize();
			
			// Check authentication
			const unsubscribe = auth.subscribe(authState => {
				if (!authState.loading && !authState.isAuthenticated) {
					window.location.href = '/login';
				}
			});

			// Initialize map
			await initializeMap();
			
			// Start location tracking
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(
					updateUserLocation,
					handleLocationError
				);

				navigator.geolocation.watchPosition(
					updateUserLocation,
					handleLocationError,
					{
						enableHighAccuracy: true,
						timeout: 10000,
						maximumAge: 1000
					}
				);
			}

			// Fetch ambulance data periodically
			fetchAmbulanceData();
			setInterval(fetchAmbulanceData, 5000);

			// Check current alert status
			checkCurrentAlert();
			setInterval(checkCurrentAlert, 3000);

			return unsubscribe;
		}
	});

	async function initializeMap() {
		if (!browser) return;
		
		const L = await import('leaflet');
		
		map = L.default.map('map').setView([16.3988, 121.0642], 10);
		
		L.default.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '© OpenStreetMap contributors'
		}).addTo(map);

		// Initialize custom icons
		const customIcon = L.default.icon({
			iconUrl: '/images/man.svg',
			iconSize: [32, 32],
			iconAnchor: [16, 32],
			popupAnchor: [0, -32]
		});

		const ambulanceIcon = L.default.icon({
			iconUrl: '/images/ambulance.png',
			iconSize: [32, 32],
			iconAnchor: [16, 32],
			popupAnchor: [0, -32]
		});

		// Store references globally for other functions
		window.L = L.default;
		window.customIcon = customIcon;
		window.ambulanceIcon = ambulanceIcon;
	}

	async function fetchAmbulanceData() {
		try {
			const data = await ambulanceApi.getAll();
			ambulanceData = data;

			// Clear existing markers
			ambulanceMarkers.forEach(marker => {
				if (map) map.removeLayer(marker);
			});
			ambulanceMarkers = [];

			// Add new markers
			if (map && window.ambulanceIcon && window.L) {
				ambulanceData.forEach(ambulance => {
					const lat = parseFloat(ambulance.latitude);
					const lng = parseFloat(ambulance.longitude);
					
					if (!isNaN(lat) && !isNaN(lng)) {
						const marker = window.L.marker([lat, lng], { icon: window.ambulanceIcon })
							.addTo(map)
							.bindPopup(`Ambulance Unit ${ambulance.id}`);
						
						ambulanceMarkers.push(marker);
					}
				});
			}
		} catch (error) {
			console.error('Error fetching ambulance data:', error);
		}
	}

	async function checkCurrentAlert() {
		try {
			const alertData = await ambulanceApi.getCurrentAlert();
			updateUIBasedOnAlert(alertData);
		} catch (error) {
			// No current alert or error
			updateUIBasedOnAlert(null);
		}
	}

	function updateUIBasedOnAlert(alertData) {
		if (alertData && alertData.status === 'done') {
			currentAlertStatus = 'done';
			currentAlertData = alertData;
			showStatusMessage = false;
		} else if (alertData) {
			currentAlertStatus = alertData.status;
			currentAlertData = alertData;
			showStatusMessage = true;
		} else {
			currentAlertStatus = null;
			currentAlertData = null;
			showStatusMessage = false;
		}
	}

	function updateUserLocation(position) {
		const userLat = position.coords.latitude;
		const userLng = position.coords.longitude;
		userLocation = [userLat, userLng];
		
		isLocationLoading = false;

		if (map && window.customIcon && window.L) {
			// Remove existing marker
			if (userLocationMarker) {
				map.removeLayer(userLocationMarker);
			}

			// Add new marker
			userLocationMarker = window.L.marker([userLat, userLng], { icon: window.customIcon })
				.addTo(map)
				.bindPopup('Your Location');

			map.setView([userLat, userLng], 13);
		}

		// Post coordinates to API
		postUserCoordinates(userLat, userLng);
	}

	function handleLocationError(error) {
		console.error('Location error:', error);
		isLocationLoading = false;
	}

	async function postUserCoordinates(latitude, longitude) {
		try {
			await userApi.updateCoordinates({
				id: "3fa85f64-5717-4562-b3fc-2c963f66afa6", // Replace with actual user ID
				latitude: latitude.toString(),
				longitude: longitude.toString()
			});
		} catch (error) {
			console.error('Error posting coordinates:', error);
		}
	}

	function handleEmergencyClick() {
		if (buttonDisabled) return;
		showEmergencyModal = true;
		startCountdown();
	}

	function startCountdown() {
		countdown = 10;
		countdownTimer = setInterval(() => {
			countdown--;
			if (countdown <= 0) {
				clearInterval(countdownTimer);
				confirmEmergency();
			}
		}, 1000);
	}

	async function confirmEmergency() {
		hideEmergencyModal();
		
		if (!userLocation) {
			alert('Location not available');
			return;
		}

		try {
			// Find nearest ambulance
			const nearestAmbulance = findNearestAmbulance();
			if (!nearestAmbulance) {
				alert('No ambulance available');
				return;
			}

			const alertData = {
				id: "3fa85f64-5717-4562-b3fc-2c963f66afa6",
				uid: "3fa85f64-5717-4562-b3fc-2c963f66afa6",
				responder: nearestAmbulance.id,
				what: emergencyType,
				status: "pending",
				respondedAt: new Date().toISOString(),
				longitude: userLocation[1].toString(),
				latitude: userLocation[0].toString()
			};

			await ambulanceApi.sendAlert(alertData);
			console.log('Emergency alert sent successfully');
		} catch (error) {
			console.error('Error sending emergency alert:', error);
			alert('Failed to send emergency alert');
		}
	}

	function findNearestAmbulance() {
		if (!userLocation || ambulanceData.length === 0) return null;

		let nearest = null;
		let shortestDistance = Infinity;

		ambulanceData.forEach(ambulance => {
			const distance = calculateDistance(
				userLocation[0],
				userLocation[1],
				parseFloat(ambulance.latitude),
				parseFloat(ambulance.longitude)
			);

			if (distance < shortestDistance) {
				shortestDistance = distance;
				nearest = ambulance;
			}
		});

		return nearest;
	}

	function calculateDistance(lat1, lon1, lat2, lon2) {
		const R = 6371; // Earth's radius in km
		const dLat = (lat2 - lat1) * Math.PI / 180;
		const dLon = (lon2 - lon1) * Math.PI / 180;
		const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
				Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
				Math.sin(dLon/2) * Math.sin(dLon/2);
		const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
		return R * c;
	}

	function hideEmergencyModal() {
		showEmergencyModal = false;
		if (countdownTimer) {
			clearInterval(countdownTimer);
		}
	}
</script>

<svelte:head>
	<title>Client Dashboard - Siglat Emergency System</title>
	<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
	<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
</svelte:head>

<div class="client-dashboard">
	<!-- Map Container -->
	<div id="map" class="map-container"></div>

	<!-- Loading Message -->
	{#if isLocationLoading}
		<div class="loading-overlay">
			<div class="loading-content">
				<div class="spinner"></div>
				<p>Your location is still fetching...</p>
			</div>
		</div>
	{/if}

	<!-- Status Message -->
	{#if showStatusMessage}
		<div class="status-message">
			Ambulance is on the way! Status: <strong>{currentAlertStatus}</strong>
		</div>
	{/if}

	<!-- Emergency Button -->
	<button 
		class="emergency-button" 
		class:disabled={buttonDisabled}
		on:click={handleEmergencyClick}
		disabled={buttonDisabled}
	>
		<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
			<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
		</svg>
	</button>

	<!-- Emergency Modal -->
	{#if showEmergencyModal}
		<div class="modal-overlay" on:click={hideEmergencyModal}>
			<div class="modal-content" on:click|stopPropagation>
				<h3>Emergency Request</h3>
				
				<div class="form-group">
					<label for="emergencyType">Emergency Type:</label>
					<select id="emergencyType" bind:value={emergencyType}>
						<option value="accident">Accident</option>
						<option value="fire">Fire</option>
						<option value="medical">Medical Emergency</option>
						<option value="other">Other</option>
					</select>
				</div>

				<div class="form-group">
					<label for="patientCount">Number of Patients:</label>
					<input type="number" id="patientCount" min="1" bind:value={patientCount} />
				</div>

				<div class="countdown">
					Auto-dispatch in: <span class="countdown-number">{countdown}s</span>
				</div>

				<div class="modal-actions">
					<button class="confirm-btn" on:click={confirmEmergency}>
						Confirm Now
					</button>
					<button class="cancel-btn" on:click={hideEmergencyModal}>
						Cancel
					</button>
				</div>
			</div>
		</div>
	{/if}
</div>

<style>
	.client-dashboard {
		position: relative;
		height: 100vh;
		width: 100%;
	}

	.map-container {
		height: calc(100vh - 72px);
		width: 100%;
	}

	.loading-overlay {
		position: fixed;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		background: rgba(0, 0, 0, 0.8);
		color: white;
		padding: 20px;
		border-radius: 10px;
		text-align: center;
		z-index: 1000;
		font-weight: bold;
	}

	.loading-content {
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 10px;
	}

	.spinner {
		width: 30px;
		height: 30px;
		border: 3px solid rgba(255, 255, 255, 0.3);
		border-top: 3px solid white;
		border-radius: 50%;
		animation: spin 1s linear infinite;
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}

	.status-message {
		position: fixed;
		top: 100px;
		left: 50%;
		transform: translateX(-50%);
		background: rgba(255, 165, 0, 0.9);
		color: white;
		padding: 15px 20px;
		border-radius: 10px;
		text-align: center;
		z-index: 1001;
		font-size: 14px;
		font-weight: bold;
	}

	.emergency-button {
		width: 60px;
		height: 60px;
		border-radius: 50%;
		background: #ef4444;
		border: none;
		position: fixed;
		bottom: 20px;
		right: 85px;
		cursor: pointer;
		display: flex;
		align-items: center;
		justify-content: center;
		color: #ffffff;
		box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
		transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
		z-index: 1000;
	}

	.emergency-button:hover:not(.disabled) {
		transform: scale(1.1);
		box-shadow: 0 4px 16px rgba(239, 68, 68, 0.4);
	}

	.emergency-button.disabled {
		opacity: 0.6;
		cursor: not-allowed;
	}

	.modal-overlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, 0.5);
		z-index: 10000;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.modal-content {
		background: white;
		padding: 30px;
		border-radius: 10px;
		max-width: 400px;
		width: 90%;
		text-align: center;
		box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
	}

	.modal-content h3 {
		margin-top: 0;
		color: #dc3545;
		margin-bottom: 20px;
	}

	.form-group {
		margin: 20px 0;
		text-align: left;
	}

	.form-group label {
		display: block;
		margin-bottom: 10px;
		font-weight: bold;
	}

	.form-group select,
	.form-group input {
		width: 100%;
		padding: 8px;
		border: 1px solid #ccc;
		border-radius: 4px;
		font-size: 14px;
		box-sizing: border-box;
	}

	.countdown {
		font-size: 24px;
		font-weight: bold;
		color: #dc3545;
		margin: 20px 0;
	}

	.countdown-number {
		color: #dc3545;
	}

	.modal-actions {
		display: flex;
		gap: 10px;
		justify-content: center;
		margin-top: 20px;
	}

	.confirm-btn {
		padding: 10px 20px;
		background-color: #dc3545;
		color: white;
		border: none;
		border-radius: 5px;
		cursor: pointer;
		font-size: 14px;
		font-weight: bold;
	}

	.cancel-btn {
		padding: 10px 20px;
		background-color: #6c757d;
		color: white;
		border: none;
		border-radius: 5px;
		cursor: pointer;
		font-size: 14px;
	}

	.confirm-btn:hover {
		background-color: #c82333;
	}

	.cancel-btn:hover {
		background-color: #5a6268;
	}

	:global(.leaflet-routing-alternatives-container),
	:global(.leaflet-routing-container) {
		display: none !important;
	}
</style>