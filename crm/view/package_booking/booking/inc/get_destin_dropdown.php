<?php
include "../../../../model/model.php";
$dest_id = isset($_POST['dest_id']) ? $_POST['dest_id'] : 0;
?>
<select id="dest_name2" name="dest_name" title="Select Destination" onchange="package_dynamic_reflect(this.id)" class="form-control" style="width:100%"> 

<option value="">Select Destination</option>
<option value="0"><?= "Without Package" ?></option>

<?php
$sq_query = mysqlQuery("select * from destination_master where status != 'Inactive'"); 

    while($row_dest = mysqli_fetch_assoc($sq_query)){ ?>
      <option value="<?php echo $row_dest['dest_id']; ?>"><?php echo $row_dest['dest_name']; ?></option>
    <?php } ?>

</select>
<script>
	$('#dest_name2').select2();
</script>

