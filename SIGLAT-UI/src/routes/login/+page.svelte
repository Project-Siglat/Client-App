<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';

	function handleHomeClick() {
		console.log('Home button clicked');
		try {
			goto('/');
		} catch (error) {
			console.error('goto failed:', error);
			window.location.href = '/';
		}
	}

	let activeTab = 'login';
	let showLoginPassword = false;
	let showPassword = false;
	let showConfirmPassword = false;
	let isLoading = false;
	let errorMessage = '';
	let successMessage = '';

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
		errorMessage = '';
		successMessage = '';
	}

	// Check for existing session on page load
	onMount(() => {
		const token = sessionStorage.getItem('token');
		
		if (token) {
			try {
				// Decode JWT to get role and check if token is valid
				const payload = JSON.parse(atob(token.split('.')[1]));
				const role = payload.role;
				const expiry = payload.exp * 1000; // Convert to milliseconds
				
				// Check if token is not expired
				if (Date.now() < expiry) {
					// Token is valid, redirect based on role
					if (role === 'Admin') {
						goto('/siglat');
					} else if (role === 'User') {
						goto('/client');
					} else {
						goto('/');
					}
					return;
				} else {
					// Token expired, remove it
					sessionStorage.removeItem('token');
				}
			} catch (error) {
				// Invalid token, remove it
				console.error('Invalid token found:', error);
				sessionStorage.removeItem('token');
			}
		}
	});

	async function handleRegister(event: Event) {
		event.preventDefault();
		isLoading = true;
		errorMessage = '';
		
		// Validate passwords match
		if (registerForm.password !== registerForm.confirmPassword) {
			errorMessage = 'Passwords do not match';
			isLoading = false;
			return;
		}

		// Validate password length
		if (registerForm.password.length < 6) {
			errorMessage = 'Password must be at least 6 characters long';
			isLoading = false;
			return;
		}

		try {
			const response = await fetch('http://localhost:5000/api/v1.0/Auth/register', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					firstName: registerForm.firstName,
					lastName: registerForm.lastName,
					middleName: registerForm.middleName || '',
					address: registerForm.address,
					gender: registerForm.gender,
					dateOfBirth: registerForm.dateOfBirth,
					phoneNumber: registerForm.phoneNumber,
					email: registerForm.email,
					hashPass: registerForm.password
				})
			});

			if (response.ok) {
				successMessage = 'Registration successful! Please login with your credentials.';
				
				// Reset form
				registerForm = {
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
				
				// Switch to login tab after 2 seconds
				setTimeout(() => {
					switchTab('login');
				}, 2000);
			} else {
				const errorData = await response.text();
				try {
					const errorJson = JSON.parse(errorData);
					if (errorJson.errors && errorJson.errors.Password) {
						errorMessage = errorJson.errors.Password[0];
					} else {
						errorMessage = errorJson.title || 'Registration failed. Please try again.';
					}
				} catch {
					errorMessage = errorData || 'Registration failed. Please try again.';
				}
			}
		} catch (error) {
			errorMessage = 'Network error. Please check your connection and try again.';
			console.error('Registration error:', error);
		} finally {
			isLoading = false;
		}
	}

	async function handleLogin(event: Event) {
		event.preventDefault();
		isLoading = true;
		errorMessage = '';
		
		// Validate password length for login too
		if (loginForm.password.length < 6) {
			errorMessage = 'Password must be at least 6 characters long';
			isLoading = false;
			return;
		}
		
		try {
			const response = await fetch('http://localhost:5000/api/v1.0/Auth/login', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					email: loginForm.email,
					password: loginForm.password
				})
			});

			if (response.ok) {
				const data = await response.json();
				
				// Store token in sessionStorage
				sessionStorage.setItem('token', data.token);
				
				// Decode JWT to get role
				const payload = JSON.parse(atob(data.token.split('.')[1]));
				const role = payload.role;
				
				successMessage = 'Login successful! Redirecting...';
				
				// Redirect based on role
				setTimeout(() => {
					if (role === 'Admin') {
						goto('/siglat');
					} else if (role === 'User') {
						goto('/client');
					} else {
						goto('/');
					}
				}, 1500);
			} else {
				const errorData = await response.text();
				errorMessage = errorData || 'Login failed. Please check your credentials.';
			}
		} catch (error) {
			errorMessage = 'Network error. Please check your connection and try again.';
			console.error('Login error:', error);
		} finally {
			isLoading = false;
		}
	}
</script>

<div class="min-h-screen bg-white text-gray-800 relative overflow-hidden">
	<!-- Back to Home Button -->
	<div class="fixed top-8 left-8 z-50">
		<button 
			on:click={handleHomeClick} 
			class="flex items-center gap-2 text-gray-800 hover:text-red-600 transition-colors bg-white/80 backdrop-blur-sm px-3 py-2 rounded-lg shadow-md border border-gray-200"
			type="button"
		>
			<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
			</svg>
			<span>Home</span>
		</button>
	</div>

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
			<!-- Error/Success Messages -->
			{#if errorMessage}
				<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
					<p class="text-red-700 text-sm">{errorMessage}</p>
				</div>
			{/if}
			
			{#if successMessage}
				<div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
					<p class="text-green-700 text-sm">{successMessage}</p>
				</div>
			{/if}
			
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
							placeholder="Enter your password (min 6 characters)"
							minlength="6"
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
					<button type="submit" disabled={isLoading} class="w-full py-3 px-4 bg-red-600 text-white border-none rounded-lg text-base font-semibold cursor-pointer transition-all hover:bg-red-700 hover:-translate-y-0.5 hover:shadow-lg mt-2 disabled:opacity-50 disabled:cursor-not-allowed">
						{isLoading ? 'Signing In...' : 'Sign In'}
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
							placeholder="Create a secure password (min 6 characters)"
							minlength="6"
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
						<p class="text-xs text-gray-600 mt-1">Password must be at least 6 characters long</p>
					</div>
					<div>
						<label for="confirmPassword" class="block text-sm font-medium text-gray-800 mb-2">Confirm Password</label>
						<input
							type={showConfirmPassword ? 'text' : 'password'}
							id="confirmPassword"
							bind:value={registerForm.confirmPassword}
							placeholder="Confirm your password (min 6 characters)"
							minlength="6"
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
					<button type="submit" disabled={isLoading} class="w-full py-3 px-4 bg-red-600 text-white border-none rounded-lg text-base font-semibold cursor-pointer transition-all hover:bg-red-700 hover:-translate-y-0.5 hover:shadow-lg mt-2 disabled:opacity-50 disabled:cursor-not-allowed">
						{isLoading ? 'Creating Account...' : 'Create Account'}
					</button>
				</form>
			{/if}
		</div>
	</section>
</div>