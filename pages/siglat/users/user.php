<?php
include "./config/env.php";
$API = $_ENV["API"];
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        let isLoading = true;
        let currentEditUser = null;
        let currentDeleteUser = null;

        // Toast notification logic
        function showToast(message, type = 'success', duration = 3000) {
            // Remove any existing toast
            const oldToast = document.getElementById('toast-notification');
            if (oldToast) oldToast.remove();

            // Create toast container
            const toast = document.createElement('div');
            toast.id = 'toast-notification';
            toast.className = `fixed top-6 right-6 z-[9999] px-4 py-3 rounded shadow-lg flex items-center gap-2 transition-all
                ${type === 'success' ? 'bg-yellow-400 text-black' : ''}
                ${type === 'error' ? 'bg-red-600 text-white' : ''}
                ${type === 'info' ? 'bg-gray-800 text-yellow-300' : ''}
            `;
            // Icon
            let icon = '';
            if (type === 'success') {
                icon = `<svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>`;
            } else if (type === 'error') {
                icon = `<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>`;
            } else {
                icon = `<svg class="w-5 h-5 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"/>
                </svg>`;
            }
            toast.innerHTML = `${icon}<span class="font-semibold">${message}</span>`;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, duration);
        }

        window.addEventListener('load', function() {
            // Simulate loading delay to show animation
            setTimeout(function() {
                isLoading = false;
                document.getElementById('loading-container').style.display = 'none';
                document.getElementById('content-container').style.display = 'block';
            }, 1000);
        });

        function openEditModal(userId) {
            currentEditUser = userId;
            document.getElementById('edit-modal').classList.remove('hidden');
            // Fill form fields with user data
            const user = window.usersList[userId];
            document.getElementById('edit_firstName').value = user.firstName ?? '';
            document.getElementById('edit_middleName').value = user.middleName ?? '';
            document.getElementById('edit_lastName').value = user.lastName ?? '';
            // Set role select value
            document.getElementById('edit_role').value = user.role ?? '';
            // Set gender select value, and ensure the correct option is selected
            const genderSelect = document.getElementById('edit_gender');
            let genderValue = (user.gender ?? '').toLowerCase();
            // Set the value if it matches one of the options, else set to ""
            if (genderValue === "male" || genderValue === "female") {
                genderSelect.value = genderValue;
            } else {
                genderSelect.value = "";
            }
            // Fix: Convert dateOfBirth to yyyy-MM-dd for input[type=date]
            let dob = user.dateOfBirth ?? '';
            if (dob) {
                // Try to parse and format as yyyy-MM-dd
                let d = new Date(dob);
                if (!isNaN(d.getTime())) {
                    // Pad month and day
                    let month = (d.getMonth() + 1).toString().padStart(2, '0');
                    let day = d.getDate().toString().padStart(2, '0');
                    let formatted = d.getFullYear() + '-' + month + '-' + day;
                    document.getElementById('edit_dateOfBirth').value = formatted;
                } else {
                    document.getElementById('edit_dateOfBirth').value = '';
                }
            } else {
                document.getElementById('edit_dateOfBirth').value = '';
            }
            document.getElementById('edit_email').value = user.email ?? '';
            document.getElementById('edit_phoneNumber').value = user.phoneNumber ?? '';
            document.getElementById('edit_address').value = user.address ?? '';
            // Clear previous error messages
            document.getElementById('edit-modal-error').textContent = '';
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }

        // Update user via API
        async function submitEditForm() {
            const user = window.usersList[currentEditUser];
            // Build payload according to IdentityDto
            const payload = {
                Id: user.id ?? user.Id ?? user.ID ?? '', // Try to get the ID from user object
                FirstName: document.getElementById('edit_firstName').value,
                MiddleName: document.getElementById('edit_middleName').value,
                LastName: document.getElementById('edit_lastName').value,
                Address: document.getElementById('edit_address').value,
                Gender: document.getElementById('edit_gender').value,
                PhoneNumber: document.getElementById('edit_phoneNumber').value,
                Role: document.getElementById('edit_role').value,
                // Fix: DateOfBirth must be sent as ISO string or yyyy-MM-dd
                DateOfBirth: document.getElementById('edit_dateOfBirth').value ? new Date(document.getElementById('edit_dateOfBirth').value).toISOString() : null,
                Email: document.getElementById('edit_email').value,
                HashPass: user.hashPass ?? user.HashPass ?? '', // Not editable, send original
                CreatedAt: user.createdAt ?? user.CreatedAt ?? '', // Not editable, send original
                UpdatedAt: new Date().toISOString() // Set to now
            };

            // Send PATCH request to .NET API
            try {
                const response = await fetch('<?php echo $API; ?>/api/v1/Admin/update-user', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': '*/*'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    showToast('User updated successfully!', 'success');
                    // Optionally, refresh page or update local user data
                    closeEditModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1200);
                } else {
                    const errorText = await response.text();
                    // Try to parse errorText as JSON
                    let errorMsg = 'Failed to update user.';
                    try {
                        const errObj = JSON.parse(errorText);
                        if (errObj && errObj.errors) {
                            errorMsg = '';
                            for (const key in errObj.errors) {
                                errorMsg += errObj.errors[key].join(' ') + ' ';
                            }
                        } else if (errObj && errObj.title) {
                            errorMsg = errObj.title;
                        }
                    } catch (e) {
                        errorMsg = errorText;
                    }
                    // Show error in modal
                    document.getElementById('edit-modal-error').textContent = errorMsg.trim();
                    showToast(errorMsg.trim(), 'error');
                }
            } catch (err) {
                document.getElementById('edit-modal-error').textContent = 'Error updating user: ' + err;
                showToast('Error updating user: ' + err, 'error');
            }
        }

        function openDeleteModal(userId) {
            currentDeleteUser = userId;
            document.getElementById('delete-modal').classList.remove('hidden');
            // Set user name in modal
            const user = window.usersList[userId];
            const fullName = [user.firstName, user.middleName, user.lastName].filter(Boolean).join(' ');
            document.getElementById('delete-user-name').textContent = fullName || 'No Name';
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }

        // REWRITE: Delete user via API
        async function confirmDeleteUser() {
            const user = window.usersList[currentDeleteUser];
            const userId = user.id ?? user.Id ?? user.ID ?? null;
            if (!userId) {
                showToast('User ID not found.', 'error');
                closeDeleteModal();
                return;
            }

            try {
                const response = await fetch(`<?php echo $API; ?>/api/v1/Admin/user?Id=${encodeURIComponent(userId)}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': '*/*'
                    }
                });

                if (response.ok) {
                    showToast('User deleted successfully!', 'success');
                    closeDeleteModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1200);
                } else {
                    const errorText = await response.text();
                    let errorMsg = 'Failed to delete user.';
                    // Custom error message for connected data
                    let customErrorMsg = "Cannot delete this data because it has connected data into it. Contact the support if ever.";
                    try {
                        const errObj = JSON.parse(errorText);
                        // If error is about connected data, show custom message
                        if (
                            (errObj && errObj.title && (
                                errObj.title.toLowerCase().includes("foreign key") ||
                                errObj.title.toLowerCase().includes("constraint") ||
                                errObj.title.toLowerCase().includes("connected") ||
                                errObj.title.toLowerCase().includes("related") ||
                                errObj.title.toLowerCase().includes("reference")
                            )) ||
                            (errObj && errObj.errors && Object.values(errObj.errors).some(arr =>
                                arr.some(msg =>
                                    msg.toLowerCase().includes("foreign key") ||
                                    msg.toLowerCase().includes("constraint") ||
                                    msg.toLowerCase().includes("connected") ||
                                    msg.toLowerCase().includes("related") ||
                                    msg.toLowerCase().includes("reference")
                                )
                            ))
                        ) {
                            errorMsg = customErrorMsg;
                        } else if (errObj && errObj.errors) {
                            errorMsg = '';
                            for (const key in errObj.errors) {
                                errorMsg += errObj.errors[key].join(' ') + ' ';
                            }
                        } else if (errObj && errObj.title) {
                            errorMsg = errObj.title;
                        }
                    } catch (e) {
                        // If errorText contains keywords, show custom message
                        if (
                            errorText.toLowerCase().includes("foreign key") ||
                            errorText.toLowerCase().includes("constraint") ||
                            errorText.toLowerCase().includes("connected") ||
                            errorText.toLowerCase().includes("related") ||
                            errorText.toLowerCase().includes("reference")
                        ) {
                            errorMsg = customErrorMsg;
                        } else {
                            errorMsg = errorText;
                        }
                    }
                    showToast(errorMsg.trim(), 'error');
                    closeDeleteModal();
                }
            } catch (err) {
                showToast('Error deleting user: ' + err, 'error');
                closeDeleteModal();
            }
        }
    </script>
    <style>
        /* Spinner animation for loading */
        .spinner-tailwind {
            border-top-color: #fbbf24;
            border-right-color: #374151;
            border-bottom-color: #374151;
            border-left-color: #374151;
            border-width: 3px;
            border-style: solid;
            border-radius: 9999px;
            width: 24px;
            height: 24px;
            animation: spin-tailwind 1s linear infinite;
        }
        @keyframes spin-tailwind {
            to { transform: rotate(360deg); }
        }
        /* Loading dots animation */
        .loading-dots-tailwind::after {
            content: '';
            animation: dots-tailwind 1.5s linear infinite;
        }
        @keyframes dots-tailwind {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }
    </style>
