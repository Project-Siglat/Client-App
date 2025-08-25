<script lang="ts">
	import { page } from '$app/stores';
	import { goto } from '$app/navigation';

	$: status = $page.status;
	$: message = $page.error?.message || 'An unexpected error occurred';

	function goHome() {
		goto('/');
	}

	function goBack() {
		window.history.back();
	}
</script>

<svelte:head>
	<title>{status} - SIGLAT Emergency Response</title>
</svelte:head>

<div class="min-h-screen bg-white text-gray-800 relative overflow-hidden flex items-center justify-center">
	<!-- Animated background elements -->
	<div class="fixed inset-0 pointer-events-none z-0">
		<div class="absolute w-80 h-80 bg-red-600 opacity-5 rounded-full -top-40 -right-40 animate-pulse"></div>
		<div class="absolute w-60 h-60 bg-red-600 opacity-5 rounded-full -bottom-30 -left-30 animate-pulse delay-150"></div>
		<div class="absolute w-40 h-40 bg-red-600 opacity-5 rounded-full top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 animate-pulse delay-300"></div>
		<div class="absolute w-32 h-32 bg-red-600 opacity-10 rounded-full top-20 left-20 animate-bounce"></div>
		<div class="absolute w-24 h-24 bg-red-600 opacity-10 rounded-full bottom-20 right-20 animate-bounce delay-500"></div>
	</div>

	<!-- Error Content -->
	<div class="relative z-10 text-center px-8 max-w-2xl mx-auto">
		<!-- Error Icon -->
		<div class="mb-8">
			<div class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-red-100 to-red-200 rounded-full shadow-xl mb-6">
				{#if status === 404}
					<svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
					</svg>
				{:else}
					<svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
					</svg>
				{/if}
			</div>
			
			<!-- SIGLAT Logo -->
			<div class="mb-6">
				<h1 class="text-4xl md:text-5xl font-bold text-red-600 mb-2">SIGLAT</h1>
				<p class="text-lg text-gray-600">Emergency Response System</p>
			</div>
		</div>

		<!-- Error Details -->
		<div class="bg-white rounded-xl shadow-2xl p-8 border border-gray-200 mb-8">
			<div class="mb-6">
				<h2 class="text-6xl md:text-8xl font-bold text-red-600 mb-4">{status}</h2>
				{#if status === 404}
					<h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Page Not Found</h3>
					<p class="text-gray-600 text-lg mb-4">
						The page you're looking for doesn't exist or has been moved.
					</p>
					<p class="text-gray-500 text-sm">
						It might have been removed, renamed, or you entered the wrong URL.
					</p>
				{:else if status === 500}
					<h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Server Error</h3>
					<p class="text-gray-600 text-lg mb-4">
						Something went wrong on our servers.
					</p>
					<p class="text-gray-500 text-sm">
						Our emergency response team has been notified and is working to fix this issue.
					</p>
				{:else}
					<h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Error {status}</h3>
					<p class="text-gray-600 text-lg mb-4">{message}</p>
				{/if}
			</div>

			<!-- Action Buttons -->
			<div class="flex flex-col sm:flex-row gap-4 justify-center">
				<button 
					on:click={goHome}
					class="px-6 py-3 bg-red-600 text-white border-none rounded-lg text-base font-semibold cursor-pointer transition-all hover:bg-red-700 hover:-translate-y-0.5 hover:shadow-lg"
				>
					<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
					</svg>
					Go to Homepage
				</button>
				
				<button 
					on:click={goBack}
					class="px-6 py-3 bg-transparent text-red-600 border-2 border-red-600 rounded-lg text-base font-semibold cursor-pointer transition-all hover:bg-red-600 hover:text-white hover:-translate-y-0.5"
				>
					<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
					</svg>
					Go Back
				</button>
			</div>
		</div>

		<!-- Emergency Contact Info -->
		{#if status === 404}
			<div class="bg-gradient-to-r from-red-50 to-red-100 rounded-lg p-6 border border-red-200">
				<p class="text-red-800 font-medium mb-2">
					<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
					Need Emergency Assistance?
				</p>
				<p class="text-red-700 text-sm">
					If this is an emergency, please contact our hotline immediately at <strong>911</strong>
				</p>
			</div>
		{/if}
	</div>
</div>
