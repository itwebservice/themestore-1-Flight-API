<?php
?>
<div class="row">

	<div class="col-md-12">

		<div class="profile_box main_block">
        	 	 <?php
				  $row_customer = mysqli_fetch_assoc(mysqlQuery($query));
				  $contact_no = $encrypt_decrypt->fnDecrypt($row_customer['contact_no'], $secret_key);
				  $email_id = $encrypt_decrypt->fnDecrypt($row_customer['email_id'], $secret_key);
				  $masked =  str_pad(substr($contact_no, -4), strlen($contact_no), '*', STR_PAD_LEFT);
				  $masked_email =  str_pad(substr($email_id, 4), strlen($email_id), '*', STR_PAD_LEFT);
        	 	 ?>

        	 	<div class="row">

        	 	<div class="col-md-6 right_border_none_sm" style="border-right: 1px solid #ddd; min-height: 105px;">

        	   		<span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <?php echo "<label>Name <em>:</em></label> ".$row_customer['first_name']." ".$row_customer['last_name']; ?>

		            </span>

		            <span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <?php echo "<label>Type <em>:</em></label> ".$row_customer['type'] ?>

		            </span>
		            <span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <?php echo "<label>Source <em>:</em></label> ".$row_customer['source'] ?>

		            </span>

		            <?php  

		        	  if($row_customer['type'] == 'Corporate'||$row_customer['type'] == 'B2B'){

		        	?>

        	 		<span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <?php echo "<label>Company Name <em>:</em></label> ".$row_customer['company_name'] ?>

		            </span>

		            <?php  } ?>

		            <span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <?php echo "<label>Gender <em>:</em></label> ".$row_customer['gender']; ?>

		            </span>

		            <span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <?php echo "<label>Birthdate <em>:</em></label> ".get_date_user($row_customer['birth_date']); ?>

		            </span> 

		            <span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <?php echo "<label>Age <em>:</em></label>".$row_customer['age'] ?>

		            </span>
		           <?php $sq_state = mysqli_fetch_assoc(mysqlQuery("select * from state_master where id='$row_customer[state_id]'"));
                    ?>  
		            <span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <?php echo "<label>State/Country <em>:</em></label>".$sq_state['state_name'] ?>

		            </span>	
		            <span class="main_block" >

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>
						  <label>Mobile No <em>:</em></label> <span onclick="showNum('m')" id='phone-ym'>
		                   <?= $masked ?> </span>
						  <span id="phone-xm" class="hidden" ><?= $contact_no;?></span>

		            </span>	

		            <span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <label>Email ID <em>:</em></label> <span onclick="showEmail('m')" id='phone-yem'><?= $masked_email ?> </span>
						  <span id="phone-xem" class="hidden" data-original-title="" title=""><?= $email_id ?></span>

		            </span>	 
					           
		        </div>

        	 	<div class="col-md-6">		        
        	 		

		            <?php  

		        	  if($row_customer['type'] == 'Corporate'||$row_customer['type'] == 'B2B'){

		        	?>

		            <span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <?php echo "<label>Landline No <em>:</em></label> ".$row_customer['landline_no']; ?>

		            </span>	

		            <?php } ?>

		            <?php  

		        	  if($row_customer['type'] == 'Corporate'||$row_customer['type'] == 'B2B'){

		        	?>

		            <span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <?php echo "<label>Alternative ID <em>:</em></label> ".$row_customer['alt_email']; ?>

		            </span>

		            <?php } ?>

		            <span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <?php echo "<label>Address1 <em>:</em></label> ".$row_customer['address']; ?>

		            </span>
					<span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <?php echo "<label>Address2 <em>:</em></label> ".$row_customer['address2']; ?>

		            </span>
		            <span class="main_block">

		                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

		                  <?php echo "<label>City <em>:</em></label> ".$row_customer['city']; ?>

		            </span>
		            <span class="main_block">

	                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

	                  <?php echo "<label>Tax No <em>:</em></label> ".$row_customer['service_tax_no']; ?>

	                </span>	 
		            <span class="main_block">

	                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

	                  <?php echo "<label>Personal Identification No(PIN) <em>:</em></label> ".strtoupper($row_customer['pan_no']); ?>

	                </span>	  
		            <span class="main_block">

	                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

	                  <?php echo "<label>Opening Balance <em>:</em></label> ".$row_customer['op_balance']; ?>

	                </span>	
		            <span class="main_block">

	                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

	                  <?php echo "<label>Balance Side <em>:</em></label> ".$row_customer['balance_side']; ?>

	                </span>	 

	                <span class="main_block">

	                  <i class="fa fa-angle-double-right cost_arrow" aria-hidden="true"></i>

	                  <?php echo "<label>Status <em>:</em></label> ".$row_customer['active_flag']; ?>

	                </span>	 

		        </div>

		        </div>

		             

		    </div> 

	</div>

</div>
<?php
if($row_customer['type'] == 'B2B' || $row_customer['type'] == 'Corporate'){
	?>
	<div class="row mg_tp_10">
		<div class="col-md-12">
			<div class="profile_box main_block">
				<h3 class="editor_title">User Details</h3>
                <div class="table-responsive">
                	<table id="tbl_dynamic_bus_booking" name="tbl_dynamic_bus_booking" class="table table-bordered no-marg">
	                	<tr class="table-heading-row">
							<th>S_No</th>
							<th>Name</th>
							<th>Mobile No</th>
							<th>Email ID</th>
						</tr>
						<?php
						$count = 1;
						$sq_customer = mysqlQuery("select * from customer_users where customer_id='$customer_id'");
						while($row_customer = mysqli_fetch_assoc($sq_customer)){
							$bg = ($row_customer['status'] == 'Inactive') ? 'danger' : ''; ?>
							<tr class="<?= $bg ?>">
								<td><?= $count ?></td>
								<td><?= $row_customer['name'] ?></td>
								<td><?= $row_customer['mobile_no'] ?></td>
								<td><?= $row_customer['email_id'] ?></td>
							</tr>
							<?php $count++;
						} ?>
	                </table>
                </div>
			</div>
		</div>
	</div>
<?php } ?>