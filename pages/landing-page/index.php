<?php
include "./components/topbar.html";
include "./pages/landing-page/structure/ui.html";
?>

<script>
fetch(API() + '/api/v1/Admin/admin', {
    method: 'GET',
    headers: {
        'accept': '*/*'
    }
})
.then(response => response.json())
.then(data => {
    console.log(data);
})
.catch(error => {
    console.error('Error:', error);
});
</script>
