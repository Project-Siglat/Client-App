<script>
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';

	let mapContainer;
	let map;
	let isLoading = true;

	onMount(async () => {
		if (browser) {
			// Dynamic import to ensure it only runs on client
			const L = await import('leaflet');
			
			// Initialize map
			map = L.default.map(mapContainer).setView([16.3988, 121.0642], 10);
			
			// Add tile layer
			L.default.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '© OpenStreetMap contributors'
			}).addTo(map);

			// Add marker for Nueva Vizcaya
			L.default.marker([16.3988, 121.0642]).addTo(map)
				.bindPopup('Nueva Vizcaya - Siglat Coverage Area');

			// Hide loading indicator
			isLoading = false;

			// Force map resize after container is visible
			setTimeout(() => {
				map.invalidateSize();
			}, 100);
		}
	});
</script>

<!-- Map Section with Enhanced Design -->
<section class="bg-white py-8 md:py-12 px-4">
	<div class="max-w-4xl mx-auto">
		<div class="text-center mb-6 md:mb-8">
			<h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">
				Nueva Vizcaya Coverage Map
			</h2>
			<div class="w-16 md:w-24 h-1 bg-gradient-to-r from-red-500 to-red-600 mx-auto rounded-full mb-4"></div>
			<p class="text-gray-600 text-sm md:text-base lg:text-lg max-w-xl md:max-w-2xl mx-auto px-2">
				Real-time monitoring and emergency response coverage across Nueva Vizcaya
			</p>
		</div>
		<div class="bg-gradient-to-br from-white to-red-50 rounded-2xl md:rounded-3xl shadow-lg md:shadow-2xl p-3 md:p-6 border border-red-200">
			<div class="relative overflow-hidden rounded-xl md:rounded-2xl border border-red-200 md:border-2 shadow-inner">
				{#if isLoading}
					<div class="absolute inset-0 bg-gradient-to-br from-white to-red-50 flex items-center justify-center z-10 h-64 md:h-80 lg:h-96">
						<div class="text-center px-4">
							<div class="relative">
								<div class="inline-block animate-spin rounded-full h-12 w-12 md:h-16 md:w-16 border-4 border-red-200 border-t-red-600 shadow-lg"></div>
								<div class="absolute inset-0 rounded-full bg-red-50 blur-xl opacity-50"></div>
							</div>
							<p class="text-gray-700 mt-4 md:mt-6 font-medium text-base md:text-lg">
								Loading interactive map...
							</p>
							<p class="text-gray-500 text-xs md:text-sm mt-2">
								Please wait while we prepare your location data
							</p>
						</div>
					</div>
				{/if}
				<div bind:this={mapContainer} class="h-64 md:h-80 lg:h-96"></div>
			</div>
		</div>
	</div>
</section>