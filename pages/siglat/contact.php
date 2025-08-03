 <!-- Contact List Modal -->
 <div id="contactListModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
     <div class="flex items-center justify-center min-h-screen p-2 sm:p-4">
         <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-[95vh] overflow-hidden mx-2 sm:mx-4">
             <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 sm:p-6 border-b bg-gray-50 gap-2 sm:gap-0">
                 <h3 class="text-lg sm:text-xl font-semibold text-gray-800"><i class="bi bi-people mr-2"></i> Contact Management</h3>
                 <button class="text-gray-400 hover:text-gray-600 text-3xl font-light leading-none self-end sm:self-auto" onclick="closeContactListModal()">&times;</button>
             </div>
             <div class="p-4 sm:p-6">
                 <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                     <div class="text-sm text-gray-600">
                         <i class="bi bi-info-circle mr-1"></i>
                         <span class="hidden sm:inline">Manage your emergency contacts and important information</span>
                         <span class="sm:hidden">Manage contacts</span>
                     </div>
                     <div class="flex gap-2 w-full sm:w-auto">
                         <button class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow-sm transition duration-200 ease-in-out text-sm" onclick="toggleViewMode()" id="viewToggleBtn">
                             <i class="bi bi-grid-3x3-gap mr-1"></i> Cards
                         </button>
                         <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 sm:px-6 py-2 rounded-lg shadow-sm transition duration-200 ease-in-out transform hover:scale-105 flex-1 sm:flex-none text-sm sm:text-base" onclick="openContactModal()">
                             <i class="bi bi-plus-circle mr-2"></i> Add Contact
                         </button>
                     </div>
                 </div>

                 <!-- Table View -->
                 <div id="tableView" class="hidden max-h-96 overflow-y-auto border border-gray-200 rounded-lg shadow-sm">
                     <table class="w-full">
                         <thead class="bg-gray-100 sticky top-0">
                             <tr>
                                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                     <i class="bi bi-tag mr-1"></i> Label
                                 </th>
                                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                     <i class="bi bi-telephone mr-1"></i> Type
                                 </th>
                                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                     <i class="bi bi-info-circle mr-1"></i> Information
                                 </th>
                                 <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                     <i class="bi bi-tools mr-1"></i> Actions
                                 </th>
                             </tr>
                         </thead>
                         <tbody id="contactsTableBody" class="bg-white divide-y divide-gray-200">
                             <!-- Contacts will be loaded from API -->
                         </tbody>
                     </table>
                 </div>

                 <!-- Card View -->
                 <div id="cardView" class="block max-h-96 overflow-y-auto">
                     <div id="contactsCardContainer" class="grid gap-4">
                         <!-- Contacts will be loaded from API -->
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>

 <!-- Contact Modal -->
 <div id="contactModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
     <div class="flex items-center justify-center min-h-screen p-2 sm:p-4">
         <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-2 sm:mx-4">
             <div class="flex justify-between items-center p-4 sm:p-6 border-b bg-gray-50">
                 <h3 id="modalTitle" class="text-lg sm:text-xl font-semibold text-gray-800"><i class="bi bi-person-plus mr-2"></i> Add Contact</h3>
                 <button class="text-gray-400 hover:text-gray-600 text-3xl font-light leading-none" onclick="closeContactModal()">&times;</button>
             </div>
             <form id="contactForm" onsubmit="saveContact(event)" class="p-4 sm:p-6">
                 <div class="hidden mb-4">
                     <label for="contactId" class="block text-sm font-medium text-gray-700 mb-2">ID:</label>
                     <input type="text" id="contactId" name="contactId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                 </div>
                 <div class="mb-6">
                     <label for="contactLabel" class="block text-sm font-medium text-gray-700 mb-2">
                         <i class="bi bi-tag mr-1"></i> Contact Label
                     </label>
                     <input type="text" id="contactLabel" name="contactLabel" required placeholder="e.g. Police Station" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                 </div>
                 <div class="mb-6">
                     <label for="contactType" class="block text-sm font-medium text-gray-700 mb-2">
                         <i class="bi bi-telephone mr-1"></i> Contact Type
                     </label>
                     <select id="contactType" name="contactType" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                         <option value="">Select Contact Type</option>
                         <option value="phone">📞 Phone</option>
                         <option value="email">📧 Email</option>
                         <option value="facebook">📘 Facebook</option>
                     </select>
                 </div>
                 <div class="mb-6">
                     <label for="contactInformation" class="block text-sm font-medium text-gray-700 mb-2">
                         <i class="bi bi-info-circle mr-1"></i> Contact Information
                     </label>
                     <input type="text" id="contactInformation" name="contactInformation" required placeholder="e.g. 0917-123-4567" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                 </div>
                 <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t">
                     <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200 ease-in-out order-2 sm:order-1" onclick="closeContactModal()">
                         <i class="bi bi-x-circle mr-2"></i> Cancel
                     </button>
                     <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition duration-200 ease-in-out transform hover:scale-105 order-1 sm:order-2">
                         <i class="bi bi-save mr-2"></i> Save
                     </button>
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
 let currentViewMode = 'card'; // 'table' or 'card'

 function generateGuid() {
     return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
         var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
         return v.toString(16);
     });
 }

 function API() {
     return '<?php echo isset($API) ? $API : ""; ?>';
 }

 // Toggle between table and card view
 function toggleViewMode() {
     const tableView = document.getElementById('tableView');
     const cardView = document.getElementById('cardView');
     const toggleBtn = document.getElementById('viewToggleBtn');

     if (currentViewMode === 'table') {
         currentViewMode = 'card';
         tableView.classList.add('hidden');
         cardView.classList.remove('hidden');
         toggleBtn.innerHTML = '<i class="bi bi-table mr-1"></i> Table';
     } else {
         currentViewMode = 'table';
         tableView.classList.remove('hidden');
         cardView.classList.add('hidden');
         toggleBtn.innerHTML = '<i class="bi bi-grid-3x3-gap mr-1"></i> Cards';
     }
 }

 // Load contacts from API
 async function loadContacts() {
     try {
         const response = await fetch(`${API()}/api/v1/Admin/contact`, {
             method: 'GET',
             headers: {
                 'accept': '*/*',
                 'Content-Type': 'application/json'
             }
         });

         if (response.ok) {
             const contacts = await response.json();
             const tbody = document.getElementById('contactsTableBody');
             const cardContainer = document.getElementById('contactsCardContainer');
             tbody.innerHTML = '';
             cardContainer.innerHTML = '';

             if (contacts.length === 0) {
                 tbody.innerHTML = `
                     <tr>
                         <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                             <i class="bi bi-inbox text-3xl mb-2 block"></i>
                             No contacts found. Add your first contact to get started.
                         </td>
                     </tr>
                 `;
                 cardContainer.innerHTML = `
                     <div class="text-center text-gray-500 py-8">
                         <i class="bi bi-inbox text-3xl mb-2 block"></i>
                         No contacts found. Add your first contact to get started.
                     </div>
                 `;
             } else {
                 contacts.forEach(contact => {
                     addContactToTable(contact.id, contact.label, contact.contactType, contact.contactInformation);
                     addContactToCards(contact.id, contact.label, contact.contactType, contact.contactInformation);
                 });
             }
         } else {
             console.error('Failed to load contacts');
             showToast('Failed to load contacts', 'error');
         }
     } catch (error) {
         console.error('Error loading contacts:', error);
         showToast('Error loading contacts', 'error');
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
         title.innerHTML = '<i class="bi bi-pencil mr-2"></i> Edit Contact';
         document.getElementById('contactId').value = id;
         document.getElementById('contactLabel').value = label;
         document.getElementById('contactType').value = type;
         document.getElementById('contactInformation').value = information;
         document.getElementById('contactId').disabled = true;
     } else {
         // Add mode
         isEditMode = false;
         editingContactId = null;
         title.innerHTML = '<i class="bi bi-person-plus mr-2"></i> Add Contact';
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

         const response = await fetch(`${API()}/api/v1/Admin/contact`, {
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
                 updateContactInCards(id, label, type, information);
                 showToast('Contact updated successfully!', 'success');
             } else {
                 // Remove empty state if it exists
                 const tbody = document.getElementById('contactsTableBody');
                 const cardContainer = document.getElementById('contactsCardContainer');
                 if (tbody.querySelector('td[colspan="4"]')) {
                     tbody.innerHTML = '';
                 }
                 if (cardContainer.querySelector('.text-center')) {
                     cardContainer.innerHTML = '';
                 }
                 addContactToTable(id, label, type, information);
                 addContactToCards(id, label, type, information);
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
     row.className = 'hover:bg-gray-50 transition duration-150';

     // Get type icon
     let typeIcon = '';
     switch(type) {
         case 'phone': typeIcon = '📞'; break;
         case 'email': typeIcon = '📧'; break;
         case 'facebook': typeIcon = '📘'; break;
         default: typeIcon = '📋';
     }

     row.innerHTML = `
         <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${label}</td>
         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
             <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                 ${typeIcon} ${type}
             </span>
         </td>
         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${information}</td>
         <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
             <button class="text-blue-600 hover:text-blue-900 mr-3 p-1 rounded transition duration-150" title="Edit" onclick="editContact('${id}', '${label}', '${type}', '${information}')">
                 <i class="bi bi-pencil-square text-lg"></i>
             </button>
             <button class="text-red-600 hover:text-red-900 p-1 rounded transition duration-150" title="Delete" onclick="deleteContact('${id}')">
                 <i class="bi bi-trash text-lg"></i>
             </button>
         </td>
     `;

     tbody.appendChild(row);
 }

 function addContactToCards(id, label, type, information) {
     const cardContainer = document.getElementById('contactsCardContainer');
     const card = document.createElement('div');
     card.setAttribute('data-id', id);
     card.className = 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-150';

     // Get type icon
     let typeIcon = '';
     switch(type) {
         case 'phone': typeIcon = '📞'; break;
         case 'email': typeIcon = '📧'; break;
         case 'facebook': typeIcon = '📘'; break;
         default: typeIcon = '📋';
     }

     card.innerHTML = `
         <div class="flex justify-between items-start mb-3">
             <h4 class="text-lg font-medium text-gray-900 truncate pr-2">${label}</h4>
             <div class="flex gap-2 flex-shrink-0">
                 <button class="text-blue-600 hover:text-blue-900 p-1 rounded transition duration-150" title="Edit" onclick="editContact('${id}', '${label}', '${type}', '${information}')">
                     <i class="bi bi-pencil-square"></i>
                 </button>
                 <button class="text-red-600 hover:text-red-900 p-1 rounded transition duration-150" title="Delete" onclick="deleteContact('${id}')">
                     <i class="bi bi-trash"></i>
                 </button>
             </div>
         </div>
         <div class="mb-2">
             <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                 ${typeIcon} ${type}
             </span>
         </div>
         <p class="text-sm text-gray-600 break-all">${information}</p>
     `;

     cardContainer.appendChild(card);
 }

 function updateContactInTable(id, label, type, information) {
     const tbody = document.getElementById('contactsTableBody');
     const rows = tbody.getElementsByTagName('tr');

     // Get type icon
     let typeIcon = '';
     switch(type) {
         case 'phone': typeIcon = '📞'; break;
         case 'email': typeIcon = '📧'; break;
         case 'facebook': typeIcon = '📘'; break;
         default: typeIcon = '📋';
     }

     for (let row of rows) {
         if (row.getAttribute('data-id') === id) {
             row.cells[0].textContent = label;
             row.cells[1].innerHTML = `
                 <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                     ${typeIcon} ${type}
                 </span>
             `;
             row.cells[2].textContent = information;
             row.cells[3].innerHTML = `
                 <button class="text-blue-600 hover:text-blue-900 mr-3 p-1 rounded transition duration-150" title="Edit" onclick="editContact('${id}', '${label}', '${type}', '${information}')">
                     <i class="bi bi-pencil-square text-lg"></i>
                 </button>
                 <button class="text-red-600 hover:text-red-900 p-1 rounded transition duration-150" title="Delete" onclick="deleteContact('${id}')">
                     <i class="bi bi-trash text-lg"></i>
                 </button>
             `;
             break;
         }
     }
 }

 function updateContactInCards(id, label, type, information) {
     const cardContainer = document.getElementById('contactsCardContainer');
     const cards = cardContainer.querySelectorAll('[data-id]');

     // Get type icon
     let typeIcon = '';
     switch(type) {
         case 'phone': typeIcon = '📞'; break;
         case 'email': typeIcon = '📧'; break;
         case 'facebook': typeIcon = '📘'; break;
         default: typeIcon = '📋';
     }

     for (let card of cards) {
         if (card.getAttribute('data-id') === id) {
             card.innerHTML = `
                 <div class="flex justify-between items-start mb-3">
                     <h4 class="text-lg font-medium text-gray-900 truncate pr-2">${label}</h4>
                     <div class="flex gap-2 flex-shrink-0">
                         <button class="text-blue-600 hover:text-blue-900 p-1 rounded transition duration-150" title="Edit" onclick="editContact('${id}', '${label}', '${type}', '${information}')">
                             <i class="bi bi-pencil-square"></i>
                         </button>
                         <button class="text-red-600 hover:text-red-900 p-1 rounded transition duration-150" title="Delete" onclick="deleteContact('${id}')">
                             <i class="bi bi-trash"></i>
                         </button>
                     </div>
                 </div>
                 <div class="mb-2">
                     <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                         ${typeIcon} ${type}
                     </span>
                 </div>
                 <p class="text-sm text-gray-600 break-all">${information}</p>
             `;
             break;
         }
     }
 }

 function editContact(id, label, type, information) {
     openContactModal(id, label, type, information);
 }

 async function deleteContact(id) {
     if (confirm('Are you sure you want to delete this contact? This action cannot be undone.')) {
         try {
             const response = await fetch(`${API()}/api/v1/Admin/contact?Id=${id}`, {
                 method: 'DELETE',
                 headers: {
                     'accept': '*/*'
                 }
             });

             if (response.ok) {
                 const tbody = document.getElementById('contactsTableBody');
                 const cardContainer = document.getElementById('contactsCardContainer');
                 const rows = tbody.getElementsByTagName('tr');
                 const cards = cardContainer.querySelectorAll('[data-id]');

                 // Remove from table
                 for (let i = 0; i < rows.length; i++) {
                     if (rows[i].getAttribute('data-id') === id) {
                         tbody.removeChild(rows[i]);
                         break;
                     }
                 }

                 // Remove from cards
                 for (let card of cards) {
                     if (card.getAttribute('data-id') === id) {
                         cardContainer.removeChild(card);
                         break;
                     }
                 }

                 // Check if both views are empty and show empty state
                 if (tbody.children.length === 0) {
                     tbody.innerHTML = `
                         <tr>
                             <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                 <i class="bi bi-inbox text-3xl mb-2 block"></i>
                                 No contacts found. Add your first contact to get started.
                             </td>
                         </tr>
                     `;
                 }

                 if (cardContainer.children.length === 0) {
                     cardContainer.innerHTML = `
                         <div class="text-center text-gray-500 py-8">
                             <i class="bi bi-inbox text-3xl mb-2 block"></i>
                             No contacts found. Add your first contact to get started.
                         </div>
                     `;
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
     let icon = type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-exclamation-circle' : 'bi-info-circle';

     toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-4 sm:px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-y-0 opacity-100 max-w-xs sm:max-w-sm text-sm sm:text-base`;
     toast.innerHTML = `
         <div class="flex items-center">
             <i class="bi ${icon} mr-2"></i>
             <span>${message}</span>
         </div>
     `;

     document.body.appendChild(toast);

     setTimeout(() => {
         toast.style.transform = 'translateY(100%)';
         toast.style.opacity = '0';
         setTimeout(() => {
             if (document.body.contains(toast)) {
                 document.body.removeChild(toast);
             }
         }, 300);
     }, 3000);
 }

 // Modal open/close logic for contact list
 function openContactListModal() {
     document.getElementById('contactListModal').classList.remove('hidden');
     // Default to card view when opening
     document.getElementById('tableView').classList.add('hidden');
     document.getElementById('cardView').classList.remove('hidden');
     currentViewMode = 'card';
 }

 function closeContactListModal() {
     document.getElementById('contactListModal').classList.add('hidden');
 }

 loadContacts();

 </script>
