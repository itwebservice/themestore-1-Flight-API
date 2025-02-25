<hr class="no-marg">
<section class="print_sec main_block inv_rece_footer_top">
    <div class="row">
        <div class="col-md-12">
            <h3 class="no-marg font_5 font_s_14">In Words : <?php echo $amount_in_word; ?></h3>
        </div>
    </div>
</section>

<hr class="no-marg">

<!-- invoice_receipt_footer -->
<section class="print_sec main_block inv_rece_footer_bottom">
    <div class="inv_rece_footer_signature border_block ">
        <div class="row">
            <div class="col-md-7 border_rt">
                <div class="inv_rece_footer_left">
                    <?php
                    if (isset($sq_terms_cond['terms_and_conditions']) && $sq_terms_cond['terms_and_conditions'] != '') { ?>
                        <h3 class="no-marg font_5 font_s_14">TERMS & CONDITIONS</h3>
                        <p class="less_opact "><?= $sq_terms_cond['terms_and_conditions'] ?></p>
                    <?php } ?>
                    <div class="signature_block text-right" style="<?= isset($sq_terms_cond['terms_and_conditions'])  ? 'margin-top:50px;' : null ?>">
                        <p class="no-marg font_s_13">RECEIVER SIGNATURE</p>
                    </div>
                </div>
            </div>
            <div class="col-md-5" style="margin-bottom:0;">
                <div class="inv_rece_footer_right" style="margin-bottom:0 !important; padding:0 !important;">
                    <h3 class="no-marg font_5 font_s_12">FOR <?= $app_name ?>
                    </h3>

                    <?php
                    if (check_sign()) {
                    ?>
                        <div class="text-right">
                            <?= get_signature() ?>
                        </div>
                    <?php } ?>
                    <br>
                    <div style="text-align:right;">

                        <p class="no-marg font_s_13">AUTHORIZED SIGNATURE</p>
                        <p class="no-marg font_s_13">GENERATED BY : <?= $emp_name ?></p>
                    </div>

                </div>
            </div>
        </div>
        <br>
    </div>

    <!-- invoice_receipt_back_detail -->
    <?php
    global $bank_details_switch;
    if($bank_details_switch == 'Yes'){
        if($branch_admin_id != 0){
          $branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_admin_id'"));
          $sq_bank_count = mysqli_num_rows(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id' and active_flag='Active'"));
          $sq_bank_branch = mysqli_fetch_assoc(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id' and active_flag='Active'"));
        }
        else{
            if($branch_admin_id == ''){
                $branch_admin_id1 = $_SESSION['branch_admin_id'];
            }else{
                $branch_admin_id1 = 1;
            }
        
          $branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_admin_id1'"));
          $sq_bank_count = mysqli_num_rows(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id1' and active_flag='Active'"));
          $sq_bank_branch = mysqli_fetch_assoc(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id1' and active_flag='Active'"));
        }
    ?>
    <div class="border_block inv_rece_back_detail mg_tp_10">
		<div class="row">
			<div class="col-md-6"><p class="border_lt"><span class="font_5">BANK NAME : </span> <?= ($sq_bank_count>0 || $sq_bank_branch['bank_name'] != '') ? $sq_bank_branch['bank_name'] : $bank_name_setting ?></p></div>
			<div class="col-md-6"><p class="border_lt"><span class="font_5">A/C TYPE : </span><?= ($sq_bank_count>0 || $sq_bank_branch['account_type'] != '') ? $sq_bank_branch['account_type'] : $acc_name ?></p></div>
			<div class="col-md-6"><p class="border_lt"><span class="font_5">BRANCH : </span> <?= ($sq_bank_count>0 || $sq_bank_branch['branch_name'] != '') ? $sq_bank_branch['branch_name'] : $bank_branch_name ?></p></div>
			<div class="col-md-6"><p class="border_lt"><span class="font_5">A/C NO : </span><?= ($sq_bank_count>0 || $sq_bank_branch['account_no'] != '') ? $sq_bank_branch['account_no'] : $bank_acc_no ?></p></div>
			<div class="col-md-6"><p class="border_lt"><span class="font_5">IFSC/SWIFT CODE : </span><?= ($sq_bank_count>0 || $sq_bank_branch['ifsc_code'] != '') ? strtoupper($sq_bank_branch['ifsc_code']) : strtoupper($bank_ifsc_code) ?><?= ($sq_bank_count>0 || $sq_bank_branch['swift_code'] != '') ? '/' . strtoupper($sq_bank_branch['swift_code']) : '/' . strtoupper($bank_swift_code) ?> </p></div>
			<div class="col-md-6"><p class="border_lt no-marg"><span class="font_5">BANK ACCOUNT NAME : </span><?= ($sq_bank_count>0 || $sq_bank_branch['account_name'] != '') ? $sq_bank_branch['account_name'] : $bank_account_name ?></p></div>
		</div>
	</div>
    <?php } ?>
</section>

<!-- Bottom_Note -->
<hr class="no-marg">
<section class="print_sec main_block inv_rece_footer_top">
    <div class="row">
        <div class="col-md-12">
            <h3 class="no-marg font_5 font_s_13 text-center less_opact">This is a Computer generated document and does
                not require any signature</h3>
        </div>
    </div>
</section>
<hr class="no-marg">
</body>

</html>