<?php
if($sq_req['quotation_for']=="DMC") : ?>
<br>
<div class="row">
	<div class="col-md-12">
		<div class="profile_box main_block">
        <h3 class="editor_title">Hotel Details</h3>
        	<div class="row"> <div class="col-md-12"> <div class="table-responsive">
				<table class="table table-bordered no-marg">
					<thead>
						<tr class="table-heading-row">
							<th>S_No.</th>
							<th>Hotel_Category</th>
							<th>Check_In</th>
							<th>Check_Out</th>
							<th>Total_Rooms</th>
							<th>Room_Category</th>
							<th>Meal_Plan</th>
						</tr>
					</thead>
					<tbody>
			        	<?php
				        	$dmc_entries = $sq_req['dmc_entries'];
							$dmc_entries_arr = json_decode($dmc_entries, true);
							$count = 1;
							foreach(array_chunk($dmc_entries_arr, 6) as $dmc_entries){
								$arr = array();
								foreach($dmc_entries as $dmc_entries1){
									if($dmc_entries1['value'] != ''){
										$arr[$dmc_entries1['name']] = $dmc_entries1['value'];
									}
								}
								if($arr['dmc_id']  != ''){
								?>
								<tr>
									<td><?= $count ?></td>
									<td><?= $arr['dmc_id'] ?></td>
									<td><?= get_date_user($arr['checkin_date']); ?></td>
									<td><?= get_date_user($arr['checkout_date']);?></td>
									<td><?= $arr['total_rooms'] ?></td>
									<td><?= $arr['room_cat1'] ?></td>
									<td><?php echo isset($arr['meal_plan']) ? $arr['meal_plan'] : ''; ?></td>
								</tr>
								<?php
								$count++;
								}
							}
			        	?>
	        		</tbody>
	        	</table>
        	</div> </div> </div>
        </div>
    </div>
</div>
<?php endif; ?>