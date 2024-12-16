<?php
include "../../model/model.php";

   


    echo '<option value="">Customer</option>';
    $sq_customer = mysqlQuery("select * from customer_master");
    while($row_customer = mysqli_fetch_assoc($sq_customer)){
        if($row_customer['type']=='B2B' || $row_customer['type']=='Corporate'){
            ?>
             <option value="<?= $row_customer['customer_id'] ?>"><?= $row_customer['company_name'] ?></option>
            <?php
    
        }
        else{
            ?>
             <option value="<?= $row_customer['customer_id'] ?>"><?= $row_customer['first_name'].' '.$row_customer['last_name'] ?></option>
            <?php
        }
    ?>
      <?php 
        }
?>