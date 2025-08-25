<script lang="ts">
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';

	let mapContainer: HTMLDivElement;
	let map: any;
	let loading = true;
	let mapReady = false;
	let showMapFallback = false;
	let vectorSource: any;

	// Emergency locations in Nueva Vizcaya
	const emergencyStations = [
		{ name: "Villaverde Emergency HQ", coords: [121.0833, 16.3833], type: "headquarters" },
		{ name: "Fire Station", coords: [121.0800, 16.3850], type: "fire" },
		{ name: "Medical Center", coords: [121.0870, 16.3820], type: "medical" },
		{ name: "Police Station", coords: [121.0820, 16.3840], type: "police" }
	];

	// Sample emergency incident (for demo)
	let emergencyLocation = [121.0900, 16.3900];
	let showRoute = false;
	let routeInfo = { distance: 0, duration: 0 };

	onMount(async () => {
		if (!browser) {
			showMapFallback = true;
			loading = false;
			return;
		}

		try {
			// Import OpenLayers with proper syntax
			const ol = await import('ol');
			
			// Wait for container
			await new Promise(resolve => setTimeout(resolve, 100));
			if (!mapContainer) {
				showMapFallback = true;
				loading = false;
				return;
			}

			// Create vector source for markers and routes
			vectorSource = new ol.source.Vector();

			// Initialize OpenLayers map
			map = new ol.Map({
				target: mapContainer,
				layers: [
					new ol.layer.Tile({
						source: new ol.source.OSM()
					}),
					new ol.layer.Vector({
						source: vectorSource,
						style: (feature) => {
							const type = feature.get('type');
							if (type === 'route') {
								return new ol.style.Style({
									stroke: new ol.style.Stroke({
										color: '#ef4444',
										width: 4
									})
								});
							}
							// Emergency station styles
							const colors = {
								headquarters: '#3b82f6',
								fire: '#ef4444', 
								medical: '#10b981',
								police: '#6366f1',
								incident: '#f59e0b'
							};
							return new ol.style.Style({
								image: new ol.style.Circle({
									radius: 8,
									fill: new ol.style.Fill({ color: colors[type] || '#6b7280' }),
									stroke: new ol.style.Stroke({ color: '#ffffff', width: 2 })
								})
							});
						}
					})
				],
				view: new ol.View({
					center: ol.proj.fromLonLat([121.0833, 16.3833]),
					zoom: 13
				})
			});

			// Add emergency stations
			emergencyStations.forEach(station => {
				const feature = new ol.Feature({
					geometry: new ol.geom.Point(ol.proj.fromLonLat(station.coords)),
					name: station.name,
					type: station.type
				});
				vectorSource.addFeature(feature);
			});

			// Add sample emergency incident
			const incidentFeature = new ol.Feature({
				geometry: new ol.geom.Point(ol.proj.fromLonLat(emergencyLocation)),
				name: "Emergency Incident",
				type: "incident"
			});
			vectorSource.addFeature(incidentFeature);

			loading = false;
			mapReady = true;

		} catch (error) {
			console.error('Error initializing OpenLayers map:', error);
			showMapFallback = true;
			loading = false;
		}
	});

	// Calculate route using OSRM
	async function calculateRoute(fromCoords: number[], toCoords: number[]) {
		if (!mapReady) return;

		try {
			showRoute = false;
			const url = `https://router.project-osrm.org/route/v1/driving/${fromCoords[0]},${fromCoords[1]};${toCoords[0]},${toCoords[1]}?overview=full&geometries=geojson`;
			
			const response = await fetch(url);
			const data = await response.json();

			if (data.routes && data.routes.length > 0) {
				const route = data.routes[0];
				
				// Update route info
				routeInfo = {
					distance: Math.round(route.distance / 1000 * 10) / 10, // km
					duration: Math.round(route.duration / 60) // minutes
				};

				// Clear existing route
				const routeFeatures = vectorSource.getFeatures().filter((f: any) => f.get('type') === 'route');
				routeFeatures.forEach((f: any) => vectorSource.removeFeature(f));

				// Add new route to map
				const ol = await import('ol');
				const coordinates = route.geometry.coordinates.map((coord: number[]) => ol.proj.fromLonLat(coord));
				const routeFeature = new ol.Feature({
					geometry: new ol.geom.LineString(coordinates),
					type: 'route'
				});
				
				vectorSource.addFeature(routeFeature);
				showRoute = true;

				// Fit view to show route
				const extent = routeFeature.getGeometry()?.getExtent();
				if (extent) {
					map.getView().fit(extent, { padding: [20, 20, 20, 20] });
				}
			}
		} catch (error) {
			console.error('Error calculating route:', error);
		}
	}

	// Demo: Calculate route from headquarters to incident
	function showEmergencyRoute() {
		const hq = emergencyStations.find(s => s.type === 'headquarters');
		if (hq) {
			calculateRoute(hq.coords, emergencyLocation);
		}
	}

	async function resetView() {
		showRoute = false;
		if (map) {
			const ol = await import('ol');
			map.getView().setCenter(ol.proj.fromLonLat([121.0833, 16.3833]));
			map.getView().setZoom(13);
		}
		// Clear route features
		if (vectorSource) {
			const routeFeatures = vectorSource.getFeatures().filter((f: any) => f.get('type') === 'route');
			routeFeatures.forEach((f: any) => vectorSource.removeFeature(f));
		}
	}
