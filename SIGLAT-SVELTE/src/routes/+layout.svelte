<script>
	import { page } from '$app/stores';
	import Topbar from '$lib/components/Topbar.svelte';
	import '../app.css';

	// Show topbar on protected routes
	$: showTopbar = $page.route && !['/', '/login'].includes($page.route.id);
</script>

{#if showTopbar}
	<Topbar />
{/if}
<main class="min-h-screen">
	<slot />
</main>