<!-- Contact List Table/Cards -->
 <div id="contactListTable" class="bg-[#2e3440] rounded-lg shadow">
     <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 border-b border-[#434c5e] gap-2">
         <h3 class="text-lg font-semibold text-[#eceff4]"><i class="bi bi-people"></i> Contact Management</h3>
         <button class="bg-[#5e81ac] hover:bg-[#81a1c1] text-white px-4 py-2 rounded text-sm sm:text-base" onclick="openContactModal()">
             <i class="bi bi-plus-circle"></i> <span class="hidden sm:inline">Add Contact</span><span class="sm:hidden">Add</span>
         </button>
     </div>
     <div class="p-4">
         <!-- Table View (Desktop) -->
         <div class="hidden md:block max-h-80 overflow-y-auto border border-[#434c5e] rounded">
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

         <!-- Card View (Mobile/Tablet) -->
         <div class="md:hidden max-h-80 overflow-y-auto space-y-3" id="contactsCardContainer">
             <!-- Contact cards will be loaded here -->
         </div>
     </div>
 </div>

 <!-- Toast Container -->
 <div id="toastContainer" class="fixed right-4 space-y-2" style="top: 65px; z-index: 2147483647;"></div>

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
     min-width: 300px;
     padding: 12px 16px;
     border-radius: 8px;
     color: white;
     font-size: 14px;
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
 </style>

 <!-- Contact Modal -->
 <div id="contactModal" class="fixed inset-0 hidden z-50">
     <div class="modal-backdrop fixed inset-0 flex items-center justify-center p-4">
         <div class="modal-content bg-[#2e3440] border border-[#434c5e] rounded-lg w-full max-w-md mx-auto shadow-2xl">
             <div class="flex justify-between items-center p-4 border-b border-[#434c5e]">
                 <h3 id="modalTitle" class="text-lg font-semibold text-[#eceff4]"><i class="bi bi-person-plus"></i> Add Contact</h3>
                 <button class="text-2xl cursor-pointer text-[#d8dee9] hover:text-[#eceff4] transition-colors p-1" onclick="closeContactModal()">&times;</button>
             </div>
             <form id="contactForm" onsubmit="saveContact(event)" class="p-4">
                 <input type="hidden" id="contactId" name="contactId">
                 <div class="mb-4">
                     <label for="contactLabel" class="block text-sm font-medium text-[#e5e9f0] mb-1"><i class="bi bi-tag"></i> Contact Label:</label>
                     <input type="text" id="contactLabel" name="contactLabel" required placeholder="e.g. Police Station" class="w-full px-3 py-2 border border-[#434c5e] bg-[#3b4252] text-[#eceff4] placeholder-[#4c566a] rounded-md text-sm focus:ring-2 focus:ring-[#5e81ac] focus:border-[#5e81ac] outline-none">
                 </div>
                 <div class="mb-4">
                     <label for="contactType" class="block text-sm font-medium text-[#e5e9f0] mb-1"><i class="bi bi-telephone"></i> Contact Type:</label>
                     <select id="contactType" name="contactType" required class="w-full px-3 py-2 border border-[#434c5e] bg-[#3b4252] text-[#eceff4] rounded-md text-sm focus:ring-2 focus:ring-[#5e81ac] focus:border-[#5e81ac] outline-none">
                         <option value="">Select Contact Type</option>
                         <option value="phone">Phone</option>
                         <option value="email">Email</option>
                         <option value="facebook">Facebook</option>
                     </select>
                 </div>
                 <div class="mb-4">
                     <label for="contactInformation" class="block text-sm font-medium text-[#e5e9f0] mb-1"><i class="bi bi-info-circle"></i> Contact Information:</label>
                     <input type="text" id="contactInformation" name="contactInformation" required placeholder="e.g. 0917-123-4567" class="w-full px-3 py-2 border border-[#434c5e] bg-[#3b4252] text-[#eceff4] placeholder-[#4c566a] rounded-md text-sm focus:ring-2 focus:ring-[#5e81ac] focus:border-[#5e81ac] outline-none">
                 </div>
                 <div class="flex flex-col sm:flex-row justify-end gap-2 pt-4 border-t border-[#434c5e]">
                     <button type="button" class="bg-[#434c5e] hover:bg-[#4c566a] text-white px-4 py-2 rounded text-sm transition-colors" onclick="closeContactModal()"><i class="bi bi-x-circle"></i> Cancel</button>
                     <button type="submit" class="bg-[#5e81ac] hover:bg-[#81a1c1] text-white px-4 py-2 rounded text-sm transition-colors"><i class="bi bi-save"></i> Save</button>
                 </div>
             </form>
         </div>
     </div>
 </div>

 <!-- Delete Confirmation Modal -->
 <div id="deleteModal" class="fixed inset-0 hidden z-50">
     <div class="modal-backdrop fixed inset-0 flex items-center justify-center p-4">
         <div class="modal-content bg-[#2e3440] border border-[#434c5e] rounded-lg w-full max-w-md mx-auto shadow-2xl">
             <div class="flex justify-between items-center p-4 border-b border-[#434c5e]">
                 <h3 class="text-lg font-semibold text-[#eceff4]"><i class="bi bi-exclamation-triangle text-[#bf616a]"></i> Delete Contact</h3>
                 <button class="text-2xl cursor-pointer text-[#d8dee9] hover:text-[#eceff4] transition-colors p-1" onclick="closeDeleteModal()">&times;</button>
             </div>
             <div class="p-4">
                 <p class="text-[#e5e9f0] mb-4 text-sm">Are you sure you want to delete this contact?</p>
                 <div id="deleteContactInfo" class="bg-[#3b4252] p-3 rounded mb-4 border border-[#434c5e]">
                     <!-- Contact info will be displayed here -->
                 </div>
                 <div class="flex flex-col sm:flex-row justify-end gap-2 pt-4 border-t border-[#434c5e]">
                     <button type="button" class="bg-[#434c5e] hover:bg-[#4c566a] text-white px-4 py-2 rounded text-sm transition-colors" onclick="closeDeleteModal()"><i class="bi bi-x-circle"></i> Cancel</button>
                     <button type="button" id="confirmDeleteBtn" class="bg-[#bf616a] hover:bg-[#d08770] text-white px-4 py-2 rounded text-sm transition-colors" onclick="confirmDelete()"><i class="bi bi-trash"></i> Delete</button>
                 </div>
             </div>
         </div>
     </div>
 </div>

 <script>
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
         <div class="text-sm text-[#d8dee9]">
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
         if (!API_BASE) {
             console.warn('API_BASE not configured');
             return;
         }

         const response = await fetch(`${API_BASE}/api/v1/Admin/contact`, {
             method: 'GET',
             headers: {
                 'accept': '*/*',
                 'Content-Type': 'application/json'
             }
         });

         if (response.ok) {
             contacts = await response.json();
             renderContacts();
         } else {
             console.error('Failed to load contacts');
         }
     } catch (error) {
         console.error('Error loading contacts:', error);
     }
 }

 function renderContacts() {
     const tbody = document.getElementById('contactsTableBody');
     const cardContainer = document.getElementById('contactsCardContainer');

     tbody.innerHTML = '';
     cardContainer.innerHTML = '';

     contacts.forEach(contact => {
         addContactToTable(contact.id, contact.label, contact.contactType, contact.contactInformation);
         addContactToCards(contact.id, contact.label, contact.contactType, contact.contactInformation);
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
     const row = document.createElement('tr');
     row.setAttribute('data-id', id);
     row.className = 'border-b border-[#434c5e] hover:bg-[#3b4252] text-[#e5e9f0]';

     row.innerHTML = `
         <td class="px-4 py-2">${label}</td>
         <td class="px-4 py-2">${type}</td>
         <td class="px-4 py-2">${information}</td>
         <td class="px-4 py-2 text-center">
             <button class="text-[#88c0d0] hover:text-[#8fbcbb] mr-2" title="Edit" onclick="editContact('${id}', '${label}', '${type}', '${information}')">✏️</button>
             <button class="text-[#bf616a] hover:text-[#bf616a] opacity-80 hover:opacity-100" title="Delete" onclick="deleteContact('${id}', '${label}', '${type}', '${information}')">🗑️</button>
         </td>
     `;

     tbody.appendChild(row);
 }

 function addContactToCards(id, label, type, information) {
     const cardContainer = document.getElementById('contactsCardContainer');
     const card = document.createElement('div');
     card.setAttribute('data-id', id);
     card.className = 'bg-[#3b4252] border border-[#434c5e] rounded-lg p-4 hover:bg-[#434c5e] transition-colors';

     card.innerHTML = `
         <div class="flex justify-between items-start mb-2">
             <h4 class="text-[#eceff4] font-semibold text-sm">${label}</h4>
             <div class="flex gap-2 ml-2">
                 <button class="text-[#88c0d0] hover:text-[#8fbcbb] text-sm" title="Edit" onclick="editContact('${id}', '${label}', '${type}', '${information}')">✏️</button>
                 <button class="text-[#bf616a] hover:text-[#bf616a] opacity-80 hover:opacity-100 text-sm" title="Delete" onclick="deleteContact('${id}', '${label}', '${type}', '${information}')">🗑️</button>
             </div>
         </div>
         <div class="space-y-1">
             <p class="text-[#d8dee9] text-xs"><i class="bi bi-telephone"></i> <strong>Type:</strong> ${type}</p>
             <p class="text-[#d8dee9] text-xs break-all"><i class="bi bi-info-circle"></i> <strong>Info:</strong> ${information}</p>
         </div>
     `;

     cardContainer.appendChild(card);
 }

 // Helper functions for button clicks
 function editContact(id, label, type, information) {
     openContactModal(id, label, type, information);
 }

 function deleteContact(id, label, type, information) {
     openDeleteModal(id, label, type, information);
 }

 // Initialize
 document.addEventListener('DOMContentLoaded', function() {
     loadContacts();
 });

 </script>
