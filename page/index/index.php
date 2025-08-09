<?php
include "./components/topbar.html";
include "./page/index/structure/bone.php";

// include "./pages/landing-page/structure/ui.html";
?>
<!-- it has to be hidden later -->
<script>
'/api/v1/Admin/fetch(API() + '/api/v1/Admin/admin', {
    method: 'GET',
    headers: {
        'accept': '*/*'
    }
})
e => response.js.then(response => response.json())
.then(data => {
    console.log(data);
})
.catch(error => {
    console.error('Error:', error);
});
</script>
