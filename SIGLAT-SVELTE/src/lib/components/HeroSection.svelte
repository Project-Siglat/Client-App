<script>
	import { onMount } from 'svelte';

	const emojis = ['🚑', '🩹', '💓', '⛑️', '🚨', '⚕️', '🆘', '🏥'];
	let floatingIcons = [];

	onMount(() => {
		// Generate random positions for floating icons
		floatingIcons = emojis.map((emoji, index) => ({
			emoji,
			style: `
				top: ${Math.random() * 80 + 10}%; 
				left: ${Math.random() * 80 + 10}%;
				animation-delay: ${Math.random() * 3}s;
			`
		}));
	});

	function navigateToLogin() {
		window.location.href = '/login';
	}
</script>

<div class="w-full p-3 font-sans bg-gradient-to-br from-white to-gray-50 h-[500px] relative overflow-hidden grid-pattern">
	<!-- Heartbeat Animation Background -->
	<div class="absolute inset-0 pointer-events-none opacity-30">
		{#each Array(5) as _, i}
			<div 
				class="absolute w-full h-0.5 bg-red-400 animate-heartbeat" 
				style="top: {20 + i * 15}%; animation-delay: {i * 0.3}s;"
			></div>
		{/each}
	</div>

	<!-- Floating Icons -->
	<div class="absolute inset-0 pointer-events-none">
		{#each floatingIcons as icon}
			<div class="absolute text-red-400 text-xl animate-float" style={icon.style}>
				{icon.emoji}
			</div>
		{/each}
	</div>

	<!-- Hero Section -->
	<div class="text-center max-w-4xl mx-auto relative z-10 flex flex-col justify-center h-full">
		<div class="flex justify-center mb-2">
			<img
				src="/images/siglat.png"
				alt="Siglat Logo"
				class="max-w-24 h-auto"
			/>
		</div>
		<h1 class="text-3xl text-red-600 mb-2 font-light tracking-tight">
			Siglat Alert System
		</h1>
		<p class="text-gray-600 text-sm leading-relaxed mb-5 max-w-lg mx-auto font-light">
			Advanced monitoring and alert management system designed to keep you
			informed and your operations secure. Stay ahead of critical events
			with real-time notifications and comprehensive alert tracking.
		</p>

		<!-- Login Button -->
		<div class="text-center relative z-10">
			<button
				on:click={navigateToLogin}
				class="bg-red-600 hover:bg-red-700 text-white px-7 py-3 rounded-full font-medium text-sm 
				       transition-all duration-300 transform hover:-translate-y-1 
				       shadow-lg hover:shadow-xl tracking-wide"
			>
				Login to Siglat Alert System
			</button>
		</div>
	</div>
</div>

<style>
	.grid-pattern {
		background-image:
			linear-gradient(rgba(220, 53, 69, 0.1) 1px, transparent 1px),
			linear-gradient(90deg, rgba(220, 53, 69, 0.1) 1px, transparent 1px);
		background-size: 50px 50px;
	}
</style>