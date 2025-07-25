<?php
include "./config/env.php";
$API = $_ENV["API"];
?>

<script>
function API()
{
  return "<?php echo $API; ?>";
}
</script>
