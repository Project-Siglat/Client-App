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
</script>

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
				<ul class="text-red-600 text-base md:text-lg font-semibold space-y-2 md:space-y-3">
					{#if loading}
						<li class="text-center flex items-center justify-center">
							<div class="animate-spin rounded-full h-5 w-5 md:h-6 md:w-6 border-2 border-red-300 border-t-red-600 mr-2 md:mr-3"></div>
							Loading emergency contacts...
						</li>
					{:else}
						{#each contacts as contact}
							<li class="text-center">{contact.label}: {contact.contactInformation}</li>
						{/each}
					{/if}
				</ul>
			</div>
		</div>
	</div>
</section>