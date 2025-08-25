<script>
	import { onMount } from 'svelte';

	let contacts = [];
	let isLoading = true;
	let error = null;

	onMount(async () => {
		try {
			const response = await fetch('http://localhost:5000/api/v1/Admin/contact');
			
			if (response.ok) {
				const data = await response.json();
				// Transform API data to display format
				contacts = data.map(contact => {
					// Format the contact information based on type
					let displayText = '';
					switch (contact.contactType?.toLowerCase()) {
						case 'emergency':
							displayText = `🚨 ${contact.label}: ${contact.contactInformation}`;
							break;
						case 'ambulance':
							displayText = `🚑 ${contact.label}: ${contact.contactInformation}`;
							break;
						case 'fire':
							displayText = `🔥 ${contact.label}: ${contact.contactInformation}`;
							break;
						case 'police':
							displayText = `👮 ${contact.label}: ${contact.contactInformation}`;
							break;
						case 'flood':
							displayText = `🌊 ${contact.label}: ${contact.contactInformation}`;
							break;
						case 'hotline':
							displayText = `📞 ${contact.label}: ${contact.contactInformation}`;
							break;
						default:
							displayText = `📞 ${contact.label}: ${contact.contactInformation}`;
					}
					return displayText;
				});
			} else {
				// Fallback to default contacts if API fails
				contacts = [
					'📞 Emergency Hotline: 911',
					'🚑 Ambulance Services: (078) 321-2345',
					'🔥 Fire Department: (078) 321-3456',
					'👮 Police Emergency: (078) 321-4567',
					'🌊 Flood Emergency: (078) 321-5678'
				];
				error = 'Unable to load latest contacts from server. Showing default contacts.';
			}
		} catch (err) {
			// Fallback to default contacts if network error
			contacts = [
				'📞 Emergency Hotline: 911',
				'🚑 Ambulance Services: (078) 321-2345',
				'🔥 Fire Department: (078) 321-3456',
				'👮 Police Emergency: (078) 321-4567',
				'🌊 Flood Emergency: (078) 321-5678'
			];
			error = 'Network error. Showing default emergency contacts.';
			console.error('Failed to fetch contacts:', err);
		} finally {
			isLoading = false;
		}
	});
</script>

<!-- Contact Section with Enhanced Design -->
<section class="bg-white py-8 md:py-12 px-4 md:px-8">
	<div class="max-w-4xl mx-auto">
		<div class="text-center mb-6 md:mb-8">
			<h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">
				Get in Touch
			</h2>
			<div class="w-16 md:w-24 h-1 bg-gradient-to-r from-red-500 to-red-600 mx-auto rounded-full mb-4"></div>
			<p class="text-gray-600 text-sm md:text-base max-w-xl mx-auto">
				Multiple ways to reach our emergency response team
			</p>
		</div>

		<div class="bg-gradient-to-br from-red-50 via-white to-red-50 rounded-2xl md:rounded-3xl shadow-xl md:shadow-2xl p-4 md:p-6 border border-red-100">
			<div class="text-center mb-4 md:mb-6">
				<div class="inline-flex items-center justify-center w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-xl md:rounded-2xl shadow-lg mb-3 md:mb-4">
					<span class="text-xl md:text-2xl">📞</span>
				</div>
				<h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 md:mb-3">
					Emergency Response & Disaster Management Contact
				</h3>
				<p class="text-gray-600 text-sm md:text-base max-w-2xl mx-auto leading-relaxed">
					Contact our emergency response team for immediate assistance
					with natural disasters, medical emergencies, and crisis
					situations. We're here 24/7 to help.
				</p>
			</div>

			<div class="bg-white rounded-xl md:rounded-2xl shadow-lg border-2 border-red-100 p-4 md:p-6">
				{#if error}
					<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
						<p class="text-yellow-800 text-sm text-center">{error}</p>
					</div>
				{/if}
				
				{#if isLoading}
					<div class="text-center flex items-center justify-center text-red-600 text-base md:text-lg font-semibold">
						<div class="animate-spin rounded-full h-5 w-5 md:h-6 md:w-6 border-2 border-red-300 border-t-red-600 mr-2 md:mr-3"></div>
						Loading emergency contacts...
					</div>
				{:else}
					<ul class="text-red-600 text-base md:text-lg font-semibold space-y-2 md:space-y-3">
						{#each contacts as contact}
							<li class="text-center">{contact}</li>
						{/each}
					</ul>
				{/if}
			</div>
		</div>
	</div>
</section>