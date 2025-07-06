<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Siglat</title>
</head>
<body>
    <!-- Eto yung tailwind CSS -->

    <!-- Eto yung cdn external dependencies -->

    <!-- Andito lahat ng mga ruta ng mga web pages.... -->
    <?php
    require_once __DIR__ . "/vendor/autoload.php";

    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    echo $_ENV["ENVIRONMENT"];

    include "./pack/route.php";
    ?>
</body>
</html>
