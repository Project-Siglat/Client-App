<script lang="ts">
	import { onMount } from 'svelte';

	let activeTab = 'login';
	let showLoginPassword = false;
	let showPassword = false;
	let showConfirmPassword = false;

	let loginForm = {
		email: '',
		password: ''
	};

	let registerForm = {
		firstName: '',
		lastName: '',
		middleName: '',
		address: '',
		gender: '',
		dateOfBirth: '',
		phoneNumber: '',
		email: '',
		password: '',
		confirmPassword: ''
	};

	function switchTab(tab: string) {
		activeTab = tab;
	}

	function handleLogin(event: Event) {
		event.preventDefault();
		console.log('Login form:', loginForm);
		// Handle login logic here
	}

	function handleRegister(event: Event) {
		event.preventDefault();
		console.log('Register form:', registerForm);
		// Handle register logic here
	}
</script>

<div class="min-h-screen bg-white text-gray-800 relative overflow-hidden">
	<!-- Back to Home Button -->
	<a href="/" class="fixed top-8 left-8 flex items-center gap-2 text-gray-800 hover:text-red-600 transition-colors z-10">
		<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
		</svg>
		<span>Home</span>
	</a>

	<!-- Animated background elements -->
	<div class="fixed inset-0 pointer-events-none z-0">
		<div class="absolute w-80 h-80 bg-red-600 opacity-10 rounded-full -top-40 -right-40 animate-pulse"></div>
		<div class="absolute w-60 h-60 bg-red-600 opacity-10 rounded-full -bottom-30 -left-30 animate-pulse delay-150"></div>
		<div class="absolute w-40 h-40 bg-red-600 opacity-10 rounded-full top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 animate-pulse delay-300"></div>
	</div>

	<!-- Login/Register Section -->
	<section class="relative min-h-screen flex flex-col items-center justify-center px-8 py-24 z-10">
		<div class="text-center mb-8">
			<h2 class="text-5xl font-bold text-red-600 mb-2">SIGLAT</h2>
			<p class="text-lg text-gray-800">Emergency Response System</p>
		</div>

		<!-- Registration/Login Tabs -->
		<div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-2xl border border-gray-200">
			<div class="mb-6">
				<div class="flex bg-gray-200 rounded-lg p-1 relative">
					<div class="absolute h-[calc(100%-8px)] w-[calc(50%-4px)] bg-red-600 rounded-md transition-transform duration-300 top-1 {activeTab === 'register' ? 'translate-x-[calc(100%+4px)]' : 'translate-x-1'}"></div>
					<button
						class="flex-1 py-3 px-4 text-sm font-medium rounded-md transition-colors relative z-10 {activeTab === 'login' ? 'text-white' : 'text-gray-800'}"
						on:click={() => switchTab('login')}
					>
						Login
					</button>
					<button
						class="flex-1 py-3 px-4 text-sm font-medium rounded-md transition-colors relative z-10 {activeTab === 'register' ? 'text-white' : 'text-gray-800'}"
						on:click={() => switchTab('register')}
					>
						Register
					</button>
				</div>
			</div>

			<!-- Login Panel -->
			{#if activeTab === 'login'}
				<form on:submit={handleLogin} class="space-y-5">
					<div>
						<label for="loginEmail" class="block text-sm font-medium text-gray-800 mb-2">Email Address</label>
						<input
							type="email"
							id="loginEmail"
							bind:value={loginForm.email}
							placeholder="Enter your email address"
							class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-gray-900 text-sm transition-all focus:border-red-600 focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-red-100"
							required
						/>
					</div>
					<div>
						<label for="loginPassword" class="block text-sm font-medium text-gray-800 mb-2">Password</label>
						<input
							type={showLoginPassword ? 'text' : 'password'}
							id="loginPassword"
							bind:value={loginForm.password}
							placeholder="Enter your password"
							class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-gray-900 text-sm transition-all focus:border-red-600 focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-red-100"
							required
						/>
						<div class="flex items-center gap-2 mt-1">
							<input
								type="checkbox"
								id="showLoginPassword"
								bind:checked={showLoginPassword}
								class="w-4 h-4 bg-white border-2 border-gray-300 rounded cursor-pointer"
							/>
							<label for="showLoginPassword" class="text-sm text-gray-800 cursor-pointer">Show password</label>
						</div>
					</div>
					<button type="submit" class="w-full py-3 px-4 bg-red-600 text-white border-none rounded-lg text-base font-semibold cursor-pointer transition-all hover:bg-red-700 hover:-translate-y-0.5 hover:shadow-lg mt-2">
						Sign In
					</button>
					<button type="button" class="w-full py-2 px-4 bg-transparent text-red-600 border border-red-600 rounded-lg text-sm font-medium cursor-pointer transition-all hover:bg-red-600 hover:text-white hover:-translate-y-0.5 mt-1">
						Forgot Password?
					</button>
				</form>
			{/if}

			<!-- Register Panel -->
			{#if activeTab === 'register'}
				<form on:submit={handleRegister} class="space-y-5">
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<label for="firstName" class="block text-sm font-medium text-gray-800 mb-2">First Name</label>
							<input
								type="text"
								id="firstName"
								bind:value={registerForm.firstName}
								placeholder="Enter your first name"
								class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-gray-900 text-sm transition-all focus:border-red-600 focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-red-100"
								required
							/>
						</div>
						<div>
							<label for="lastName" class="block text-sm font-medium text-gray-800 mb-2">Last Name</label>
							<input
								type="text"
								id="lastName"
								bind:value={registerForm.lastName}
								placeholder="Enter your last name"
								class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-gray-900 text-sm transition-all focus:border-red-600 focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-red-100"
								required
							/>
						</div>
					</div>
					<div>
						<label for="middleName" class="block text-sm font-medium text-gray-800 mb-2">Middle Name (Optional)</label>
						<input
							type="text"
							id="middleName"
							bind:value={registerForm.middleName}
							placeholder="Enter your middle name"
							class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-gray-900 text-sm transition-all focus:border-red-600 focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-red-100"
						/>
					</div>
					<div>
						<label for="address" class="block text-sm font-medium text-gray-800 mb-2">Address</label>
						<input
							type="text"
							id="address"
							bind:value={registerForm.address}
							placeholder="Enter your complete address"
							class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-gray-900 text-sm transition-all focus:border-red-600 focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-red-100"
							required
						/>
					</div>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<label for="gender" class="block text-sm font-medium text-gray-800 mb-2">Gender</label>
							<select
								id="gender"
								bind:value={registerForm.gender}
								class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-gray-900 text-sm cursor-pointer transition-all focus:border-red-600 focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-red-100"
								required
							>
								<option value="" disabled>Select your gender</option>
								<option value="Male">Male</option>
								<option value="Female">Female</option>
							</select>
						</div>
						<div>
							<label for="dateOfBirth" class="block text-sm font-medium text-gray-800 mb-2">Birthdate</label>
							<input
								type="date"
								id="dateOfBirth"
								bind:value={registerForm.dateOfBirth}
								class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-gray-900 text-sm transition-all focus:border-red-600 focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-red-100"
								required
							/>
						</div>
					</div>
					<div>
						<label for="phoneNumber" class="block text-sm font-medium text-gray-800 mb-2">Phone Number</label>
						<input
							type="tel"
							id="phoneNumber"
							bind:value={registerForm.phoneNumber}
							placeholder="Enter your phone number"
							class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-gray-900 text-sm transition-all focus:border-red-600 focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-red-100"
							required
						/>
					</div>
					<div>
						<label for="email" class="block text-sm font-medium text-gray-800 mb-2">Email Address</label>
						<input
							type="email"
							id="email"
							bind:value={registerForm.email}
							placeholder="Enter your email address"
							class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-gray-900 text-sm transition-all focus:border-red-600 focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-red-100"
							required
						/>
					</div>
					<div>
						<label for="password" class="block text-sm font-medium text-gray-800 mb-2">Password</label>
						<input
							type={showPassword ? 'text' : 'password'}
							id="password"
							bind:value={registerForm.password}
							placeholder="Create a secure password"
							class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-gray-900 text-sm transition-all focus:border-red-600 focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-red-100"
							required
						/>
						<div class="flex items-center gap-2 mt-1">
							<input
								type="checkbox"
								id="showPassword"
								bind:checked={showPassword}
								class="w-4 h-4 bg-white border-2 border-gray-300 rounded cursor-pointer"
							/>
							<label for="showPassword" class="text-sm text-gray-800 cursor-pointer">Show password</label>
						</div>
					</div>
					<div>
						<label for="confirmPassword" class="block text-sm font-medium text-gray-800 mb-2">Confirm Password</label>
						<input
							type={showConfirmPassword ? 'text' : 'password'}
							id="confirmPassword"
							bind:value={registerForm.confirmPassword}
							placeholder="Confirm your password"
							class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-gray-900 text-sm transition-all focus:border-red-600 focus:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-red-100"
							required
						/>
						<div class="flex items-center gap-2 mt-1">
							<input
								type="checkbox"
								id="showConfirmPassword"
								bind:checked={showConfirmPassword}
								class="w-4 h-4 bg-white border-2 border-gray-300 rounded cursor-pointer"
							/>
							<label for="showConfirmPassword" class="text-sm text-gray-800 cursor-pointer">Show password</label>
						</div>
					</div>
					<button type="submit" class="w-full py-3 px-4 bg-red-600 text-white border-none rounded-lg text-base font-semibold cursor-pointer transition-all hover:bg-red-700 hover:-translate-y-0.5 hover:shadow-lg mt-2">
						Create Account
					</button>
				</form>
			{/if}
		</div>
	</section>
</div>