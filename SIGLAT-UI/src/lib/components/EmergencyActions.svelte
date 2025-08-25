<script lang="ts">
	import { goto } from '$app/navigation';

	function handleEmergency() {
		// In real implementation, this would open emergency reporting form
		alert('🚨 EMERGENCY ALERT SYSTEM ACTIVATED!\n\nIn a real emergency:\n• Call 911 immediately\n• Contact local authorities\n• Use SIGLAT for coordination\n\nThis is a demo system.');
	}

	function handleReportIncident() {
		// Navigate to incident reporting (would be a separate page/modal)
		goto('/login?action=report');
	}

	function handleGetHelp() {
		// Scroll to contacts section
		document.getElementById('emergency-contacts')?.scrollIntoView({ behavior: 'smooth' });
	}

	function handleLogin() {
		goto('/login');
	}
</script>

<!-- Emergency Action Center -->
<div class="bg-white border-b border-gray-200">
	<div class="max-w-5xl mx-auto px-4 py-6">
		<!-- Emergency Alert Banner -->
		<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
			<div class="flex items-center space-x-3">
				<div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
					<span class="text-red-600 text-lg">🚨</span>
				</div>
				<div class="flex-1">
					<h3 class="font-semibold text-red-900">Emergency Response System</h3>
					<p class="text-sm text-red-700">24/7 monitoring and rapid response for Nueva Vizcaya</p>
				</div>
				<div class="flex items-center space-x-2">
					<div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
					<span class="text-green-700 text-sm font-medium">ACTIVE</span>
				</div>
			</div>
		</div>

		<!-- Primary Action Buttons -->
		<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
			<!-- Emergency Alert -->
			<button 
				on:click={handleEmergency}
				class="bg-red-600 hover:bg-red-700 text-white rounded-lg p-4 transition-all hover:scale-105 shadow-lg hover:shadow-xl"
			>
				<div class="text-center">
					<div class="text-2xl mb-2">🚨</div>
					<div class="font-semibold">EMERGENCY</div>
					<div class="text-xs opacity-90">Immediate Response</div>
				</div>
			</button>

			<!-- Report Incident -->
			<button 
				on:click={handleReportIncident}
				class="bg-orange-600 hover:bg-orange-700 text-white rounded-lg p-4 transition-all hover:scale-105 shadow-lg hover:shadow-xl"
			>
				<div class="text-center">
					<div class="text-2xl mb-2">📋</div>
					<div class="font-semibold">REPORT</div>
					<div class="text-xs opacity-90">Log Incident</div>
				</div>
			</button>

			<!-- Get Help -->
			<button 
				on:click={handleGetHelp}
				class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-4 transition-all hover:scale-105 shadow-lg hover:shadow-xl"
			>
				<div class="text-center">
					<div class="text-2xl mb-2">📞</div>
					<div class="font-semibold">GET HELP</div>
					<div class="text-xs opacity-90">Contact Support</div>
				</div>
			</button>

			<!-- System Login -->
			<button 
				on:click={handleLogin}
				class="bg-gray-700 hover:bg-gray-800 text-white rounded-lg p-4 transition-all hover:scale-105 shadow-lg hover:shadow-xl"
			>
				<div class="text-center">
					<div class="text-2xl mb-2">🔐</div>
					<div class="font-semibold">LOGIN</div>
					<div class="text-xs opacity-90">System Access</div>
				</div>
			</button>
		</div>

		<!-- Quick Stats -->
		<div class="mt-6 bg-gray-50 rounded-lg p-4">
			<div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
				<div>
					<div class="text-lg font-semibold text-blue-600">100%</div>
					<div class="text-xs text-gray-600">Area Coverage</div>
				</div>
				<div>
					<div class="text-lg font-semibold text-green-600">&lt;3min</div>
					<div class="text-xs text-gray-600">Response Time</div>
				</div>
				<div>
					<div class="text-lg font-semibold text-purple-600">24/7</div>
					<div class="text-xs text-gray-600">Monitoring</div>
				</div>
				<div>
					<div class="text-lg font-semibold text-orange-600">4</div>
					<div class="text-xs text-gray-600">Response Stations</div>
				</div>
			</div>
		</div>
	</div>
</div>