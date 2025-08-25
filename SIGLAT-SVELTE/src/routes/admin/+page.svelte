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
	<title>Admin Dashboard - Siglat Emergency System</title>
</svelte:head>

<div class="admin-dashboard">
	<header class="dashboard-header">
		<div class="header-content">
			<div class="header-left">
				<img src="/images/siglat.png" alt="Siglat Logo" class="logo" />
				<h1>Admin Dashboard</h1>
			</div>
			<button class="logout-btn" on:click={logout}>Logout</button>
		</div>
	</header>

	<main class="dashboard-content">
		<div class="welcome-card">
			<h2>Welcome to Admin Dashboard</h2>
			<p>Manage emergency services, monitor alerts, and oversee system operations.</p>
		</div>

		<div class="stats-grid">
			<div class="stat-card">
				<h3>Active Alerts</h3>
				<div class="stat-number">12</div>
			</div>
			<div class="stat-card">
				<h3>Available Ambulances</h3>
				<div class="stat-number">8</div>
			</div>
			<div class="stat-card">
				<h3>Response Time Avg</h3>
				<div class="stat-number">4.2 min</div>
			</div>
			<div class="stat-card">
				<h3>Total Users</h3>
				<div class="stat-number">1,247</div>
			</div>
		</div>
	</main>
</div>

<style>
	.admin-dashboard {
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

	.welcome-card {
		background: white;
		padding: 2rem;
		border-radius: 0.5rem;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		margin-bottom: 2rem;
	}

	.welcome-card h2 {
		margin: 0 0 1rem 0;
		color: #343a40;
	}

	.welcome-card p {
		margin: 0;
		color: #6c757d;
	}

	.stats-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 1.5rem;
	}

	.stat-card {
		background: white;
		padding: 1.5rem;
		border-radius: 0.5rem;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		text-align: center;
	}

	.stat-card h3 {
		margin: 0 0 1rem 0;
		color: #6c757d;
		font-size: 0.875rem;
		text-transform: uppercase;
		letter-spacing: 0.05em;
	}

	.stat-number {
		font-size: 2rem;
		font-weight: bold;
		color: #dc3545;
	}
</style>