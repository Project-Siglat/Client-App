<div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-black text-white">
  <!-- Header -->
  <?php include "./pages/landing-page/components/topbar.php"; ?>

  <!-- Hero Section -->
  <section class="py-24 px-6 bg-gradient-to-b from-transparent via-red-950/10 to-transparent">
    <div class="container mx-auto text-center">
      <h2 class="text-6xl font-bold mb-6 text-white leading-tight">Emergency Response System</h2>
      <p class="text-xl mb-10 text-gray-300 max-w-2xl mx-auto leading-relaxed">
        SIGLAT provides immediate emergency rescue services 24/7 in Villaverde, Nueva Vizcaya. When disaster strikes, we're here to help.
      </p>
      <div class="space-x-4">
        <button class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-4 px-10 rounded-xl transition-all duration-300 shadow-lg hover:shadow-red-500/30">
          Emergency Alert
        </button>
        <button class="border-2 border-gradient-to-r border-yellow-500 text-yellow-400 hover:bg-gradient-to-r hover:from-yellow-500/10 hover:to-red-500/10 hover:text-yellow-300 font-semibold py-4 px-10 rounded-xl transition-all duration-300">
          Learn More
        </button>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="py-20 px-6 bg-gradient-to-b from-gray-900/50 via-black to-gray-900/50">
    <div class="container mx-auto">
      <h3 class="text-4xl font-bold text-center mb-16 text-white">Why Choose SIGLAT</h3>
      <div class="grid md:grid-cols-3 gap-8">
        <div class="text-center p-8 bg-gradient-to-b from-gray-800/50 to-black/50 rounded-2xl border border-yellow-500/20 hover:border-yellow-500/40 transition-all duration-300 backdrop-blur-sm">
          <div class="text-yellow-400 text-5xl mb-6">🚨</div>
          <h4 class="text-xl font-semibold mb-4 text-gray-100">Rapid Response</h4>
          <p class="text-gray-400 leading-relaxed">Lightning-fast emergency response to save lives and protect property in critical situations.</p>
        </div>
        <div class="text-center p-8 bg-gradient-to-b from-gray-800/50 to-black/50 rounded-2xl border border-red-500/20 hover:border-red-500/40 transition-all duration-300 backdrop-blur-sm">
          <div class="text-red-400 text-5xl mb-6">🏥</div>
          <h4 class="text-xl font-semibold mb-4 text-gray-100">Professional Rescue</h4>
          <p class="text-gray-400 leading-relaxed">Trained emergency professionals equipped to handle any crisis in Villaverde, Nueva Vizcaya.</p>
        </div>
        <div class="text-center p-8 bg-gradient-to-b from-gray-800/50 to-black/50 rounded-2xl border border-yellow-500/20 hover:border-yellow-500/40 transition-all duration-300 backdrop-blur-sm">
          <div class="text-yellow-400 text-5xl mb-6">🕐</div>
          <h4 class="text-xl font-semibold mb-4 text-gray-100">24/7 Availability</h4>
          <p class="text-gray-400 leading-relaxed">Round-the-clock emergency services - we're always ready to respond when you need us most.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-20 px-6 bg-gradient-to-r from-red-900/30 via-red-800/40 to-red-900/30">
    <div class="container mx-auto text-center">
      <h3 class="text-4xl font-bold mb-6 text-white">Need Emergency Assistance?</h3>
      <p class="text-xl mb-10 text-gray-300 leading-relaxed">SIGLAT is here to help residents of Villaverde, Nueva Vizcaya in times of crisis.</p>
      <button class="bg-gradient-to-r from-black via-gray-900 to-black hover:from-gray-900 hover:to-black text-yellow-400 font-semibold py-4 px-10 rounded-xl transition-all duration-300 border border-yellow-500/30 hover:border-yellow-500/60 shadow-lg">
        Contact Emergency Services
      </button>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gradient-to-r from-black via-gray-900 to-black py-12 px-6 border-t border-gray-800">
    <div class="container mx-auto text-center">
      <p class="text-gray-500">&copy; 2024 SIGLAT Emergency Services. All rights reserved.</p>
    </div>
  </footer>
</div>

<script>
function toggleMobileMenu() {
  const mobileMenu = document.getElementById('mobileMenu');
  const hamburger = document.getElementById('hamburger');
  const close = document.getElementById('close');
  const menuButton = document.getElementById('mobileMenuButton');
  const menuLinks = mobileMenu.querySelectorAll('nav a');

  const isOpen = mobileMenu.classList.contains('max-h-0');

  if (isOpen) {
    // Open menu
    mobileMenu.classList.remove('max-h-0');
    mobileMenu.classList.add('max-h-96');
    hamburger.classList.add('hidden');
    close.classList.remove('hidden');
    menuButton.querySelector('svg').classList.add('rotate-90');

    // Animate menu items
    menuLinks.forEach((link, index) => {
      setTimeout(() => {
        link.classList.remove('translate-y-2', 'opacity-0');
        link.classList.add('translate-y-0', 'opacity-100');
      }, index * 50);
    });
  } else {
    // Close menu
    mobileMenu.classList.remove('max-h-96');
    mobileMenu.classList.add('max-h-0');
    hamburger.classList.remove('hidden');
    close.classList.add('hidden');
    menuButton.querySelector('svg').classList.remove('rotate-90');

    // Reset menu items
    menuLinks.forEach(link => {
      link.classList.remove('translate-y-0', 'opacity-100');
      link.classList.add('translate-y-2', 'opacity-0');
    });
  }
}
</script>
