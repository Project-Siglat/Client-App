<script lang="ts">
	import { onMount } from 'svelte';

	interface Contact {
		id: string;
		label: string;
		contactType: string;
		contactInformation: string;
	}

	let contacts: Contact[] = [];
	let loading = true;

	onMount(async () => {
		try {
			const response = await fetch('http://localhost:5000/api/v1.0/Admin/contact');
			if (response.ok) {
				contacts = await response.json();
			} else {
				// Fallback to static contacts if API fails
				contacts = [
					{
						id: '1',
						label: 'Emergency Hotline',
						contactType: 'Phone',
						contactInformation: '911'
					},
					{
						id: '2',
						label: 'Disaster Response',
						contactType: 'Phone',
						contactInformation: '(078) 396-2345'
					},
					{
						id: '3',
						label: 'Medical Emergency',
						contactType: 'Phone',
						contactInformation: '(078) 396-1234'
					},
					{
						id: '4',
						label: 'Fire Department',
						contactType: 'Phone',
						contactInformation: '(078) 396-3456'
					},
					{
						id: '5',
						label: 'Police Station',
						contactType: 'Phone',
						contactInformation: '(078) 396-4567'
					}
				];
			}
		} catch (error) {
			console.error('Failed to fetch contacts:', error);
			// Fallback to static contacts if API fails
			contacts = [
				{
					id: '1',
					label: 'Emergency Hotline',
					contactType: 'Phone',
					contactInformation: '911'
				},
				{
					id: '2',
					label: 'Disaster Response',
					contactType: 'Phone',
					contactInformation: '(078) 396-2345'
				},
				{
					id: '3',
					label: 'Medical Emergency',
					contactType: 'Phone',
					contactInformation: '(078) 396-1234'
				},
				{
					id: '4',
					label: 'Fire Department',
					contactType: 'Phone',
					contactInformation: '(078) 396-3456'
				},
				{
					id: '5',
					label: 'Police Station',
					contactType: 'Phone',
					contactInformation: '(078) 396-4567'
				}
			];
		} finally {
			loading = false;
		}
	});

	function getContactIcon(label: string): string {
		if (label.includes('Emergency') || label.includes('911')) return '🚨';
		if (label.includes('Medical') || label.includes('Health')) return '🏥';
		if (label.includes('Fire')) return '🚒';
		if (label.includes('Police')) return '👮';
		if (label.includes('Disaster')) return '🌪️';
		return '📞';
	}

	function handleContactClick(contact: Contact) {
		if (contact.contactType === 'Phone') {
			window.open(`tel:${contact.contactInformation}`, '_self');
		}
	}
</script>

<!-- Emergency Contacts Section -->
<div class="bg-white border-b border-gray-200">
	<div class="max-w-5xl mx-auto px-4 py-8">
		<!-- Section Header -->
		<div class="text-center mb-8">
			<h2 class="text-2xl font-bold text-gray-900 mb-2">Emergency Contacts</h2>
			<p class="text-gray-600 max-w-2xl mx-auto">
				Quick access to emergency services and support. In life-threatening emergencies, always call 911 first.
			</p>
		</div>

		<!-- Emergency Contacts Grid -->
		{#if loading}
			<!-- Loading skeleton -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
				{#each Array(6) as _}
					<div class="bg-gray-50 rounded-lg p-4 animate-pulse">
						<div class="w-12 h-12 bg-gray-200 rounded-lg mb-3"></div>
						<div class="w-24 h-4 bg-gray-200 rounded mb-2"></div>
						<div class="w-32 h-3 bg-gray-200 rounded"></div>
					</div>
				{/each}
			</div>
		{:else}
			<!-- Primary Emergency Contact -->
			<div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
				<div class="text-center">
					<div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
						<span class="text-white text-2xl">🚨</span>
					</div>
					<h3 class="text-xl font-bold text-red-900 mb-2">Life-Threatening Emergency</h3>
					<p class="text-red-700 mb-4">For immediate emergency response</p>
					<button 
						on:click={() => window.open('tel:911', '_self')}
						class="bg-red-600 text-white px-8 py-3 rounded-lg font-bold text-lg hover:bg-red-700 transition-all hover:scale-105 shadow-lg"
					>
						📞 CALL 911 NOW
					</button>
				</div>
			</div>

			<!-- Contact Grid -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
				{#each contacts as contact}
					<button
						on:click={() => handleContactClick(contact)}
						class="bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg p-4 transition-all hover:scale-105 hover:shadow-md text-left"
					>
						<div class="flex items-center space-x-3 mb-3">
							<div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm border border-gray-200">
								<span class="text-xl">{getContactIcon(contact.label)}</span>
							</div>
							<div class="flex-1">
								<h4 class="font-semibold text-gray-900">{contact.label}</h4>
								<p class="text-sm text-gray-600">{contact.contactType}</p>
							</div>
						</div>
						<div class="bg-white rounded-lg p-3 border border-gray-200">
							<div class="font-bold text-blue-600 text-lg">{contact.contactInformation}</div>
							<div class="text-xs text-gray-500 mt-1">Tap to call</div>
						</div>
					</button>
				{/each}
			</div>
		{/if}

		<!-- Emergency Information -->
		<div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
			<div class="text-center mb-4">
				<h3 class="text-lg font-semibold text-blue-900 mb-2">Emergency Response Guidelines</h3>
			</div>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
				<div class="space-y-2">
					<div class="flex items-start space-x-2">
						<span class="text-red-600 mt-0.5">🚨</span>
						<span><strong>Life-threatening emergencies:</strong> Call 911 immediately</span>
					</div>
					<div class="flex items-start space-x-2">
						<span class="text-blue-600 mt-0.5">📞</span>
						<span><strong>Non-emergency incidents:</strong> Use appropriate contact numbers</span>
					</div>
				</div>
				<div class="space-y-2">
					<div class="flex items-start space-x-2">
						<span class="text-green-600 mt-0.5">⏱️</span>
						<span><strong>Response time:</strong> Emergency services typically arrive within 3 minutes</span>
					</div>
					<div class="flex items-start space-x-2">
						<span class="text-purple-600 mt-0.5">📍</span>
						<span><strong>Coverage area:</strong> All of Villaverde, Nueva Vizcaya</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>