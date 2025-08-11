<?php
include "./config/env.php";
$request_uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$page_directory = "./page";

// Handle root path directly
if ($request_uri === "/") {
    $index_file = $page_directory . "/index/index.php";
    if (file_exists($index_file)) {
        include $index_file;
        exit();
    }
} else {
    // Extract folder name directly from URI
    $folder = ltrim($request_uri, "/");
    // Only check if it contains no additional slashes (single level)
    if (strpos($folder, "/") === false) {
        $index_file = $page_directory . "/" . $folder . "/index.php";
        if (file_exists($index_file)) {
            include $index_file;
            exit();
        }
    }
}

// 404 fallback
$not_found_file = $page_directory . "/404/index.php";
if (file_exists($not_found_file)) {
    include $not_found_file;
}

?>
