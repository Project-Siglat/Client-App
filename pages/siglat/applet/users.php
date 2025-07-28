<?php
include "./config/env.php";
$API = $_ENV["API"];

// Make API call to get user list
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $API . "/api/v1/Admin/userlist",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["accept: */*"],
]);

$response = curl_exec($curl);
curl_close($curl);

// Decode the JSON response
$users = json_decode($response, true);

// Make API call to get gender options
$genderCurl = curl_init();
curl_setopt_array($genderCurl, [
    CURLOPT_URL => $API . "/api/v1/Admin/genders",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["accept: */*"],
]);

$genderResponse = curl_exec($genderCurl);
curl_close($genderCurl);

// Decode the gender options (fallback to default if API fails)
$genders = json_decode($genderResponse, true);
if (!$genders || !is_array($genders)) {
    $genders = ["Male", "Female"];
}
?>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-0 left-1/2 transform -translate-x-1/2 z-50" style="top: 65px;">
    <div id="toast" class="hidden px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 min-w-80 max-w-md">
        <svg id="toast-icon" class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"></svg>
        <span id="toast-message" class="text-sm font-medium"></span>
        <button onclick="hideToast()" class="ml-auto text-sm hover:opacity-80">×</button>
    </div>
</div>

<div class="bg-nord0 min-h-screen p-3 md:p-4" style="background-color: #2E3440;">
    <div class="max-w-full mx-auto">
        <!-- <h1 class="text-xl md:text-2xl font-bold mb-4 text-center" style="color: #88C0D0;">User Management</h1> -->

        <?php if (!empty($users) && is_array($users)): ?>

        <!-- Table Layout -->
        <div class="w-full rounded-lg overflow-hidden shadow border" style="background-color: #3B4252; border-color: #4C566A;">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-xs uppercase border-b" style="background-color: #434C5E; color: #88C0D0; border-color: #4C566A;">Name & Role</th>
                        <th class="px-4 py-3 text-left font-semibold text-xs uppercase border-b" style="background-color: #434C5E; color: #88C0D0; border-color: #4C566A;">Personal Info</th>
                        <th class="px-4 py-3 text-left font-semibold text-xs uppercase border-b" style="background-color: #434C5E; color: #88C0D0; border-color: #4C566A;">Contact</th>
                        <th class="px-4 py-3 text-left font-semibold text-xs uppercase border-b" style="background-color: #434C5E; color: #88C0D0; border-color: #4C566A;">Address</th>
                        <th class="px-4 py-3 text-left font-semibold text-xs uppercase border-b" style="background-color: #434C5E; color: #88C0D0; border-color: #4C566A;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $index => $user): ?>
                    <tr id="user-row-<?php echo htmlspecialchars(
                        $user["id"] ?? "",
                    ); ?>" class="hover:bg-opacity-50" style="background-color: #3B4252;" onmouseover="this.style.backgroundColor='#434C5E'" onmouseout="this.style.backgroundColor='#3B4252'">
                        <td class="px-4 py-3 border-b align-top" style="border-color: #4C566A; color: #D8DEE9;">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" style="color: #88C0D0;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold mb-1 text-sm user-name" style="color: #88C0D0;">
                                        <?php
                                        $fullName = trim(
                                            ($user["firstName"] ?? "") .
                                                " " .
                                                ($user["middleName"] ?? "") .
                                                " " .
                                                ($user["lastName"] ?? ""),
                                        );
                                        echo htmlspecialchars(
                                            $fullName ?: "No Name",
                                        );
                                        ?>
                                    </div>
                                    <span class="user-role inline-block px-2 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide" style="background-color: #BF616A; color: #ECEFF4;"><?php echo htmlspecialchars(
                                        $user["role"] ?? "User",
                                    ); ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 border-b align-top" style="border-color: #4C566A; color: #D8DEE9;">
                            <div class="text-xs font-semibold mb-1 uppercase" style="color: #4C566A;">Gender</div>
                            <div class="user-gender mb-2 text-sm"><?php echo htmlspecialchars(
                                $user["gender"] ?? "Not specified",
                            ); ?></div>
                            <div class="text-xs font-semibold mb-1 uppercase" style="color: #4C566A;">Date of Birth</div>
                            <div class="user-dob text-sm"><?php
                            $dateOfBirth = $user["dateOfBirth"] ?? "";
                            if (!empty($dateOfBirth)) {
                                $date = new DateTime($dateOfBirth);
                                echo htmlspecialchars($date->format("M d, Y"));
                            } else {
                                echo "Not specified";
                            }
                            ?></div>
                        </td>
                        <td class="px-4 py-3 border-b align-top" style="border-color: #4C566A; color: #D8DEE9;">
                            <div class="flex items-center gap-1 mb-2">
                                <svg class="w-4 h-4" style="color: #88C0D0;" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                                <span class="user-email text-sm"><?php echo htmlspecialchars(
                                    $user["email"] ?? "No email",
                                ); ?></span>
                            </div>
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" style="color: #88C0D0;" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                                <span class="user-phone text-sm"><?php echo htmlspecialchars(
                                    $user["phoneNumber"] ?? "No phone",
                                ); ?></span>
                            </div>
                        </td>
                        <td class="user-address px-4 py-3 border-b align-top text-sm" style="border-color: #4C566A; color: #D8DEE9;">
                            <?php echo htmlspecialchars(
                                $user["address"] ?? "Not specified",
                            ); ?>
                        </td>
                        <td class="px-4 py-3 border-b align-top" style="border-color: #4C566A;">
                            <div class="flex gap-2">
                                <button onclick="openEditModal('<?php echo htmlspecialchars(
                                    $user["id"] ?? "",
                                ); ?>')" class="px-2 py-1 rounded text-xs font-semibold" style="background-color: #88C0D0; color: #2E3440;" title="Edit User">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                </button>
                                <button onclick="openDeleteModal('<?php echo htmlspecialchars(
                                    $user["id"] ?? "",
                                ); ?>', '<?php echo htmlspecialchars(
    $fullName ?: "Unknown User",
); ?>')" class="px-2 py-1 rounded text-xs font-semibold" style="background-color: #BF616A; color: #ECEFF4;" title="Delete User">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php else: ?>
        <div class="border px-4 py-3 rounded-lg text-center max-w-md mx-auto" style="background-color: #BF616A; border-color: #D08770; color: #ECEFF4;">
            <svg class="w-6 h-6 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <p class="font-semibold">No users found or error retrieving data.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
        <div class="rounded-lg shadow-lg p-6 w-full max-w-md relative" style="background-color: #3B4252;">
            <button class="absolute top-2 right-2 hover:text-gray-200" style="color: #D8DEE9;" onclick="closeEditModal()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <h2 class="text-xl font-bold mb-4 text-center" style="color: #88C0D0;">Edit User</h2>
            <!-- Error message area -->
            <div id="edit-modal-error" class="text-sm mb-2 text-center" style="color: #BF616A;"></div>
            <form onsubmit="event.preventDefault(); submitEditForm();">
                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: #D8DEE9;">First Name</label>
                        <input id="edit_firstName" type="text" class="w-full px-3 py-2 rounded border focus:outline-none focus:ring-2" style="background-color: #2E3440; color: #D8DEE9; border-color: #4C566A; focus:ring-color: #88C0D0;" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: #D8DEE9;">Middle Name</label>
                        <input id="edit_middleName" type="text" class="w-full px-3 py-2 rounded border focus:outline-none focus:ring-2" style="background-color: #2E3440; color: #D8DEE9; border-color: #4C566A; focus:ring-color: #88C0D0;" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: #D8DEE9;">Last Name</label>
                        <input id="edit_lastName" type="text" class="w-full px-3 py-2 rounded border focus:outline-none focus:ring-2" style="background-color: #2E3440; color: #D8DEE9; border-color: #4C566A; focus:ring-color: #88C0D0;" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: #D8DEE9;">Role</label>
                        <select id="edit_role" class="w-full px-3 py-2 rounded border focus:outline-none focus:ring-2" style="background-color: #2E3440; color: #D8DEE9; border-color: #4C566A; focus:ring-color: #88C0D0;">
                            <option value="">Select Role</option>
                            <option value="User">User</option>
                            <option value="Admin">Admin</option>
                            <option value="Ambulance">Ambulance</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: #D8DEE9;">Gender</label>
                        <select id="edit_gender" class="w-full px-3 py-2 rounded border focus:outline-none focus:ring-2" style="background-color: #2E3440; color: #D8DEE9; border-color: #4C566A; focus:ring-color: #88C0D0;">
                            <option value="">Select Gender</option>
                            <?php foreach ($genders as $gender): ?>
                                <option value="<?php echo htmlspecialchars(
                                    $gender,
                                ); ?>"><?php echo htmlspecialchars(
    ucfirst($gender),
); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: #D8DEE9;">Date of Birth</label>
                        <input id="edit_dateOfBirth" type="date" class="w-full px-3 py-2 rounded border focus:outline-none focus:ring-2" style="background-color: #2E3440; color: #D8DEE9; border-color: #4C566A; focus:ring-color: #88C0D0;" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: #D8DEE9;">Email</label>
                        <input id="edit_email" type="email" class="w-full px-3 py-2 rounded border focus:outline-none focus:ring-2" style="background-color: #2E3440; color: #D8DEE9; border-color: #4C566A; focus:ring-color: #88C0D0;" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: #D8DEE9;">Phone Number</label>
                        <input id="edit_phoneNumber" type="text" class="w-full px-3 py-2 rounded border focus:outline-none focus:ring-2" style="background-color: #2E3440; color: #D8DEE9; border-color: #4C566A; focus:ring-color: #88C0D0;" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: #D8DEE9;">Address</label>
                        <input id="edit_address" type="text" class="w-full px-3 py-2 rounded border focus:outline-none focus:ring-2" style="background-color: #2E3440; color: #D8DEE9; border-color: #4C566A; focus:ring-color: #88C0D0;" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded font-semibold hover:opacity-80 transition" style="background-color: #4C566A; color: #D8DEE9;" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded font-semibold hover:opacity-80 transition" style="background-color: #88C0D0; color: #2E3440;">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
        <div class="rounded-lg shadow-lg p-6 w-full max-w-sm relative" style="background-color: #3B4252;">
            <button class="absolute top-2 right-2 hover:text-gray-200" style="color: #D8DEE9;" onclick="closeDeleteModal()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="flex flex-col items-center">
                <svg class="w-10 h-10 mb-2" style="color: #BF616A;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <h2 class="text-lg font-bold mb-2" style="color: #88C0D0;">Delete User</h2>
                <p class="mb-4" style="color: #D8DEE9;">Are you sure you want to delete <span id="delete-user-name" class="font-semibold" style="color: #88C0D0;"></span>?</p>
                <div class="flex gap-2">
                    <button class="px-4 py-2 rounded font-semibold hover:opacity-80 transition" style="background-color: #4C566A; color: #D8DEE9;" onclick="closeDeleteModal()">Cancel</button>
                    <button class="px-4 py-2 rounded font-semibold hover:opacity-80 transition" style="background-color: #BF616A; color: #ECEFF4;" onclick="confirmDeleteUser()">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentEditUserId = null;
