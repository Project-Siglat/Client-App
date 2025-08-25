<script lang="ts">
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';

	let mapContainer: HTMLDivElement;
	let map: any;

	onMount(async () => {
		if (browser && typeof window !== 'undefined') {
			// Wait for Leaflet to be available
			while (!window.L) {
				await new Promise(resolve => setTimeout(resolve, 100));
			}

			// Initialize map
			map = window.L.map(mapContainer).setView([16.3301, 121.171], 10);
			
			window.L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
				maxZoom: 19,
				attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
			}).addTo(map);

			// Hide loading indicator
			const loadingElement = document.getElementById('map-loading');
			setTimeout(() => {
				if (loadingElement) {
					loadingElement.style.display = 'none';
				}
			}, 2000);

			// Add marker for Villaverde, Nueva Vizcaya
			window.L.marker([16.3833, 121.0833])
				.addTo(map)
				.bindPopup('Villaverde, Nueva Vizcaya')
				.openPopup();
		}
	});
</script>

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
				<div
					id="map-loading"
					class="absolute inset-0 bg-gradient-to-br from-white to-red-50 flex items-center justify-center z-10"
				>
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
				<div bind:this={mapContainer} class="h-64 sm:h-80 md:h-96 lg:h-[400px]"></div>
			</div>
		</div>
	</div>
</section>