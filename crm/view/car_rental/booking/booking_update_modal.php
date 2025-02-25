<?php
include "../../../model/model.php";

$booking_id = $_POST['booking_id'];
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_POST['branch_status'];
$sq_enq_info = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$booking_id' and delete_status='0'"));
$reflections = json_decode($sq_enq_info['reflections']);
if($reflections[0]->tax_apply_on == '1') { 
    $tax_apply_on = 'Basic Amount';
}
else if($reflections[0]->tax_apply_on == '2') { 
    $tax_apply_on = 'Service Charge';
}
else if($reflections[0]->tax_apply_on == '3') { 
    $tax_apply_on = 'Total';
}else{
    $tax_apply_on = '';
}
?>

<form id="frm_booking_update">
    <input type="hidden" id="booking_id" name="booking_id" value="<?= $booking_id ?>">
    <input type="hidden" id="car_sc" name="car_sc" value="<?php echo $reflections[0]->car_sc ?>">
    <input type="hidden" id="car_markup" name="car_markup" value="<?php echo $reflections[0]->car_markup ?>">
    <input type="hidden" id="car_taxes" name="car_taxes" value="<?php echo $reflections[0]->car_taxes ?>">
    <input type="hidden" id="car_markup_taxes" name="car_markup_taxes"
        value="<?php echo $reflections[0]->car_markup_taxes ?>">
    <input type="hidden" id="tax_apply_on" name="tax_apply_on" value="<?php echo $tax_apply_on ?>">
    <input type="hidden" id="atax_apply_on" name="atax_apply_on" value="<?php echo $reflections[0]->tax_apply_on ?>">
    <input type="hidden" id="tax_value1" name="tax_value1" value="<?php echo $reflections[0]->tax_value ?>">
    <input type="hidden" id="markup_tax_value1" name="markup_tax_value1" value="<?php echo $reflections[0]->markup_tax_value ?>">

    <div class="modal fade" id="booking_update_modal" role="dialog" aria-labelledby="myModalLabel"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document" style="width: 95% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Car Rental Booking</h4>
                </div>
                <div class="modal-body">


                    <div class="panel panel-default panel-body app_panel_style feildset-panel">
                        <legend>Customer Details</legend>
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                <select name="customer_id1" id="customer_id1" style="width: 100%"
                                    onchange="customer_info_load('1')" disabled>
                                    <?php
                                    $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_enq_info[customer_id]'"));
                                    if ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') {
                                    ?>
                                    <option value="<?= $sq_customer['customer_id'] ?>">
                                        <?= $sq_customer['company_name'] ?></option>
                                    <?php } else { ?>
                                    <option value="<?= $sq_customer['customer_id'] ?>">
                                        <?= $sq_customer['first_name'] . ' ' . $sq_customer['last_name'] ?></option>
                                    <?php } ?>
                                    <?php get_customer_dropdown($role, $branch_admin_id, $branch_status); ?>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="mobile_no1" name="mobile_no1" title="Mobile Number"
                                    placeholder="Mobile No" title="Mobile No" readonly>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="email_id1" name="email_id1" title="Email Id"
                                    placeholder="Email ID" title="Email ID" readonly>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="company_name1" class="hidden" name="company_name1"
                                    title="Company Name" placeholder="Company Name" title="Company Name" readonly>
                            </div>
                            <script>
                            customer_info_load('1');
                            </script>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-sm-4 col-xs-12">
                                <input type="text" id="pass_name1" name="pass_name1" title="Guest Name"
                                    placeholder="Guest Name" value="<?= $sq_enq_info['pass_name'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default panel-body app_panel_style feildset-panel">
                        <legend>Quotation Details</legend>
                        <div class="row">
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <?php
                                $quo_id = "Without Quotation";
                                if($sq_enq_info['quotation_id'] != 0){
                                    $sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_quotation_master where quotation_id='$sq_enq_info[quotation_id]'"));
                                    $quotation_date = $sq_quotation['quotation_date'];
                                    $yr = explode("-", $quotation_date);
                                    $year = $yr[0];
                                    $quo_id = get_quotation_id($sq_enq_info['quotation_id'], $year). ': ' . $sq_quotation['customer_name'];
                                }
                                    ?>
                                    <input type="text" name="quotation_id" id="quotation_id" value="<?php echo $quo_id; ?>"
                                    readonly>

                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <select name="vehicle_name1" id="vehicle_name1" title="Vehicle Name"
                                    class="form-control" onchange="get_capacity(this.id,'1')">
                                    <option value="<?= $sq_enq_info['vehicle_name'] ?>">
                                        <?= $sq_enq_info['vehicle_name'] ?></option>
                                    <option value="">*Select Vehicle</option>
                                    <?php
                                    $sql = mysqlQuery("select * from b2b_transfer_master where status!='Inactive'");
                                    while ($row = mysqli_fetch_assoc($sql)) {
                                    ?>
                                    <option value="<?= $row['vehicle_name'] ?>"><?= $row['vehicle_name'] ?></option>
                                    <?php }  ?>
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <select name="travel_type1" id="travel_type1" title="Travel Type" class="form-control"
                                    onchange="reflect_feilds();" disabled>

                                    <option value="<?= $sq_enq_info['travel_type'] ?>">
                                        <?= $sq_enq_info['travel_type'] ?></option>
                                    <option value="Local">Local</option>
                                    <option value="Outstation">Outstation</option>

                                </select>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="capacity1" name="capacity1"
                                    onchange="validate_balance(this.id);calculate_total_fees(this.id, '1');"
                                    placeholder="Capacity" title="Capacity" class="form-control"
                                    value="<?= $sq_enq_info['capacity'] ?>">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="total_pax1" name="total_pax1"
                                    onchange="validate_balance(this.id);calculate_total_fees(this.id, '1')"
                                    placeholder="No Of Pax" title="No Of Pax" value="<?= $sq_enq_info['total_pax'] ?>">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <textarea type="text" name="local_places_to_visit1" onchange="validate_spaces(this.id)"
                                    id="local_places_to_visit1" placeholder="Route" title="Route"
                                    rows="1"><?= $sq_enq_info['local_places_to_visit'] ?></textarea>
                                <select name="places_to_visit1" id="places_to_visit1" title="Places To visit"
                                    class="form-control" onchange="get_car_cost();">
                                    <option value="<?= $sq_enq_info['places_to_visit'] ?>">
                                        <?= $sq_enq_info['places_to_visit'] ?></option>
                                    <option value="">*Select Route</option>
                                    <?php
                                    $sql = mysqlQuery("select * from car_rental_tariff_entries where tour_type='Outstation'");
                                    while ($row = mysqli_fetch_assoc($sql)) {
                                    ?>
                                    <option value="<?= $row['route'] ?>"><?= $row['route'] ?></option>
                                    <?php }  ?>
                                </select>
                            </div>


                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="from_date1" name="from_date1" placeholder="From Date"
                                    title="From Date" class="form-control"
                                    value="<?= date('d-m-Y', strtotime($sq_enq_info['from_date'])) ?>"
                                    onchange="get_to_date(this.id,'to_date1');total_days_reflect('1');;calculate_total_fees(this.id, '1')">
                            </div>

                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="to_date1" name="to_date1" placeholder="To Date" title="To Date"
                                    class="form-control"
                                    value="<?= date('d-m-Y', strtotime($sq_enq_info['to_date'])) ?>"
                                    onchange="validate_validDate('from_date1','to_date1');total_days_reflect('1');calculate_total_fees(this.id, '1')">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="total_hrs1" name="total_hrs1" placeholder="Total Hrs"
                                    title="Total Hrs" class="form-control" value="<?= $sq_enq_info['total_hrs'] ?>">
                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="days_of_traveling1" name="days_of_traveling1"
                                    onchange="validate_balance(this.id);calculate_total_fees(this.id, '1');"
                                    placeholder="Days Of Travelling" title="Days Of Travelling"
                                    value="<?= $sq_enq_info['days_of_traveling'] ?>"
                                    value="<?= $sq_enq_info['markup_cost_subtotal'] ?>">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="rate1" name="rate1" class="text-right form-control"
                                    placeholder="Daily Rate" title="Daily Rate" onchange="calculate_total_fees(this.id,'1');get_auto_values('booking_date1','basic_amount1','payment_mode','service_charge1','markup_cost1','update','true','service_charge');"
                                    class="form-control" value="<?= $sq_enq_info['rate'] ?>">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="extra_km1" name="extra_km1" placeholder="Extra Km Rate"
                                    title="Extra Km Rate" class="text-right form-control"
                                    onchange="validate_balance(this.id)" value="<?= $sq_enq_info['extra_km'] ?>">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="extra_hr_cost1" name="extra_hr_cost1"
                                    class="text-right form-control" placeholder="Extra Hr Rate" title="Extra Hr Rate"
                                    onchange="calculate_total_fees(this.id, '1');validate_balance(this.id);"
                                    value="<?= $sq_enq_info['extra_hr_cost'] ?>">
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="total_max_km1" name="total_max_km1" placeholder="Total Max Km"
                                    title="Total Max Km" class="form-control"
                                    value="<?= $sq_enq_info['total_max_km'] ?>">
                                <input type="text" id="total_km1" name="total_km1" class="text-right form-control"
                                    placeholder="Total Km" title="Total Km" class="form-control"
                                    value="<?= $sq_enq_info['total_km'] ?>">
                            </div>
                            <?php
                            if($sq_enq_info['travel_type'] == 'Outstation'){
                                ?>
                                <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                    <input type="text" id="traveling_date1" name="traveling_date1"
                                        placeholder="Travelling Date" title="Travelling Date"
                                        value="<?= (($sq_enq_info['traveling_date'])!='1970-01-01') ? date('d-m-Y', strtotime($sq_enq_info['traveling_date'])) : ''  ?>">
                                </div>
                                <?php
                            }else{ ?>
                            <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_10">
                                <input type="text" id="traveling_date1" name="traveling_date1"
                                    placeholder="Travelling Date" title="Travelling Date">
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="panel panel-default panel-body app_panel_style feildset-panel">
                                <legend>Costing Details</legend>
                                <div class="row">

                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="driver_allowance1" name="driver_allowance1"
                                            placeholder="Driver Allowance" class="text-right form-control"
                                            title="Driver Allowance"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id)"
                                            value="<?= $sq_enq_info['driver_allowance'] ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="permit_charges1" name="permit_charges1"
                                            class="text-right form-control" placeholder="Permit Charges"
                                            title="Permit Charges"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id)"
                                            value="<?= $sq_enq_info['permit_charges'] ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="toll_and_parking1" name="toll_and_parking1"
                                            class="text-right form-control" placeholder="Toll & Parking"
                                            title="Toll & Parking"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id)"
                                            value="<?= $sq_enq_info['toll_and_parking'] ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="state_entry_tax1" name="state_entry_tax1"
                                            class="text-right form-control" placeholder="State Entry"
                                            title="State Entry"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id)"
                                            value="<?= $sq_enq_info['state_entry_tax'] ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="other_charges1" name="other_charges1"
                                            class="text-right form-control" placeholder="Other Charges"
                                            title="Other Charges"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id)"
                                            value="<?= $sq_enq_info['other_charges'] ?>">
                                    </div>
                                </div>
                                <?php
                $basic_cost1 = $sq_enq_info['basic_amount'];
                $service_charge = $sq_enq_info['service_charge'];
                $markup = $sq_enq_info['markup_cost'];

                $bsmValues = json_decode($sq_enq_info['bsm_values']);
                $service_tax_amount = 0;
                if ($sq_enq_info['service_tax_subtotal'] !== 0.00 && ($sq_enq_info['service_tax_subtotal']) !== '') {
                    $service_tax_subtotal1 = explode(',', $sq_enq_info['service_tax_subtotal']);
                    for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
                        $service_tax = explode(':', $service_tax_subtotal1[$i]);
                        $service_tax_amount = $service_tax_amount + $service_tax[2];
                    }
                }
                $markupservice_tax_amount = 0;
                if ($sq_enq_info['markup_cost_subtotal'] !== 0.00 && $sq_enq_info['markup_cost_subtotal'] !== "") {
                    $service_tax_markup1 = explode(',', $sq_enq_info['markup_cost_subtotal']);
                    for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
                        $service_tax = explode(':', $service_tax_markup1[$i]);
                        $markupservice_tax_amount = $markupservice_tax_amount + $service_tax[2];
                    }
                }
                foreach ($bsmValues[0] as $key => $value) {
                    switch ($key) {
                        case 'basic':
                        $basic_cost = ($value != "") ? $basic_cost1 + $service_tax_amount : $basic_cost1;
                        $inclusive_b = $value;
                        break;
                        case 'service':
                        $service_charge = ($value != "") ? $service_charge + $service_tax_amount : $service_charge;
                        $inclusive_s = $value;
                        break;
                        case 'markup':
                        $markup = ($value != "") ? $markup + $markupservice_tax_amount : $markup;
                        $inclusive_m = $value;
                        break;
                    }
                }
                ?>
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <small
                                            id="basic_show1"><?= ($inclusive_b == '') ? '&nbsp;' : 'Inclusive Amount : <span>' . $inclusive_b ?></span></small>
                                        <input type="text" id="basic_amount1" name="basic_amount1"
                                            class="text-right form-control" placeholder="*Basic Amount"
                                            title="Basic Amount"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id);get_auto_values('booking_date1','basic_amount1','payment_mode','service_charge1','markup_cost1','update','true','service_charge');" value="<?= $basic_cost ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <small
                                            id="service_show1"><?= ($inclusive_s == '') ? '&nbsp;' : 'Inclusive Amount : <span>' . $inclusive_s ?></span></small>
                                        <input type="text" id="service_charge1" name="service_charge1"
                                            class="text-right form-control" placeholder="Service Charge"
                                            title="Service Charge"
                                            onchange="validate_balance(this.id);get_auto_values('booking_date1','basic_amount1','payment_mode','service_charge1','markup_cost1','update','true','service_charge');" value="<?= $service_charge ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <small>&nbsp;</small>
                                        <input type="text" id="service_tax_subtotal1" name="service_tax_subtotal1"
                                            class="text-right form-control"
                                            value="<?= $sq_enq_info['service_tax_subtotal'] ?>" placeholder="Tax Amount"
                                            title="Tax Amount" readonly>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <small
                                            id="markup_show1"><?= ($inclusive_m == '') ? '&nbsp;' : 'Inclusive Amount : <span>' . $inclusive_m ?></span></small>
                                        <input type="text" id="markup_cost1" name="markup_cost1"
                                            placeholder="Markup Amount" title="Markup Amount"
                                            class="text-right form-control"
                                            onchange="validate_balance(this.id);get_auto_values('booking_date1','basic_amount1','payment_mode','service_charge1','markup_cost1','update','false','markup');"
                                            value="<?= $markup ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="service_tax_markup1" name="service_tax_markup1"
                                            class="text-right form-control" placeholder="Markup Tax" title="Markup Tax"
                                            value="<?= $sq_enq_info['markup_cost_subtotal'] ?>" readonly>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="total_cost1" name="total_cost1"
                                            class="text-right form-control" placeholder="Total" title="Total"
                                            onchange="calculate_total_fees(this.id, '1');validate_balance(this.id)"
                                            readonly value="<?= $sq_enq_info['total_cost'] ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="roundoff1" class="text-right form-control"
                                            name="roundoff1" placeholder="Round Off" title="Round Off"
                                            value="<?= $sq_enq_info['roundoff'] ?>" readonly>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" id="total_fees1" class="amount_feild_highlight text-right"
                                            name="total_fees1" placeholder="Net Total" title="Net Total" readonly
                                            value="<?= $sq_enq_info['total_fees'] ?>">
                                            <input type="hidden" id="old_total" value="<?= $sq_enq_info['total_fees'] ?>"/>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                                        <input type="text" name="due_date1" id="due_date1" placeholder="Due Date"
                                            title="Due Date" value="<?= get_date_user($sq_enq_info['due_date']) ?>">
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <input type="text" name="booking_date1" id="booking_date1"
                                            value="<?= get_date_user($sq_enq_info['created_at']) ?>"
                                            placeholder="Booking Date" title="Booking Date"
                                            onchange="check_valid_date(this.id);get_auto_values('booking_date1','basic_amount1','payment_mode','service_charge1','markup_cost1','update','false','markup');">
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>


                    <div class="row text-center">
                        <div class="col-xs-12">
                            <button class="btn btn-sm btn-success" id="car_update"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Update</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>


