<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import 'ol/ol.css';
	import Map from 'ol/Map';
	import View from 'ol/View';
	import TileLayer from 'ol/layer/Tile';
	import OSM from 'ol/source/OSM';
	import { fromLonLat, toLonLat } from 'ol/proj';
	import Feature from 'ol/Feature';
	import Point from 'ol/geom/Point';
	import VectorLayer from 'ol/layer/Vector';
	import VectorSource from 'ol/source/Vector';
	import { Style, Icon, Circle, Fill, Stroke } from 'ol/style';
	import LineString from 'ol/geom/LineString';

	let userInfo: any = null;
	let userDetails: any = null;
	let isLoading = true;
	let map: Map;
	let mapElement: HTMLElement;
	let userLocation: [number, number] | null = null;
	let showUserSidebar = false;

	// Emergency stations data
	const emergencyStations = [
		{ id: 1, name: "Central Police Station", type: "police", coords: [120.9842, 14.5995], phone: "(02) 123-4567" },
		{ id: 2, name: "City Fire Department", type: "fire", coords: [120.9790, 14.6010], phone: "(02) 234-5678" },
		{ id: 3, name: "General Hospital", type: "medical", coords: [120.9820, 14.5980], phone: "(02) 345-6789" },
		{ id: 4, name: "Emergency Response Unit", type: "emergency", coords: [120.9800, 14.5970], phone: "(02) 456-7890" },
		{ id: 5, name: "Police Substation North", type: "police", coords: [120.9860, 14.6020], phone: "(02) 567-8901" },
		{ id: 6, name: "Fire Station South", type: "fire", coords: [120.9780, 14.5950], phone: "(02) 678-9012" }
	];

	onMount(() => {
		const token = sessionStorage.getItem('token');
		
		if (!token) {
			goto('/login');
			return;
		}

		try {
			const payload = JSON.parse(atob(token.split('.')[1]));
			const expiry = payload.exp * 1000;
			
			if (Date.now() >= expiry) {
				sessionStorage.removeItem('token');
				goto('/login');
				return;
			}

			if (payload.role !== 'User' && payload.role !== 'Admin') {
				goto('/login');
				return;
			}

			userInfo = payload;
			isLoading = false;
			
			// Initialize map after user is authenticated
			setTimeout(initializeMap, 100);
			getCurrentLocation();
			fetchUserDetails();
		} catch (error) {
			console.error('Invalid token:', error);
			sessionStorage.removeItem('token');
			goto('/login');
		}
	});

	async function fetchUserDetails() {
		const token = sessionStorage.getItem('token');
		if (!token || !userInfo?.nameid) return;

		try {
			const response = await fetch(`http://localhost:5000/api/v1.0/Identity/${userInfo.nameid}`, {
				headers: {
					'Authorization': `Bearer ${token}`,
					'Content-Type': 'application/json'
				}
			});

			if (response.ok) {
				userDetails = await response.json();
			} else {
				console.warn('Failed to fetch user details:', response.status);
			}
		} catch (error) {
			console.error('Error fetching user details:', error);
		}
	}

	function initializeMap() {
		if (!mapElement) return;

		// Default to Manila coordinates
		const defaultCenter = fromLonLat([120.9842, 14.5995]);

		map = new Map({
			target: mapElement,
			layers: [
				new TileLayer({
					source: new OSM()
				})
			],
			view: new View({
				center: defaultCenter,
				zoom: 13
			}),
			controls: []
		});

		// Add emergency stations to map
		addEmergencyStations();
	}

	function getCurrentLocation() {
		if ('geolocation' in navigator) {
			navigator.geolocation.getCurrentPosition(
				(position) => {
					userLocation = [position.coords.longitude, position.coords.latitude];
					if (map) {
						map.getView().setCenter(fromLonLat(userLocation));
						addUserLocationMarker();
					}
				},
				(error) => {
					console.warn('Geolocation error:', error);
				}
			);
		}
	}

	function addUserLocationMarker() {
		if (!userLocation || !map) return;

		const userFeature = new Feature({
			geometry: new Point(fromLonLat(userLocation)),
			type: 'user'
		});

		userFeature.setStyle(new Style({
			image: new Circle({
				radius: 8,
				fill: new Fill({ color: '#3b82f6' }),
				stroke: new Stroke({ color: '#ffffff', width: 3 })
			})
		}));

		const userSource = new VectorSource({
			features: [userFeature]
		});

		const userLayer = new VectorLayer({
			source: userSource
		});

		map.addLayer(userLayer);
	}

	function addEmergencyStations() {
		if (!map) return;

		const features = emergencyStations.map(station => {
			const feature = new Feature({
				geometry: new Point(fromLonLat(station.coords)),
				station: station
			});

			let iconColor = '#dc2626'; // red default

			switch (station.type) {
				case 'police':
					iconColor = '#2563eb'; // blue
					break;
				case 'fire':
					iconColor = '#dc2626'; // red
					break;
				case 'medical':
					iconColor = '#16a34a'; // green
					break;
				case 'emergency':
					iconColor = '#ea580c'; // orange
					break;
			}

			feature.setStyle(new Style({
				image: new Circle({
					radius: 12,
					fill: new Fill({ color: iconColor }),
					stroke: new Stroke({ color: '#ffffff', width: 3 })
				})
			}));

			return feature;
		});

		const stationSource = new VectorSource({
			features: features
		});

		const stationLayer = new VectorLayer({
			source: stationSource
		});

		map.addLayer(stationLayer);

		// Add click handler for stations
		map.on('click', (event) => {
			map.forEachFeatureAtPixel(event.pixel, (feature) => {
				const station = feature.get('station');
				if (station) {
					console.log('Station clicked:', station);
				}
			});
		});
	}

	function centerOnUser() {
		if (userLocation && map) {
			map.getView().animate({
				center: fromLonLat(userLocation),
				zoom: 15,
				duration: 1000
			});
		}
	}

	function handleLogout() {
		sessionStorage.removeItem('token');
		goto('/');
	}
