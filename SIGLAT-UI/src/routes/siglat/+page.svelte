<script>
	import { onMount } from "svelte";
	import { goto } from "$app/navigation";
	
	let isAuthenticated = false;
	let userRole = "";
	let activeTab = "dashboard";
	let contacts = [];
	let incidents = [];
	let analytics = null;
	let showContactModal = false;
	let showIncidentModal = false;
	let showDeleteModal = false;
	let showDeleteIncidentModal = false;
	let editingContact = null;
	let editingIncident = null;
	let deletingContact = null;
	let deletingIncident = null;
	let toast = { show: false, message: "", type: "" };
	
	let contactForm = {
		id: null,
		label: "",
		contactType: "Phone",
		contactInformation: "",
	};
	
	let incidentForm = {
		id: null,
		incidentType: "",
		description: "",
		involvedAgencies: [],
		whoReportedId: "", // This will be set from JWT and be non-editable
		timestamp: "",
		latitude: "",
		longitude: "",
		location: "",
		status: "Active",
		priority: "Medium",
		notes: ""
	};
	
	onMount(() => {
		const token = sessionStorage.getItem("token");
		if (!token) {
			goto("/login");
			return;
		}
		
		try {
			const payload = JSON.parse(atob(token.split(".")[1]));
			userRole = payload.role;
			
			if (userRole !== "Admin") {
				goto("/");
				return;
			}
			
			isAuthenticated = true;
			loadContacts();
			loadIncidents();
			loadAnalytics();
		} catch (error) {
			sessionStorage.removeItem("token");
			goto("/login");
		}
	});
	
	function logout() {
		sessionStorage.removeItem("token");
		goto("/login");
	}
	
	async function loadContacts() {
		try {
			const response = await fetch("http://localhost:5000/api/v1.0/Admin/contact");
			if (response.ok) {
				contacts = await response.json();
			}
		} catch (error) {
			showToast("Failed to load contacts", "error");
		}
	}
	
	// Load analytics from API
	async function loadAnalytics() {
		try {
			const token = sessionStorage.getItem("token");
			const response = await fetch("http://localhost:5000/api/v1.0/Report/analytics", {
				headers: {
					"Authorization": `Bearer ${token}`
				}
			});
			if (response.ok) {
				analytics = await response.json();
			} else {
				console.error("Failed to load analytics:", response.status);
				analytics = {
					totalReports: 0,
					byStatus: [],
					byIncidentType: [],
					byPriority: [],
					byAgencies: []
				};
			}
		} catch (error) {
			console.error("Error loading analytics:", error);
			analytics = {
				totalReports: 0,
				byStatus: [],
				byIncidentType: [],
				byPriority: [],
				byAgencies: []
			};
		}
	}
	async function loadIncidents() {
		try {
			const token = sessionStorage.getItem("token");
			const response = await fetch("http://localhost:5000/api/v1.0/Report", {
				headers: {
					"Authorization": `Bearer ${token}`
				}
			});
			if (response.ok) {
				incidents = await response.json();
			} else {
				console.error("Failed to load incidents:", response.status);
				// Keep empty array if API fails
				incidents = [];
			}
		} catch (error) {
			console.error("Error loading incidents:", error);
			showToast("Failed to load incidents", "error");
			incidents = [];
		}
	}
	
	function openContactModal(contact = null) {
		editingContact = contact;
		if (contact) {
			contactForm = {
				id: contact.id,
				label: contact.label,
				contactType: contact.contactType,
				contactInformation: contact.contactInformation,
			};
		} else {
			contactForm = {
				id: null,
				label: "",
				contactType: "Phone",
				contactInformation: "",
			};
		}
		showContactModal = true;
	}
	
	function closeContactModal() {
		showContactModal = false;
		editingContact = null;
	}
	
	function openDeleteModal(contact) {
		deletingContact = contact;
		showDeleteModal = true;
	}
	
	function closeDeleteModal() {
		showDeleteModal = false;
		deletingContact = null;
	}
	
	function openIncidentModal(incident = null) {
		editingIncident = incident;
		if (incident) {
			incidentForm = {
				id: incident.id,
				incidentType: incident.incidentType,
				description: incident.description,
				involvedAgencies: incident.involvedAgencies ? incident.involvedAgencies.split(',').map(a => a.trim()) : [],
				whoReportedId: incident.whoReportedId,
				timestamp: incident.timestamp ? new Date(incident.timestamp).toISOString().slice(0, 16) : "",
				latitude: incident.latitude || "",
				longitude: incident.longitude || "",
				location: incident.location || "",
				status: incident.status,
				priority: incident.priority || "Medium",
				notes: incident.notes || ""
			};
		} else {
			// Get current user ID from JWT token
			const token = sessionStorage.getItem("token");
			let currentUserId = "";
			if (token) {
				try {
					const payload = JSON.parse(atob(token.split(".")[1]));
					currentUserId = payload.nameid || payload.sub || "";
				} catch (error) {
					console.error("Error parsing token:", error);
				}
			}
			
			incidentForm = {
				id: null,
				incidentType: "",
				description: "",
				involvedAgencies: [],
				whoReportedId: currentUserId,
				timestamp: new Date().toISOString().slice(0, 16),
				latitude: "",
				longitude: "",
				location: "",
				status: "Active",
				priority: "Medium",
				notes: ""
			};
		}
		showIncidentModal = true;
	}
	
	function closeIncidentModal() {
		showIncidentModal = false;
		editingIncident = null;
	}
	
	function openDeleteIncidentModal(incident) {
		deletingIncident = incident;
		showDeleteIncidentModal = true;
	}
	
	function closeDeleteIncidentModal() {
		showDeleteIncidentModal = false;
		deletingIncident = null;
	}
	
	function toggleAgency(agency) {
		const index = incidentForm.involvedAgencies.indexOf(agency);
		if (index > -1) {
			incidentForm.involvedAgencies = incidentForm.involvedAgencies.filter(a => a !== agency);
		} else {
			incidentForm.involvedAgencies = [...incidentForm.involvedAgencies, agency];
		}
	}
	
	async function saveIncident() {
		try {
			const token = sessionStorage.getItem("token");
			const method = editingIncident ? "PUT" : "POST";
			const url = editingIncident 
				? `http://localhost:5000/api/v1.0/Report/${incidentForm.id}`
				: "http://localhost:5000/api/v1.0/Report";
			
			const incidentData = {
				id: incidentForm.id,
				incidentType: incidentForm.incidentType,
				description: incidentForm.description,
				timestamp: new Date(incidentForm.timestamp).toISOString(),
				latitude: incidentForm.latitude ? parseFloat(incidentForm.latitude) : null,
				longitude: incidentForm.longitude ? parseFloat(incidentForm.longitude) : null,
				location: incidentForm.location,
				status: incidentForm.status,
				involvedAgencies: incidentForm.involvedAgencies.join(', '),
				priority: incidentForm.priority,
				notes: incidentForm.notes
			};
			
			const response = await fetch(url, {
				method,
				headers: {
					"Content-Type": "application/json",
					"Authorization": `Bearer ${token}`
				},
				body: JSON.stringify(incidentData)
			});
			
			if (response.ok) {
				await loadIncidents();
				await loadAnalytics();
				closeIncidentModal();
				showToast(
					editingIncident ? "Incident updated successfully" : "Incident reported successfully",
					"success"
				);
			} else {
				const errorText = await response.text();
				console.error("Save failed:", response.status, errorText);
				showToast("Failed to save incident", "error");
			}
		} catch (error) {
			console.error("Save error:", error);
			showToast("Failed to save incident", "error");
		}
	}
	
	async function deleteIncident() {
		if (!deletingIncident) return;
		
		try {
			const token = sessionStorage.getItem("token");
			const response = await fetch(`http://localhost:5000/api/v1.0/Report/${deletingIncident.id}`, {
				method: "DELETE",
				headers: {
					"Authorization": `Bearer ${token}`
				}
			});
			
			if (response.ok) {
				await loadIncidents();
				await loadAnalytics();
				closeDeleteIncidentModal();
				showToast("Incident deleted successfully", "success");
			} else {
				showToast("Failed to delete incident", "error");
			}
		} catch (error) {
			showToast("Failed to delete incident", "error");
		}
	}
	
	function generateGuid() {
		return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
			var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
			return v.toString(16);
		});
	}
	
	async function saveContact() {
		const token = sessionStorage.getItem("token");
		const method = "POST"; // Use POST for both create and update
		const url = `http://localhost:5000/api/v1.0/Admin/contact`;
		
		try {
			const contactData = {
				label: contactForm.label,
				contactType: contactForm.contactType,
				contactInformation: contactForm.contactInformation,
			};
			
			// Include ID for updates, generate new GUID for creates
			if (editingContact) {
				contactData.id = contactForm.id;
			} else {
				contactData.id = generateGuid();
			}
			
			const response = await fetch(url, {
				method,
				headers: {
					"Content-Type": "application/json",
					"Authorization": `Bearer ${token}`
				},
				body: JSON.stringify(contactData)
			});
			
			if (response.ok) {
				await loadContacts();
				closeContactModal();
				showToast(
					editingContact ? "Contact updated successfully" : "Contact created successfully",
					"success"
				);
			} else {
				const errorText = await response.text();
				console.error("Save failed:", response.status, errorText);
				showToast("Failed to save contact", "error");
			}
		} catch (error) {
			console.error("Save error:", error);
			showToast("Failed to save contact", "error");
		}
	}
	
	async function deleteContact() {
		if (!deletingContact) return;
		
		const token = sessionStorage.getItem("token");
		
		try {
			const response = await fetch(`http://localhost:5000/api/v1.0/Admin/contact?Id=${deletingContact.id}`, {
				method: "DELETE",
				headers: {
					"Authorization": `Bearer ${token}`
				}
			});
			
			if (response.ok) {
				await loadContacts();
				closeDeleteModal();
				showToast("Contact deleted successfully", "success");
			} else {
				showToast("Failed to delete contact", "error");
			}
		} catch (error) {
			showToast("Failed to delete contact", "error");
		}
	}
	
	function showToast(message, type) {
		toast = { show: true, message, type };
		setTimeout(() => {
			toast.show = false;
		}, 3000);
	}
	
	function getContactIcon(type) {
		switch (type) {
			case "Phone": return "📞";
			case "Email": return "📧";
			case "Facebook": return "📘";
			default: return "📞";
		}
	}
	
	function getContactBadgeClass(type) {
		switch (type) {
			case "Phone": return "bg-green-100 text-green-800";
			case "Email": return "bg-blue-100 text-blue-800";
			case "Facebook": return "bg-purple-100 text-purple-800";
			default: return "bg-gray-100 text-gray-800";
		}
	}
	
	// Simple pie chart calculation
	function calculatePieChart(data) {
		const total = data.reduce((sum, item) => sum + item.count, 0);
		if (total === 0) return [];
		
		let cumulativeAngle = 0;
		return data.map((item, index) => {
			const percentage = (item.count / total) * 100;
			const angle = (item.count / total) * 360;
			const slice = {
				...item,
				percentage: percentage.toFixed(1),
				startAngle: cumulativeAngle,
				endAngle: cumulativeAngle + angle,
				color: getSliceColor(index)
			};
			cumulativeAngle += angle;
			return slice;
		});
	}
	
	function getSliceColor(index) {
		const colors = [
			'#ef4444', // red-500
			'#3b82f6', // blue-500  
			'#10b981', // emerald-500
			'#f59e0b', // amber-500
			'#8b5cf6', // violet-500
			'#ec4899', // pink-500
			'#6b7280'  // gray-500
		];
		return colors[index % colors.length];
	}
