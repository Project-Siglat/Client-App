<?php
include "./config/env.php";
$request_uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
switch ($request_uri) {
    // here is the landing page
    case "/":
        include "./pages/landing-page/core.php";
        break;
    case "/about":
        include "./pages/landing-page/about.php";
        break;
    case "/contact":
        include "./pages/contact/core.php";
        break;
    case "/login":
        include "./pages/auth/login.php";
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
        include "./pages/client/core.php";
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
