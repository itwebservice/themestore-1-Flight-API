<form id="frm_tab_1">

    <div class="app_panel">
        <div class="container-fluid mg_tp_10">

            <div class="app_panel_content no-pad">
                <div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                    <legend>Tour Details</legend>

                    <div class="row">
                        <div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10">
                            <select name="quotation_id" id="quotation_id" title="Select Quotation" style="width:100%;"
                                onchange="quotation_info_load()">
                                <option value="">*Select Quotation</option>
                                <option value="0"><?= "Sale Without Quotation" ?></option>
                                <?php
                                $query = "select * from package_tour_quotation_master where status='1' order by quotation_id desc";
                                if ($branch_status == 'yes') {
                                    if ($role == 'Branch Admin' || $role == 'Accountant' || $role_id > '7') {
                                        $query = "select * from package_tour_quotation_master where status='1' and branch_admin_id='$branch_admin_id' order by quotation_id desc";
                                    } elseif ($role != 'Admin' && $role != 'Branch Admin' && $role_id != '7' && $role_id < '7') {
                                        $query = "select * from package_tour_quotation_master where status='1' and emp_id='$emp_id' and branch_admin_id='$branch_admin_id' order by quotation_id desc";
                                    }
                                } elseif ($role != 'Admin' && $role != 'Branch Admin' && $role_id != '7' && $role_id < '7') {
                                    $query = "select * from package_tour_quotation_master where status='1' and emp_id='$emp_id' order by quotation_id desc";
                                }
                                $sq_quotation = mysqlQuery($query);
                                while ($row_quotation = mysqli_fetch_assoc($sq_quotation)) {
                                    
                                    $sq_enq_count = mysqli_num_rows(mysqlQuery("select * from enquiry_master_entries where followup_status='Converted' and quotation_id='$row_quotation[quotation_id]' and enquiry_id in(select enquiry_id from enquiry_master where enquiry_type='Package Booking') "));
                                    $bg = ($sq_enq_count == 0 ) ? '' : '#7ab676';

                                    $sq_cost =  mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id = '$row_quotation[quotation_id]'"));

                                    $cust_user_name = '';
                                    if($row_quotation['user_id'] != 0){ 
                                        $row_user = mysqli_fetch_assoc(mysqlQuery("Select name from customer_users where user_id ='$row_quotation[user_id]'"));
                                        $cust_user_name = ' ('.$row_user['name'].')';
                                    }

                                    $yr = explode("-", $row_quotation['quotation_date']);
                                    $basic_cost = $sq_cost['basic_amount'];
                                    $service_charge = $sq_cost['service_charge'];
                                    $service_tax_amount = 0;
                                    $tax_show = '';
                                    $bsmValues = json_decode($sq_cost['bsmValues']);
                                    $discount_in = $sq_cost['discount_in'];
                                    $discount = $sq_cost['discount'];
                                    if($discount_in == 'Percentage'){
                                        $act_discount = floatval($service_charge) * floatval($discount) / 100;
                                    }else{
                                        $act_discount = $discount;
                                    }
                                    $service_charge = $service_charge - floatval($act_discount);
                                    $tour_cost = $basic_cost + $service_charge;

                                    if ($sq_cost['service_tax_subtotal'] !== 0.00 && ($sq_cost['service_tax_subtotal']) !== '') {
                                        $service_tax_subtotal1 = explode(',', $sq_cost['service_tax_subtotal']);
                                        for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
                                            $service_tax = explode(':', $service_tax_subtotal1[$i]);
                                            $service_tax_amount = floatval($service_tax_amount) + floatval($service_tax[2]);
                                            $name .= $service_tax[0] . ' ';
                                            $percent = $service_tax[1];
                                        }
                                    }
                                    if ($bsmValues[0]->service != '') {   //inclusive service charge
                                        $newBasic = $tour_cost + $service_tax_amount;
                                        $tax_show = '';
                                    } else {
                                        $tax_show =  $name . $percent . ($service_tax_amount);
                                        $newBasic = $tour_cost;
                                    }

                                    ////////////Basic Amount Rules
                                    if ($bsmValues[0]->basic != '') { //inclusive markup
                                        $newBasic = $tour_cost + $service_tax_amount;
                                        $tax_show = '';
                                    }

                                    $quotation_cost = $basic_cost + $service_charge + $service_tax_amount + $row_quotation['train_cost'] + $row_quotation['cruise_cost'] + $row_quotation['flight_cost'] + $row_quotation['visa_cost'] + $row_quotation['guide_cost'] + $row_quotation['misc_cost'];
                                       
                                    $tcs_percentage=$bsmValues[0]->tcsper;

                                    if ($bsmValues[0]->tcsper != '') { 
                                        $tcs_amount = ($quotation_cost * $tcs_percentage) / 100;
                                        $quotation_cost += $tcs_amount;
                                    }
                                    $quotation_cost = floor($quotation_cost);
                                    //Currency conversion
                                    $currency_amount1 = currency_conversion($currency, $row_quotation['currency_code'], $quotation_cost);
                                    if ($row_quotation['currency_code'] != '0' && $currency != $row_quotation['currency_code']) {
                                        $currency_amount = ' (' . $currency_amount1 . ')';
                                    } else {
                                        $currency_amount = '';
                                    }
                                    ?>
                                    <option style="background-color:<?= $bg ?> !important;" value="<?= $row_quotation['quotation_id'] ?>">
                                        <?= get_quotation_id($row_quotation['quotation_id'],$yr[0]) . ' : ' . $row_quotation['customer_name'] .$cust_user_name. ' : ' . $quotation_cost . ' /-' . $currency_amount ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10" id="package_types_html">
                        </div>
                        <div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10" id="dest_div">
                        </div>
                        <div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10" id="package_div">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <input type="text" id="txt_package_tour_name" name="txt_package_tour_name"
                                placeholder="Package Tour Name" title="Package Tour Name">
                            <input type="hidden" id="txt_package_package_id" name="txt_package_package_id"
                                placeholder="Package Id" title="Package Id">
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <select name="tour_type" id="tour_type" title="Tour Type"
                                onchange="passport_fields_toggle(this.value);calculate_total_tour_cost();">
                                <option value="">Tour Type</option>
                                <option value="Domestic">Domestic</option>
                                <option value="International">International</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <input class="pck-tour-tab-txt" type="text" id="txt_package_from_date"
                                name="txt_package_from_date" placeholder="From Date" title="From Date"
                                onchange="due_date_reflect();get_to_date(this.id,'txt_package_to_date');total_days_reflect();">
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <input class="pck-tour-tab-txt" type="text" id="txt_package_to_date"
                                name="txt_package_to_date" placeholder="To Date" title="To Date"
                                onchange="validate_validDate('txt_package_from_date','txt_package_to_date');total_days_reflect();">
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
                            <input class="pck-tour-tab-txt" type="text" id="txt_tour_total_days"
                                name="txt_tour_total_days" placeholder="Tour Total Days" title="Tour Total Days"
                                onchange="validate_balance(this.id);" readonly>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 hidden">
                            <select name="taxation_type" id="taxation_type" title="Taxation Type">

                            </select>
                        </div>
                        <div class="col-md-3 col-sm-4 col-xs-12 mg_bt_10" id="currency_div">
                        </div>
                    </div>

                    <div id="package_program"></div>
                </div>
                <div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                    <legend>Customer Details</legend>

                    <!-- <div class="row text-right">
                            <button class="btn btn-info btn-sm ico_left mg_bt_20" title="Add Customer" onclick="customer_save_modal()"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Customer</button>
                    </div> -->
                    <div class="row mg_tp_20">
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <select name="customer_id" id="customer_id_p" class="customer_dropdown"
                                title="Select Customer Name" style="width:100%" onchange="customer_info_load(this.id);">
                                <option value="">Select Customer</option>
                                <option value="ncust">New Customer</option>
                                <?php
                                if ($branch_status == 'yes' && $role != 'Admin') {
                                    $sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' and branch_admin_id='$branch_admin_id' order by customer_id desc");
                                    while ($row_cust = mysqli_fetch_assoc($sq_query)) {
                                        if ($row_cust['type'] == 'Corporate' || $row_cust['type'] == 'B2B') { ?>
                                <option value="<?php echo $row_cust['customer_id']; ?>">
                                    <?php echo $row_cust['company_name'] ?></option>
                                <?php } else {
                                        ?>
                                <option value="<?php echo $row_cust['customer_id']; ?>">
                                    <?php echo $row_cust['first_name'] . ' ' . $row_cust['last_name']; ?></option>
                                <?php } ?>
                                }
                                <?php
                                    }
                                } else {
                                    $sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' order by customer_id desc");
                                    while ($row_cust = mysqli_fetch_assoc($sq_query)) {
                                        if ($row_cust['type'] == 'Corporate' || $row_cust['type'] == 'B2B') { ?>
                                <option value="<?php echo $row_cust['customer_id']; ?>">
                                    <?php echo $row_cust['company_name'] ?></option>
                                <?php } else {
                                        ?>
                                <option value="<?php echo $row_cust['customer_id']; ?>">
                                    <?php echo $row_cust['first_name'] . ' ' . $row_cust['last_name']; ?></option>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <input type="text" id="txt_contact_person_name" name="txt_contact_person_name"
                                placeholder="Contact Person Name" title="Contact Person Name" maxlength="50" disabled />
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <input type="text" id="txt_m_mobile_no" name="txt_m_mobile_no" placeholder="Mobile No."
                                title="Mobile No" disabled />
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <input type="text" id="txt_m_email_id" name="txt_m_email_id"
                                onchange="validate_email(this.id);" placeholder="Email Id" title="Email Id"
                                maxlength="50" disabled />
                        </div>
                        <div class="col-md-2 col-sm-6 col-xs-12 mg_bt_10 hidden">
                            <input type="text" id="company_name" name="company_name" class="hidden" title="Company Name"
                                disabled />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <input type="text" id="txt_m_address" name="txt_m_address" placeholder="Address"
                                title="Address" disabled />
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
                            <input type="text" id="txt_m_city" name="txt_m_city" onchange="validate_city(this.id)"
                                placeholder="City" title="City" disabled>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <!-- <select id="txt_m_state" name="txt_m_state" style="width:100%" title="State">
                    <option value="">State</option> 
                        <?php
                        $sq_country = mysqlQuery("select distinct(state_name) from state_master");
                        while ($row_country = mysqli_fetch_assoc($sq_country)) {
                        ?>
                        <option value="<?= $row_country['state_name'] ?>"><?= $row_country['state_name'] ?></option>
                        <?php } ?>
                    </select> -->
                            <input type="text" id="txt_m_state" name="txt_m_state" placeholder="State/Country Name"
                                title="State/Country Name" disabled />
                        </div>
                        <!-- <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <select name="country_name" id="country_name" title="Country Name" style="width:100%">
                                <option value="">Country</option>
                                <?php
                                $sq_country = mysqlQuery("select distinct(country_name) from country_state_list");
                                while ($row_country = mysqli_fetch_assoc($sq_country)) {
                                ?>
                                <option value="<?= $row_country['country_name'] ?>">
                                    <?= $row_country['country_name'] ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <input type="text" id="credit_amount" class="hidden" name="credit_amount"
                                placeholder="Credit Note Balance" title="Credit Note Balance" readonly>
                        </div>
                    </div>
                    <div class="row mg_tp_10">
                        <div class="col-md-4">
                            <input id="copy_details1" name="copy_details1" type="checkbox" onClick="copy_details();">
                            &nbsp;&nbsp;<label for="copy_details1">Passenger Details same as above</label>
                        </div>
                    </div>

                </div>

                <div class="panel panel-default panel-body app_panel_style feildset-panel" style="margin-top: 40px;">
                    <legend>Passenger Details</legend>
                    <div class="row text-right mg_bt_10">
                        <div class="col-xs-12">
                            <div class="col-md-6 text-left">
                                <button type="button" class="btn btn-info btn-sm ico_left pull-left"
                                    style="margin-right:10px" onclick="display_format_modal();"><i
                                        class="fa fa-download" aria-hidden="true"></i>&nbsp;&nbsp;CSV Format</button>
                                <div class="div-upload  mg_bt_20" id="div_upload_button">
                                    <div id="cust_csv_upload" class="upload-button1"><span>CSV</span></div>
                                    <span id="cust_status"></span>
                                    <ul id="files"></ul>
                                    <input type="hidden" id="txt_cust_csv_upload_dir" name="txt_cust_csv_upload_dir">
                                </div>
                            </div>
                            <button type="button" class="btn btn-excel btn-sm"
                                onClick="addRow('tbl_package_tour_member')" title="Add row"><i
                                    class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-pdf btn-sm"
                                onClick="deleteRow('tbl_package_tour_member')" title="Delete row"><i
                                    class="fa fa-trash"></i></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <table id="tbl_package_tour_member"
                                    class="table table-bordered table-hover table-striped" style="width:1504px">
                                    <tr>
                                        <td><input id="check-btn-member-1" type="checkbox" checked></td>
                                        <td><input maxlength="15" type="text" name="username" value="1"
                                                placeholder="Sr. No." disabled /></td>
                                        <td><select id="cmb_m_honorific1" name="cmb_m_honorific1" title="Honorific"
                                                onchange="changeGender(this.id);">
                                                <option value="Mr"> Mr </option>
                                                <option value="Mrs"> Mrs </option>
                                                <option value="Miss"> Miss </option>
                                                <option value="Smt"> Smt </option>
                                                <option value="Infant"> Infant </option>
                                            </select>
                                        </td>
                                        <td style="width: 129px;"><input type="text" style="width: 129px;"
                                                id="txt_m_first_name1" name="txt_m_first_name1"
                                                onchange="fname_validate(this.id);" placeholder="*First Name"
                                                title="First Name" /></td>
                                        <td><input type="text" style="width: 129px;" onchange="fname_validate(this.id);"
                                                id="txt_m_middle_name1" name="txt_m_middle_name1"
                                                placeholder="Middle Name" title="Middle Name" /></td>
                                        <td style="width: 129px;"><input type="text" style="width: 129px;"
                                                id="txt_m_last_name1" onchange="fname_validate(this.id);"
                                                name="txt_m_last_name1" placeholder="Last Name" title="Last Name" />
                                        </td>
                                        <td><select id="cmb_m_gender1" name="cmb_m_gender1" title="Select gender">
                                                <option value="Male"> M </option>
                                                <option value="Female"> F </option>
                                            </select>
                                        </td>
                                        <td><input type="text" maxlength="20" style="width: 129px;" id="m_birthdate1"
                                                name="m_birthdate1"
                                                onchange="calculate_age_member(this.id); adolescence_reflect(this.id);"
                                                value="<?= date('d-m-Y',  strtotime(' -1 day')) ?>"
                                                placeholder="*Birth Date" title="Birth date" /></td>
                                        <td style="width: 130px;"><input type="text" id="txt_m_age1" name="txt_m_age1"
                                                placeholder="*Age" onchange="validate_balance(this.id)" disabled
                                                title="Age(Y:M:D)" /></td>
                                        <td><select id="txt_m_adolescence1" name="txt_m_adolescence1" disabled
                                                title="Adolescence">
                                                <option value=""></option>
                                                <option value="Adult">A</option>
                                                <option value="Children">C</option>
                                                <option value="Infant">I</option>
                                            </select></td>
                                        <td style="width: 139px;"><input type="text" style="width: 129px;"
                                                id="txt_m_passport_no1" onchange="validate_specialChar(this.id);"
                                                name="txt_m_passport_no1" placeholder="Passport No" title="Passport No"
                                                disabled></td>
                                        <td style="width: 130px;"><input type="text" style="width: 129px;"
                                                id="txt_m_passport_issue_date1"
                                                onchange="validate_futuredate('txt_m_passport_issue_date1')"
                                                name="txt_m_passport_issue_date1" placeholder="Issue Date"
                                                title="Passport Issue Date" disabled></td>
                                        <td style="width: 132px;"><input type="text" style="width: 129px;"
                                                id="txt_m_passport_expiry_date1" onchange="validate_pastDate(this.id)"
                                                name="txt_m_passport_expiry_date1" placeholder="Expiry Date"
                                                title="Passport Expiry Date" disabled></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="panel panel-default main_block bg_light pad_8 text-center mg_bt_150" style="background-color: #fff; border: none;">
                            <button class="btn btn-sm btn-info ico_right">Next&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>
<script>
function checkExpiryDate(id) {
    var idate = document.getElementById(id).value;
    var today = new Date().getTime(),
        idate = idate.split("-");

    idate = new Date(idate[2], idate[1] - 1, idate[0]).getTime();
    if ((today - idate) < 0) {} else {
        error_msg_alert(" Expiry date should not be past date");
        $('#' + id).css({
            'border': '1px solid red'
        });
        document.getElementById(id).value = "";
        $('#' + id).focus();
        g_validate_status = false;
        return false;
    }
}

cust_csv_upload();

function cust_csv_upload() {
    var type = "passenger_list";
    var btnUpload = $('#cust_csv_upload');
    var status = $('#cust_status');
    new AjaxUpload(btnUpload, {
        action: 'tab_1/upload_passenger_csv.php',
        name: 'uploadfile',
        onSubmit: function(file, ext) {

            if (!confirm('Do you want to import this file?')) {
                return false;
            }

            if (!(ext && /^(csv)$/.test(ext))) {
                // extension is not allowed 
                status.text('Only excel sheet files are allowed');
                //return false;
            }
            status.text('Uploading...');
        },
        onComplete: function(file, response) {
            //On completion clear the status
            status.text('');
            //Add uploaded file to list
            if (response === "error") {
                alert("File is not uploaded.");
                //$('<li></li>').appendTo('#files').html('<img src="./uploads/'+file+'" alt="" /><br />'+file).addClass('success');
            } else {
                ///$('<li></li>').appendTo('#files').text(file).addClass('error');
                document.getElementById("txt_cust_csv_upload_dir").value = response;
                cust_csv_save();

            }
        }
    });

}

function cust_csv_save() {
    var tour_type = $('#tour_type').val();
    var cust_csv_dir = document.getElementById("txt_cust_csv_upload_dir").value;
    var base_url = $('#base_url').val();
    $.ajax({
        type: 'post',
        url: base_url + 'controller/package_tour/booking/passenger_csv_save.php',
        data: {
            cust_csv_dir: cust_csv_dir,
            tour_type: tour_type
        },
        success: function(result) {

            var table = document.getElementById("tbl_package_tour_member");
            var pass_arr = JSON.parse(result);
            for (var i = 0; i < pass_arr.length; i++) {
                var row = table.rows[i];
                row.cells[2].childNodes[0].value = pass_arr[i]['m_honorific'];
                $('#' + row.cells[2].childNodes[0].id).trigger('change');

                row.cells[3].childNodes[0].value = pass_arr[i]['m_first_name'];
                $('#' + row.cells[3].childNodes[0].id).trigger('change');

                row.cells[4].childNodes[0].value = pass_arr[i]['m_middle_name'];
                $('#' + row.cells[4].childNodes[0].id).trigger('change');

                row.cells[5].childNodes[0].value = pass_arr[i]['m_last_name'];
                $('#' + row.cells[5].childNodes[0].id).trigger('change');

                row.cells[6].childNodes[0].value = pass_arr[i]['m_gender'];
                $('#' + row.cells[6].childNodes[0].id).trigger('change');

                row.cells[7].childNodes[0].value = pass_arr[i]['m_birth_date1'];
                $('#' + row.cells[7].childNodes[0].id).trigger('change');

                row.cells[8].childNodes[0].value = pass_arr[i]['m_age'];
                $('#' + row.cells[8].childNodes[0].id).trigger('change');

                row.cells[9].childNodes[0].value = pass_arr[i]['m_adolescence'];
                $('#' + row.cells[9].childNodes[0].id).trigger('change');

                if (tour_type == 'International') {
                    row.cells[10].childNodes[0].value = pass_arr[i]['m_passport_no'];
                    row.cells[11].childNodes[0].value = pass_arr[i]['m_passport_issue_date1'];
                    row.cells[12].childNodes[0].value = pass_arr[i]['m_passport_expiry_date1'];

                    if (pass_arr[i]['m_passport_no'] != 'Na') {
                        $('#txt_m_passport_no' + (i + 1)).removeAttr("disabled");
                        $('#' + row.cells[10].childNodes[0].id).trigger('change');

                        $('#txt_m_passport_birth_date' + (i + 1)).removeAttr("disabled");
                        $('#' + row.cells[11].childNodes[0].id).trigger('change');

                        $('#txt_m_passport_expiry_date' + (i + 1)).removeAttr("disabled");
                        $('#' + row.cells[12].childNodes[0].id).trigger('change');
                    }
                    passport_fields_toggle(tour_type);
                }

                $('#' + row.cells[7].childNodes[0].getAttribute('id')).trigger('change');
                if (i != pass_arr.length - 1) {
                    if (table.rows[i + 1] == undefined) {
                        addRow('tbl_package_tour_member');
                    }
                }

            }
        }
    });
}

function passenger_list_reflect() {
    $.post('tab_1/passenger_list_reflect.php', {}, function(data) {
        $('#passenger_list').html(data);
    });
}
passenger_list_reflect();

function display_format_modal() {
    var base_url = $('#base_url').val();
    window.location = base_url + "images/csv_format/passenger_list.csv";
}
</script>
<?php include "guideline_modal.php"; ?>
<script src="../js/tab_1.js"></script>