</script>

{#if isAuthenticated}
	<div class="min-h-screen bg-gray-100">
		<header class="bg-white shadow-sm border-b">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<div class="flex justify-between items-center py-6">
					<div>
						<h1 class="text-3xl font-bold text-red-600">SIGLAT Admin</h1>
						<p class="text-gray-600">Emergency Response System Administration</p>
					</div>
					<div class="flex items-center gap-4">
						<span class="text-sm text-gray-700">Welcome, Admin</span>
						<button 
							on:click={logout}
							class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
						>
							Logout
						</button>
					</div>
				</div>
			</div>
		</header>
		
		<nav class="bg-white border-b border-gray-200">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<div class="flex space-x-8">
					<button
						class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {
							activeTab === 'dashboard' 
								? 'border-red-500 text-red-600' 
								: 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
						}"
						on:click={() => activeTab = 'dashboard'}
					>
						Dashboard
					</button>
					<button
						class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {
							activeTab === 'contacts' 
								? 'border-red-500 text-red-600' 
								: 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
						}"
						on:click={() => activeTab = 'contacts'}
					>
						Contact Management
					</button>
					<button
						class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {
							activeTab === 'incidents' 
								? 'border-red-500 text-red-600' 
								: 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
						}"
						on:click={() => activeTab = 'incidents'}
					>
						Incident Management
					</button>
				</div>
			</div>
		</nav>
		
		<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
			{#if activeTab === "dashboard"}
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
					<div class="bg-white rounded-lg shadow p-6">
						<div class="flex items-center">
							<div class="flex-shrink-0">
								<div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
									<span class="text-red-600 text-lg">📞</span>
								</div>
							</div>
							<div class="ml-5 w-0 flex-1">
								<dl>
									<dt class="text-sm font-medium text-gray-500 truncate">Total Contacts</dt>
									<dd class="text-lg font-medium text-gray-900">{contacts.length}</dd>
								</dl>
							</div>
						</div>
					</div>
					
					<div class="bg-white rounded-lg shadow p-6">
						<div class="flex items-center">
							<div class="flex-shrink-0">
								<div class="w-8 h-8 bg-orange-100 rounded-md flex items-center justify-center">
									<span class="text-orange-600 text-lg">🚨</span>
								</div>
							</div>
							<div class="ml-5 w-0 flex-1">
								<dl>
									<dt class="text-sm font-medium text-gray-500 truncate">Active Incidents</dt>
									<dd class="text-lg font-medium text-gray-900">
										{analytics ? analytics.byStatus.find(s => s.status === 'Active')?.count || 0 : 0}
									</dd>
								</dl>
							</div>
						</div>
					</div>
					
					<div class="bg-white rounded-lg shadow p-6">
						<div class="flex items-center">
							<div class="flex-shrink-0">
								<div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
									<span class="text-green-600 text-lg">🏥</span>
								</div>
							</div>
							<div class="ml-5 w-0 flex-1">
								<dl>
									<dt class="text-sm font-medium text-gray-500 truncate">Emergency Services</dt>
									<dd class="text-lg font-medium text-gray-900">Active</dd>
								</dl>
							</div>
						</div>
					</div>
					
					<div class="bg-white rounded-lg shadow p-6">
						<div class="flex items-center">
							<div class="flex-shrink-0">
								<div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
									<span class="text-blue-600 text-lg">📊</span>
								</div>
							</div>
							<div class="ml-5 w-0 flex-1">
								<dl>
									<dt class="text-sm font-medium text-gray-500 truncate">System Status</dt>
									<dd class="text-lg font-medium text-gray-900">Online</dd>
								</dl>
							</div>
						</div>
					</div>
				</div>
				
				{#if analytics}
					<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
						<!-- Incident Status Chart -->
						<div class="bg-white rounded-lg shadow p-6">
							<h3 class="text-lg font-medium text-gray-900 mb-4">Incidents by Status</h3>
							{#if analytics.byStatus.length > 0}
								<div class="flex items-center">
									<div class="w-32 h-32 relative mr-6">
										<svg viewBox="0 0 100 100" class="w-full h-full transform -rotate-90">
											{#each calculatePieChart(analytics.byStatus) as slice, i}
												<circle
													cx="50"
													cy="50"
													r="40"
													fill="none"
													stroke={slice.color}
													stroke-width="20"
													stroke-dasharray="{slice.percentage * 2.51} 251"
													stroke-dashoffset="{-slice.startAngle * 2.51 / 360}"
													class="transition-all duration-300"
												/>
											{/each}
										</svg>
									</div>
									<div class="flex-1">
										{#each calculatePieChart(analytics.byStatus) as slice}
											<div class="flex items-center mb-2">
												<div class="w-4 h-4 rounded-full mr-2" style="background-color: {slice.color}"></div>
												<span class="text-sm text-gray-600">{slice.status}: {slice.count} ({slice.percentage}%)</span>
											</div>
										{/each}
									</div>
								</div>
							{:else}
								<p class="text-gray-500 text-center py-8">No incident data available</p>
							{/if}
						</div>
						
						<!-- Incident Types Chart -->
						<div class="bg-white rounded-lg shadow p-6">
							<h3 class="text-lg font-medium text-gray-900 mb-4">Incidents by Type</h3>
							{#if analytics.byIncidentType.length > 0}
								<div class="flex items-center">
									<div class="w-32 h-32 relative mr-6">
										<svg viewBox="0 0 100 100" class="w-full h-full transform -rotate-90">
											{#each calculatePieChart(analytics.byIncidentType) as slice, i}
												<circle
													cx="50"
													cy="50"
													r="40"
													fill="none"
													stroke={slice.color}
													stroke-width="20"
													stroke-dasharray="{slice.percentage * 2.51} 251"
													stroke-dashoffset="{-slice.startAngle * 2.51 / 360}"
													class="transition-all duration-300"
												/>
											{/each}
										</svg>
									</div>
									<div class="flex-1">
										{#each calculatePieChart(analytics.byIncidentType) as slice}
											<div class="flex items-center mb-2">
												<div class="w-4 h-4 rounded-full mr-2" style="background-color: {slice.color}"></div>
												<span class="text-sm text-gray-600">{slice.type}: {slice.count} ({slice.percentage}%)</span>
											</div>
										{/each}
									</div>
								</div>
							{:else}
								<p class="text-gray-500 text-center py-8">No incident data available</p>
							{/if}
						</div>
					</div>
					
					<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
						<!-- Priority Chart -->
						<div class="bg-white rounded-lg shadow p-6">
							<h3 class="text-lg font-medium text-gray-900 mb-4">Incidents by Priority</h3>
							{#if analytics.byPriority.length > 0}
								<div class="flex items-center">
									<div class="w-32 h-32 relative mr-6">
										<svg viewBox="0 0 100 100" class="w-full h-full transform -rotate-90">
											{#each calculatePieChart(analytics.byPriority) as slice, i}
												<circle
													cx="50"
													cy="50"
													r="40"
													fill="none"
													stroke={slice.color}
													stroke-width="20"
													stroke-dasharray="{slice.percentage * 2.51} 251"
													stroke-dashoffset="{-slice.startAngle * 2.51 / 360}"
													class="transition-all duration-300"
												/>
											{/each}
										</svg>
									</div>
									<div class="flex-1">
										{#each calculatePieChart(analytics.byPriority) as slice}
											<div class="flex items-center mb-2">
												<div class="w-4 h-4 rounded-full mr-2" style="background-color: {slice.color}"></div>
												<span class="text-sm text-gray-600">{slice.priority}: {slice.count} ({slice.percentage}%)</span>
											</div>
										{/each}
									</div>
								</div>
							{:else}
								<p class="text-gray-500 text-center py-8">No incident data available</p>
							{/if}
						</div>
						
						<!-- Agencies Chart -->
						<div class="bg-white rounded-lg shadow p-6">
							<h3 class="text-lg font-medium text-gray-900 mb-4">Involved Agencies</h3>
							{#if analytics.byAgencies.length > 0}
								<div class="flex items-center">
									<div class="w-32 h-32 relative mr-6">
										<svg viewBox="0 0 100 100" class="w-full h-full transform -rotate-90">
											{#each calculatePieChart(analytics.byAgencies) as slice, i}
												<circle
													cx="50"
													cy="50"
													r="40"
													fill="none"
													stroke={slice.color}
													stroke-width="20"
													stroke-dasharray="{slice.percentage * 2.51} 251"
													stroke-dashoffset="{-slice.startAngle * 2.51 / 360}"
													class="transition-all duration-300"
												/>
											{/each}
										</svg>
									</div>
									<div class="flex-1">
										{#each calculatePieChart(analytics.byAgencies) as slice}
											<div class="flex items-center mb-2">
												<div class="w-4 h-4 rounded-full mr-2" style="background-color: {slice.color}"></div>
												<span class="text-sm text-gray-600">{slice.agency}: {slice.count} ({slice.percentage}%)</span>
											</div>
										{/each}
									</div>
								</div>
							{:else}
								<p class="text-gray-500 text-center py-8">No agency data available</p>
							{/if}
						</div>
					</div>
				{/if}
				
				<div class="mt-8 bg-white rounded-lg shadow">
					<div class="px-6 py-4 border-b border-gray-200">
						<h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
					</div>
					<div class="p-6">
						<p class="text-gray-500">System running smoothly. All emergency contact services are operational.</p>
					</div>
				</div>
			{:else if activeTab === "incidents"}
				<div class="bg-white rounded-lg shadow">
					<div class="px-6 py-4 border-b border-gray-200">
						<div class="flex justify-between items-center">
							<h3 class="text-lg font-medium text-gray-900">Incident Management</h3>
							<button
								on:click={() => openIncidentModal()}
								class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
							>
								Report Incident
							</button>
						</div>
					</div>
					
					{#if incidents.length === 0}
						<div class="p-6 text-center">
							<div class="text-gray-400 text-4xl mb-4">🚨</div>
							<p class="text-gray-500">No incidents reported. Click "Report Incident" to add the first one.</p>
						</div>
					{:else}
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200">
								<thead class="bg-gray-50">
									<tr>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Incident</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agencies</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reported By</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
										<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
									</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-200">
									{#each incidents as incident}
										<tr class="hover:bg-gray-50">
											<td class="px-6 py-4">
												<div>
													<div class="text-sm font-medium text-gray-900">{incident.incidentType}</div>
													<div class="text-sm text-gray-500 max-w-xs truncate">{incident.description}</div>
												</div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="flex flex-wrap gap-1">
													{#each (incident.involvedAgencies || "").split(',').filter(a => a.trim()) as agency}
														<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {
															agency.trim() === 'PNP' ? 'bg-blue-100 text-blue-800' :
															agency.trim() === 'BFP' ? 'bg-red-100 text-red-800' :
															'bg-purple-100 text-purple-800'
														}">
															{agency.trim()}
														</span>
													{/each}
												</div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
												{incident.reporterName || 'Unknown'}
											</td>
											<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
												{new Date(incident.timestamp).toLocaleString()}
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {
													incident.status === 'Active' ? 'bg-red-100 text-red-800' :
													incident.status === 'Resolved' ? 'bg-green-100 text-green-800' :
													'bg-yellow-100 text-yellow-800'
												}">
													{incident.status}
												</span>
											</td>
											<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
												<button
													on:click={() => openIncidentModal(incident)}
													class="text-red-600 hover:text-red-900 mr-3"
												>
													Edit
												</button>
												<button
													on:click={() => openDeleteIncidentModal(incident)}
													class="text-red-600 hover:text-red-900"
												>
													Delete
												</button>
											</td>
										</tr>
									{/each}
								</tbody>
							</table>
						</div>
					{/if}
				</div>
			{:else if activeTab === "contacts"}
				<div class="bg-white rounded-lg shadow">
					<div class="px-6 py-4 border-b border-gray-200">
						<div class="flex justify-between items-center">
							<h3 class="text-lg font-medium text-gray-900">Emergency Contacts</h3>
							<button
								on:click={() => openContactModal()}
								class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
							>
								Add Contact
							</button>
						</div>
					</div>
					
					{#if contacts.length === 0}
						<div class="p-6 text-center">
							<div class="text-gray-400 text-4xl mb-4">📞</div>
							<p class="text-gray-500">No contacts found. Add your first emergency contact.</p>
						</div>
					{:else}
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200">
								<thead class="bg-gray-50">
									<tr>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
										<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
									</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-200">
									{#each contacts as contact}
										<tr class="hover:bg-gray-50">
											<td class="px-6 py-4 whitespace-nowrap">
										<div class="flex items-center">
											<div class="h-10 w-10 flex-shrink-0">
												<div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
													<span class="text-red-600 font-medium text-sm">
														{contact.label.charAt(0).toUpperCase()}
													</span>
												</div>
											</div>
											<div class="ml-4">
												<div class="text-sm font-medium text-gray-900">{contact.label}</div>
											</div>
										</div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {getContactBadgeClass(contact.contactType)}">
													{getContactIcon(contact.contactType)} {contact.contactType}
												</span>
											</td>
											<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
												{contact.contactInformation}
											</td>
											<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
												<button
													on:click={() => openContactModal(contact)}
													class="text-red-600 hover:text-red-900 mr-3"
												>
													Edit
												</button>
												<button
													on:click={() => openDeleteModal(contact)}
													class="text-red-600 hover:text-red-900"
												>
													Delete
												</button>
											</td>
										</tr>
									{/each}
								</tbody>
							</table>
						</div>
					{/if}
				</div>
			{/if}
		</main>
	</div>
{:else}
	<div class="min-h-screen bg-gray-100 flex items-center justify-center">
		<div class="text-center">
			<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
			<p class="text-gray-600">Verifying access...</p>
		</div>
	</div>
{/if}

{#if showContactModal}
	<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
		<div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
			<div class="mt-3">
				<h3 class="text-lg font-medium text-gray-900 mb-4">
					{editingContact ? "Edit Contact" : "Add New Contact"}
				</h3>
				
				<form on:submit|preventDefault={saveContact}>
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
						<input
							type="text"
							bind:value={contactForm.label}
							required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
							placeholder="Enter contact name"
						/>
					</div>
					
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 mb-2">Contact Type</label>
						<select
							bind:value={contactForm.contactType}
							required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
						>
							<option value="Phone">Phone</option>
							<option value="Email">Email</option>
							<option value="Facebook">Facebook</option>
						</select>
					</div>
					
					<div class="mb-6">
						<label class="block text-sm font-medium text-gray-700 mb-2">Contact Value</label>
						<input
							type="text"
							bind:value={contactForm.contactInformation}
							required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
							placeholder={contactForm.contactType === "Phone" ? "Enter phone number" : 
										contactForm.contactType === "Email" ? "Enter email address" : 
										"Enter Facebook handle"}
						/>
					</div>
					
					<div class="flex justify-end space-x-3">
						<button
							type="button"
							on:click={closeContactModal}
							class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
						>
							Cancel
						</button>
						<button
							type="submit"
							class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
						>
							{editingContact ? "Update" : "Save"}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
{/if}

{#if showIncidentModal}
	<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
		<div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
			<div class="mt-3">
				<h3 class="text-lg font-medium text-gray-900 mb-4">
					{editingIncident ? "Edit Incident" : "Report New Incident"}
				</h3>
				
				<form on:submit|preventDefault={saveIncident}>
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 mb-2">Incident Type</label>
						<input
							type="text"
							bind:value={incidentForm.incidentType}
							required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
							placeholder="e.g., Vehicular Crash, House Fire, Medical Emergency"
						/>
					</div>
					
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
						<textarea
							bind:value={incidentForm.description}
							required
							rows="4"
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
							placeholder="Provide detailed description of the incident..."
						></textarea>
					</div>
					
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 mb-2">Involved Agencies</label>
						<div class="space-y-2">
							<label class="flex items-center">
								<input
									type="checkbox"
									checked={incidentForm.involvedAgencies.includes('PNP')}
									on:change={() => toggleAgency('PNP')}
									class="rounded border-gray-300 text-red-600 focus:ring-red-500"
								/>
								<span class="ml-2 text-sm text-gray-700">PNP (Philippine National Police)</span>
							</label>
							<label class="flex items-center">
								<input
									type="checkbox"
									checked={incidentForm.involvedAgencies.includes('BFP')}
									on:change={() => toggleAgency('BFP')}
									class="rounded border-gray-300 text-red-600 focus:ring-red-500"
								/>
								<span class="ml-2 text-sm text-gray-700">BFP (Bureau of Fire Protection)</span>
							</label>
							<label class="flex items-center">
								<input
									type="checkbox"
									checked={incidentForm.involvedAgencies.includes('MDRRMO')}
									on:change={() => toggleAgency('MDRRMO')}
									class="rounded border-gray-300 text-red-600 focus:ring-red-500"
								/>
								<span class="ml-2 text-sm text-gray-700">MDRRMO (Municipal Disaster Risk Reduction)</span>
							</label>
						</div>
					</div>
					
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Reported By (ID)</label>
							<input
								type="text"
								value={incidentForm.whoReportedId}
								readonly
								class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-500 cursor-not-allowed"
								placeholder="Auto-filled from login"
							/>
						</div>
						
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Date & Time</label>
							<input
								type="datetime-local"
								bind:value={incidentForm.timestamp}
								required
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
							/>
						</div>
					</div>
					
					<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
							<input
								type="number"
								step="any"
								bind:value={incidentForm.latitude}
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
								placeholder="e.g., 14.5995"
							/>
						</div>
						
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
							<input
								type="number"
								step="any"
								bind:value={incidentForm.longitude}
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
								placeholder="e.g., 120.9842"
							/>
						</div>
						
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
							<input
								type="text"
								bind:value={incidentForm.location}
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
								placeholder="e.g., Barangay Centro"
							/>
						</div>
					</div>
					
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
							<select
								bind:value={incidentForm.status}
								required
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
							>
								<option value="Active">Active</option>
								<option value="In Progress">In Progress</option>
								<option value="Resolved">Resolved</option>
								<option value="Cancelled">Cancelled</option>
							</select>
						</div>
						
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
							<select
								bind:value={incidentForm.priority}
								required
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
							>
								<option value="Low">Low</option>
								<option value="Medium">Medium</option>
								<option value="High">High</option>
								<option value="Critical">Critical</option>
							</select>
						</div>
					</div>
					
					<div class="mb-6">
						<label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
						<textarea
							bind:value={incidentForm.notes}
							rows="3"
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
							placeholder="Additional notes or comments..."
						></textarea>
					</div>
					
					<div class="flex justify-end space-x-3">
						<button
							type="button"
							on:click={closeIncidentModal}
							class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
						>
							Cancel
						</button>
						<button
							type="submit"
							class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
						>
							{editingIncident ? "Update Incident" : "Report Incident"}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
{/if}

{#if showDeleteModal && deletingContact}
	<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
		<div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
			<div class="mt-3">
				<div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
					<span class="text-red-600 text-2xl">⚠️</span>
				</div>
				<h3 class="text-lg font-medium text-gray-900 text-center mb-4">
					Delete Contact
				</h3>
				<p class="text-sm text-gray-500 text-center mb-6">
					Are you sure you want to delete <strong>{deletingContact.label}</strong>? 
					This action cannot be undone.
				</p>
				
				<div class="flex justify-center space-x-3">
					<button
						type="button"
						on:click={closeDeleteModal}
						class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
					>
						Cancel
					</button>
					<button
						type="button"
						on:click={deleteContact}
						class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
					>
						Delete
					</button>
				</div>
			</div>
		</div>
	</div>
{/if}

{#if showDeleteIncidentModal && deletingIncident}
	<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
		<div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
			<div class="mt-3">
				<div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
					<span class="text-red-600 text-2xl">🚨</span>
				</div>
				<h3 class="text-lg font-medium text-gray-900 text-center mb-4">
					Delete Incident
				</h3>
				<p class="text-sm text-gray-500 text-center mb-6">
					Are you sure you want to delete incident <strong>"{deletingIncident.incidentType}"</strong>? 
					This action cannot be undone.
				</p>
				
				<div class="flex justify-center space-x-3">
					<button
						type="button"
						on:click={closeDeleteIncidentModal}
						class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
					>
						Cancel
					</button>
					<button
						type="button"
						on:click={deleteIncident}
						class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
					>
						Delete
					</button>
				</div>
			</div>
		</div>
	</div>
{/if}

{#if toast.show}
	<div class="fixed top-4 right-4 z-50">
		<div class="rounded-md p-4 {toast.type === 'success' ? 'bg-green-50 border-l-4 border-green-400' : 'bg-red-50 border-l-4 border-red-400'}">
			<div class="flex">
				<div class="flex-shrink-0">
					{#if toast.type === 'success'}
						<span class="text-green-400">✅</span>
					{:else}
						<span class="text-red-400">❌</span>
					{/if}
				</div>
				<div class="ml-3">
					<p class="text-sm {toast.type === 'success' ? 'text-green-800' : 'text-red-800'}">
						{toast.message}
					</p>
				</div>
			</div>
		</div>
	</div>
{/if}