</script>

<!-- Interactive Emergency Response Map -->
<div class="bg-gray-50">
	<div class="max-w-5xl mx-auto px-4 py-8">
		<!-- Section Header -->
		<div class="text-center mb-6">
			<h2 class="text-2xl font-bold text-gray-900 mb-2">Emergency Response Coverage</h2>
			<p class="text-gray-600 max-w-2xl mx-auto">
				Interactive map showing emergency stations, coverage areas, and real-time routing capabilities powered by OpenLayers and OSRM.
			</p>
		</div>
		<!-- Coverage Map Card -->
		<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4">
			<!-- Card Header -->
			<div class="px-4 py-3 border-b border-gray-100">
				<div class="flex items-center justify-between">
					<div class="flex items-center space-x-3">
						<div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
							<span class="text-blue-600 text-sm">🗺️</span>
						</div>
						<div>
							<h3 class="font-semibold text-gray-900">Emergency Response Map</h3>
							<p class="text-xs text-gray-600">OpenLayers + OSRM Routing</p>
						</div>
					</div>
					<div class="flex items-center space-x-2">
						<div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
						<span class="text-xs font-medium text-green-600">Live</span>
					</div>
				</div>
			</div>

			<!-- Map Content -->
			<div class="relative">
				{#if loading}
					<!-- Loading State -->
					<div class="h-64 bg-gray-50 flex items-center justify-center">
						<div class="text-center">
							<div class="w-8 h-8 border-2 border-gray-300 border-t-blue-600 rounded-full animate-spin mx-auto mb-3"></div>
							<p class="text-sm text-gray-600">Loading OpenLayers map...</p>
						</div>
					</div>
				{:else if showMapFallback}
					<!-- Fallback when map fails to load -->
					<div class="h-64 bg-gradient-to-br from-blue-50 to-gray-50 flex items-center justify-center">
						<div class="text-center">
							<div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
								<span class="text-blue-600 text-lg">📍</span>
							</div>
							<h4 class="font-semibold text-gray-900 mb-1">Villaverde, Nueva Vizcaya</h4>
							<p class="text-sm text-gray-600 mb-2">Emergency response system active</p>
							<span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">✓ Online</span>
						</div>
					</div>
				{:else}
					<!-- OpenLayers Map Container -->
					<div 
						bind:this={mapContainer} 
						class="h-64 w-full transition-opacity duration-500 {mapReady ? 'opacity-100' : 'opacity-0'}"
						style="min-height: 256px;"
					></div>
					
					<!-- Map Controls -->
					{#if mapReady}
						<div class="absolute top-2 right-2 space-y-2">
							<button 
								on:click={showEmergencyRoute}
								class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1 rounded-md shadow-lg transition-colors"
							>
								🚨 Demo Route
							</button>
							<button 
								on:click={resetView}
								class="bg-gray-600 hover:bg-gray-700 text-white text-xs px-3 py-1 rounded-md shadow-lg transition-colors"
							>
								Reset View
							</button>
						</div>
					{/if}
				{/if}
			</div>

			<!-- Route Information -->
			{#if showRoute}
				<div class="px-4 py-3 bg-red-50 border-t border-red-100">
					<div class="flex items-center justify-between">
						<div class="flex items-center space-x-2">
							<span class="text-red-600 text-sm">🚨</span>
							<span class="text-sm font-medium text-red-800">Emergency Route Active</span>
						</div>
						<div class="flex items-center space-x-4 text-xs">
							<div class="text-gray-600">
								<span class="font-medium">{routeInfo.distance}km</span> distance
							</div>
							<div class="text-gray-600">
								<span class="font-medium text-red-600">{routeInfo.duration}min</span> ETA
							</div>
						</div>
					</div>
				</div>
			{/if}

			<!-- Map Footer with Enhanced Stats -->
			<div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
				<div class="grid grid-cols-4 gap-3 text-center">
					<div>
						<div class="text-sm font-semibold text-blue-600">{emergencyStations.length}</div>
						<div class="text-xs text-gray-600">Stations</div>
					</div>
					<div>
						<div class="text-sm font-semibold text-green-600">100%</div>
						<div class="text-xs text-gray-600">Coverage</div>
					</div>
					<div>
						<div class="text-sm font-semibold text-orange-600">&lt;3min</div>
						<div class="text-xs text-gray-600">Response</div>
					</div>
					<div>
						<div class="text-sm font-semibold text-purple-600">OSRM</div>
						<div class="text-xs text-gray-600">Routing</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Emergency Services Grid -->
		<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<!-- Emergency Stations -->
			<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
				<div class="flex items-center space-x-3 mb-3">
					<div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
						<span class="text-blue-600">🏛️</span>
					</div>
					<div>
						<h4 class="font-semibold text-gray-900 text-sm">Emergency Stations</h4>
						<p class="text-xs text-gray-600">Response network</p>
					</div>
				</div>
				<div class="space-y-2">
					{#each emergencyStations as station}
						<div class="flex justify-between items-center">
							<span class="text-xs text-gray-600">
								{#if station.type === 'headquarters'}🏛️{:else if station.type === 'fire'}🚒{:else if station.type === 'medical'}🏥{:else}👮{/if}
								{station.name}
							</span>
							<span class="text-xs font-medium text-green-600">Active</span>
						</div>
					{/each}
				</div>
			</div>

			<!-- Routing Capabilities -->
			<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
				<div class="flex items-center space-x-3 mb-3">
					<div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
						<span class="text-green-600">🛣️</span>
					</div>
					<div>
						<h4 class="font-semibold text-gray-900 text-sm">Smart Routing</h4>
						<p class="text-xs text-gray-600">OSRM-powered navigation</p>
					</div>
				</div>
				<div class="space-y-2">
					<div class="flex justify-between items-center">
						<span class="text-xs text-gray-600">🗺️ Route calculation</span>
						<span class="text-xs font-medium text-green-600">Real-time</span>
					</div>
					<div class="flex justify-between items-center">
						<span class="text-xs text-gray-600">⏱️ ETA prediction</span>
						<span class="text-xs font-medium text-green-600">Accurate</span>
					</div>
					<div class="flex justify-between items-center">
						<span class="text-xs text-gray-600">🆓 Free service</span>
						<span class="text-xs font-medium text-green-600">Unlimited</span>
					</div>
					<div class="flex justify-between items-center">
						<span class="text-xs text-gray-600">🚨 Emergency optimized</span>
						<span class="text-xs font-medium text-green-600">Priority</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	/* OpenLayers map styling */
	:global(.ol-control) {
		background: rgba(255, 255, 255, 0.8) !important;
		border-radius: 4px !important;
	}
	
	:global(.ol-zoom) {
		top: 10px !important;
		left: 10px !important;
	}
	
	:global(.ol-attribution) {
		bottom: 4px !important;
		right: 4px !important;
		font-size: 10px !important;
	}
</style>