let currentDeleteUserId = null;
const API_BASE = '<?php echo $API; ?>';

// Users array that can be modified
<?php echo "let users = " . json_encode($users) . ";"; ?>

// Toast Functions
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    const toastIcon = document.getElementById('toast-icon');

    toastMessage.textContent = message;

    if (type === 'success') {
        toast.style.backgroundColor = '#A3BE8C';
        toast.style.color = '#2E3440';
        toastIcon.innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>';
    } else {
        toast.style.backgroundColor = '#BF616A';
        toast.style.color = '#ECEFF4';
        toastIcon.innerHTML = '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>';
    }

    toast.classList.remove('hidden');

    setTimeout(() => {
        hideToast();
    }, 4000);
}

function hideToast() {
    document.getElementById('toast').classList.add('hidden');
}

// Edit Modal Functions
function openEditModal(userId) {
    currentEditUserId = userId;
    // Find user data from the existing users array

    const user = users.find(u => u.id === userId);
    if (user) {
        document.getElementById('edit_firstName').value = user.firstName || '';
        document.getElementById('edit_middleName').value = user.middleName || '';
        document.getElementById('edit_lastName').value = user.lastName || '';
        document.getElementById('edit_role').value = user.role || '';
        document.getElementById('edit_gender').value = user.gender || '';
        document.getElementById('edit_dateOfBirth').value = user.dateOfBirth ? user.dateOfBirth.split('T')[0] : '';
        document.getElementById('edit_email').value = user.email || '';
        document.getElementById('edit_phoneNumber').value = user.phoneNumber || '';
        document.getElementById('edit_address').value = user.address || '';
    }

    document.getElementById('edit-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
    document.getElementById('edit-modal-error').textContent = '';
    currentEditUserId = null;
}

function updateUserInTable(userData) {
    const row = document.getElementById('user-row-' + userData.id);
    if (!row) return;

    // Update name
    const fullName = ((userData.firstName || '') + ' ' + (userData.middleName || '') + ' ' + (userData.lastName || '')).trim() || 'No Name';
    row.querySelector('.user-name').textContent = fullName;

    // Update role
    row.querySelector('.user-role').textContent = userData.role || 'User';

    // Update gender
    row.querySelector('.user-gender').textContent = userData.gender || 'Not specified';

    // Update date of birth
    let dobText = 'Not specified';
    if (userData.dateOfBirth) {
        const date = new Date(userData.dateOfBirth);
        dobText = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }
    row.querySelector('.user-dob').textContent = dobText;

    // Update email
    row.querySelector('.user-email').textContent = userData.email || 'No email';

    // Update phone
    row.querySelector('.user-phone').textContent = userData.phoneNumber || 'No phone';

    // Update address
    row.querySelector('.user-address').textContent = userData.address || 'Not specified';

    // Update users array
    const userIndex = users.findIndex(u => u.id === userData.id);
    if (userIndex !== -1) {
        users[userIndex] = userData;
    }
}

async function submitEditForm() {
    if (!currentEditUserId) return;

    const userData = {
        id: currentEditUserId,
        firstName: document.getElementById('edit_firstName').value,
        middleName: document.getElementById('edit_middleName').value,
        lastName: document.getElementById('edit_lastName').value,
        role: document.getElementById('edit_role').value,
        gender: document.getElementById('edit_gender').value,
        dateOfBirth: document.getElementById('edit_dateOfBirth').value ? new Date(document.getElementById('edit_dateOfBirth').value).toISOString() : null,
        email: document.getElementById('edit_email').value,
        phoneNumber: document.getElementById('edit_phoneNumber').value,
        address: document.getElementById('edit_address').value,
        hashPass: "secret"
    };

    try {
        const response = await fetch(API_BASE + '/api/v1/Admin/update-user', {
            method: 'POST',
            headers: {
                'accept': '*/*',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        });

        if (response.ok) {
            updateUserInTable(userData);
            closeEditModal();
            showToast('User updated successfully!', 'success');
        } else {
            const errorText = await response.text();
            document.getElementById('edit-modal-error').textContent = 'Error updating user: ' + errorText;
            showToast('Failed to update user: ' + errorText, 'error');
        }
    } catch (error) {
        document.getElementById('edit-modal-error').textContent = 'Error updating user: ' + error.message;
        showToast('Error updating user: ' + error.message, 'error');
    }
}

// Delete Modal Functions
function openDeleteModal(userId, userName) {
    currentDeleteUserId = userId;
    document.getElementById('delete-user-name').textContent = userName;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    currentDeleteUserId = null;
}

async function confirmDeleteUser() {
    if (!currentDeleteUserId) return;

    try {
        const response = await fetch(API_BASE + '/api/v1/Admin/user?Id=' + currentDeleteUserId, {
            method: 'DELETE',
            headers: {
                'accept': '*/*'
            }
        });

        if (response.ok) {
            closeDeleteModal();
            showToast('User deleted successfully!', 'success');
            location.reload(); // Refresh the page to show updated data
        } else {
            const errorText = await response.text();
            showToast('Failed to delete user: ' + errorText, 'error');
        }
    } catch (error) {
        showToast('Error deleting user: ' + error.message, 'error');
    }
}
</script>
