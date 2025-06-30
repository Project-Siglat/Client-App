
// Route PHP
<?php if ($_SERVER["REQUEST_URI"] == "/"): ?>
    <section>Hello</section>
<?php endif; ?>

<?php if ($_SERVER["REQUEST_URI"] == "/client"): ?>
    <?php include "./client/index.php"; ?>
<?php endif; ?>
