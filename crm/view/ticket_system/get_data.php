<?php
$getClient = $_GET['clientId'];
$dataAll = file_get_contents('https://itourssupport.in/model/get-ticket-api.php?cid=' . $getClient);
echo $dataAll;
?>
<script>
$('#ticketTable').DataTable();
</script>