</script>

<svelte:head>
	<title>SIGLAT - Emergency Map</title>
</svelte:head>

{#if isLoading}
	<div class="fixed inset-0 bg-gray-900 flex items-center justify-center z-50">
		<div class="text-center text-white">
			<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-500 mx-auto mb-4"></div>
			<p>Loading Emergency Map...</p>
		</div>
	</div>
{:else}
	<!-- Full Screen Map -->
	<div bind:this={mapElement} class="w-full h-screen relative">
		
		<!-- Top Header Bar -->
		<div class="absolute top-0 left-0 right-0 z-10 bg-white/90 backdrop-blur-sm shadow-sm border-b border-gray-200">
			<div class="flex justify-between items-center px-4 py-3">
				<div class="flex items-center gap-3">
					<div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
						<svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
							<path d="M13 6a3 3 0 11-6 0 3 3 0 616 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
						</svg>
					</div>
					<div>
						<h1 class="text-lg font-semibold text-gray-900">SIGLAT Emergency</h1>
					</div>
				</div>
			</div>
		</div>

		<!-- Fancy Floating Burger Menu -->
		<div class="absolute top-20 right-4 z-30">
			<button
				on:click={() => showUserSidebar = !showUserSidebar}
				class="group relative w-14 h-14 bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 hover:bg-white transition-all duration-300 hover:scale-105 hover:shadow-3xl"
				class:active={showUserSidebar}
			>
				<!-- Burger Icon with Animation -->
				<div class="absolute inset-0 flex flex-col justify-center items-center">
					<div class="relative w-6 h-4 flex flex-col justify-between">
						<span 
							class="block h-0.5 w-6 bg-gray-700 rounded-full transform transition-all duration-300 group-hover:bg-red-600"
							class:rotate-45={showUserSidebar}
							class:translate-y-1.5={showUserSidebar}
						></span>
						<span 
							class="block h-0.5 w-6 bg-gray-700 rounded-full transition-all duration-300 group-hover:bg-red-600"
							class:opacity-0={showUserSidebar}
						></span>
						<span 
							class="block h-0.5 w-6 bg-gray-700 rounded-full transform transition-all duration-300 group-hover:bg-red-600"
							class:-rotate-45={showUserSidebar}
							class:-translate-y-1.5={showUserSidebar}
						></span>
					</div>
				</div>

				<!-- Glowing Ring Effect -->
				<div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-red-500/20 to-orange-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
				
				<!-- Ripple Effect -->
				<div class="absolute inset-0 rounded-2xl animate-ping opacity-20 bg-red-500" class:block={showUserSidebar} class:hidden={!showUserSidebar}></div>
			</button>
		</div>

		<!-- Recenter Button -->
		<div class="absolute bottom-6 left-4 z-10">
			<button
				on:click={centerOnUser}
				class="bg-white text-gray-700 p-3 rounded-xl shadow-lg hover:bg-gray-50 transition-all hover:scale-105"
				title="Center on my location"
			>
				<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
					<path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
				</svg>
			</button>
		</div>

		<!-- User Info Sidebar -->
		{#if showUserSidebar}
			<div class="absolute top-0 right-0 h-full w-80 bg-white/95 backdrop-blur-sm shadow-xl z-20 transform transition-transform duration-300 border-l border-gray-200">
				<div class="p-4 border-b border-gray-200">
					<div class="flex justify-between items-center">
						<h2 class="text-lg font-semibold text-gray-900">User Profile</h2>
						<button
							on:click={() => showUserSidebar = false}
							class="text-gray-500 hover:text-gray-700"
						>
							<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
							</svg>
						</button>
					</div>
				</div>

				<div class="p-4 overflow-y-auto h-full pb-20">
					{#if userDetails || userInfo}
						<div class="space-y-4">
							<!-- Profile Avatar -->
							<div class="text-center">
								<div class="w-20 h-20 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
									<svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
										<path d="M13 6a3 3 0 11-6 0 3 3 0 616 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
									</svg>
								</div>
								<h3 class="text-lg font-medium text-gray-900">
									{#if userDetails}
										{userDetails.firstName} {userDetails.lastName}
									{:else}
										{userInfo.unique_name || 'User'}
									{/if}
								</h3>
								<p class="text-sm text-gray-500">Emergency System User</p>
							</div>

							<!-- User Details -->
							<div class="space-y-3">
								<div class="bg-gray-50 rounded-lg p-3">
									<div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</div>
									<p class="text-sm text-gray-900 mt-1">{userDetails?.email || userInfo?.email || 'Not provided'}</p>
								</div>

								{#if userDetails?.firstName}
									<div class="bg-gray-50 rounded-lg p-3">
										<div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Full Name</div>
										<p class="text-sm text-gray-900 mt-1">{userDetails.firstName} {userDetails.middleName || ''} {userDetails.lastName}</p>
									</div>
								{/if}

								{#if userDetails?.phoneNumber}
									<div class="bg-gray-50 rounded-lg p-3">
										<div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Phone Number</div>
										<p class="text-sm text-gray-900 mt-1">{userDetails.phoneNumber}</p>
									</div>
								{/if}

								{#if userDetails?.address}
									<div class="bg-gray-50 rounded-lg p-3">
										<div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Address</div>
										<p class="text-sm text-gray-900 mt-1">{userDetails.address}</p>
									</div>
								{/if}

								{#if userDetails?.gender}
									<div class="bg-gray-50 rounded-lg p-3">
										<div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Gender</div>
										<p class="text-sm text-gray-900 mt-1">{userDetails.gender}</p>
									</div>
								{/if}

								{#if userDetails?.dateOfBirth}
									<div class="bg-gray-50 rounded-lg p-3">
										<div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Date of Birth</div>
										<p class="text-sm text-gray-900 mt-1">{new Date(userDetails.dateOfBirth).toLocaleDateString()}</p>
									</div>
								{/if}

								<div class="bg-gray-50 rounded-lg p-3">
									<div class="text-xs font-medium text-gray-500 uppercase tracking-wide">User ID</div>
									<p class="text-sm text-gray-900 mt-1">{userInfo.nameid || 'N/A'}</p>
								</div>

								<div class="bg-gray-50 rounded-lg p-3">
									<div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Role</div>
									<p class="text-sm text-gray-900 mt-1">{userInfo.role || 'User'}</p>
								</div>

								<div class="bg-gray-50 rounded-lg p-3">
									<div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Session Expires</div>
									<p class="text-sm text-gray-900 mt-1">{userInfo.exp ? new Date(userInfo.exp * 1000).toLocaleString() : 'N/A'}</p>
								</div>
							</div>

							<!-- Emergency Contacts -->
							<div class="mt-6">
								<h4 class="text-md font-medium text-gray-900 mb-3">Emergency Contacts</h4>
								<div class="space-y-2">
									<a
										href="tel:911"
										class="w-full bg-red-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-red-700 transition-colors text-center block"
									>
										🚨 Call 911 - Emergency
									</a>
									<a
										href="tel:(02) 123-4567"
										class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors text-sm text-center block"
									>
										👮 Police: (02) 123-4567
									</a>
									<a
										href="tel:(02) 234-5678"
										class="w-full bg-orange-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-orange-700 transition-colors text-sm text-center block"
									>
										🚒 Fire: (02) 234-5678
									</a>
									<a
										href="tel:(02) 345-6789"
										class="w-full bg-green-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-green-700 transition-colors text-sm text-center block"
									>
										🏥 Medical: (02) 345-6789
									</a>
								</div>
							</div>

							<!-- Map Legend -->
							<div class="mt-6">
								<h4 class="text-md font-medium text-gray-900 mb-3">Map Legend</h4>
								<div class="space-y-2">
									<div class="flex items-center gap-3">
										<div class="w-4 h-4 bg-blue-600 rounded-full"></div>
										<span class="text-sm text-gray-700">Your Location</span>
									</div>
									<div class="flex items-center gap-3">
										<div class="w-4 h-4 bg-blue-600 rounded-full border-2 border-white shadow-sm"></div>
										<span class="text-sm text-gray-700">Police Station</span>
									</div>
									<div class="flex items-center gap-3">
										<div class="w-4 h-4 bg-red-600 rounded-full border-2 border-white shadow-sm"></div>
										<span class="text-sm text-gray-700">Fire Department</span>
									</div>
									<div class="flex items-center gap-3">
										<div class="w-4 h-4 bg-green-600 rounded-full border-2 border-white shadow-sm"></div>
										<span class="text-sm text-gray-700">Medical Center</span>
									</div>
									<div class="flex items-center gap-3">
										<div class="w-4 h-4 bg-orange-600 rounded-full border-2 border-white shadow-sm"></div>
										<span class="text-sm text-gray-700">Emergency Unit</span>
									</div>
								</div>
							</div>

							<!-- Logout Button -->
							<div class="mt-6 pt-4 border-t border-gray-200">
								<button
									on:click={handleLogout}
									class="w-full bg-red-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-red-700 transition-colors"
								>
									🚪 Logout
								</button>
							</div>
						</div>
					{/if}
				</div>
			</div>
		{/if}
	</div>
{/if}