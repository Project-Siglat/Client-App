<script>
	import { onMount } from 'svelte';
	import { auth } from '$lib/stores/auth.js';

	onMount(() => {
		auth.initialize();
		
		const unsubscribe = auth.subscribe(authState => {
			if (!authState.loading && !authState.isAuthenticated) {
				window.location.href = '/login';
			}
		});

		return unsubscribe;
	});

	function logout() {
		auth.logout();
		window.location.href = '/';
	}
</script>

<svelte:head>
	<title>Ambulance Dashboard - Siglat Emergency System</title>
</svelte:head>

<div class="ambulance-dashboard">
	<header class="dashboard-header">
		<div class="header-content">
			<div class="header-left">
				<img src="/images/ambulance.png" alt="Ambulance" class="logo" />
				<h1>Ambulance Dashboard</h1>
			</div>
			<button class="logout-btn" on:click={logout}>Logout</button>
		</div>
	</header>

	<main class="dashboard-content">
		<div class="status-card">
			<h2>Unit Status</h2>
			<div class="status-indicator available">
				<div class="status-dot"></div>
				<span>Available</span>
			</div>
		</div>

		<div class="alerts-section">
			<h3>Active Alerts</h3>
			<div class="alert-card">
				<div class="alert-header">
					<span class="alert-type">Medical Emergency</span>
					<span class="alert-priority high">High Priority</span>
				</div>
				<p class="alert-location">Location: Brgy. San Nicolas, Villaverde</p>
				<p class="alert-time">Received: 2 minutes ago</p>
				<div class="alert-actions">
					<button class="accept-btn">Accept</button>
					<button class="view-btn">View Details</button>
				</div>
			</div>
		</div>
	</main>
</div>

<style>
	.ambulance-dashboard {
		min-height: 100vh;
		background-color: #f8f9fa;
	}

	.dashboard-header {
		background: white;
		border-bottom: 1px solid #dee2e6;
		padding: 1rem 2rem;
	}

	.header-content {
		display: flex;
		justify-content: space-between;
		align-items: center;
		max-width: 1200px;
		margin: 0 auto;
	}

	.header-left {
		display: flex;
		align-items: center;
		gap: 1rem;
	}

	.logo {
		width: 40px;
		height: 40px;
	}

	.header-left h1 {
		margin: 0;
		color: #dc3545;
		font-size: 1.5rem;
	}

	.logout-btn {
		padding: 0.5rem 1rem;
		background: #dc3545;
		color: white;
		border: none;
		border-radius: 0.25rem;
		cursor: pointer;
		transition: background-color 0.2s;
	}

	.logout-btn:hover {
		background: #c82333;
	}

	.dashboard-content {
		max-width: 1200px;
		margin: 0 auto;
		padding: 2rem;
	}

	.status-card {
		background: white;
		padding: 2rem;
		border-radius: 0.5rem;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		margin-bottom: 2rem;
	}

	.status-card h2 {
		margin: 0 0 1rem 0;
		color: #343a40;
	}

	.status-indicator {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		font-weight: 500;
	}

	.status-indicator.available {
		color: #28a745;
	}

	.status-dot {
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background-color: #28a745;
		animation: pulse 2s infinite;
	}

	@keyframes pulse {
		0% { opacity: 1; }
		50% { opacity: 0.5; }
		100% { opacity: 1; }
	}

	.alerts-section h3 {
		margin: 0 0 1rem 0;
		color: #343a40;
	}

	.alert-card {
		background: white;
		padding: 1.5rem;
		border-radius: 0.5rem;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		border-left: 4px solid #dc3545;
	}

	.alert-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 1rem;
	}

	.alert-type {
		font-weight: bold;
		color: #343a40;
	}

	.alert-priority {
		padding: 0.25rem 0.5rem;
		border-radius: 0.25rem;
		font-size: 0.75rem;
		font-weight: bold;
		text-transform: uppercase;
	}

	.alert-priority.high {
		background: #dc3545;
		color: white;
	}

	.alert-location,
	.alert-time {
		margin: 0.5rem 0;
		color: #6c757d;
		font-size: 0.875rem;
	}

	.alert-actions {
		display: flex;
		gap: 0.5rem;
		margin-top: 1rem;
	}

	.accept-btn {
		padding: 0.5rem 1rem;
		background: #28a745;
		color: white;
		border: none;
		border-radius: 0.25rem;
		cursor: pointer;
		font-weight: 500;
	}

	.view-btn {
		padding: 0.5rem 1rem;
		background: #6c757d;
		color: white;
		border: none;
		border-radius: 0.25rem;
		cursor: pointer;
	}

	.accept-btn:hover {
		background: #218838;
	}

	.view-btn:hover {
		background: #5a6268;
	}
</style>