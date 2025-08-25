import { browser } from '$app/environment';

const API_BASE_URL = 'http://localhost:5000/api/v1';

export function getApiUrl() {
	return API_BASE_URL;
}

export async function apiRequest(endpoint, options = {}) {
	if (!browser) return null;
	
	const url = `${API_BASE_URL}${endpoint}`;
	const token = sessionStorage.getItem('authToken');
	
	const defaultOptions = {
		headers: {
			'Content-Type': 'application/json',
			...(token && { Authorization: `Bearer ${token}` })
		}
	};
	
	const finalOptions = {
		...defaultOptions,
		...options,
		headers: {
			...defaultOptions.headers,
			...options.headers
		}
	};
	
	try {
		const response = await fetch(url, finalOptions);
		
		if (!response.ok) {
			const error = await response.text();
			throw new Error(error || `HTTP ${response.status}`);
		}
		
		const contentType = response.headers.get('content-type');
		if (contentType && contentType.includes('application/json')) {
			return await response.json();
		}
		
		return await response.text();
	} catch (error) {
		console.error('API Request failed:', error);
		throw error;
	}
}

export const authApi = {
	login: (credentials) => apiRequest('/Auth/login', {
		method: 'POST',
		body: JSON.stringify(credentials)
	}),
	
	register: (userData) => apiRequest('/Auth/register', {
		method: 'POST',
		body: JSON.stringify(userData)
	})
};

export const ambulanceApi = {
	getAll: () => apiRequest('/Ambulance'),
	
	sendAlert: (alertData) => apiRequest('/Ambulance/alert', {
		method: 'POST',
		body: JSON.stringify(alertData)
	}),
	
	getCurrentAlert: () => apiRequest('/Ambulance/alert/current')
};

export const userApi = {
	updateCoordinates: (coordinates) => apiRequest('/User/coordinates', {
		method: 'POST',
		body: JSON.stringify(coordinates)
	})
};