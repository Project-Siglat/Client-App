<?php
$env = $_ENV["ENVIRONMENT"];

if ($env == "localhost" && "develop") {
    include "./components/topbar.html";

    $package = ["map", "map-api"];

    foreach ($package as $folder) {
        $file_path = "./package/" . $folder . "/index.html";
        if (file_exists($file_path)) {
            include $file_path;
        } else {
            error_log("File not found: " . $file_path);
        }
    }
    include "./page/engine/structure/ui.html";
} else {
    http_response_code(404);
}
?>
