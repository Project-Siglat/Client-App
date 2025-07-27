 <!-- Contact List Modal -->
 <div id="contactListModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
     <div class="flex items-center justify-center min-h-screen p-4">
         <div class="bg-white rounded-lg max-w-2xl w-full">
             <div class="flex justify-between items-center p-4 border-b">
                 <h3 class="text-lg font-semibold"><i class="bi bi-people"></i> Contact Management</h3>
                 <span class="text-2xl cursor-pointer hover:text-gray-600" onclick="closeContactListModal()">&times;</span>
             </div>
             <div class="p-4">
                 <div class="mb-4 flex justify-end">
                     <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded" onclick="openContactModal()">
                         <i class="bi bi-plus-circle"></i> Add Contact
                     </button>
                 </div>
                 <div class="max-h-80 overflow-y-auto border rounded">
                     <table class="w-full">
                         <thead class="bg-gray-50">
                             <tr>
                                 <th class="px-4 py-2 text-left"><i class="bi bi-tag"></i> Label</th>
                                 <th class="px-4 py-2 text-left"><i class="bi bi-telephone"></i> Type</th>
                                 <th class="px-4 py-2 text-left"><i class="bi bi-info-circle"></i> Information</th>
                                 <th class="px-4 py-2 text-center"><i class="bi bi-tools"></i> Actions</th>
                             </tr>
                         </thead>
                         <tbody id="contactsTableBody">
                             <!-- Contacts will be loaded from API -->
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
     </div>
 </div>

 <!-- Contact Modal -->
 <div id="contactModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
     <div class="flex items-center justify-center min-h-screen p-4">
         <div class="bg-white rounded-lg max-w-md w-full">
             <div class="flex justify-between items-center p-4 border-b">
                 <h3 id="modalTitle" class="text-lg font-semibold"><i class="bi bi-person-plus"></i> Add Contact</h3>
                 <span class="text-2xl cursor-pointer hover:text-gray-600" onclick="closeContactModal()">&times;</span>
             </div>
             <form id="contactForm" onsubmit="saveContact(event)" class="p-4">
                 <div class="hidden mb-4">
                     <label for="contactId" class="block text-sm font-medium text-gray-700 mb-1">ID:</label>
                     <input type="text" id="contactId" name="contactId" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                 </div>
                 <div class="mb-4">
                     <label for="contactLabel" class="block text-sm font-medium text-gray-700 mb-1"><i class="bi bi-tag"></i> Contact Label:</label>
                     <input type="text" id="contactLabel" name="contactLabel" required placeholder="e.g. Police Station" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                 </div>
                 <div class="mb-4">
                     <label for="contactType" class="block text-sm font-medium text-gray-700 mb-1"><i class="bi bi-telephone"></i> Contact Type:</label>
                     <select id="contactType" name="contactType" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                         <option value="">Select Contact Type</option>
                         <option value="phone">Phone</option>
                         <option value="email">Email</option>
                         <option value="facebook">Facebook</option>
                     </select>
                 </div>
                 <div class="mb-4">
                     <label for="contactInformation" class="block text-sm font-medium text-gray-700 mb-1"><i class="bi bi-info-circle"></i> Contact Information:</label>
                     <input type="text" id="contactInformation" name="contactInformation" required placeholder="e.g. 0917-123-4567" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                 </div>
                 <div class="flex justify-end gap-2 pt-4 border-t">
                     <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded" onclick="closeContactModal()"><i class="bi bi-x-circle"></i> Cancel</button>
                     <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"><i class="bi bi-save"></i> Save</button>
                 </div>
             </form>
         </div>
     </div>
 </div>


 <script>
 // Contact CRUD operations
 let isEditMode = false;
 let editingContactId = null;
 let contactIdCounter = 6; // Start from 6 since we have 5 sample contacts

 const API_BASE = '<?php echo $API; ?>';

 function generateGuid() {
     return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
         var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
         return v.toString(16);
     });
 }

 // Load contacts from API
 async function loadContacts() {
     try {
         const response = await fetch(`${API_BASE}/api/v1/Admin/contact`, {
             method: 'GET',
             headers: {
                 'accept': '*/*',
                 'Content-Type': 'application/json'
             }
         });

         if (response.ok) {
             const contacts = await response.json();
             const tbody = document.getElementById('contactsTableBody');
             tbody.innerHTML = '';

             contacts.forEach(contact => {
                 addContactToTable(contact.id, contact.label, contact.contactType, contact.contactInformation);
             });
         } else {
             console.error('Failed to load contacts');
         }
     } catch (error) {
         console.error('Error loading contacts:', error);
     }
 }

 function openContactModal(id = null, label = '', type = '', information = '') {
     const modal = document.getElementById('contactModal');
     const title = document.getElementById('modalTitle');
     const form = document.getElementById('contactForm');

     if (id) {
         // Edit mode
         isEditMode = true;
         editingContactId = id;
         title.textContent = 'Edit Contact';
         document.getElementById('contactId').value = id;
         document.getElementById('contactLabel').value = label;
         document.getElementById('contactType').value = type;
         document.getElementById('contactInformation').value = information;
         document.getElementById('contactId').disabled = true;
     } else {
         // Add mode
         isEditMode = false;
         editingContactId = null;
         title.textContent = 'Add Contact';
         form.reset();
         document.getElementById('contactId').value = generateGuid();
         document.getElementById('contactLabel').value = '';
         document.getElementById('contactId').disabled = true;
     }

     modal.classList.remove('hidden');
 }

 function closeContactModal() {
     const modal = document.getElementById('contactModal');
     modal.classList.add('hidden');
     document.getElementById('contactForm').reset();
     isEditMode = false;
     editingContactId = null;
 }

 async function saveContact(event) {
     event.preventDefault();

     const id = document.getElementById('contactId').value;
     const label = document.getElementById('contactLabel').value;
     const type = document.getElementById('contactType').value;
     const information = document.getElementById('contactInformation').value;

     try {
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
             if (isEditMode) {
                 updateContactInTable(id, label, type, information);
                 showToast('Contact updated successfully!', 'success');
             } else {
                 addContactToTable(id, label, type, information);
                 contactIdCounter++;
                 showToast('Contact added successfully!', 'success');
             }
         } else {
             showToast('Failed to save contact. Please try again.', 'error');
         }
     } catch (error) {
         console.error('Error saving contact:', error);
         showToast('Error saving contact. Please try again.', 'error');
     }

     closeContactModal();
 }

 function addContactToTable(id, label, type, information) {
     const tbody = document.getElementById('contactsTableBody');
     const row = document.createElement('tr');
     row.setAttribute('data-id', id);
     row.className = 'border-b hover:bg-gray-50';

     row.innerHTML = `
         <td class="px-4 py-2">${label}</td>
         <td class="px-4 py-2">${type}</td>
         <td class="px-4 py-2">${information}</td>
         <td class="px-4 py-2 text-center">
             <button class="text-blue-600 hover:text-blue-800 mr-2" title="Edit" onclick="editContact('${id}', '${label}', '${type}', '${information}')">✏️</button>
             <button class="text-red-600 hover:text-red-800" title="Delete" onclick="deleteContact('${id}')">🗑️</button>
         </td>
     `;

     tbody.appendChild(row);
 }

 function updateContactInTable(id, label, type, information) {
     const tbody = document.getElementById('contactsTableBody');
     const rows = tbody.getElementsByTagName('tr');

     for (let row of rows) {
         if (row.getAttribute('data-id') === id) {
             row.cells[0].textContent = label;
             row.cells[1].textContent = type;
             row.cells[2].textContent = information;
             row.cells[3].innerHTML = `
                 <button class="text-blue-600 hover:text-blue-800 mr-2" title="Edit" onclick="editContact('${id}', '${label}', '${type}', '${information}')">✏️</button>
                 <button class="text-red-600 hover:text-red-800" title="Delete" onclick="deleteContact('${id}')">🗑️</button>
             `;
             break;
         }
     }
 }

 function editContact(id, label, type, information) {
     openContactModal(id, label, type, information);
 }

 async function deleteContact(id) {
     if (confirm('Are you sure you want to delete this contact?')) {
         try {
             const response = await fetch(`${API_BASE}/api/v1/Admin/contact?Id=${id}`, {
                 method: 'DELETE',
                 headers: {
                     'accept': '*/*'
                 }
             });

             if (response.ok) {
                 const tbody = document.getElementById('contactsTableBody');
                 const rows = tbody.getElementsByTagName('tr');

                 for (let i = 0; i < rows.length; i++) {
                     if (rows[i].getAttribute('data-id') === id) {
                         tbody.removeChild(rows[i]);
                         break;
                     }
                 }
                 showToast('Contact deleted successfully!', 'success');
             } else {
                 showToast('Failed to delete contact. Please try again.', 'error');
             }
         } catch (error) {
             console.error('Error deleting contact:', error);
             showToast('Error deleting contact. Please try again.', 'error');
         }
     }
 }

 // Toast notification
 function showToast(message, type = 'info') {
     let toast = document.createElement('div');
     let bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
     toast.className = `fixed bottom-4 left-1/2 transform -translate-x-1/2 ${bgColor} text-white px-4 py-2 rounded shadow-lg z-50 transition-opacity duration-300`;
     toast.innerHTML = `<span>${message}</span>`;
     document.body.appendChild(toast);
     setTimeout(() => {
         toast.style.opacity = '0';
         setTimeout(() => document.body.removeChild(toast), 300);
     }, 2000);
 }

 // Modal open/close logic for contact list
 function openContactListModal() {
     document.getElementById('contactListModal').classList.remove('hidden');
 }
 function closeContactListModal() {
     document.getElementById('contactListModal').classList.add('hidden');
 }
 loadContacts();

 </script>
