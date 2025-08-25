<div
	class="w-full p-3 font-sans bg-gradient-to-br from-white to-gray-50 h-[500px] relative overflow-hidden grid-pattern"
>
	<!-- Heartbeat Animation Background -->
	<div class="absolute inset-0 pointer-events-none opacity-30">
		{#each Array(5) as _, i}
			<div class="heartbeat-line" style="top: {20 + i * 15}%; animation-delay: {i * 0.3}s;"></div>
		{/each}
	</div>

	<!-- Floating Icons -->
	<div class="absolute inset-0 pointer-events-none">
		<div class="absolute top-8 left-6 text-red-400 text-2xl float-animation">🚑</div>
		<div class="absolute top-16 right-8 text-red-500 text-xl float-animation-delay">🩹</div>
		<div class="absolute top-32 left-1/4 text-red-600 text-xl heart-rate-animation">💓</div>
		<div class="absolute top-40 right-1/4 text-red-400 text-2xl float-animation-delay">⛑️</div>
		<div class="absolute bottom-16 left-6 text-red-500 text-xl float-animation">🚨</div>
		<div class="absolute bottom-24 right-8 text-red-600 text-xl float-animation-delay">⚕️</div>
		<div class="absolute top-1/3 right-4 text-red-400 text-lg float-animation">🆘</div>
		<div class="absolute bottom-1/3 left-4 text-red-500 text-xl float-animation-delay">🏥</div>
	</div>

	<!-- Hero Section -->
	<div class="text-center max-w-4xl mx-auto relative z-10 flex flex-col justify-center h-full">
		<div class="flex justify-center mb-2">
			<img src="/images/siglat.png" alt="Siglat Logo" class="max-w-24 h-auto" />
		</div>
		<h1 class="text-red-600 text-3xl mb-2 font-light tracking-tight">
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
				class="login-button bg-red-600 text-white border-none py-3 px-7 text-sm font-medium rounded-full cursor-pointer transition-all duration-300 shadow-lg hover:bg-red-700 hover:-translate-y-1 hover:shadow-xl active:-translate-y-0.5 active:shadow-lg"
				on:click={() => window.location.href = '/login'}
			>
				Login to Siglat Alert System
			</button>
		</div>
	</div>
</div>

<style>
	@keyframes float-up-down {
		0%, 100% { transform: translateY(0px); }
		50% { transform: translateY(-10px); }
	}

	.float-animation {
		animation: float-up-down 3s ease-in-out infinite;
	}

	.float-animation-delay {
		animation: float-up-down 3s ease-in-out infinite;
		animation-delay: 1.5s;
	}

	@keyframes heart-rate-bounce {
		0%, 100% { transform: translateY(0px) scale(1); }
		25% { transform: translateY(-15px) scale(1.2); }
		50% { transform: translateY(5px) scale(0.9); }
		75% { transform: translateY(-8px) scale(1.1); }
	}

	.heart-rate-animation {
		animation: heart-rate-bounce 1.2s ease-in-out infinite;
	}

	.grid-pattern {
		background-image:
			linear-gradient(rgba(220, 53, 69, 0.1) 1px, transparent 1px),
			linear-gradient(90deg, rgba(220, 53, 69, 0.1) 1px, transparent 1px);
		background-size: 50px 50px;
	}

	@keyframes heartbeat {
		0%, 20%, 40%, 60%, 80%, 100% {
			opacity: 0.3;
			transform: scaleY(1);
		}
		10% { opacity: 1; transform: scaleY(1.8); }
		30% { opacity: 1; transform: scaleY(2.2); }
		50% { opacity: 1; transform: scaleY(1.5); }
		70% { opacity: 1; transform: scaleY(1.2); }
	}

	.heartbeat-line {
		position: absolute;
		width: 100%;
		height: 2px;
		background: rgba(220, 53, 69, 0.2);
		animation: heartbeat 2s infinite;
		transform-origin: left center;
	}
</style>