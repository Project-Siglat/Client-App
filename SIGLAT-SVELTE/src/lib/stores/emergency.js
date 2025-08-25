import { writable } from 'svelte/store';

export const emergencyAlerts = writable([]);
export const ambulances = writable([]);
export const userLocation = writable(null);