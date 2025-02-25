<?php
include "../../../../model/model.php";
$from_date_filter = $_POST['from_date_filter'];
$to_date_filter = $_POST['to_date_filter'];
$ledger_id = $_POST['ledger_id'];
$financial_year_id = $_POST['financial_year_id'];
$branch_admin_id = $_POST['branch_admin_id'];
$chk_opnbalance = $_POST['chk_opnbalance'];
$chk_trans = $_POST['chk_trans'];
$dateChunks = json_decode($_POST['dateChunk']);
$role = $_SESSION['role'];

if($chk_trans == 1){
	$temp_arr = array();
	$transactions = array();
}

$sq_lq = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where ledger_id='$ledger_id'"));
if($chk_opnbalance == 1){
	$balance = $sq_lq['balance'];
	$side_t = ($sq_lq['balance_side']=='Debit') ? '(Dr)' : '(Cr)';
}else{
	$balance = 0;
	$side_t = '(Cr)';
}
if($from_date_filter != ''){
		//Calaculate previous year closing balance as opening balance of this year
		$sq_ob = "select * from finance_transaction_master where gl_id='$ledger_id' and payment_amount != '0'";
		$from_date_filter = date('Y-m-d', strtotime($from_date_filter));
		$to_date_filter = date('Y-m-d', strtotime($to_date_filter));
		$sq_ob .= " and payment_date < '$from_date_filter'";
		$sq_ledger_ob = mysqlQuery($sq_ob);
		$balance = 0;
		$total_debit = 0;
		$total_credit = 0;
		if($chk_opnbalance == 1){

			$total_debit = ($sq_lq['balance_side']=='Debit') ? $sq_lq['balance'] : '0';
			$total_credit = ($sq_lq['balance_side']=='Credit') ? $sq_lq['balance'] : '0';
		}
		while($row_ledger_ob = mysqli_fetch_assoc($sq_ledger_ob)){

			$debit_amount = ($row_ledger_ob['payment_side'] == 'Debit') ? $row_ledger_ob['payment_amount'] : '' ;
			$credit_amount = ($row_ledger_ob['payment_side'] == 'Credit') ? $row_ledger_ob['payment_amount'] : '' ;
			if($row_ledger_ob['payment_side'] == 'Debit'){
				$total_debit += $row_ledger_ob['payment_amount'];
			} 
			else{
				$total_credit += $row_ledger_ob['payment_amount'];
			}
		}
		if($total_debit>$total_credit){
			$balance =  $total_debit - $total_credit;
			$side_t='(Dr)';
		}
		else{
			$balance =  $total_credit - $total_debit;	
			$side_t='(Cr)';
		}
}

////////////////////////////////// END /////////////////////////////////////////////

