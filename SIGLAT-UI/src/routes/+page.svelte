<script lang="ts">
	import HeroSection from '$lib/components/HeroSection.svelte';
	import EmergencyActions from '$lib/components/EmergencyActions.svelte';
	import StatusDashboard from '$lib/components/StatusDashboard.svelte';
	import ContactSection from '$lib/components/ContactSection.svelte';
	import MapSection from '$lib/components/MapSection.svelte';
	import EmergencySection from '$lib/components/EmergencySection.svelte';
	import Footer from '$lib/components/Footer.svelte';
</script>

<main class="font-sans bg-gray-50">
	<!-- 1. Header/Navigation + Hero -->
	<HeroSection />
	
	<!-- 2. Emergency Action Center - Primary CTAs -->
	<EmergencyActions />
	
	<!-- 3. Live Status Dashboard - System health and monitoring -->
	<StatusDashboard />
	
	<!-- 4. Emergency Contacts - Quick access to help -->
	<div id="emergency-contacts">
		<ContactSection />
	</div>
	
	<!-- 5. Interactive Map - Coverage and routing -->
	<MapSection />
	
	<!-- 6. Service Information - Hours and capabilities -->
	<EmergencySection />
	
	<!-- 7. Footer - Additional info -->
	<Footer />
</main>