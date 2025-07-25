<?php
include "./config/env.php";
$request_uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
switch ($request_uri) {
    // here is the landing page
    case "/":
        include "./pages/landing-page/index.php";
        break;
    case "/login":
        include "./pages/auth/index.php";
        break;
    case "/env":
        $env = $_ENV["ENVIRONMENT"];
        echo $env;
        break;

    // siglat
    case "/siglat":
        include "./pages/siglat/core.php";
        break;

    // here is the user set
    case "/client":
        include "./pages/client/index.php";
        break;

    // here is the ambulance set
    case "/ambulance":
        include "./pages/ambulance/core.php";
        break;
    default:
        http_response_code(404);
        include "./pages/error/404.php";
        break;
}

?>
