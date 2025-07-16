<div class="bg-white min-h-screen relative px-4">
  <a href="/" class="absolute top-4 left-4 inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-xl shadow-lg">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
    Go Home
  </a>

  <div class="flex flex-col justify-center items-center min-h-screen">
    <div class="max-w-md w-full text-center">
      <div class="mb-8">
        <h1 class="text-8xl font-bold text-gray-800 mb-4 drop-shadow-lg">404</h1>
        <div class="w-24 h-2 bg-gradient-to-r from-blue-500 to-green-500 mx-auto rounded-full shadow-lg"></div>
      </div>

      <h2 class="text-3xl font-bold text-gray-700 mb-4">Page Not Found</h2>
      <p class="text-gray-600 text-lg mb-6">The page you're looking for doesn't exist.</p>
      <p class="text-sm text-gray-500 mb-8 break-all font-mono bg-gray-100 p-3 rounded-lg border" id="current-url"></p>
    </div>
  </div>

  <script>
    document.getElementById('current-url').textContent = window.location.href;
  </script>
</div>
