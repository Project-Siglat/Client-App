<?php
include "./config/env.php";
$request_uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$page_folders = [];
$page_directory = "./page";

if (is_dir($page_directory)) {
    $dir_handle = opendir($page_directory);
    while (($item = readdir($dir_handle)) !== false) {
        if (
            $item != "." &&
            $item != ".." &&
            is_dir($page_directory . "/" . $item)
        ) {
            $page_folders[] = $item;
        }
    }
    closedir($dir_handle);
}

$page_found = false;

foreach ($page_folders as $folder) {
    if ($folder == "index" && $request_uri == "/") {
        $index_file = $page_directory . "/" . $folder . "/index.php";
        if (file_exists($index_file)) {
            include $index_file;
            $page_found = true;
        }
        break;
    } elseif ($request_uri == "/" . $folder) {
        $index_file = $page_directory . "/" . $folder . "/index.php";
        if (file_exists($index_file)) {
            include $index_file;
            $page_found = true;
        }
        break;
    }
}

if (!$page_found) {
    $not_found_file = $page_directory . "/404/index.php";
    if (file_exists($not_found_file)) {
        include $not_found_file;
    }
}

?>
