  <header class="sticky top-0 z-50 bg-gradient-to-r from-black via-red-950 to-black backdrop-blur-lg border-b border-yellow-500/20">
    <div class="container mx-auto px-4 sm:px-6 py-2">
      <div class="flex items-center justify-between">
        <h1 class="text-lg sm:text-xl font-bold text-white tracking-wide">SIGLAT</h1>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center space-x-6">
          <nav class="flex space-x-6">
            <a href="/" class="text-gray-300 hover:text-yellow-400 transition-all duration-300 font-medium">Home</a>
            <a href="/contact" class="text-gray-300 hover:text-yellow-400 transition-all duration-300 font-medium">Contact</a>
          </nav>
          <a href="/login" class="bg-yellow-500 hover:bg-yellow-600 text-black font-medium px-3 py-1.5 rounded transition-all duration-300">Login / Register</a>
        </div>

        <!-- Mobile Menu Button -->
        <button id="mobileMenuButton" class="md:hidden text-gray-300 hover:text-yellow-400 transition-all duration-300" onclick="toggleMobileMenu()">
          <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path id="hamburger" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            <path id="close" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" class="hidden"></path>
          </svg>
        </button>
      </div>

      <!-- Mobile Navigation Menu -->
      <div id="mobileMenu" class="md:hidden overflow-hidden max-h-0 transition-all duration-300 ease-in-out">
        <div class="mt-2 pb-2 border-t border-gray-700">
          <nav class="flex flex-col space-y-2 pt-2">
            <a href="/" class="text-gray-300 hover:text-yellow-400 transition-all duration-300 font-medium transform translate-y-2 opacity-0 transition-all duration-300 delay-75">Home</a>
            <a href="/contact" class="text-gray-300 hover:text-yellow-400 transition-all duration-300 font-medium transform translate-y-2 opacity-0 transition-all duration-300 delay-150">Contact</a>
            <a href="/login" class="bg-yellow-500 hover:bg-yellow-600 text-black font-medium px-3 py-1.5 rounded transition-all duration-300 text-center transform translate-y-2 opacity-0 transition-all duration-300 delay-200">Login / Register</a>
          </nav>
        </div>
      </div>
    </div>
  </header>
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
