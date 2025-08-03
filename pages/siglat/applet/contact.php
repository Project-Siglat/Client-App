<!-- Contact List Table -->
 <div id="contactListTable" class="bg-[#2e3440] rounded-lg shadow">
     <div class="flex flex-col xs:flex-row justify-between items-start xs:items-center p-3 sm:p-4 border-b border-[#434c5e] gap-2">
         <h3 class="text-base sm:text-lg font-semibold text-[#eceff4]"><i class="bi bi-people"></i> <span class="hidden xs:inline">Contact Management</span><span class="xs:hidden">Contacts</span></h3>
         <button id="addContactBtn" class="bg-[#5e81ac] hover:bg-[#81a1c1] text-white px-3 py-2 sm:px-4 rounded text-xs sm:text-sm lg:text-base w-full xs:w-auto">
             <i class="bi bi-plus-circle"></i> <span class="hidden xs:inline">Add Contact</span><span class="xs:hidden">Add</span>
         </button>
     </div>
     <div class="p-3 sm:p-4">
         <!-- Loading indicator -->
         <div id="loadingIndicator" class="hidden text-center py-6 sm:py-8">
             <div class="inline-block animate-spin rounded-full h-6 w-6 sm:h-8 sm:w-8 border-b-2 border-[#5e81ac]"></div>
             <p class="text-[#d8dee9] mt-2 text-sm sm:text-base">Loading contacts...</p>
         </div>

         <!-- Table View -->
         <div id="contactsTableView" class="max-h-80 overflow-y-auto border border-[#434c5e] rounded">
             <table class="w-full">
                 <thead class="bg-[#3b4252]">
                     <tr>
                         <th class="px-4 py-2 text-left text-[#e5e9f0]"><i class="bi bi-tag"></i> Label</th>
                         <th class="px-4 py-2 text-left text-[#e5e9f0]"><i class="bi bi-telephone"></i> Type</th>
                         <th class="px-4 py-2 text-left text-[#e5e9f0]"><i class="bi bi-info-circle"></i> Information</th>
                         <th class="px-4 py-2 text-center text-[#e5e9f0]"><i class="bi bi-tools"></i> Actions</th>
                     </tr>
                 </thead>
                 <tbody id="contactsTableBody">
                     <!-- Contacts will be loaded from API -->
                 </tbody>
             </table>
         </div>

         <!-- No contacts message -->
         <div id="noContactsMessage" class="hidden text-center py-6 sm:py-8">
             <i class="bi bi-people text-3xl sm:text-4xl text-[#4c566a] mb-2"></i>
             <p class="text-[#d8dee9] text-sm sm:text-base">No contacts found</p>
             <p class="text-[#4c566a] text-xs sm:text-sm">Add your first contact to get started</p>
         </div>
     </div>
 </div>

 <!-- Toast Container -->
 <div id="toastContainer" class="fixed right-2 sm:right-4 space-y-2" style="top: 65px; z-index: 2147483647;"></div>

 <style>
 .modal-backdrop {
     background-color: rgba(0, 0, 0, 0.5);
     opacity: 0;
     transition: opacity 0.3s ease-in-out;
 }

 .modal-backdrop.show {
     opacity: 1;
 }

 .modal-content {
     transform: scale(0.7) translateY(-50px);
     opacity: 0;
     transition: all 0.3s ease-in-out;
 }

 .modal-content.show {
     transform: scale(1) translateY(0);
     opacity: 1;
 }

 .toast {
     min-width: 250px;
     max-width: 300px;
     padding: 12px 16px;
     border-radius: 8px;
     color: white;
     font-size: 13px;
     box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
     transform: translateX(100%);
     opacity: 0;
     transition: all 0.3s ease-in-out;
     z-index: 99999;
 }

 .toast.show {
     transform: translateX(0);
     opacity: 1;
 }

 .toast.success {
     background-color: #a3be8c;
     border-left: 4px solid #8fbcbb;
 }

 .toast.error {
     background-color: #bf616a;
     border-left: 4px solid #d08770;
 }

 .toast.info {
     background-color: #5e81ac;
     border-left: 4px solid #81a1c1;
 }

 @media (max-width: 480px) {
     .toast {
         min-width: 200px;
         font-size: 12px;
         padding: 10px 14px;
     }
 }
 </style>

 <!-- Contact Modal -->
 <div id="contactModal" class="fixed inset-0 hidden z-50">
     <div class="modal-backdrop fixed inset-0 flex items-center justify-center p-2 sm:p-4">
         <div class="modal-content bg-[#2e3440] border border-[#434c5e] rounded-lg w-full max-w-sm sm:max-w-md mx-auto shadow-2xl">
             <div class="flex justify-between items-center p-3 sm:p-4 border-b border-[#434c5e]">
                 <h3 id="modalTitle" class="text-base sm:text-lg font-semibold text-[#eceff4]"><i class="bi bi-person-plus"></i> Add Contact</h3>
                 <button id="closeContactModalBtn" class="text-xl sm:text-2xl cursor-pointer text-[#d8dee9] hover:text-[#eceff4] transition-colors p-1">&times;</button>
             </div>
             <form id="contactForm" class="p-3 sm:p-4">
                 <input type="hidden" id="contactId" name="contactId">
                 <div class="mb-3 sm:mb-4">
                     <label for="contactLabel" class="block text-xs sm:text-sm font-medium text-[#e5e9f0] mb-1"><i class="bi bi-tag"></i> Contact Label:</label>
                     <input type="text" id="contactLabel" name="contactLabel" required placeholder="e.g. Police Station" class="w-full px-3 py-2 border border-[#434c5e] bg-[#3b4252] text-[#eceff4] placeholder-[#4c566a] rounded-md text-xs sm:text-sm focus:ring-2 focus:ring-[#5e81ac] focus:border-[#5e81ac] outline-none">
                 </div>
                 <div class="mb-3 sm:mb-4">
                     <label for="contactType" class="block text-xs sm:text-sm font-medium text-[#e5e9f0] mb-1"><i class="bi bi-telephone"></i> Contact Type:</label>
                     <select id="contactType" name="contactType" required class="w-full px-3 py-2 border border-[#434c5e] bg-[#3b4252] text-[#eceff4] rounded-md text-xs sm:text-sm focus:ring-2 focus:ring-[#5e81ac] focus:border-[#5e81ac] outline-none">
                         <option value="">Select Contact Type</option>
                         <option value="phone">Phone</option>
                         <option value="email">Email</option>
                         <option value="facebook">Facebook</option>
                     </select>
                 </div>
                 <div class="mb-3 sm:mb-4">
                     <label for="contactInformation" class="block text-xs sm:text-sm font-medium text-[#e5e9f0] mb-1"><i class="bi bi-info-circle"></i> Contact Information:</label>
                     <input type="text" id="contactInformation" name="contactInformation" required placeholder="e.g. 0917-123-4567" class="w-full px-3 py-2 border border-[#434c5e] bg-[#3b4252] text-[#eceff4] placeholder-[#4c566a] rounded-md text-xs sm:text-sm focus:ring-2 focus:ring-[#5e81ac] focus:border-[#5e81ac] outline-none">
                 </div>
                 <div class="flex flex-col xs:flex-row justify-end gap-2 pt-3 sm:pt-4 border-t border-[#434c5e]">
                     <button type="button" id="cancelContactBtn" class="bg-[#434c5e] hover:bg-[#4c566a] text-white px-4 py-2 rounded text-xs sm:text-sm transition-colors"><i class="bi bi-x-circle"></i> Cancel</button>
                     <button type="submit" id="saveContactBtn" class="bg-[#5e81ac] hover:bg-[#81a1c1] text-white px-4 py-2 rounded text-xs sm:text-sm transition-colors"><i class="bi bi-save"></i> Save</button>
                 </div>
             </form>
         </div>
     </div>
 </div>

 <!-- Delete Confirmation Modal -->
 <div id="deleteModal" class="fixed inset-0 hidden z-50">
     <div class="modal-backdrop fixed inset-0 flex items-center justify-center p-2 sm:p-4">
         <div class="modal-content bg-[#2e3440] border border-[#434c5e] rounded-lg w-full max-w-sm sm:max-w-md mx-auto shadow-2xl">
             <div class="flex justify-between items-center p-3 sm:p-4 border-b border-[#434c5e]">
                 <h3 class="text-base sm:text-lg font-semibold text-[#eceff4]"><i class="bi bi-exclamation-triangle text-[#bf616a]"></i> Delete Contact</h3>
                 <button id="closeDeleteModalBtn" class="text-xl sm:text-2xl cursor-pointer text-[#d8dee9] hover:text-[#eceff4] transition-colors p-1">&times;</button>
             </div>
             <div class="p-3 sm:p-4">
                 <p class="text-[#e5e9f0] mb-3 sm:mb-4 text-xs sm:text-sm">Are you sure you want to delete this contact?</p>
                 <div id="deleteContactInfo" class="bg-[#3b4252] p-3 rounded mb-3 sm:mb-4 border border-[#434c5e]">
                     <!-- Contact info will be displayed here -->
                 </div>
                 <div class="flex flex-col xs:flex-row justify-end gap-2 pt-3 sm:pt-4 border-t border-[#434c5e]">
                     <button type="button" id="cancelDeleteBtn" class="bg-[#434c5e] hover:bg-[#4c566a] text-white px-4 py-2 rounded text-xs sm:text-sm transition-colors"><i class="bi bi-x-circle"></i> Cancel</button>
                     <button type="button" id="confirmDeleteBtn" class="bg-[#bf616a] hover:bg-[#d08770] text-white px-4 py-2 rounded text-xs sm:text-sm transition-colors"><i class="bi bi-trash"></i> Delete</button>
                 </div>
             </div>
         </div>
     </div>
 </div>

 <script>
 (function() {
     'use strict';

     // Global variables
     let isEditMode = false;
     let editingContactId = null;
     let deleteContactId = null;
     let contacts = [];

     // API Base URL - handle undefined API function gracefully
     const API_BASE = typeof API === 'function' ? API() : '';

     // Utility functions
     function generateGuid() {
         return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
             var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
             return v.toString(16);
         });
     }

     function showToast(message, type = 'info') {
         const toastContainer = document.getElementById('toastContainer');
         const toast = document.createElement('div');
         toast.className = `toast ${type}`;
         toast.textContent = message;

         toastContainer.appendChild(toast);

         // Trigger animation
         setTimeout(() => {
             toast.classList.add('show');
         }, 10);

         // Auto remove after 4 seconds
         setTimeout(() => {
             toast.classList.remove('show');
             setTimeout(() => {
                 if (toast.parentNode) {
                     toast.parentNode.removeChild(toast);
                 }
             }, 300);
         }, 4000);
     }

     function showMessage(message, type = 'info') {
         showToast(message, type);
     }

     function showLoading(show = true) {
         const loadingIndicator = document.getElementById('loadingIndicator');
         const tableView = document.getElementById('contactsTableView');
         const noContactsMessage = document.getElementById('noContactsMessage');

         if (show) {
             loadingIndicator.classList.remove('hidden');
             if (tableView) tableView.classList.add('hidden');
             if (noContactsMessage) noContactsMessage.classList.add('hidden');
         } else {
             loadingIndicator.classList.add('hidden');
             if (tableView) tableView.classList.remove('hidden');
         }
     }

     // Modal functions
     function openContactModal(id = null, label = '', type = '', information = '') {
         const modal = document.getElementById('contactModal');
         const title = document.getElementById('modalTitle');
         const form = document.getElementById('contactForm');
         const backdrop = modal.querySelector('.modal-backdrop');
         const content = modal.querySelector('.modal-content');

         if (id) {
             // Edit mode
             isEditMode = true;
             editingContactId = id;
             title.innerHTML = '<i class="bi bi-pencil"></i> Edit Contact';
             document.getElementById('contactId').value = id;
             document.getElementById('contactLabel').value = label;
             document.getElementById('contactType').value = type;
             document.getElementById('contactInformation').value = information;
         } else {
             // Add mode
             isEditMode = false;
             editingContactId = null;
             title.innerHTML = '<i class="bi bi-person-plus"></i> Add Contact';
             form.reset();
             document.getElementById('contactId').value = generateGuid();
         }

         modal.classList.remove('hidden');
         setTimeout(() => {
             backdrop.classList.add('show');
             content.classList.add('show');
         }, 10);
     }

     function closeContactModal() {
         const modal = document.getElementById('contactModal');
         const backdrop = modal.querySelector('.modal-backdrop');
         const content = modal.querySelector('.modal-content');

         backdrop.classList.remove('show');
         content.classList.remove('show');

         setTimeout(() => {
             modal.classList.add('hidden');
             document.getElementById('contactForm').reset();
             isEditMode = false;
             editingContactId = null;
         }, 300);
     }

     function openDeleteModal(id, label, type, information) {
         const modal = document.getElementById('deleteModal');
         const deleteInfo = document.getElementById('deleteContactInfo');
         const backdrop = modal.querySelector('.modal-backdrop');
         const content = modal.querySelector('.modal-content');

         deleteContactId = id;

         deleteInfo.innerHTML = `
             <div class="text-xs sm:text-sm text-[#d8dee9]">
                 <p><strong>Label:</strong> ${label}</p>
                 <p><strong>Type:</strong> ${type}</p>
                 <p><strong>Information:</strong> ${information}</p>
             </div>
         `;

         modal.classList.remove('hidden');
         setTimeout(() => {
             backdrop.classList.add('show');
             content.classList.add('show');
         }, 10);
     }

     function closeDeleteModal() {
         const modal = document.getElementById('deleteModal');
         const backdrop = modal.querySelector('.modal-backdrop');
         const content = modal.querySelector('.modal-content');

         backdrop.classList.remove('show');
         content.classList.remove('show');

         setTimeout(() => {
             modal.classList.add('hidden');
             deleteContactId = null;
         }, 300);
     }

     // CRUD operations
     async function loadContacts() {
         try {
             showLoading(true);

             if (!API_BASE) {
                 console.warn('API_BASE not configured - using local storage fallback');
                 // Try to load from local storage if available
                 const storedContacts = localStorage.getItem('contacts');
                 if (storedContacts) {
                     contacts = JSON.parse(storedContacts);
                 } else {
                     contacts = [];
                 }
                 showLoading(false);
                 renderContacts();
                 if (contacts.length === 0) {
                     showToast('No API configured. Add contacts locally.', 'info');
                 }
                 return;
             }

             const response = await fetch(`${API_BASE}/api/v1/Admin/contact`, {
                 method: 'GET',
                 headers: {
                     'accept': '*/*',
                     'Content-Type': 'application/json'
                 }
             });

             showLoading(false);

             if (response.ok) {
                 const data = await response.json();
                 contacts = Array.isArray(data) ? data : [];
                 renderContacts();
                 if (contacts.length === 0) {
                     showToast('No contacts found', 'info');
                 }
             } else {
                 console.error('Failed to load contacts:', response.status, response.statusText);
                 showToast(`Failed to load contacts: ${response.status} ${response.statusText}`, 'error');
                 // Initialize empty contacts array so UI can still function
                 contacts = [];
                 renderContacts();
             }
         } catch (error) {
             showLoading(false);
             console.error('Error loading contacts:', error);
             showToast('Error loading contacts. Check your connection.', 'error');
             // Initialize empty contacts array so UI can still function
             contacts = [];
             renderContacts();
         }
     }

     function renderContacts() {
         const tbody = document.getElementById('contactsTableBody');
         const noContactsMessage = document.getElementById('noContactsMessage');

         if (tbody) tbody.innerHTML = '';

         if (contacts.length === 0) {
             if (noContactsMessage) noContactsMessage.classList.remove('hidden');
             return;
         } else {
             if (noContactsMessage) noContactsMessage.classList.add('hidden');
         }

         contacts.forEach(contact => {
             addContactToTable(contact.id, contact.label, contact.contactType, contact.contactInformation);
         });
     }

     async function saveContact(event) {
         event.preventDefault();

         const id = document.getElementById('contactId').value;
         const label = document.getElementById('contactLabel').value;
         const type = document.getElementById('contactType').value;
         const information = document.getElementById('contactInformation').value;

         try {
             if (!API_BASE) {
                 // Local storage fallback
                 const contact = { id, label, contactType: type, contactInformation: information };

                 if (isEditMode) {
                     const index = contacts.findIndex(c => c.id === id);
                     if (index !== -1) {
                         contacts[index] = contact;
                     }
                 } else {
                     contacts.push(contact);
                 }

                 // Save to local storage
                 localStorage.setItem('contacts', JSON.stringify(contacts));

                 renderContacts();
                 showToast(isEditMode ? 'Contact updated successfully!' : 'Contact added successfully!', 'success');
                 closeContactModal();
                 return;
             }

             const contactData = {
                 id: id,
                 label: label,
                 contactType: type,
                 contactInformation: information,
                 createdAt: new Date().toISOString(),
                 updatedAt: new Date().toISOString()
             };

             const response = await fetch(`${API_BASE}/api/v1/Admin/contact`, {
                 method: 'POST',
                 headers: {
                     'accept': '*/*',
                     'Content-Type': 'application/json'
                 },
                 body: JSON.stringify(contactData)
             });

             if (response.ok) {
                 // Update local contacts array instead of reloading
                 const contact = { id, label, contactType: type, contactInformation: information };

                 if (isEditMode) {
                     const index = contacts.findIndex(c => c.id === id);
                     if (index !== -1) {
                         contacts[index] = contact;
                     }
                 } else {
                     contacts.push(contact);
                 }

                 renderContacts();
                 showToast(isEditMode ? 'Contact updated successfully!' : 'Contact added successfully!', 'success');
             } else {
                 showToast('Failed to save contact', 'error');
             }
         } catch (error) {
             console.error('Error saving contact:', error);
             showToast('Error saving contact', 'error');
         }

         closeContactModal();
     }

     async function confirmDelete() {
         if (!deleteContactId) return;

         try {
             if (!API_BASE) {
                 // Local storage fallback
                 contacts = contacts.filter(c => c.id !== deleteContactId);
                 localStorage.setItem('contacts', JSON.stringify(contacts));
                 renderContacts();
                 showToast('Contact deleted successfully!', 'success');
                 closeDeleteModal();
                 return;
             }

             const response = await fetch(`${API_BASE}/api/v1/Admin/contact?Id=${deleteContactId}`, {
                 method: 'DELETE',
                 headers: {
                     'accept': '*/*'
                 }
             });

             if (response.ok) {
                 // Update local contacts array instead of reloading
                 contacts = contacts.filter(c => c.id !== deleteContactId);
                 renderContacts();
                 showToast('Contact deleted successfully!', 'success');
             } else {
                 showToast('Failed to delete contact', 'error');
             }
         } catch (error) {
             console.error('Error deleting contact:', error);
             showToast('Error deleting contact', 'error');
         }

         closeDeleteModal();
     }

     // Render functions
     function addContactToTable(id, label, type, information) {
         const tbody = document.getElementById('contactsTableBody');
         if (!tbody) return;

         const row = document.createElement('tr');
         row.setAttribute('data-id', id);
         row.className = 'border-b border-[#434c5e] hover:bg-[#3b4252] text-[#e5e9f0]';

         row.innerHTML = `
             <td class="px-4 py-2">${label}</td>
             <td class="px-4 py-2">${type}</td>
             <td class="px-4 py-2">${information}</td>
             <td class="px-4 py-2 text-center">
                 <button class="edit-contact text-[#88c0d0] hover:text-[#8fbcbb] mr-2" title="Edit" data-id="${id}" data-label="${label}" data-type="${type}" data-information="${information}">✏️</button>
                 <button class="delete-contact text-[#bf616a] hover:text-[#bf616a] opacity-80 hover:opacity-100" title="Delete" data-id="${id}" data-label="${label}" data-type="${type}" data-information="${information}">🗑️</button>
             </td>
         `;

         tbody.appendChild(row);
     }

     // Event listeners
     function initializeEventListeners() {
         // Add contact button
         const addContactBtn = document.getElementById('addContactBtn');
         if (addContactBtn) {
             addContactBtn.addEventListener('click', () => openContactModal());
         }

         // Contact form
         const contactForm = document.getElementById('contactForm');
         if (contactForm) {
             contactForm.addEventListener('submit', saveContact);
         }

         // Close contact modal buttons
         const closeContactModalBtn = document.getElementById('closeContactModalBtn');
         const cancelContactBtn = document.getElementById('cancelContactBtn');
         if (closeContactModalBtn) {
             closeContactModalBtn.addEventListener('click', closeContactModal);
         }
         if (cancelContactBtn) {
             cancelContactBtn.addEventListener('click', closeContactModal);
         }

         // Close delete modal buttons
         const closeDeleteModalBtn = document.getElementById('closeDeleteModalBtn');
         const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
         if (closeDeleteModalBtn) {
             closeDeleteModalBtn.addEventListener('click', closeDeleteModal);
         }
         if (cancelDeleteBtn) {
             cancelDeleteBtn.addEventListener('click', closeDeleteModal);
         }

         // Confirm delete button
         const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
         if (confirmDeleteBtn) {
             confirmDeleteBtn.addEventListener('click', confirmDelete);
         }

         // Event delegation for dynamically created buttons
         document.addEventListener('click', function(e) {
             if (e.target.classList.contains('edit-contact')) {
                 const id = e.target.dataset.id;
                 const label = e.target.dataset.label;
                 const type = e.target.dataset.type;
                 const information = e.target.dataset.information;
                 openContactModal(id, label, type, information);
             }

             if (e.target.classList.contains('delete-contact')) {
                 const id = e.target.dataset.id;
                 const label = e.target.dataset.label;
                 const type = e.target.dataset.type;
                 const information = e.target.dataset.information;
                 openDeleteModal(id, label, type, information);
             }
         });
     }

     // Initialize
     document.addEventListener('DOMContentLoaded', function() {
         initializeEventListeners();
         loadContacts();
     });

 })();
 </script>
