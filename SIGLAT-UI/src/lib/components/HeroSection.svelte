<script lang="ts">
	import { goto } from '$app/navigation';

	function handleLogin() {
		goto('/login');
	}
</script>

<!-- Clean Navigation Header -->
<div class="bg-white border-b border-gray-200 sticky top-0 z-50">
	<nav class="bg-white shadow-sm px-4 py-3">
		<div class="max-w-5xl mx-auto flex items-center justify-between">
			<!-- Logo and Brand -->
			<div class="flex items-center space-x-3">
				<div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center">
					<img src="/siglat.png" alt="SIGLAT" class="w-6 h-6" />
				</div>
				<div>
					<h1 class="text-xl font-bold text-gray-900">SIGLAT</h1>
					<p class="text-xs text-gray-600">Emergency Response System</p>
				</div>
			</div>

			<!-- Status and Navigation -->
			<div class="flex items-center space-x-4">
				<!-- System Status Indicator -->
				<div class="hidden sm:flex items-center space-x-2 bg-green-50 px-3 py-1.5 rounded-full">
					<div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
					<span class="text-green-700 text-sm font-medium">System Online</span>
				</div>
				
				<!-- Emergency Hotline -->
				<div class="hidden md:flex items-center space-x-2 bg-red-50 px-3 py-1.5 rounded-full">
					<span class="text-red-600 text-sm">📞</span>
					<span class="text-red-700 text-sm font-medium">911</span>
				</div>
				
				<!-- Login Button -->
				<button 
					on:click={handleLogin}
					class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
				>
					Access System
				</button>
			</div>
		</div>
	</nav>

	<!-- Hero Banner -->
	<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
		<div class="max-w-5xl mx-auto px-4 py-8 text-center">
			<h2 class="text-3xl md:text-4xl font-bold mb-3">
				Nueva Vizcaya Emergency Response
			</h2>
			<p class="text-lg md:text-xl text-blue-100 mb-6 max-w-3xl mx-auto">
				Advanced incident management and rapid response coordination for Villaverde and surrounding areas
			</p>
			
			<!-- Key Features -->
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
				<div class="bg-white/10 backdrop-blur rounded-lg p-4">
					<div class="text-2xl mb-2">⚡</div>
					<h3 class="font-semibold mb-1">Rapid Response</h3>
					<p class="text-sm text-blue-100">Sub-3 minute emergency response times</p>
				</div>
				<div class="bg-white/10 backdrop-blur rounded-lg p-4">
					<div class="text-2xl mb-2">🗺️</div>
					<h3 class="font-semibold mb-1">Smart Routing</h3>
					<p class="text-sm text-blue-100">AI-powered route optimization</p>
				</div>
				<div class="bg-white/10 backdrop-blur rounded-lg p-4">
					<div class="text-2xl mb-2">📱</div>
					<h3 class="font-semibold mb-1">24/7 Monitoring</h3>
					<p class="text-sm text-blue-100">Continuous system surveillance</p>
				</div>
			</div>
		</div>
	</div>
</div>