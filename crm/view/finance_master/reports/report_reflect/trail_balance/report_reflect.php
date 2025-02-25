<?php
include "../../../../../model/model.php";  
$to_date = $_POST['from_date'];
$financial_year_id = $_POST['financial_year_id'];
$branch_admin_id = $_POST['branch_admin_id'];
$role = $_SESSION['role'];
$sq_fin = mysqli_fetch_assoc(mysqlQuery("select from_date,to_date from financial_year where financial_year_id='$financial_year_id'"));
if($to_date != ''){
	$to_date1 = get_date_db($to_date);
	$flag = ($to_date1 >= $sq_fin['from_date'] && $to_date1 <= $sq_fin['to_date']) ? 1 : 0;
}
else{
	
	$flag = 1;
}
if($flag == 1){
?>
<div class="row mg_tp_20">
<!-- ////////////////////////////////////////////////////DEBIT START/////////////////////////////////////////////////////////////////// -->
	<div class="col-md-6 pl_sheet">
		<div class="panel panel-default main_block">
			<div class="panel-heading main_block">
				<div class="col-md-10 no-pad">
					<strong>Particulars</strong>
				</div>
				<div class="col-md-2 no-pad text-right"><strong>Debit</strong></div>
			</div>
			<div class="quadrant main_block">
				<?php
				$total_sub_group_amount = 0;
				$q = mysqlQuery("select * from ledger_master where dr_cr='Dr'");
				while($row_q = mysqli_fetch_assoc($q)){

					$debit_amount = 0;
					$credit_amount = 0;
					$total_amount = 0;
					$debit_amount = ($row_q['balance_side']=='Debit') ? $row_q['balance'] : '0';
					$credit_amount = ($row_q['balance_side']=='Credit') ? $row_q['balance'] : '0';
					
					$q1 = "select * from finance_transaction_master where gl_id='$row_q[ledger_id]'";	//Debit Total
					if($to_date!=""){
						$to_date = get_date_db($to_date);
						$q1 .=" and payment_date <= '$to_date'";	
					}	
					if($financial_year_id != ""){
						$q1 .=" and financial_year_id='$financial_year_id'";		
					} 	
					if($branch_admin_id != "0" && $role != 'Admin' && $role !='Accountant'){
						$q1 .=" and branch_admin_id='$branch_admin_id'";		
					} 			            	
					$sq_opening_balance = mysqlQuery($q1);
					while($row_balance = mysqli_fetch_assoc($sq_opening_balance)){
						if($row_balance['payment_side'] == 'Debit'){
							$debit_amount += $row_balance['payment_amount'];
						}else{
							$credit_amount += $row_balance['payment_amount'];
						}
					}
					$total_amount = $debit_amount - $credit_amount;					
					if($total_amount != ''){
						?>					         
						<div class="part_entry main_block">
							<div class="col-md-8 no-pad">
								<span class="part_entry_text"><?= $row_q['ledger_name'] ?></span>
							</div>
							<div class="col-md-2 no-pad text-right"></div>
							<div class="col-md-2 no-pad text-right">
								<span class="part_entry_m_count" id="subgroup_amount"><?= number_format($total_amount,2) ?></span>
							</div>
						</div>
						<?php
						$total_sub_group_amount += $total_amount; 
					}
				} ?>
			</div>  <!-- Quadrant End -->
			<!-- Total -->
			<div class="panel-footer main_block">
				<div class="row">
					<div class="col-md-8"><strong>Total :</strong></div>
					<div class="col-md-4 text-right"><strong id="span_total_sales"><?= number_format($total_sub_group_amount,2) ?></strong></div>
				</div>
			</div>
		</div>
	</div>
<!-- //////////////////////////////////////////////////////DEBIT END/////////////////////////////////////////////////////////////////////-->
<!-- //////////////////////////////////////////////////////CREDIT START//////////////////////////////////////////////////////////////////  -->
	<div class="col-md-6 pl_sheet">
		<div class="panel panel-default main_block">
			<div class="panel-heading main_block">
				<div class="col-md-10 no-pad">
					<strong>Particulars</strong>
				</div>
				<div class="col-md-2 no-pad text-right"><strong>Credit</strong></div>
			</div>
			<div class="quadrant main_block">				
				<?php
				$total_sub_group_amount_d = 0;
				$q = mysqlQuery("select * from ledger_master where ledger_id!='165' and dr_cr='Cr'");
				while($row_q = mysqli_fetch_assoc($q)){

					$debit_amount = 0;
					$credit_amount = 0;
					$total_amount = 0;
					$debit_amount = ($row_q['balance_side']=='Debit') ? $row_q['balance'] : '0';
					$credit_amount = ($row_q['balance_side']=='Credit') ? $row_q['balance'] : '0';
					//Debit Total 
					$q1 = "select * from finance_transaction_master where gl_id='$row_q[ledger_id]'";	
					if($to_date!=""){
						$to_date = get_date_db($to_date);
						$q1 .=" and payment_date <= '$to_date'";	
					}		
					if($financial_year_id != ""){
						$q1 .=" and financial_year_id='$financial_year_id'";		
					} 	
					if($branch_admin_id != "0" && $role != 'Admin' && $role !='Accountant'){
						$q1 .=" and branch_admin_id='$branch_admin_id'";		
					} 					
					$sq_opening_balance = mysqlQuery($q1);            	
					while($row_balance = mysqli_fetch_assoc($sq_opening_balance)){
						if($row_balance['payment_side'] == 'Debit'){
							$debit_amount += $row_balance['payment_amount'];
						}else{
							$credit_amount += $row_balance['payment_amount'];
						}
					}
					$total_amount = $credit_amount - $debit_amount;
					if($total_amount != '0'){		
						?>			
						<div class="part_entry main_block">
							<div class="col-md-8 no-pad">
								<span class="part_entry_text"><?= $row_q['ledger_name'] ?></span>
							</div>
							<div class="col-md-2 no-pad text-right"></div>
							<div class="col-md-2 no-pad text-right">
								<span class="part_entry_m_count" id="subgroup_amount"><?= number_format($total_amount,2) ?></span>
							</div>
						</div>
						<?php 
						$total_sub_group_amount_d += $total_amount; 
					}
				} ?>
			</div>   <!-- quadrant end -->
			<!-- Total -->
			<div class="panel-footer main_block">
				<div class="row">
					<div class="col-md-8"><strong>Total :</strong></div>
					<div class="col-md-4 text-right"><strong id="span_total_sales_d1"><?= number_format($total_sub_group_amount_d,2) ?></strong></div>
				</div>
			</div>			
		</div>
	</div>
<!-- //////////////////////////////////////////////////////CREDIT END////////////////////////////////////////////////////////////////// -->
</div>
<?php }
else{
	echo '0';
} ?>

