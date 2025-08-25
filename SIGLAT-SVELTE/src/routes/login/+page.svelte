<script>
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';

	let activeTab = 'login';
	let loginForm = { email: '', password: '' };
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
	let showLoginPassword = false;
	let showPassword = false;
	let showConfirmPassword = false;
	let toasts = [];

	function switchTab(tab) {
		activeTab = tab;
	}

	function showToast(message, type = 'info') {
		const toast = {
			id: Date.now(),
			message,
			type
		};
		toasts = [...toasts, toast];
		
		setTimeout(() => {
			removeToast(toast.id);
		}, 5000);
	}

	function removeToast(id) {
		toasts = toasts.filter(toast => toast.id !== id);
	}

	async function handleLogin(event) {
		event.preventDefault();
		
		try {
			// API call to login endpoint
			const response = await fetch('http://localhost:5000/api/v1/Auth/login', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					email: loginForm.email,
					password: loginForm.password
				})
			});

			if (response.ok) {
				const data = await response.json();
				sessionStorage.setItem('authToken', data.token);
				showToast('Login successful!', 'success');
				
				// Redirect based on role
				setTimeout(() => {
					if (data.role === 'admin') {
						goto('/admin');
					} else if (data.role === 'ambulance') {
						goto('/ambulance');
					} else {
						goto('/client');
					}
				}, 1000);
			} else {
				const error = await response.text();
				showToast(error || 'Login failed', 'error');
			}
		} catch (error) {
			showToast('Network error. Please try again.', 'error');
		}
	}

	async function handleRegister(event) {
		event.preventDefault();
		
		if (registerForm.password !== registerForm.confirmPassword) {
			showToast('Passwords do not match', 'error');
			return;
		}

		try {
			const response = await fetch('http://localhost:5000/api/v1/Auth/register', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					firstName: registerForm.firstName,
					lastName: registerForm.lastName,
					middleName: registerForm.middleName,
					address: registerForm.address,
					gender: registerForm.gender,
					dateOfBirth: registerForm.dateOfBirth,
					phoneNumber: registerForm.phoneNumber,
					email: registerForm.email,
					password: registerForm.password
				})
			});

			if (response.ok) {
				showToast('Registration successful! Please login.', 'success');
				switchTab('login');
				// Clear form
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
			} else {
				const error = await response.text();
				showToast(error || 'Registration failed', 'error');
			}
		} catch (error) {
			showToast('Network error. Please try again.', 'error');
		}
	}
</script>

<svelte:head>
	<title>Login - Siglat Emergency System</title>
</svelte:head>

