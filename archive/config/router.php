

<?php if ($_SERVER["REQUEST_URI"] == "/"): ?>
    <section>Hello</section>
<?php endif; ?>


<?php if ($_SERVER["REQUEST_URI"] == "/client"): ?>

    <?php include "../functions/header.php";
    // setTitle("Wow");
    // include "../client/index.php";
    ?>
<?php endif; ?>

<?php if ($_SERVER["REQUEST_URI"] == "/banana"): ?>

    <?php
    include "../functions/header.php";
    setTitle("Wow");

    // include "../client/index.php";
    ?>
<?php endif; ?>
