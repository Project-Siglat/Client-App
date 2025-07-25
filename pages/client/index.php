<?php
include "./pages/client/component/topbar.php";
include "./pages/client/component/map.php";
?>

<script>
const authToken = sessionStorage.getItem('token');
if (!authToken) {
    window.location.href = '/login';
}
</script>
