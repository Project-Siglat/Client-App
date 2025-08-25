<script lang="ts">
	import { onMount } from 'svelte';

	// System status data
	let systemStatus = {
		overall: 'operational',
		lastUpdate: new Date().toLocaleTimeString(),
		services: [
			{ name: 'Emergency Response', status: 'operational', icon: '🚨' },
			{ name: 'Communication Network', status: 'operational', icon: '📡' },
			{ name: 'GPS Tracking', status: 'operational', icon: '📍' },
			{ name: 'Database Systems', status: 'operational', icon: '💾' },
			{ name: 'Mobile Apps', status: 'operational', icon: '📱' },
			{ name: 'Alert Broadcasting', status: 'operational', icon: '📢' }
		],
		recentActivity: [
			{ time: '2 min ago', event: 'System health check completed', type: 'info' },
			{ time: '15 min ago', event: 'Emergency drill completed successfully', type: 'success' },
			{ time: '1 hour ago', event: 'Routine maintenance completed', type: 'info' },
			{ time: '3 hours ago', event: 'New responder station online', type: 'success' }
		]
	};

	function getStatusColor(status: string) {
		switch (status) {
			case 'operational': return 'text-green-600 bg-green-100';
			case 'warning': return 'text-yellow-600 bg-yellow-100';
			case 'critical': return 'text-red-600 bg-red-100';
			default: return 'text-gray-600 bg-gray-100';
		}
	}

	function getActivityColor(type: string) {
		switch (type) {
			case 'success': return 'text-green-600';
			case 'warning': return 'text-yellow-600';
			case 'error': return 'text-red-600';
			default: return 'text-gray-600';
		}
	}

	// Auto-update timestamps
	onMount(() => {
		const interval = setInterval(() => {
			systemStatus.lastUpdate = new Date().toLocaleTimeString();
		}, 30000); // Update every 30 seconds

		return () => clearInterval(interval);
	});
</script>

<!-- Live Status Dashboard -->
<div class="bg-gray-50">
	<div class="max-w-5xl mx-auto px-4 py-6">
		<!-- Dashboard Header -->
		<div class="mb-6">
			<div class="flex items-center justify-between mb-2">
				<h2 class="text-xl font-bold text-gray-900">System Status Dashboard</h2>
				<div class="flex items-center space-x-2 text-sm text-gray-600">
					<span>Last updated: {systemStatus.lastUpdate}</span>
					<div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
				</div>
			</div>
			<p class="text-gray-600">Real-time monitoring of SIGLAT emergency response system</p>
		</div>

		<!-- Overall Status -->
		<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
			<div class="p-6">
				<div class="flex items-center justify-between">
					<div class="flex items-center space-x-4">
						<div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
							<span class="text-green-600 text-xl">✅</span>
						</div>
						<div>
							<h3 class="text-lg font-semibold text-gray-900">All Systems Operational</h3>
							<p class="text-gray-600">Emergency response system is fully functional</p>
						</div>
					</div>
					<div class="text-right">
						<div class="text-2xl font-bold text-green-600">100%</div>
						<div class="text-sm text-gray-600">Uptime</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Services Grid -->
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
			{#each systemStatus.services as service}
				<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
					<div class="flex items-center space-x-3">
						<div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
							<span class="text-lg">{service.icon}</span>
						</div>
						<div class="flex-1">
							<h4 class="font-medium text-gray-900 text-sm">{service.name}</h4>
							<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {getStatusColor(service.status)}">
								{service.status.toUpperCase()}
							</span>
						</div>
					</div>
				</div>
			{/each}
		</div>

		<!-- Recent Activity & Coverage Map -->
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
			<!-- Recent Activity -->
			<div class="bg-white rounded-lg shadow-sm border border-gray-200">
				<div class="px-4 py-3 border-b border-gray-100">
					<h3 class="font-semibold text-gray-900">Recent Activity</h3>
					<p class="text-xs text-gray-600">Latest system events and updates</p>
				</div>
				<div class="p-4">
					<div class="space-y-3">
						{#each systemStatus.recentActivity as activity}
							<div class="flex items-start space-x-3">
								<div class="w-2 h-2 rounded-full mt-2 {getActivityColor(activity.type)} bg-current"></div>
								<div class="flex-1">
									<p class="text-sm text-gray-900">{activity.event}</p>
									<p class="text-xs text-gray-600">{activity.time}</p>
								</div>
							</div>
						{/each}
					</div>
				</div>
			</div>

			<!-- Coverage Overview -->
			<div class="bg-white rounded-lg shadow-sm border border-gray-200">
				<div class="px-4 py-3 border-b border-gray-100">
					<h3 class="font-semibold text-gray-900">Coverage Overview</h3>
					<p class="text-xs text-gray-600">Emergency response coverage areas</p>
				</div>
				<div class="p-4">
					<div class="space-y-4">
						<!-- Coverage Stats -->
						<div class="grid grid-cols-2 gap-4">
							<div class="text-center p-3 bg-blue-50 rounded-lg">
								<div class="text-lg font-semibold text-blue-600">Nueva Vizcaya</div>
								<div class="text-xs text-blue-700">Primary Coverage</div>
							</div>
							<div class="text-center p-3 bg-green-50 rounded-lg">
								<div class="text-lg font-semibold text-green-600">4 Stations</div>
								<div class="text-xs text-green-700">Response Points</div>
							</div>
						</div>
						
						<!-- Coverage Areas -->
						<div class="space-y-2">
							<div class="flex justify-between items-center p-2 bg-gray-50 rounded">
								<span class="text-sm text-gray-700">🏛️ Villaverde Central</span>
								<span class="text-xs font-medium text-green-600">Active</span>
							</div>
							<div class="flex justify-between items-center p-2 bg-gray-50 rounded">
								<span class="text-sm text-gray-700">🚒 Fire Department</span>
								<span class="text-xs font-medium text-green-600">Active</span>
							</div>
							<div class="flex justify-between items-center p-2 bg-gray-50 rounded">
								<span class="text-sm text-gray-700">🏥 Medical Center</span>
								<span class="text-xs font-medium text-green-600">Active</span>
							</div>
							<div class="flex justify-between items-center p-2 bg-gray-50 rounded">
								<span class="text-sm text-gray-700">👮 Police Station</span>
								<span class="text-xs font-medium text-green-600">Active</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>