?>
<div class="row"> <div class="col-md-12 mg_tp_20"><div class="table-responsive">
	<table class="table table-hover table-bordered" id="tbl_list_ledger_sub" style="margin: 20px 0 !important;  padding: 0px !important;">
		<thead>
			<tr class="table-heading-row">
				<th>SR.NO</th>
				<th>Date</th>
				<th>Trans_Type</th>
				<th>Particulars</th>
				<th>Debit</th>
				<th>Credit</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td><?php echo "Opening Balance"; ?></td>
				<td><?php echo ($side_t=='(Dr)' && $chk_opnbalance) ? number_format($balance,2) : ''; ?></td>
				<td><?php echo ($side_t=='(Cr)' && $chk_opnbalance) ? number_format($balance,2) : ''; ?></td>
				<td></td>
			</tr>
			<?php
			$count = 1;
			$balance_column = 0;
			$total_debit1 = 0;
			$total_credit1 = 0;
			if($chk_opnbalance == 1){
				$total_debit1 = ($side_t == '(Dr)') ? $balance : '0';
				$total_credit1 = ($side_t == '(Cr)') ? $balance : '0';
				if($side_t == '(Dr)'){
					$balance_column = $balance - $balance_column;
				}else{
					$balance_column = $balance_column + $balance;
				}
			}
			$sq_q = "select * from finance_transaction_master where gl_id='$ledger_id' and payment_amount != '0' ";
			if($from_date_filter != '' && $to_date_filter != ''){
				$from_date_filter = date('Y-m-d', strtotime($from_date_filter));
				$to_date_filter = date('Y-m-d', strtotime($to_date_filter));
				$sq_q .= " and  payment_date between '$from_date_filter' and '$to_date_filter'";
			}
			if($role != 'Admin'){
				if($branch_admin_id != '0'){
					$sq_q .= " and branch_admin_id = '$branch_admin_id'";
				}
			}
			$sq_q .= ' order by finance_transaction_id ';
			$sq_ledger_info = mysqlQuery($sq_q);
			while($row_ledger = mysqli_fetch_assoc($sq_ledger_info)){

				$sq_le_name = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where ledger_id='$row_ledger[gl_id]'"));
				$debit_amount = ($row_ledger['payment_side'] == 'Debit') ? $row_ledger['payment_amount'] : 0 ;
				$credit_amount = ($row_ledger['payment_side'] == 'Credit') ? $row_ledger['payment_amount'] : 0 ;
				$particular = addslashes($row_ledger['payment_particular']);
				
				$balance_column = $balance_column + $debit_amount - $credit_amount;
				if($chk_trans == 1){
					$temp_arr = array(
						get_date_user($row_ledger['payment_date']),
						$row_ledger['type'],
						$sq_le_name['ledger_name'].' ('.$row_ledger['module_entry_id'].'_'.$row_ledger['module_name'].')',
						$row_ledger['payment_side'],
						$debit_amount,
						$credit_amount,
						$balance_column
					);
					array_push($transactions,$temp_arr);
				}
				else{
				
					?>
					<tr>
						<td><?= $count ?></td>
						<td><?= get_date_user($row_ledger['payment_date']) ?></td>
						<td><?= $row_ledger['type'] ?></td>
						<td style="cursor:pointer;text-decoration: underline;" onclick="show_history('<?= $row_ledger['module_entry_id'] ?>','<?= $row_ledger['module_name'] ?>','<?= $row_ledger['finance_transaction_id'] ?>','<?= $sq_le_name['customer_id'] ?>','<?= $sq_le_name['user_type'] ?>','<?= addslashes($sq_le_name['ledger_name']) ?>','<?= $row_ledger['type'] ?>')"><?= $row_ledger['module_entry_id'].'_'.$row_ledger['module_name'] ?></td>
						<td><?= $debit_amount ?></td>
						<td><?= $credit_amount ?></td>
						<td><?= number_format($balance_column,2) ?></td>
					</tr>
					<?php
					$count++;
					if($row_ledger['payment_side'] == 'Debit'){
						$total_debit1 += $row_ledger['payment_amount'];
					} 
					else{
						$total_credit1 += $row_ledger['payment_amount'];
					}
				}
			} //while close
			if($chk_trans == 0){

				if($total_debit1 > $total_credit1){
					$balance1 =  $total_debit1 - $total_credit1;
					$side_t1='(Dr)';
				}
				else{
					$balance1 =  $total_credit1 - $total_debit1;	
					$side_t1='(Cr)';
				}
			}
			if($chk_trans == 1){
				$finalArray=array();
				foreach($dateChunks as $key2=>$value2){

					$transArray=array();
					$paymentFromDate = date('Y-m-d', strtotime($value2->fromDate));
					$paymentToDate = date('Y-m-d', strtotime($value2->toDate));
					while (strtotime($paymentFromDate) <= strtotime($paymentToDate)) {

						foreach($transactions as $key=>$value){

							$paymentDate = date('Y-m-d', strtotime($value[0]));
							if (strtotime($paymentDate) == strtotime($paymentFromDate)){
								array_push($transArray,$value);
							}
						}
						$paymentFromDate = date ("Y-m-d", strtotime("+1 day", strtotime($paymentFromDate)));
					}
					array_push($finalArray,array(
						'from_date'=>$value2->fromDate,
						'to_date'=>$value2->toDate,
						'transactions'=>json_encode($transArray),
						'fortnight'=>$value2->fortnight
					));
				}
				$balance_column = 0;
				$tdebit_amount = 0;
				$tcredit_amount = 0;	
				if($chk_opnbalance == 1){
					if($side_t == '(Dr)'){
						$balance_column = $balance - $balance_column;
					}else{
						$balance_column = $balance_column + $balance;
					}
				}		
				foreach($finalArray as $key=>$value){

					$adebit_amount = 0;
					$acredit_amount = 0;
					$total_amount = 0;
					$count1 = 1;
					$time=strtotime($value['to_date']);
					$month=date("F",$time);
					$transactions = json_decode($value['transactions']);
					if(sizeof($transactions) != 0){

					foreach($transactions as $key=>$value2){
						if($value2[3] == 'Debit'){
							$adebit_amount = $adebit_amount + $value2[4];
						}else{
							$acredit_amount = $acredit_amount + $value2[5];
						}
					}
					$balance_column = $balance_column + $adebit_amount - $acredit_amount;
					$tdebit_amount =  $tdebit_amount + $adebit_amount;
					$tcredit_amount = $tcredit_amount + $acredit_amount;
					?>
					<tr>
						<td><?= $count ?></td>
						<td><?= get_date_user($value['to_date']) ?></td>
						<td><?= 'NA' ?></td>
						<td><?php echo 'For the '.$value['fortnight'].' Fortnight '.' of '.$month; ?></td>
						<td><?= number_format($adebit_amount,2)?></td>
						<td><?= number_format($acredit_amount,2) ?></td>
						<td><?= number_format($balance_column,2) ?></td>
					</tr>
					<?php  $count++; } }
					if($side_t == '(Dr)'){
						$tdebit_amount =  $tdebit_amount + $balance;
					}else{
						$tcredit_amount =  $tcredit_amount + $balance;
					}
					
					if($tdebit_amount > $tcredit_amount){
						$balance1 =  $tdebit_amount - $tcredit_amount;
						$side_t1='(Dr)';
					}
					else{
						$balance1 =  $tcredit_amount - $tdebit_amount;	
						$side_t1='(Cr)';
					} ?>
				<?php } ?>
			</tbody>
			<tfoot>
			<?php if($chk_trans == 1){ ?>
			<tr class="table-heading-row">
				<td></td>
				<td></td>
				<td></td>
				<td class="text-right">Total : </td>
				<td><?= number_format($tdebit_amount,2) ?></td>
				<td><?= number_format($tcredit_amount,2) ?></td>
				<td></td>
			</tr>
			<tr class="table-heading-row">
				<td></td>
				<td></td>
				<td></td>
				<td class="text-right" class="text-right">BALANCE : </td>
				<td><?= number_format($balance1,2).$side_t1 ?> </td>
				<td></td>
				<td></td>
			</tr>
		<?php } else{ ?>
			<tr class="table-heading-row">
				<td></td>
				<td></td>
				<td></td>
				<td class="text-right">Total : </td>
				<td><?= number_format($total_debit1,2) ?></td>
				<td><?= number_format($total_credit1,2) ?></td>
				<td></td>
			</tr>
			<tr class="table-heading-row">
				<td></td>
				<td></td>
				<td></td>
				<td class="text-right" class="text-right">BALANCE : </td>
				<td><?= number_format($balance1,2).$side_t1 ?> </td>
				<td></td>
				<td></td>
			</tr>
		<?php } ?>
		</tfoot>
	</table>
	</div></div></div>
<script>
$('#tbl_list_ledger_sub').dataTable({
		"pagingType": "full_numbers"
});
</script>