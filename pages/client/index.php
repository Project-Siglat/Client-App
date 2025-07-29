<script>
<?php include "./config/session.js"; ?>

</script>
<?php
// include "./pages/client/component/topbar.php";
include "./components/topbar.html";

$DEBUG = false;

if (!$DEBUG) {
    include "./pages/client/component/map.html";
} else {
    include "./pages/client/component/debugmap.html";
}

include "./pages/client/widget/frame.php";


?>
