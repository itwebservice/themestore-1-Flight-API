<?php include "../../../model/model.php";
$customer = isset($_POST['customer']) ? $_POST['customer'] : '';
$sq = mysqli_fetch_assoc(mysqlQuery("select first_name,last_name,company_name,type from customer_master where customer_id ='$customer'"));
echo ($sq['type'] == 'Corporate' || $sq['type'] == 'B2B') ? $sq['type'].'-'.$sq['company_name'] : $sq['type'].'-'.$sq['first_name'].' '.$sq['last_name'];
?>