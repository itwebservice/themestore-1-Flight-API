<?php
include "../../model/model.php";

$tourwise_id = isset($_POST['cmb_tourwise_traveler_id']) ? $_POST['cmb_tourwise_traveler_id'] : 0;

$sq_tourwise = mysqli_fetch_assoc(mysqlQuery("select * from tourwise_traveler_details where id = '$tourwise_id' and delete_status='0' "));
$date = $sq_tourwise['form_date'];
$yr = explode("-", $date);
$year = $yr[0];

$sq_total_tour_paid_amount = mysqli_fetch_assoc(mysqlQuery("select sum(amount) as sum from payment_master where tourwise_traveler_id='$tourwise_id' and clearance_status!='Pending' AND clearance_status!='Cancelled'"));

$sq_ref_paid_count = mysqli_num_rows(mysqlQuery("SELECT SUM(total_refund) as total FROM refund_traveler_cancelation where tourwise_traveler_id ='$tourwise_id' and clearance_status!='Cancelled' and clearance_status!='Pending'"));
$sq_ref_paid_amt1 = mysqli_fetch_assoc(mysqlQuery("SELECT SUM(total_refund) as total FROM refund_traveler_cancelation where tourwise_traveler_id ='$tourwise_id' and clearance_status!='Cancelled' and clearance_status!='Pending'"));
$total_refund1 = ($sq_ref_paid_count > 0 ) ? $sq_ref_paid_amt1['total'] : 0;

$total_amount = $sq_total_tour_paid_amount['sum'];

$unique_timestamp =  md5(uniqid());

$sq_refund_info_c = mysqli_num_rows(mysqlQuery("select * from refund_traveler_estimate where tourwise_traveler_id='$tourwise_id'"));
$sq_refund_info1 = mysqli_fetch_assoc(mysqlQuery("select * from refund_traveler_estimate where tourwise_traveler_id='$tourwise_id'"));
$total_refund_paid = ($sq_refund_info_c > 0 ) ? $sq_refund_info1['total_refund_amount'] : 0;

$booking_fee = floatval($sq_tourwise['net_total']);

$sale_Amount = $booking_fee - floatval($total_refund_paid);

$refund_amount = floatval($total_amount) - floatval($sale_Amount);
$remaining = floatval($total_refund_paid) - floatval($total_refund1);
?>
<input type="hidden" id="txt_tourwise_traveler_id" name="txt_tourwise_traveler_id" value="<?php echo $tourwise_id; ?>">
<input type="hidden" id="txt_unique_timestamp" name="unique_timestamp" value="<?php echo $unique_timestamp; ?>">
<input type="hidden" id="remaining" name="remaining" value="<?php echo $remaining; ?>">
<input type="hidden" id="refund_amount" name="refund_amount" value="<?php echo $total_refund_paid ?>">

<!-- //begin_panel('Refund Details',67)  -->

