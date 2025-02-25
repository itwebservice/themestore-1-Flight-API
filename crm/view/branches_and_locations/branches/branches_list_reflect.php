<?php 
include_once('../../../model/model.php');

$location_id = (isset($_POST['location_id'])) ? $_POST['location_id'] : '';
$count = 0;
?>

<div class="row mg_tp_20"> <div class="col-md-12 no-pad"> <div class="table-responsive">
	
	<table class="table table-hover" id="branch_table_id" style="margin: 20px 0 !important;">
		<thead>
			<tr class="table-heading-row">
				<th style="width: 65px;">S_No.</th>
				<th>Location</th>
				<th>Branch</th>
				<th>Address</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$query = "select * from branches where 1";
			if($location_id!=""){
				$query .= " and location_id='$location_id'";
			}
			$sq_branch = mysqlQuery($query);
			while($row_branch = mysqli_fetch_assoc($sq_branch))
			{
				$location_id = $row_branch['location_id'];
				$sq_location = mysqli_fetch_assoc(mysqlQuery("select * from locations where location_id='$location_id'"));

				$bg = ($row_branch['active_flag']=="Inactive") ? "danger" : "";

				$pincode = $row_branch['pincode'];
				$pincode = ($pincode!='')?'('.$pincode.')':'';
				?>
				<tr class="<?= $bg ?>">
					<td><?= ++$count ?></td>
					<td><?= $sq_location['location_name'] ?></td>
					<td><?= $row_branch['branch_name'] ?></td>
					<td><?= ucfirst($row_branch['address1'].' '.$row_branch['address2'].'  '.$pincode) ?></td>
					<td>
						<button onclick="branch_edit_modal(<?= $row_branch['branch_id'] ?>);btnDisableEnable(this.id)" id="branchEdit<?= $row_branch['branch_id'] ?>" class="btn btn-info btn-sm" title="Update Details"><i class="fa fa-pencil-square-o"></i></button>
					</td>
				</tr>
				<?php
			}	
			?>
		</tbody>
	</table>

</div> </div> </div>

<div id="div_branch_edit_modal"></div>
<script type="text/javascript">
	$('#branch_table_id').dataTable({
		"pagingType": "full_numbers"
	});
</script>