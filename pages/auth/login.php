<div class="min-h-screen bg-gradient-to-br from-slate-900 via-gray-900 to-slate-900 text-white relative overflow-hidden flex items-center justify-center">
  <!-- Back to Home Button -->
  <a href="/" class="absolute top-4 left-4 sm:top-6 sm:left-6 z-20 flex items-center gap-1 sm:gap-2 px-2 sm:px-4 py-2 bg-gray-800/60 hover:bg-gray-700/80 border border-gray-600/30 rounded-xl transition-all duration-300 text-gray-300 hover:text-white backdrop-blur-lg">
    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
    </svg>
    <span class="font-medium text-sm sm:text-base">Home</span>
  </a>

  <!-- Animated background elements -->
  <div class="absolute inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-1/4 left-1/4 w-64 h-64 sm:w-96 sm:h-96 bg-red-500/10 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-1/4 right-1/4 w-64 h-64 sm:w-96 sm:h-96 bg-yellow-500/10 rounded-full blur-3xl animate-pulse delay-700"></div>
    <div class="absolute top-1/2 left-1/2 w-48 h-48 sm:w-64 sm:h-64 bg-blue-500/5 rounded-full blur-3xl animate-pulse delay-1000"></div>
  </div>

  <!-- Login/Register Section -->
  <section class="px-4 sm:px-6 relative z-10 w-full max-w-sm sm:max-w-lg">
    <div class="text-center mb-6 sm:mb-8">
      <h2 class="text-3xl sm:text-4xl md:text-5xl font-black mb-4 leading-tight bg-gradient-to-r from-white via-gray-100 to-white bg-clip-text text-transparent">
        SIGLAT
      </h2>
      <p class="text-sm sm:text-base text-gray-300 leading-relaxed">
        Emergency Response System
      </p>
    </div>

    <!-- Registration/Login Tabs -->
    <div class="w-full">
      <div class="bg-gradient-to-r from-gray-800/60 to-gray-900/60 p-1.5 sm:p-2 rounded-2xl backdrop-blur-lg border border-gray-600/30">
        <div class="flex" role="tablist">
          <button id="registerTab" class="flex-1 py-2 sm:py-3 px-2 sm:px-4 text-center font-semibold rounded-xl transition-all duration-300 bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg text-sm sm:text-base" role="tab" aria-selected="true">
            Register
          </button>
          <button id="loginTab" class="flex-1 py-2 sm:py-3 px-2 sm:px-4 text-center font-semibold rounded-xl transition-all duration-300 text-gray-300 hover:text-white hover:bg-gray-700/30 text-sm sm:text-base" role="tab" aria-selected="false">
            Login
          </button>
        </div>
      </div>

      <!-- Tab Content -->
      <div id="registerPanel" class="mt-4 sm:mt-6 bg-gradient-to-b from-gray-800/40 to-gray-900/40 p-4 sm:p-6 rounded-2xl backdrop-blur-lg border border-gray-600/20" role="tabpanel">
        <form class="space-y-3">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <input type="text" placeholder="First Name" class="px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
            <input type="text" placeholder="Last Name" class="px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
          </div>
          <input type="text" placeholder="Middle Name (Optional)" class="w-full px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <select class="px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white focus:border-red-500 focus:outline-none transition-colors text-sm">
              <option value="" disabled selected class="text-gray-400">Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
            <input type="date" placeholder="Birthdate" class="px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
          </div>
          <input type="email" placeholder="Email Address" class="w-full px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
          <input type="password" placeholder="Password" class="w-full px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
          <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-red-500/40 transform hover:-translate-y-1 mt-4 text-sm sm:text-base">
            Create Account
          </button>
        </form>
      </div>

      <div id="loginPanel" class="mt-4 sm:mt-6 bg-gradient-to-b from-gray-800/40 to-gray-900/40 p-6 sm:p-8 rounded-2xl backdrop-blur-lg border border-gray-600/20 hidden" role="tabpanel">
        <form class="space-y-4">
          <input type="email" placeholder="Email Address" class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-yellow-500 focus:outline-none transition-colors">
          <input type="password" placeholder="Password" class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-yellow-500 focus:outline-none transition-colors">
          <button type="submit" class="w-full bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-500 hover:to-yellow-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-yellow-500/40 transform hover:-translate-y-1">
            Sign In
          </button>
        </form>
      </div>
    </div>
  </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerTab = document.getElementById('registerTab');
    const loginTab = document.getElementById('loginTab');
    const registerPanel = document.getElementById('registerPanel');
    const loginPanel = document.getElementById('loginPanel');

    registerTab.addEventListener('click', function() {
        // Update tabs
        registerTab.classList.add('bg-gradient-to-r', 'from-red-600', 'to-red-700', 'text-white', 'shadow-lg');
        registerTab.classList.remove('text-gray-300', 'hover:text-white', 'hover:bg-gray-700/30');
        loginTab.classList.remove('bg-gradient-to-r', 'from-yellow-600', 'to-yellow-700', 'text-white', 'shadow-lg');
        loginTab.classList.add('text-gray-300', 'hover:text-white', 'hover:bg-gray-700/30');

        // Update panels
        registerPanel.classList.remove('hidden');
        loginPanel.classList.add('hidden');

        // Update ARIA
        registerTab.setAttribute('aria-selected', 'true');
        loginTab.setAttribute('aria-selected', 'false');
    });

    loginTab.addEventListener('click', function() {
        // Update tabs
        loginTab.classList.add('bg-gradient-to-r', 'from-yellow-600', 'to-yellow-700', 'text-white', 'shadow-lg');
        loginTab.classList.remove('text-gray-300', 'hover:text-white', 'hover:bg-gray-700/30');
        registerTab.classList.remove('bg-gradient-to-r', 'from-red-600', 'to-red-700', 'text-white', 'shadow-lg');
        registerTab.classList.add('text-gray-300', 'hover:text-white', 'hover:bg-gray-700/30');

        // Update panels
        loginPanel.classList.remove('hidden');
        registerPanel.classList.add('hidden');

        // Update ARIA
        loginTab.setAttribute('aria-selected', 'true');
        registerTab.setAttribute('aria-selected', 'false');
    });
});
</script>
