<?php
include "../../../../model/model.php";
$branch_status = $_POST['branch_status'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$emp_id = $_SESSION['emp_id'];
?>
<div class="app_panel_content Filter-panel">
		
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">
		<select name="ticket_id_filter" id="ticket_id_filter" title="Select Booking" style="width:100%" onchange="ticket_upload_list_reflect()">
	        <option value="">Select Booking ID</option>
	        <?php 
       		$query = "select * from ticket_master where 1 and delete_status='0' and cancel_type!=1 ";
	        include "../../../../model/app_settings/branchwise_filteration.php";
	        $query .= " order by ticket_id desc ";
	        $sq_ticket = mysqlQuery($query);
	        while($row_ticket = mysqli_fetch_assoc($sq_ticket)){
				
				$cancel_type = $row_ticket['cancel_type'];
				if($cancel_type != 1){
					$date = $row_ticket['created_at'];
					$yr = explode("-", $date);
					$year = $yr[0];
					$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_ticket[customer_id]'"));
					if($sq_customer['type'] == 'Corporate'||$sq_customer['type']=='B2B'){
						$cust_name = $sq_customer['company_name'];
					}else{
						$cust_name = $sq_customer['first_name'].' '.$sq_customer['last_name'];
					}
					?>
					<option value="<?= $row_ticket['ticket_id'] ?>"><?= get_ticket_booking_id($row_ticket['ticket_id'],$year).' : '.$cust_name ?></option>
					<?php
				}
	        }
	        ?>
	    </select>
	</div>
		
</div>
<div id="div_ticket_upload_list"></div>
<script>
$('#ticket_id_filter').select2();
function ticket_upload_list_reflect()
{
	var ticket_id = $('#ticket_id_filter').val();
	$.post('upload_ticket/ticket_upload_list_reflect.php', { ticket_id : ticket_id }, function(data){
		$('#div_ticket_upload_list').html(data);
	});
}
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>