<div class="nord-container">
	<!-- Back to Home Button -->
	<a href="/" class="nord-back-button">
		<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="nord-icon">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
		</svg>
		<span>Home</span>
	</a>

	<!-- Toast Container -->
	<div class="nord-toast-container">
		{#each toasts as toast (toast.id)}
			<div class="nord-toast {toast.type}" on:click={() => removeToast(toast.id)}>
				{toast.message}
			</div>
		{/each}
	</div>

	<!-- Animated background elements -->
	<div class="nord-bg-elements">
		<div class="nord-bg-element"></div>
		<div class="nord-bg-element"></div>
		<div class="nord-bg-element"></div>
	</div>

	<!-- Login/Register Section -->
	<section class="nord-auth-section">
		<div class="nord-header">
			<h2 class="nord-title">SIGLAT</h2>
			<p class="nord-subtitle">Emergency Response System</p>
		</div>

		<!-- Registration/Login Tabs -->
		<div class="nord-tabs-container">
			<div class="nord-tabs">
				<div class="nord-tablist">
					<div class="nord-tab-indicator {activeTab === 'register' ? 'register' : ''}"></div>
					<button
						class="nord-tab"
						class:active={activeTab === 'login'}
						aria-selected={activeTab === 'login'}
						on:click={() => switchTab('login')}
					>
						Login
					</button>
					<button
						class="nord-tab"
						class:active={activeTab === 'register'}
						aria-selected={activeTab === 'register'}
						on:click={() => switchTab('register')}
					>
						Register
					</button>
				</div>
			</div>

			<!-- Tab Content -->
			{#if activeTab === 'login'}
				<div class="nord-panel">
					<form class="nord-form" on:submit={handleLogin}>
						<div class="nord-floating-label">
							<label for="loginEmail" class="nord-label">Email Address</label>
							<input
								type="email"
								id="loginEmail"
								bind:value={loginForm.email}
								placeholder="Enter your email address"
								class="nord-input"
								required
							/>
						</div>
						<div class="nord-floating-label">
							<label for="loginPassword" class="nord-label">Password</label>
							<input
								type={showLoginPassword ? 'text' : 'password'}
								id="loginPassword"
								bind:value={loginForm.password}
								placeholder="Enter your password"
								class="nord-input"
								required
							/>
							<div class="nord-checkbox-container">
								<input
									type="checkbox"
									id="showLoginPassword"
									class="nord-checkbox"
									bind:checked={showLoginPassword}
								/>
								<label for="showLoginPassword" class="nord-checkbox-label">Show password</label>
							</div>
						</div>
						<button type="submit" class="nord-button">Sign In</button>
						<button type="button" class="nord-forgot-password">
							Forgot Password?
						</button>
					</form>
				</div>
			{:else}
				<div class="nord-panel">
					<form class="nord-form" on:submit={handleRegister}>
						<div class="nord-input-group">
							<div class="nord-floating-label">
								<label for="firstName" class="nord-label">First Name</label>
								<input
									type="text"
									id="firstName"
									bind:value={registerForm.firstName}
									placeholder="Enter your first name"
									class="nord-input"
									required
								/>
							</div>
							<div class="nord-floating-label">
								<label for="lastName" class="nord-label">Last Name</label>
								<input
									type="text"
									id="lastName"
									bind:value={registerForm.lastName}
									placeholder="Enter your last name"
									class="nord-input"
									required
								/>
							</div>
						</div>
						<div class="nord-floating-label">
							<label for="middleName" class="nord-label">Middle Name (Optional)</label>
							<input
								type="text"
								id="middleName"
								bind:value={registerForm.middleName}
								placeholder="Enter your middle name"
								class="nord-input"
							/>
						</div>
						<div class="nord-floating-label">
							<label for="address" class="nord-label">Address</label>
							<input
								type="text"
								id="address"
								bind:value={registerForm.address}
								placeholder="Enter your complete address"
								class="nord-input"
								required
							/>
						</div>
						<div class="nord-input-group">
							<div class="nord-floating-label">
								<label for="gender" class="nord-label">Gender</label>
								<select
									id="gender"
									bind:value={registerForm.gender}
									class="nord-select"
									required
								>
									<option value="" disabled>Select your gender</option>
									<option value="Male">Male</option>
									<option value="Female">Female</option>
								</select>
							</div>
							<div class="nord-floating-label">
								<label for="dateOfBirth" class="nord-label">Birthdate</label>
								<input
									type="date"
									id="dateOfBirth"
									bind:value={registerForm.dateOfBirth}
									class="nord-input"
									required
								/>
							</div>
						</div>
						<div class="nord-floating-label">
							<label for="phoneNumber" class="nord-label">Phone Number</label>
							<input
								type="tel"
								id="phoneNumber"
								bind:value={registerForm.phoneNumber}
								placeholder="Enter your phone number"
								class="nord-input"
								required
							/>
						</div>
						<div class="nord-floating-label">
							<label for="email" class="nord-label">Email Address</label>
							<input
								type="email"
								id="email"
								bind:value={registerForm.email}
								placeholder="Enter your email address"
								class="nord-input"
								required
							/>
						</div>
						<div class="nord-floating-label">
							<label for="password" class="nord-label">Password</label>
							<input
								type={showPassword ? 'text' : 'password'}
								id="password"
								bind:value={registerForm.password}
								placeholder="Create a secure password"
								class="nord-input"
								required
							/>
							<div class="nord-checkbox-container">
								<input
									type="checkbox"
									id="showPassword"
									class="nord-checkbox"
									bind:checked={showPassword}
								/>
								<label for="showPassword" class="nord-checkbox-label">Show password</label>
							</div>
						</div>
						<div class="nord-floating-label">
							<label for="confirmPassword" class="nord-label">Confirm Password</label>
							<input
								type={showConfirmPassword ? 'text' : 'password'}
								id="confirmPassword"
								bind:value={registerForm.confirmPassword}
								placeholder="Confirm your password"
								class="nord-input"
								required
							/>
							<div class="nord-checkbox-container">
								<input
									type="checkbox"
									id="showConfirmPassword"
									class="nord-checkbox"
									bind:checked={showConfirmPassword}
								/>
								<label for="showConfirmPassword" class="nord-checkbox-label">Show password</label>
							</div>
						</div>
						<button type="submit" class="nord-button">Create Account</button>
					</form>
				</div>
			{/if}
		</div>
	</section>
</div>

<style>
	/* Import the styles from the original file */
	:global(.nord-container) {
		min-height: 100vh;
		background-color: #ffffff;
		color: #495057;
		position: relative;
		overflow-x: hidden;
		overflow-y: auto;
	}

	:global(.nord-back-button) {
		position: fixed;
		top: 2rem;
		left: 2rem;
		display: flex;
		align-items: center;
		gap: 0.5rem;
		color: #495057;
		text-decoration: none;
		transition: color 0.2s;
		z-index: 10;
	}

	:global(.nord-back-button:hover) {
		color: #dc3545;
	}

	:global(.nord-icon) {
		width: 1.5rem;
		height: 1.5rem;
	}

	:global(.nord-toast-container) {
		position: fixed;
		top: 2rem;
		right: 2rem;
		z-index: 50;
		max-width: 400px;
		width: 100%;
	}

	:global(.nord-bg-elements) {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		pointer-events: none;
		overflow: hidden;
		z-index: 0;
	}

	:global(.nord-bg-element) {
		position: absolute;
		background-color: #dc3545;
		opacity: 0.1;
		border-radius: 50%;
	}

	:global(.nord-bg-element:nth-child(1)) {
		width: 20rem;
		height: 20rem;
		top: -10rem;
		right: -10rem;
		animation: float 20s infinite ease-in-out;
	}

	:global(.nord-bg-element:nth-child(2)) {
		width: 15rem;
		height: 15rem;
		bottom: -7.5rem;
		left: -7.5rem;
		animation: float 25s infinite ease-in-out reverse;
	}

	:global(.nord-bg-element:nth-child(3)) {
		width: 10rem;
		height: 10rem;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		animation: float-center 30s infinite ease-in-out;
	}

	@keyframes float {
		0%, 100% { transform: translateY(0); }
		50% { transform: translateY(-2rem); }
	}

	@keyframes float-center {
		0%, 100% { transform: translate(-50%, -50%) translateY(0); }
		50% { transform: translate(-50%, -50%) translateY(-2rem); }
	}

	:global(.nord-auth-section) {
		position: relative;
		min-height: 100vh;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		padding: 6rem 2rem 2rem;
		z-index: 1;
	}

	:global(.nord-header) {
		text-align: center;
		margin-bottom: 2rem;
	}

	:global(.nord-title) {
		font-size: 3rem;
		font-weight: bold;
		color: #dc3545;
		margin-bottom: 0.5rem;
	}

	:global(.nord-subtitle) {
		font-size: 1.125rem;
		color: #495057;
		margin: 0;
	}

	:global(.nord-tabs-container) {
		background-color: #ffffff;
		border-radius: 0.75rem;
		padding: 2rem;
		box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
		width: 100%;
		max-width: 36rem;
		border: 1px solid #dee2e6;
	}

	:global(.nord-tabs) {
		margin-bottom: 1.5rem;
	}

	:global(.nord-tablist) {
		display: flex;
		background-color: #e9ecef;
		border-radius: 0.5rem;
		padding: 0.25rem;
		position: relative;
	}

	:global(.nord-tab-indicator) {
		position: absolute;
		height: calc(100% - 0.5rem);
		width: calc(50% - 0.25rem);
		background-color: #dc3545;
		border-radius: 0.375rem;
		transition: transform 0.3s ease;
		top: 0.25rem;
		left: 0.25rem;
	}

	:global(.nord-tab-indicator.register) {
		transform: translateX(calc(100% + 0.25rem));
	}

	:global(.nord-tab) {
		flex: 1;
		padding: 0.75rem 1rem;
		border: none;
		background-color: transparent;
		color: #495057;
		font-size: 0.875rem;
		font-weight: 500;
		border-radius: 0.375rem;
		cursor: pointer;
		transition: all 0.2s;
		position: relative;
		z-index: 1;
	}

	:global(.nord-tab[aria-selected="true"]) {
		color: #ffffff;
	}

	:global(.nord-panel) {
		display: block;
	}

	:global(.nord-form) {
		display: flex;
		flex-direction: column;
		gap: 1.25rem;
	}

	:global(.nord-input-group) {
		display: flex;
		gap: 1rem;
	}

	:global(.nord-floating-label) {
		position: relative;
		width: 100%;
		display: flex;
		flex-direction: column;
		gap: 0.5rem;
	}

	:global(.nord-input), :global(.nord-select) {
		width: 100%;
		padding: 0.875rem 1rem;
		background-color: #ffffff;
		border: 2px solid #dee2e6;
		border-radius: 0.5rem;
		color: #212529;
		font-size: 0.875rem;
		transition: all 0.2s;
		outline: none;
	}

	:global(.nord-label) {
		color: #495057;
		font-size: 0.875rem;
		font-weight: 500;
		display: block;
	}

	:global(.nord-input:focus), :global(.nord-select:focus) {
		border-color: #dc3545;
		background-color: #f8f9fa;
		box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
	}

	:global(.nord-checkbox-container) {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		margin-top: 0.25rem;
	}

	:global(.nord-checkbox) {
		width: 1rem;
		height: 1rem;
		background-color: #ffffff;
		border: 2px solid #dee2e6;
		border-radius: 0.25rem;
		cursor: pointer;
		transition: all 0.2s;
		appearance: none;
		position: relative;
	}

	:global(.nord-checkbox:checked) {
		background-color: #dc3545;
		border-color: #dc3545;
	}

	:global(.nord-checkbox:checked::after) {
		content: "✓";
		position: absolute;
		color: #ffffff;
		font-size: 0.75rem;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}

	:global(.nord-checkbox-label) {
		color: #495057;
		font-size: 0.8125rem;
		cursor: pointer;
		user-select: none;
	}

	:global(.nord-button) {
		width: 100%;
		padding: 0.875rem 1rem;
		background-color: #dc3545;
		color: #ffffff;
		border: none;
		border-radius: 0.5rem;
		font-size: 1rem;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.2s;
		margin-top: 0.5rem;
	}

	:global(.nord-button:hover) {
		background-color: #c82333;
		transform: translateY(-1px);
		box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
	}

	:global(.nord-forgot-password) {
		width: 100%;
		padding: 0.625rem 1rem;
		background-color: transparent;
		color: #dc3545;
		border: 1px solid #dc3545;
		border-radius: 0.5rem;
		font-size: 0.875rem;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.2s;
		margin-top: 0.25rem;
	}

	:global(.nord-forgot-password:hover) {
		background-color: #dc3545;
		color: #ffffff;
		transform: translateY(-1px);
	}

	:global(.nord-toast) {
		background-color: #ffffff;
		color: #495057;
		padding: 1rem 1.5rem;
		border-radius: 0.5rem;
		margin-bottom: 0.5rem;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
		animation: slideIn 0.3s ease;
		border: 1px solid #dee2e6;
		cursor: pointer;
	}

	:global(.nord-toast.success) {
		border-left: 4px solid #28a745;
	}

	:global(.nord-toast.error) {
		border-left: 4px solid #dc3545;
	}

	@keyframes slideIn {
		from {
			transform: translateX(100%);
			opacity: 0;
		}
		to {
			transform: translateX(0);
			opacity: 1;
		}
	}

	@media (max-width: 640px) {
		:global(.nord-input-group) {
			flex-direction: column;
			gap: 1.25rem;
		}
	}
</style>