<div class="row mg_tp_20 mg_bt_10">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-xs-12 mg_bt_10_xs mg_tp_20">
        <div class="widget_parent-bg-img bg-green">
            <div class="widget_parent">
                <div class="stat_content main_block">
                    <span class="main_block content_span" data-original-title="" title="">
                        <span class="stat_content-tilte pull-left" data-original-title="" title="">Booking ID</span>
                        <span class="stat_content-amount pull-right" data-original-title=""
                            title=""><?= get_group_booking_id($tourwise_id, $year) ?></span>
                    </span>
                    <span class="main_block content_span" data-original-title="" title="">
                        <span class="stat_content-tilte pull-left" data-original-title="" title="">Total Sale</span>
                        <span class="stat_content-amount pull-right" data-original-title=""
                            title=""><?= ($booking_fee == '') ? '0.00' : number_format($booking_fee, 2) ?></span>
                    </span>
                    <span class="main_block content_span" data-original-title="" title="">
                        <span class="stat_content-tilte pull-left" data-original-title="" title="">Paid Amount</span>
                        <span class="stat_content-amount pull-right" data-original-title="" title="">
                            <?= ($total_amount == '') ? '0.00' : number_format($total_amount, 2) ?></span>
                    </span>
                    <span class="main_block content_span" data-original-title="" title="">
                        <span class="stat_content-tilte pull-left" data-original-title="" title="">Cancellation
                            Amount</span>
                        <span class="stat_content-amount pull-right" data-original-title=""
                            title=""><?= number_format($sq_refund_info1['cancel_amount'], 2); ?></span>
                    </span>
                    <span class="main_block content_span" data-original-title="" title="">
                        <span class="stat_content-tilte pull-left" data-original-title="" title="">Refund Amount</span>
                        <span class="stat_content-amount pull-right" data-original-title=""
                            title=""><?php echo number_format($sq_refund_info1['total_refund_amount'], 2); ?></span>
                    </span>
                    <span class="main_block content_span" data-original-title="" title="">
                        <span class="stat_content-tilte pull-left" data-original-title="" title="">Pending Refund
                            Amount</span>
                        <span class="stat_content-amount pull-right" data-original-title=""
                            title=""><?php echo number_format($remaining, 2); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12 mg_tp_20">
        <form id="frm_traveler_refund">
            <h3 class="editor_title">Refund Details</h3>
            <div class="panel panel-default panel-body mg_bt_10">
                <div class="row">
                    <div class="col-sm-6 col-xs-12 mg_bt_10">
                        <input type="text" id="txt_total_refund_cost_c" name="txt_total_refund_cost_c"
                            title="Refund Amount" placeholder="*Refund Amount"
                            onchange="validate_balance(this.id);payment_amount_validate(this.id,'cmb_refund_mode','transaction_id','bank_name')"
                            class="form-control" />
                    </div>
                    <div class="col-sm-6 col-xs-12 mg_bt_10">
                        <input type="text" id="refund_date" name="refund_date" placeholder="*Refund Date" title="Refund Date"
                            value="<?= date('d-m-Y') ?>" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-xs-12 mg_bt_10">
                        <select id="cmb_refund_mode" name="cmb_refund_mode" class="form-control"
                            onchange="payment_master_toggles(this.id, 'bank_name', 'transaction_id', 'bank_id')"
                            title="Payment Mode">
                            <?php get_payment_mode_dropdown(); ?>
                        </select>
                    </div>
                    <div class="col-sm-6 col-xs-12 mg_bt_10">
                        <input type="text" id="bank_name" name="bank_name" placeholder="Bank Name"
                            class="form-control bank_suggest" title="Bank Name" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-xs-12 mg_bt_10">
                        <input type="number" id="transaction_id" onchange="validate_balance(this.id);"
                            name="transaction_id" placeholder="Cheque No / ID" title="Cheque No / ID"
                            class="form-control" disabled>
                    </div>
                    <div class="col-sm-6 col-xs-12 mg_bt_10">
                        <select name="bank_id" id="bank_id" title="Bank" class="form-control" disabled>
                            <?php get_bank_dropdown('Debitor Bank')  ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 mg_bt_10">
                        <select id="traveler_id" name="traveler_id" class="form-control">
                            <?php
                            $sq_traveler_query = mysqlQuery("select * from travelers_details where status='Cancel' and traveler_group_id IN ( select traveler_group_id from tourwise_traveler_details where id='$tourwise_id' ) ");
                            while ($row_traveler = mysqli_fetch_assoc($sq_traveler_query)) {
                            ?>
                            <option value="<?php echo $row_traveler['traveler_id'] ?>">
                                <?php echo $row_traveler['m_honorific'] . ' ' . $row_traveler['first_name'] . ' ' . $row_traveler['last_name']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="row col-xs-12 text-center mg_tp_20">
                    <button class="btn btn-sm btn-success" id="group_refund"><i
                            class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-12 col-sm-12 col-xs-12 mg_tp_20">
        <div id="refund_canceled_traveler_summary_tbl">
            <?php include "refund_canceled_traveler_summary_tbl.php" ?>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL ?>js/app/field_validation.js"></script>
<script>
$('#refund_date').datetimepicker({
    timepicker: false,
    format: 'd-m-Y'
})
</script>
<script src="js/refund_booking.js"></script>