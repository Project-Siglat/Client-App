<!DOCTYPE html>
<html>
<head>
    <style>
        .spinner {
            border: 4px solid #374151;
            border-top: 4px solid #fbbf24;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-dots {
            display: inline-block;
        }

        .loading-dots::after {
            content: '';
            animation: dots 1.5s linear infinite;
        }

        @keyframes dots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }
    </style>
    <script>
        let isLoading = true;

        window.addEventListener('load', function() {
            // Simulate loading delay to show animation
            setTimeout(function() {
                isLoading = false;
                document.getElementById('loading-container').style.display = 'none';
                document.getElementById('content-container').style.display = 'block';
            }, 1000);
        });
    </script>
</head>
<body>

<div id="loading-container" class="bg-black min-h-screen p-8 flex flex-col items-center justify-center">
    <h1 class="text-4xl font-bold text-yellow-400 mb-8">User</h1>
    <div class="bg-gray-900 rounded-lg shadow-lg p-8 text-center">
        <div class="spinner mb-4"></div>
        <p class="text-yellow-300 text-lg">Loading users<span class="loading-dots"></span></p>
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
?>

<div class="bg-black min-h-screen p-8">
    <h1 class="text-4xl font-bold text-yellow-400 mb-8">User</h1>

    <?php if (!empty($users) && is_array($users)): ?>
    <div class="overflow-x-auto bg-gray-900 rounded-lg shadow-lg">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-red-600">
                    <th class="border border-gray-700 px-4 py-3 text-left text-yellow-300 font-semibold">First Name</th>
                    <th class="border border-gray-700 px-4 py-3 text-left text-yellow-300 font-semibold">Middle Name</th>
                    <th class="border border-gray-700 px-4 py-3 text-left text-yellow-300 font-semibold">Last Name</th>
                    <th class="border border-gray-700 px-4 py-3 text-left text-yellow-300 font-semibold">Address</th>
                    <th class="border border-gray-700 px-4 py-3 text-left text-yellow-300 font-semibold">Gender</th>
                    <th class="border border-gray-700 px-4 py-3 text-left text-yellow-300 font-semibold">Phone Number</th>
                    <th class="border border-gray-700 px-4 py-3 text-left text-yellow-300 font-semibold">Role</th>
                    <th class="border border-gray-700 px-4 py-3 text-left text-yellow-300 font-semibold">Date of Birth</th>
                    <th class="border border-gray-700 px-4 py-3 text-left text-yellow-300 font-semibold">Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr class="bg-black hover:bg-gray-800 transition-colors">
                    <td class="border border-gray-700 px-4 py-3 text-white"><?php echo htmlspecialchars(
                        $user["firstName"] ?? "",
                    ); ?></td>
                    <td class="border border-gray-700 px-4 py-3 text-white"><?php echo htmlspecialchars(
                        $user["middleName"] ?? "",
                    ); ?></td>
                    <td class="border border-gray-700 px-4 py-3 text-white"><?php echo htmlspecialchars(
                        $user["lastName"] ?? "",
                    ); ?></td>
                    <td class="border border-gray-700 px-4 py-3 text-white"><?php echo htmlspecialchars(
                        $user["address"] ?? "",
                    ); ?></td>
                    <td class="border border-gray-700 px-4 py-3 text-white"><?php echo htmlspecialchars(
                        $user["gender"] ?? "",
                    ); ?></td>
                    <td class="border border-gray-700 px-4 py-3 text-white"><?php echo htmlspecialchars(
                        $user["phoneNumber"] ?? "",
                    ); ?></td>
                    <td class="border border-gray-700 px-4 py-3 text-white"><?php echo htmlspecialchars(
                        $user["role"] ?? "",
                    ); ?></td>
                    <td class="border border-gray-700 px-4 py-3 text-white"><?php
                    $dateOfBirth = $user["dateOfBirth"] ?? "";
                    if (!empty($dateOfBirth)) {
                        $date = new DateTime($dateOfBirth);
                        echo htmlspecialchars($date->format("m/d/Y"));
                    } else {
                        echo "";
                    }
                    ?></td>
                    <td class="border border-gray-700 px-4 py-3 text-white"><?php echo htmlspecialchars(
                        $user["email"] ?? "",
                    ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="bg-red-600 border border-red-500 text-yellow-300 px-4 py-3 rounded-lg">
        <p class="font-semibold">No users found or error retrieving data.</p>
    </div>
    <?php endif; ?>
</div>
</div>

</body>
</html>