<script>
$('#vendor_id1, #customer_id1').select2();
$('#from_date1,#to_date1').datetimepicker({
    timepicker: false,
    format: 'd-m-Y'
});
$('#booking_update_modal').modal('show');

$('#from_date1,#to_date1,#total_hrs1,#total_km1,#total_max_km1,#driver_allowance1,#permit_charges1,#toll_and_parking1,#state_entry_tax1,#other_charges1,#local_places_to_visit1,#places_to_visit1,#traveling_date1')
    .hide();

$('#from_date1,#to_date,#due_date1,#booking_date1,#traveling_date1').datetimepicker({
    timepicker: false,
    format: 'd-m-Y'
});

function reflect_feilds() {
    var type = $('#travel_type1').val();

    if (type == 'Local') {
        $('#from_date1,#to_date1,#total_hrs1,#total_km1,#local_places_to_visit1').show();
        $('#total_max_km1,#driver_allowance1,#permit_charges1,#toll_and_parking1,#state_entry_tax1,#other_charges1,#places_to_visit1,#traveling_date1')
            .hide();
    }
    if (type == 'Outstation') {
        $('#total_hrs1,#total_km1,#local_places_to_visit1').hide();
        $('#from_date1,#to_date1,#total_max_km1,#driver_allowance1,#permit_charges1,#toll_and_parking1,#state_entry_tax1,#other_charges1,#places_to_visit1,#traveling_date1')
            .show();
    }
}
reflect_feilds();
$(function() {
    $('#frm_booking_update').validate({
        rules: {
            booking_date1: {
                required: true
            },
        },
        submitHandler: function(form) {

            $('#car_update').prop('disabled',true);
            var booking_id = $('#booking_id').val();
            var travel_type = $('#travel_type1').val();
            var capacity = $('#capacity1').val();
            var total_pax = $('#total_pax1').val();
            var places_to_visit = $('#places_to_visit1').val();
            var local_places_to_visit = $('#local_places_to_visit1').val();
            var from_date = $('#from_date1').val();
            var to_date = $('#to_date1').val();
            var total_hrs = $('#total_hrs1').val();
            var total_km = $('#total_km1').val();
            var rate = $('#rate1').val();
            var days_of_traveling = $('#days_of_traveling1').val();
            var customer_id = $('#customer_id1').val();
            var vehicle_name = $('#vehicle_name1').val();
            var traveling_date = $('#traveling_date1').val();
            var pass_name = $('#pass_name1').val();
            var extra_km = $('#extra_km1').val();
            var total_max_km = $('#total_max_km1').val();
            var extra_hr_cost = $('#extra_hr_cost1').val();
            var driver_allowance = $('#driver_allowance1').val();
            var permit_charges = $('#permit_charges1').val();
            var toll_and_parking = $('#toll_and_parking1').val();
            var state_entry_tax = $('#state_entry_tax1').val();
            var other_charges = $('#other_charges1').val();
            var basic_amount = $('#basic_amount1').val();
            var markup_cost = $('#markup_cost1').val();
            var markup_cost_subtotal = $('#service_tax_markup1').val();
            var service_charge = $('#service_charge1').val();
            var service_tax_subtotal = $('#service_tax_subtotal1').val();
            var total_cost = $('#total_cost1').val();
            var old_total = $('#old_total').val();
            var total_fees = $('#total_fees1').val();
            var due_date1 = $('#due_date1').val();
            var booking_date1 = $('#booking_date1').val();

            var base_url = $('#base_url').val();

            var car_sc = $('#car_sc').val();
            var car_markup = $('#car_markup').val();
            var car_taxes = $('#car_taxes').val();
            var car_markup_taxes = $('#car_markup_taxes').val();
            var tax_apply_on = $('#atax_apply_on').val();
            var tax_value = $('#tax_value1').val();
            var markup_tax_value = $('#markup_tax_value1').val();
            var reflections = [];
            reflections.push({
                'car_sc': car_sc,
                'car_markup': car_markup,
                'car_taxes': car_taxes,
                'car_markup_taxes': car_markup_taxes,
                'tax_apply_on':tax_apply_on,
                'tax_value':tax_value,
                'markup_tax_value':markup_tax_value
            });
            var roundoff = $('#roundoff1').val();
            var bsmValues = [];
            bsmValues.push({
                "basic": $('#basic_show1').find('span').text(),
                "service": $('#service_show1').find('span').text(),
                "markup": $('#markup_show1').find('span').text()
            });
            //Validation for booking and payment date in login financial year
            var check_date1 = $('#booking_date1').val();
            $.post(base_url + 'view/load_data/finance_date_validation.php', {
                check_date: check_date1
            }, function(data) {
                if (data !== 'valid') {
                    error_msg_alert("The Booking date does not match between selected Financial year.");
                    $('#car_update').prop('disabled',false);
                    return false;
                } else {
                    $('#car_update').button('loading');
                    $('#car_update').prop('disabled',true);

                    $.ajax({
                        type: 'post',
                        url: base_url +
                            'controller/car_rental/booking/booking_update.php',
                        data: {
                            booking_id: booking_id,
                            customer_id: customer_id,
                            total_pax: total_pax,
                            pass_name: pass_name,
                            days_of_traveling: days_of_traveling,
                            travel_type: travel_type,
                            places_to_visit: places_to_visit,
                            extra_km: extra_km,
                            service_charge: service_charge,
                            service_tax_subtotal: service_tax_subtotal,
                            total_cost: total_cost,
                            driver_allowance: driver_allowance,
                            permit_charges: permit_charges,
                            toll_and_parking: toll_and_parking,
                            state_entry_tax: state_entry_tax,
                            total_fees: total_fees,
                            due_date1: due_date1,
                            booking_date1: booking_date1,
                            capacity: capacity,
                            from_date: from_date,
                            to_date: to_date,
                            total_hrs: total_hrs,
                            total_km: total_km,
                            rate: rate,
                            total_max_km: total_max_km,
                            extra_hr_cost: extra_hr_cost,
                            other_charges: other_charges,
                            basic_amount: basic_amount,
                            markup_cost: markup_cost,
                            markup_cost_subtotal: markup_cost_subtotal,
                            vehicle_name: vehicle_name,
                            local_places_to_visit: local_places_to_visit,
                            traveling_date: traveling_date,
                            reflections: reflections,
                            roundoff: roundoff,
                            bsmValues: bsmValues,old_total:old_total
                        },
                        success: function(result) {
                            msg_popup_reload(result);
                            $('#car_update').prop('disabled',true);
                        },
                        error: function(result) {
                            console.log(result.responseText);
                        }
                    });
                }
            });


        }
    });
});
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>