</head>
<body class="bg-black min-h-screen">

<div id="loading-container" class="bg-black min-h-screen p-6 flex flex-col items-center justify-center">
    <h1 class="text-2xl font-bold text-yellow-400 mb-4">User</h1>
    <div class="bg-gray-900 rounded-lg shadow-lg p-4 text-center">
        <div class="spinner-tailwind mb-3"></div>
        <p class="text-yellow-300 text-sm">Loading users<span class="loading-dots-tailwind"></span></p>
    </div>
</div>

<div id="content-container" style="display: none;">
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
echo "<script>window.usersList = " . json_encode($users ?? []) . ";</script>";
?>

<div class="bg-black min-h-screen p-3 md:p-4">
    <div class="max-w-full mx-auto">
        <h1 class="text-xl md:text-2xl font-bold text-yellow-400 mb-4 text-center">User Management</h1>

        <?php if (!empty($users) && is_array($users)): ?>

        <!-- Table Layout for Desktop -->
        <div class="hidden md:block">
            <div class="w-full bg-gray-800 rounded-lg overflow-hidden shadow border border-gray-700">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="bg-gray-700 text-yellow-400 px-4 py-3 text-left font-semibold text-xs uppercase border-b border-gray-600">Name & Role</th>
                            <th class="bg-gray-700 text-yellow-400 px-4 py-3 text-left font-semibold text-xs uppercase border-b border-gray-600">Personal Info</th>
                            <th class="bg-gray-700 text-yellow-400 px-4 py-3 text-left font-semibold text-xs uppercase border-b border-gray-600">Contact</th>
                            <th class="bg-gray-700 text-yellow-400 px-4 py-3 text-left font-semibold text-xs uppercase border-b border-gray-600">Address</th>
                            <th class="bg-gray-700 text-yellow-400 px-4 py-3 text-left font-semibold text-xs uppercase border-b border-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $index => $user): ?>
                        <tr class="hover:bg-gray-700">
                            <td class="px-4 py-3 border-b border-gray-700 text-gray-200 align-top">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <div class="text-yellow-400 font-semibold mb-1 text-sm">
                                            <?php
                                            $fullName = trim(
                                                ($user["firstName"] ?? "") .
                                                    " " .
                                                    ($user["middleName"] ??
                                                        "") .
                                                    " " .
                                                    ($user["lastName"] ?? ""),
                                            );
                                            echo htmlspecialchars(
                                                $fullName ?: "No Name",
                                            );
                                            ?>
                                        </div>
                                        <span class="inline-block bg-red-600 text-white px-2 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide"><?php echo htmlspecialchars(
                                            $user["role"] ?? "User",
                                        ); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-b border-gray-700 text-gray-200 align-top">
                                <div class="text-xs text-gray-400 font-semibold mb-1 uppercase">Gender</div>
                                <div class="mb-2 text-sm"><?php echo htmlspecialchars(
                                    $user["gender"] ?? "Not specified",
                                ); ?></div>
                                <div class="text-xs text-gray-400 font-semibold mb-1 uppercase">Date of Birth</div>
                                <div class="text-sm"><?php
                                $dateOfBirth = $user["dateOfBirth"] ?? "";
                                if (!empty($dateOfBirth)) {
                                    $date = new DateTime($dateOfBirth);
                                    echo htmlspecialchars(
                                        $date->format("M d, Y"),
                                    );
                                } else {
                                    echo "Not specified";
                                }
                                ?></div>
                            </td>
                            <td class="px-4 py-3 border-b border-gray-700 text-gray-200 align-top">
                                <div class="flex items-center gap-1 mb-2">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                    </svg>
                                    <span class="text-sm"><?php echo htmlspecialchars(
                                        $user["email"] ?? "No email",
                                    ); ?></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                    </svg>
                                    <span class="text-sm"><?php echo htmlspecialchars(
                                        $user["phoneNumber"] ?? "No phone",
                                    ); ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-b border-gray-700 text-gray-200 align-top text-sm">
                                <?php echo htmlspecialchars(
                                    $user["address"] ?? "Not specified",
                                ); ?>
                            </td>
                            <td class="px-4 py-3 border-b border-gray-700 align-top">
                                <div class="flex gap-2">
                                    <button class="inline-flex items-center gap-1 px-2 py-1 rounded bg-green-600 hover:bg-green-700 text-white text-xs font-semibold transition" onclick="openEditModal(<?php echo $index; ?>)">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    <button class="inline-flex items-center gap-1 px-2 py-1 rounded bg-red-600 hover:bg-red-700 text-white text-xs font-semibold transition" onclick="openDeleteModal(<?php echo $index; ?>)">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9zM4 5a2 2 0 012-2v1a3 3 0 003 3h2a3 3 0 003-3V3a2 2 0 012 2v6.5l1.5 1.5A1 1 0 0116 15H4a1 1 0 01-.8-.4L4 5zm3.293 7.293a1 1 0 011.414 0L9 12.586l.293-.293a1 1 0 011.414 1.414L10.414 14l.293.293a1 1 0 01-1.414 1.414L9 15.414l-.293.293a1 1 0 01-1.414-1.414L7.586 14l-.293-.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card Layout for Mobile -->
        <div class="block md:hidden">
            <?php foreach ($users as $index => $user): ?>
            <div class="bg-gray-800 rounded-lg p-4 shadow border border-gray-700 mb-4 transition hover:-translate-y-1 hover:shadow-lg hover:border-gray-600">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-yellow-400 font-semibold text-base">
                        <?php
                        $fullName = trim(
                            ($user["firstName"] ?? "") .
                                " " .
                                ($user["middleName"] ?? "") .
                                " " .
                                ($user["lastName"] ?? ""),
                        );
                        echo htmlspecialchars($fullName ?: "No Name");
                        ?>
                    </span>
                    <span class="inline-block bg-red-600 text-white px-2 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide"><?php echo htmlspecialchars(
                        $user["role"] ?? "User",
                    ); ?></span>
                </div>
                <div class="grid grid-cols-1 gap-2 mt-2">
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase">Gender</span>
                        <span class="block text-sm text-gray-200"><?php echo htmlspecialchars(
                            $user["gender"] ?? "Not specified",
                        ); ?></span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase">Date of Birth</span>
                        <span class="block text-sm text-gray-200"><?php
                        $dateOfBirth = $user["dateOfBirth"] ?? "";
                        if (!empty($dateOfBirth)) {
                            $date = new DateTime($dateOfBirth);
                            echo htmlspecialchars($date->format("M d, Y"));
                        } else {
                            echo "Not specified";
                        }
                        ?></span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase">Address</span>
                        <span class="block text-sm text-gray-200"><?php echo htmlspecialchars(
                            $user["address"] ?? "Not specified",
                        ); ?></span>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-700">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        <span class="text-sm text-gray-200"><?php echo htmlspecialchars(
                            $user["email"] ?? "No email",
                        ); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                        </svg>
                        <span class="text-sm text-gray-200"><?php echo htmlspecialchars(
                            $user["phoneNumber"] ?? "No phone",
                        ); ?></span>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-700">
                    <div class="flex gap-2">
                        <button class="inline-flex items-center gap-1 px-2 py-1 rounded bg-green-600 hover:bg-green-700 text-white text-xs font-semibold transition" onclick="openEditModal(<?php echo $index; ?>)">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                            </svg>
                            Edit
                        </button>
                        <button class="inline-flex items-center gap-1 px-2 py-1 rounded bg-red-600 hover:bg-red-700 text-white text-xs font-semibold transition" onclick="openDeleteModal(<?php echo $index; ?>)">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9zM4 5a2 2 0 012-2v1a3 3 0 003 3h2a3 3 0 003-3V3a2 2 0 012 2v6.5l1.5 1.5A1 1 0 0116 15H4a1 1 0 01-.8-.4L4 5zm3.293 7.293a1 1 0 011.414 0L9 12.586l.293-.293a1 1 0 011.414 1.414L10.414 14l.293.293a1 1 0 01-1.414 1.414L9 15.414l-.293.293a1 1 0 01-1.414-1.414L7.586 14l-.293-.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Edit Modal -->
        <div id="edit-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
            <div class="bg-gray-900 rounded-lg shadow-lg p-6 w-full max-w-md relative">
                <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-200" onclick="closeEditModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <h2 class="text-xl font-bold text-yellow-400 mb-4 text-center">Edit User</h2>
                <!-- Error message area -->
                <div id="edit-modal-error" class="text-red-400 text-sm mb-2 text-center"></div>
                <form onsubmit="event.preventDefault(); submitEditForm();">
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <label class="block text-xs text-gray-400 font-semibold mb-1">First Name</label>
                            <input id="edit_firstName" type="text" class="w-full px-3 py-2 rounded bg-gray-800 text-gray-200 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 font-semibold mb-1">Middle Name</label>
                            <input id="edit_middleName" type="text" class="w-full px-3 py-2 rounded bg-gray-800 text-gray-200 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 font-semibold mb-1">Last Name</label>
                            <input id="edit_lastName" type="text" class="w-full px-3 py-2 rounded bg-gray-800 text-gray-200 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 font-semibold mb-1">Role</label>
                            <select id="edit_role" class="w-full px-3 py-2 rounded bg-gray-800 text-gray-200 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="">Select Role</option>
                                <option value="User">User</option>
                                <option value="Admin">Admin</option>
                                <option value="Ambulance">Ambulance</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 font-semibold mb-1">Gender</label>
                            <select id="edit_gender" class="w-full px-3 py-2 rounded bg-gray-800 text-gray-200 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 font-semibold mb-1">Date of Birth</label>
                            <input id="edit_dateOfBirth" type="date" class="w-full px-3 py-2 rounded bg-gray-800 text-gray-200 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 font-semibold mb-1">Email</label>
                            <input id="edit_email" type="email" class="w-full px-3 py-2 rounded bg-gray-800 text-gray-200 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 font-semibold mb-1">Phone Number</label>
                            <input id="edit_phoneNumber" type="text" class="w-full px-3 py-2 rounded bg-gray-800 text-gray-200 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 font-semibold mb-1">Address</label>
                            <input id="edit_address" type="text" class="w-full px-3 py-2 rounded bg-gray-800 text-gray-200 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 rounded bg-gray-700 text-gray-200 hover:bg-gray-600 font-semibold" onclick="closeEditModal()">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded bg-yellow-400 text-black font-semibold hover:bg-yellow-500 transition">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Modal -->
        <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
            <div class="bg-gray-900 rounded-lg shadow-lg p-6 w-full max-w-sm relative">
                <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-200" onclick="closeDeleteModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="flex flex-col items-center">
                    <svg class="w-10 h-10 text-red-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <h2 class="text-lg font-bold text-yellow-400 mb-2">Delete User</h2>
                    <p class="text-gray-200 mb-4">Are you sure you want to delete <span id="delete-user-name" class="font-semibold text-yellow-300"></span>?</p>
                    <div class="flex gap-2">
                        <button class="px-4 py-2 rounded bg-gray-700 text-gray-200 hover:bg-gray-600 font-semibold" onclick="closeDeleteModal()">Cancel</button>
                        <button class="px-4 py-2 rounded bg-red-600 text-white font-semibold hover:bg-red-700 transition" onclick="confirmDeleteUser()">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>
        <div class="bg-red-600 border border-red-500 text-yellow-300 px-4 py-3 rounded-lg text-center max-w-md mx-auto">
            <svg class="w-6 h-6 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <p class="font-semibold">No users found or error retrieving data.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>

</body>
</html>
