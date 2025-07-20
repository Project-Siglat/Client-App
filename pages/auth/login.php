<?php
include "./config/env.php";

$API = $_ENV["API"];

// Get the 'what' parameter from URL
$what = isset($_GET["what"]) ? $_GET["what"] : "login";
$isRegister = $what === "register";
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-gray-900 to-slate-900 text-white relative overflow-hidden flex items-center justify-center">
  <!-- Back to Home Button -->
  <a href="/" class="absolute top-4 left-4 sm:top-6 sm:left-6 z-20 flex items-center gap-1 sm:gap-2 px-2 sm:px-4 py-2 bg-gray-800/60 hover:bg-gray-700/80 border border-gray-600/30 rounded-xl transition-all duration-300 text-gray-300 hover:text-white backdrop-blur-lg">
    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
    </svg>
    <span class="font-medium text-sm sm:text-base">Home</span>
  </a>

  <!-- Toast Container -->
  <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

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
          <button id="loginTab" class="flex-1 py-2 sm:py-3 px-2 sm:px-4 text-center font-semibold rounded-xl transition-all duration-300 <?php echo !$isRegister
              ? "bg-gradient-to-r from-yellow-600 to-yellow-700 text-white shadow-lg"
              : "text-gray-300 hover:text-white hover:bg-gray-700/30"; ?> text-sm sm:text-base" role="tab" aria-selected="<?php echo !$isRegister
     ? "true"
     : "false"; ?>">
            Login
          </button>
          <button id="registerTab" class="flex-1 py-2 sm:py-3 px-2 sm:px-4 text-center font-semibold rounded-xl transition-all duration-300 <?php echo $isRegister
              ? "bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg"
              : "text-gray-300 hover:text-white hover:bg-gray-700/30"; ?> text-sm sm:text-base" role="tab" aria-selected="<?php echo $isRegister
     ? "true"
     : "false"; ?>">
            Register
          </button>
        </div>
      </div>

      <!-- Tab Content -->
      <div id="loginPanel" class="mt-4 sm:mt-6 bg-gradient-to-b from-gray-800/40 to-gray-900/40 p-6 sm:p-8 rounded-2xl backdrop-blur-lg border border-gray-600/20 <?php echo $isRegister
          ? "hidden"
          : ""; ?>" role="tabpanel">
        <form id="loginForm" class="space-y-4">
          <input type="email" id="loginEmail" placeholder="Email Address" class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-yellow-500 focus:outline-none transition-colors">
          <input type="password" id="loginPassword" placeholder="Password" class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-yellow-500 focus:outline-none transition-colors">
          <button type="submit" class="w-full bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-500 hover:to-yellow-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-yellow-500/40 transform hover:-translate-y-1">
            Sign In
          </button>
        </form>
      </div>

      <div id="registerPanel" class="mt-4 sm:mt-6 bg-gradient-to-b from-gray-800/40 to-gray-900/40 p-4 sm:p-6 rounded-2xl backdrop-blur-lg border border-gray-600/20 <?php echo !$isRegister
          ? "hidden"
          : ""; ?>" role="tabpanel">
        <form id="registerForm" class="space-y-3">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <input type="text" id="firstName" placeholder="First Name" class="px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
            <input type="text" id="lastName" placeholder="Last Name" class="px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
          </div>
          <input type="text" id="middleName" placeholder="Middle Name (Optional)" class="w-full px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
          <input type="text" id="address" placeholder="Address" class="w-full px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <select id="gender" class="px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white focus:border-red-500 focus:outline-none transition-colors text-sm">
              <option value="" disabled selected class="text-gray-400">Gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
            <input type="date" id="dateOfBirth" placeholder="Birthdate" class="px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
          </div>
          <input type="tel" id="phoneNumber" placeholder="Phone Number" class="w-full px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
          <input type="email" id="email" placeholder="Email Address" class="w-full px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
          <input type="password" id="password" placeholder="Password" class="w-full px-3 py-2.5 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition-colors text-sm">
          <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-red-500/40 transform hover:-translate-y-1 mt-4 text-sm sm:text-base">
            Create Account
          </button>
        </form>
      </div>
    </div>
  </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const registerTab = $('#registerTab');
    const loginTab = $('#loginTab');
    const registerPanel = $('#registerPanel');
    const loginPanel = $('#loginPanel');

    // Toast function
    function showToast(message, type = 'success') {
        const toastId = 'toast-' + Date.now();
        const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        const toast = $(`
            <div id="${toastId}" class="flex items-center p-4 mb-4 text-white rounded-lg ${bgColor} shadow-lg transform transition-all duration-300 translate-x-full">
                <div class="ml-3 text-sm font-medium">${message}</div>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 text-white hover:text-gray-300 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8" onclick="$(this).parent().fadeOut()">
                    <span class="sr-only">Close</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        `);

        $('#toastContainer').append(toast);
        setTimeout(() => {
            toast.removeClass('translate-x-full');
        }, 100);

        setTimeout(() => {
            toast.fadeOut(() => {
                toast.remove();
            });
        }, 5000);
    }

    // Function to update URL parameter
    function updateURLParam(param, value) {
        const url = new URL(window.location);
        url.searchParams.set(param, value);
        window.history.pushState({}, '', url);
    }

    registerTab.on('click', function() {
        // Update URL parameter
        updateURLParam('what', 'register');

        // Update tabs
        registerTab.addClass('bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg');
        registerTab.removeClass('text-gray-300 hover:text-white hover:bg-gray-700/30');
        loginTab.removeClass('bg-gradient-to-r from-yellow-600 to-yellow-700 text-white shadow-lg');
        loginTab.addClass('text-gray-300 hover:text-white hover:bg-gray-700/30');

        // Update panels
        registerPanel.removeClass('hidden');
        loginPanel.addClass('hidden');

        // Update ARIA
        registerTab.attr('aria-selected', 'true');
        loginTab.attr('aria-selected', 'false');
    });

    loginTab.on('click', function() {
        // Update URL parameter
        updateURLParam('what', 'login');

        // Update tabs
        loginTab.addClass('bg-gradient-to-r from-yellow-600 to-yellow-700 text-white shadow-lg');
        loginTab.removeClass('text-gray-300 hover:text-white hover:bg-gray-700/30');
        registerTab.removeClass('bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg');
        registerTab.addClass('text-gray-300 hover:text-white hover:bg-gray-700/30');

        // Update panels
        loginPanel.removeClass('hidden');
        registerPanel.addClass('hidden');

        // Update ARIA
        loginTab.attr('aria-selected', 'true');
        registerTab.attr('aria-selected', 'false');
    });

    // Handle registration form submission
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();

        const registrationData = {
            id: '00000000-0000-0000-0000-000000000000',
            firstName: $('#firstName').val(),
            middleName: $('#middleName').val() || '',
            lastName: $('#lastName').val(),
            address: $('#address').val(),
            gender: $('#gender').val(),
            phoneNumber: $('#phoneNumber').val(),
            role: 'User',
            dateOfBirth: $('#dateOfBirth').val(),
            email: $('#email').val(),
            hashPass: $('#password').val(),
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString()
        };

        $.ajax({
            url: '<?php echo $API; ?>/api/v1/Auth/register',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(registrationData),
            success: function(response) {
                showToast('Registration successful!', 'success');
                $('#registerForm')[0].reset();

                // Switch to login tab after successful registration
                setTimeout(() => {
                    loginTab.click();
                }, 1000);
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Registration failed';
                if (xhr.responseText) {
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        errorMessage = errorResponse.message || errorMessage;
                    } catch (e) {
                        errorMessage = xhr.responseText;
                    }
                }
                showToast(errorMessage, 'error');
                console.error('Registration error:', xhr.responseText);
            }
        });
    });

    // Handle login form submission
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();

        const loginData = {
            email: $('#loginEmail').val(),
            password: $('#loginPassword').val()
        };

        $.ajax({
            url: '<?php echo $API; ?>/api/v1/Auth/login',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(loginData),
            success: function(response) {
                showToast('Login successful!', 'success');

                // Store token in session storage if provided in response
                if (response) {
                    sessionStorage.setItem('token', response);
                }

                // Redirect to /client after successful login
                setTimeout(() => {
                    window.location.href = '/client';
                }, 1000);
            },
            error: function(xhr, status, error) {
                showToast('Credentials are invalid', 'error');
                console.error('Login error:', xhr.responseText);
            }
        });
    });
});
</script>
