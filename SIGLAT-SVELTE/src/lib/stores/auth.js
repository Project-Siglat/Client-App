import { writable } from 'svelte/store';
import { browser } from '$app/environment';

function createAuthStore() {
	const { subscribe, set, update } = writable({
		user: null,
		token: null,
		isAuthenticated: false,
		loading: true
	});

	return {
		subscribe,
		login: (userData, token) => {
			if (browser) {
				sessionStorage.setItem('authToken', token);
				sessionStorage.setItem('userData', JSON.stringify(userData));
			}
			set({
				user: userData,
				token,
				isAuthenticated: true,
				loading: false
			});
		},
		logout: () => {
			if (browser) {
				sessionStorage.removeItem('authToken');
				sessionStorage.removeItem('userData');
			}
			set({
				user: null,
				token: null,
				isAuthenticated: false,
				loading: false
			});
		},
		initialize: () => {
			if (browser) {
				const token = sessionStorage.getItem('authToken');
				const userData = sessionStorage.getItem('userData');
				
				if (token && userData) {
					try {
						const user = JSON.parse(userData);
						set({
							user,
							token,
							isAuthenticated: true,
							loading: false
						});
					} catch (error) {
						console.error('Failed to parse user data:', error);
						set({
							user: null,
							token: null,
							isAuthenticated: false,
							loading: false
						});
					}
				} else {
					set({
						user: null,
						token: null,
						isAuthenticated: false,
						loading: false
					});
				}
			}
		}
	};
}

export const auth = createAuthStore();