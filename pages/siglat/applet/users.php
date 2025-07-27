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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $index => $user): ?>
                    <tr class="hover:bg-opacity-50" style="background-color: #3B4252;" onmouseover="this.style.backgroundColor='#434C5E'" onmouseout="this.style.backgroundColor='#3B4252'">
                        <td class="px-4 py-3 border-b align-top" style="border-color: #4C566A; color: #D8DEE9;">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" style="color: #88C0D0;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <div class="font-semibold mb-1 text-sm" style="color: #88C0D0;">
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
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide" style="background-color: #BF616A; color: #ECEFF4;"><?php echo htmlspecialchars(
                                        $user["role"] ?? "User",
                                    ); ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 border-b align-top" style="border-color: #4C566A; color: #D8DEE9;">
                            <div class="text-xs font-semibold mb-1 uppercase" style="color: #4C566A;">Gender</div>
                            <div class="mb-2 text-sm"><?php echo htmlspecialchars(
                                $user["gender"] ?? "Not specified",
                            ); ?></div>
                            <div class="text-xs font-semibold mb-1 uppercase" style="color: #4C566A;">Date of Birth</div>
                            <div class="text-sm"><?php
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
                                <span class="text-sm"><?php echo htmlspecialchars(
                                    $user["email"] ?? "No email",
                                ); ?></span>
                            </div>
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" style="color: #88C0D0;" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                                <span class="text-sm"><?php echo htmlspecialchars(
                                    $user["phoneNumber"] ?? "No phone",
                                ); ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 border-b align-top text-sm" style="border-color: #4C566A; color: #D8DEE9;">
                            <?php echo htmlspecialchars(
                                $user["address"] ?? "Not specified",
                            ); ?>
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
</div>
