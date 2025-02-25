<form id="frm_tab4">
    <div class="app_panel">

        <div class="container">
            <div class="row">
                <div class="col-md-12 app_accordion">
                    <div class="panel-group main_block" id="accordion" role="tablist" aria-multiselectable="true">

                        <!-- Accordian-1 Start --><!-- Group Costing -->
                        <div class="accordion_content main_block mg_bt_20">

                            <div class="panel panel-default main_block">
                                <div class="panel-heading main_block" role="tab" id="heading1">
                                    <div class="Normal main_block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="true" aria-controls="collapse1" id="collapsed1">
                                        <div class="col-md-12"><span>Group Costing</span></div>
                                    </div>
                                </div>
                                <div id="collapse1" class="panel-collapse collapse in main_block" role="tabpanel" aria-labelledby="heading1">
                                    <div class="panel-body">
                                        <div class="row mg_tp_10">
                                            <div class="col-xs-12">
                                                <h3 class="editor_title">Land Cost</h3>
                                                <div class="panel panel-default panel-body app_panel_style">
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <div class="table-responsive">
                                                                <table id="tbl_package_tour_quotation_dynamic_costing" name="tbl_package_tour_quotation_dynamic_costing" class="table border_0 no-marg">
                                                                    <tr>
                                                                        <td class="header_btn" style="display:none;"><input class="css-checkbox" id="chk_costing1" type="checkbox" checked disabled><span class="css-label" for="chk_costing1"> </span></td>
                                                                        <td class="header_btn hidden" style="display:none;">
                                                                            <small>&nbsp;</small><input type="text" maxlength="15" value="1" name="username" placeholder="Sr. No." class="form-control" disabled />
                                                                        </td>
                                                                        <td class="header_btn"><small>&nbsp;</small><input type="text" id="package_type-" name="package_type-" placeholder="Package Type" title="Package Type" style="width:150px" readonly><span>Package Type</span></td>
                                                                        <td class="header_btn"><small>&nbsp;</small><input type="number" id="tour_cost-" name="tour_cost-" placeholder="Hotel Cost" title="Hotel Cost" value="0" onchange="quotation_cost_calculate(this.id);" style="width:100px"><span>Hotel Cost</span></td>
                                                                        <td class="header_btn"><small>&nbsp;</small><input type="number" id="transport_cost1-" name="transport_cost1-" placeholder="Transport Cost" title="Transport Cost" onchange="quotation_cost_calculate(this.id);" style="width:100px" value="0"><span>Transport Cost</span></td>
                                                                        <?php
                                                                        $add_class1 = '';
                                                                        if ($role == 'B2b') {
                                                                            $add_class1 = "hidden";
                                                                        } else {
                                                                            $add_class1 = "number";
                                                                        } ?>
                                                                        <td class="header_btn"><small>&nbsp;</small><input type="number" id="excursion_cost-" name="excursion_cost-" onchange="quotation_cost_calculate(this.id);;" placeholder="Activity Cost" title="Activity Cost" style="width:100px" value="0"><span>Activity Cost</span></td>

                                                                        <td class="header_btn"><small id="basic_show-" style="color:#000000">&nbsp;</small><input type="<?= $add_class1 ?>" id="basic_amount-" name="basic_amount-" onchange="get_business(this.id,'true');;" placeholder="Basic Amount" title="Basic Amount" style="width:100px" readonly><span>Basic Amount</span></td>
                                                                        <td class="header_btn"><small id="service_show-" style="color:#000000">&nbsp;</small><input type="<?= $add_class1 ?>" id="service_charge-" name="service_charge-" onchange="get_business(this.id,'false');quotation_cost_calculate(this.id); " value="0.00" placeholder="Service charge" title="Service charge" style="width:100px"><span>Service charge</span></td>
                                                                        <td class="header_btn"><small>&nbsp;</small><select title="Discount In" id="discount_in-" name="discount_in-" class="form-control" onchange="get_business(this.id,'true');quotation_cost_calculate(this.id);" style="width: 150px!important;">
                                                                                <option value="Percentage">Percentage</option>
                                                                                <option value="Flat">Flat</option>
                                                                            </select> <span>Discount In</span></td>
                                                                        <td class="header_btn"><small>&nbsp;</small><input type="<?= $add_class1 ?>" id="discount_amt-" name="discount_amt-" onchange="get_business(this.id,'true');quotation_cost_calculate(this.id); " placeholder="Discount" title="Discount" style="width:100px"><span>Discount</span></td>
                                                                        <td class="header_btn"><small id="tax_apply_show-" style="color:#000000">&nbsp;</small><select title="Tax Apply On" id="tax_apply_on-" name="tax_apply_on-" class="form-control" onchange="get_business(this.id,'true');" style="width: 150px!important;">
                                                                                <option value="">*Tax Apply On</option>
                                                                                <option value="1">Basic Amount</option>
                                                                                <option value="2">Service Charge</option>
                                                                                <option value="3">Total</option>
                                                                            </select><span>Tax Apply On</span></td>
                                                                        <td class="header_btn"><small id="tax_show-" style="color:#000000">&nbsp;</small><select title="Select Tax" id="tax_value-" name="tax_value-" class="form-control" onchange="get_business(this.id,'true');" style="width: 180px!important;">
                                                                                <option value="">*Select Tax</option>
                                                                                <?php get_tax_dropdown('Income') ?>
                                                                            </select><span>Select Tax</span></td>

                                                                        <td class="header_btn"><small>&nbsp;</small><input type="text" id="service_tax_subtotal-" name="service_tax_subtotal-" readonly placeholder="Tax Amount" title="Tax Amount" style="width:180px"><span>Tax Amount</span></td>

                                                                        <td class="header_btn"><small>&nbsp;</small><input type="text" id="total_tour_cost-" class="amount_feild_highlight text-right" name="total_tour_cost-" placeholder="Total Cost" title="Total Cost" style="width: 100px;" readonly><span>Total Cost</span></td>

                                                                        <td class="header_btn hidden" style="display:none;">
                                                                            <small>&nbsp;</small><input type="text" id="package_name1" name="package_name1" placeholder="Package Name" title="Package Name" style="width: 160px;display:none;" readonly>
                                                                        </td>

                                                                        <td class="header_btn hidden" style="display:none;">
                                                                            <small>&nbsp;</small><input type="text" id="package_id1" name="package_id1" placeholder="Package ID" title="Package ID" style="display:none;">
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mg_tp_20">
                                            <div class="col-xs-12">
                                                <h3 class="editor_title">Travel Cost</h3>
                                                <div class="panel panel-default panel-body app_panel_style">
                                                    <!-- Other costs -->
                                                    <div class="row">
                                                        <div class="col-md-4 header_btn mg_bt_10">
                                                            <span>Flight Cost</span>
                                                            <input type="text" id="flight_cost" name="flight_cost" placeholder="Flight Cost" title="Flight Cost" onchange="">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Train Cost</span>
                                                            <input type="text" id="train_cost" name="train_cost" placeholder="Train Cost" title="Train Cost" onchange="">
                                                        </div>
                                                        <div class="col-md-4 header_btn mg_bt_10">
                                                            <span>Cruise Cost</span>
                                                            <input type="text" id="cruise_cost" name="cruise_cost" placeholder="Cruise Cost" title="Cruise Cost" onchange="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Accordian-1 End-->
                        <!-- Accordian-2 Start --><!-- Per person Costing -->
                        <div class="accordion_content main_block">

                            <div class="panel panel-default main_block">
                                <div class="panel-heading main_block" role="tab" id="heading2">
                                    <div class="Normal main_block" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="true" aria-controls="collapse2" id="collapsed2">
                                        <div class="col-md-12"><span>Per Person Costing</span></div>
                                    </div>
                                </div>
                                <div id="collapse2" class="panel-collapse collapse main_block" role="tabpanel" aria-labelledby="heading2">
                                    <div class="panel-body">
                                        <div class="row mg_tp_10">
                                            <div class="col-xs-12">
                                                <h3 class="editor_title">Land Cost</h3>
                                                <div class="panel panel-default panel-body app_panel_style">
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <div class="table-responsive">
                                                                <table id="tbl_adult_child_head" name="tbl_adult_child_head" class="table border_0 no-marg">
                                                                    <tr>
                                                                        <td class="col-md-3"><span>Package Type</span></th>
                                                                        <td><span>Adult_Cost</span></td>
                                                                        <td><span>Child_with_bed</span></td>
                                                                        <td><span>Child_w/o_bed</span></td>
                                                                        <td><span>Infant_Cost</span></td>
                                                                    </tr>
                                                                </table>
                                                                <div>
                                                                </div>
                                                            </div>
                                                            <!-- Adult & child cost -->
                                                            <div class="row">
                                                                <div class="col-xs-12">
                                                                    <div class="table-responsive">
                                                                        <table id="tbl_package_tour_quotation_adult_child" name="tbl_package_tour_quotation_adult_child" class="table border_0 no-marg">
                                                                            <tr>
                                                                                <td class="col-md-3"><input type="text" id="ppackage_type1" name="ppackage_type1" placeholder="Package Type" title="Package Type" readonly></td>
                                                                                <td><input type="text" onchange=";" id="adult_cost" name="adult_cost" placeholder="Adult Cost" title="Adult Cost"></td>
                                                                                <td><input type="text" onchange=";" id="child_with" name="child_with" placeholder="Child with Bed Cost" title="Child with Bed Cost"></td>
                                                                                <td><input type="text" onchange=";" id="child_without" name="child_without" placeholder="Child w/o Bed Cost" title="Child w/o Bed Cost">
                                                                                </td>
                                                                                <td><input type="text" onchange=";" id="infant_cost" name="infant_cost" placeholder="Infant Cost" title="Infant Cost"></td>
                                                                                <td><input type="hidden" id="pacakge_id2" name="pacakge_id2" placeholder="Package Id" title="Package Id"></td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mg_tp_20">
                                            <div class="col-xs-12">
                                                <h3 class="editor_title">Travel Cost</h3>
                                                <div class="panel panel-default panel-body app_panel_style">
                                                    <!-- Other costs -->
                                                    <div class="row">
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Flight Adult Cost</span>
                                                            <input type="number" id="flight_acost" name="flight_acost" placeholder="Flight Adult Cost" title="Flight Adult Cost">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Flight Child Cost</span>
                                                            <input type="number" id="flight_ccost" name="flight_ccost" placeholder="Flight Child Cost" title="Flight Child Cost">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Flight Infant Cost</span>
                                                            <input type="number" id="flight_icost" name="flight_icost" placeholder="Flight Infant Cost" title="Flight Infant Cost">
                                                        </div>
                                                    </div>
                                                    <div class="row mg_tp_10">
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Train Adult Cost</span>
                                                            <input type="number" id="train_acost" name="train_acost" placeholder="Train Adult Cost" title="Train Adult Cost">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Train Child Cost</span>
                                                            <input type="number" id="train_ccost" name="train_ccost" placeholder="Train Child Cost" title="Train Child Cost">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Train Infant Cost</span>
                                                            <input type="number" id="train_icost" name="train_icost" placeholder="Train Infant Cost" title="Train Infant Cost">
                                                        </div>
                                                    </div>
                                                    <div class="row mg_tp_10">
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Cruise Adult Cost</span>
                                                            <input type="number" id="cruise_acost" name="cruise_acost" placeholder="Cruise Adult Cost" title="Cruise Adult Cost">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Cruise Child Cost</span>
                                                            <input type="number" id="cruise_ccost" name="cruise_ccost" placeholder="Cruise Child Cost" title="Cruise Child Cost">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Cruise Infant Cost</span>
                                                            <input type="number" id="cruise_icost" name="cruise_icost" placeholder="Cruise Infant Cost" title="Cruise Infant Cost">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Accordian-2 End-->

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <h3 class="editor_title">Other Costing</h3>
                    <div class="panel panel-default panel-body app_panel_style">
                        <div class="col-md-3 header_btn mg_bt_10">
                            <span>Visa Cost</span>
                            <input type="text" id="visa_cost" name="visa_cost" placeholder="Visa Cost" title="Visa Cost" onchange="">
                        </div>
                        <div class="col-md-3 header_btn mg_bt_10">
                            <span>Guide Cost</span>
                            <input type="text" id="guide_cost" name="guide_cost" placeholder="Guide Cost" title="Guide Cost" onchange="">
                        </div>
                        <div class="col-md-3 header_btn mg_bt_10">
                            <span>Miscellaneous Cost</span>
                            <input type="text" id="misc_cost" name="misc_cost" placeholder="Miscellaneous Cost" title="Miscellaneous Cost" onchange="">
                        </div>
                        <div class="col-md-3 header_btn mg_bt_10">
                            <span>Miscellaneous Description</span>
                            <textarea id="other_desc" name="other_desc" placeholder="Miscellaneous Description" title="Miscellaneous Description"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">

                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-sm-12 mg_bt_10 hidden">
                            <input type="hidden" id="discount" name="discount" placeholder="Discount" title="Discount" />
                        </div>
                        <div class="col-md-3 col-sm-6 col-sm-12 mg_bt_10">
                            <select name="costing_type" id="costing_type" title="Select Costing type" class="form-control">
                                <option value="1">Group Costing</option>
                                <option value="2">Per Person costing</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <select name="currency_code" id="currency_code" title="Currency" style="width:100%" data-toggle="tooltip" required>
                                <?php
                                $sq_app_setting = mysqli_fetch_assoc(mysqlQuery("select currency from app_settings"));
                                if ($sq_app_setting['currency'] != '0') {

                                    $sq_currencyd = mysqli_fetch_assoc(mysqlQuery("SELECT `id`,`currency_code` FROM `currency_name_master` WHERE id=" . $sq_app_setting['currency']));
                                ?>
                                    <option value="<?= $sq_currencyd['id'] ?>"><?= $sq_currencyd['currency_code'] ?>
                                    </option>
                                <?php } ?>
                                <option value=''>*Select Currency</option>
                                <?php
                                $sq_currency = mysqlQuery("select * from currency_name_master order by currency_code");
                                while ($row_currency = mysqli_fetch_assoc($sq_currency)) {
                                ?>
                                    <option value="<?= $row_currency['id'] ?>"><?= $row_currency['currency_code'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="div-upload">
                                <div id="price_structure" class="upload-button1"><span>Price Structure</span></div>
                                <span id="photo_status"></span>
                                <ul id="files"></ul>
                                <input type="hidden" id="upload_url" name="upload_url">
                            </div>
	                        <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note : Only Excel or Word files are allowed."><i class="fa fa-question-circle"></i></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-sm-12 mg_bt_10"></div>
                        <div class="col-md-6 col-sm-12">
	                        <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note : Group Costing or Per person costing to display on quotation."><i class="fa fa-question-circle"></i></button>
                        </div>
                    </div>
                    <div class="row mg_tp_20 text-center mg_bt_30">
                        <div class="col-md-12">
                            <button class="btn btn-info btn-sm ico_left" type="button" onclick="switch_to_tab3()"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Previous</button>
                            &nbsp;&nbsp;
                            <button class="btn btn-sm btn-success" id="btn_quotation_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="login_id" name="login_id" value="<?= $login_id ?>">
            </div>
        </div>
    </div>
</form>
<?= end_panel() ?>

<script>
    $('#currency_code').select2();

    upload_price_struct();

    function upload_price_struct() {
        var btnUpload = $('#price_structure');
        $(btnUpload).find('span').text('Price Structure');

        new AjaxUpload(btnUpload, {
            action: '../upload_price_structure.php',
            name: 'uploadfile',
            onSubmit: function(file, ext) {
                if (!(ext && /^(xlsx|docx|xls)$/.test(ext))) {
                    error_msg_alert('Only Excel or word files are allowed');
                    return false;
                }
                $(btnUpload).find('span').text('Uploading...');
            },
            onComplete: function(file, response) {
                if (response === "error") {
                    error_msg_alert("File is not uploaded.");
                    $(btnUpload).find('span').text('Upload');
                } else {
                    $(btnUpload).find('span').text('Uploaded');
                    success_msg_alert('File is uploaded!');
                    $("#upload_url").val(response);
                }
            }
        });
    }
    function get_business(id, flag, change = false) {

        var offset = id.split('-');
        get_auto_values('quotation_date', 'basic_amount-' + offset[1], 'payment_mode', 'service_charge-' + offset[1],
            'markup', 'save', flag, 'markup', 'discount_amt-'+ offset[1], offset[1], change);
    }

    function switch_to_tab3() {
        $('#tab4_head').removeClass('active');
        $('#tab3_head').addClass('active');
        $('.bk_tab').removeClass('active');
        $('#tab3').addClass('active');
        $('html, body').animate({
            scrollTop: $('.bk_tab_head').offset().top
        }, 200);
    }

    $('#frm_tab4').validate({

        rules: {



        },

        submitHandler: function(form, e) {
            e.preventDefault();
            $('#btn_quotation_save').prop('disabled', true);
            var login_id = $("#login_id").val();

            var emp_id = $("#emp_id").val();

            var enquiry_id = $("#enquiry_id").val();

            var tour_name = $('#tour_name').val();

            var from_date = $('#from_date').val();

            var to_date = $('#to_date').val();

            var total_days = $('#total_days').val();

            var customer_name = $('#customer_name').val();

            var user_id = 0;
            if($('#user_dropdown').html() != ''){
                user_id = $('#user_id').val();
            }

            var email_id = $('#email_id').val();
            var mobile_no = $('#mobile_no').val();
			var country_code = $('#country_code').val();

            var total_adult = $('#total_adult').val();

            var total_infant = $('#total_infant').val();

            var total_passangers = $('#total_passangers').val();

            var children_without_bed = $('#children_without_bed').val();

            var children_with_bed = $('#children_with_bed').val();

            var quotation_date = $('#quotation_date').val();

            var booking_type = $('#booking_type').val();

            var train_cost = $('#train_cost').val();

            var flight_cost = $('#flight_cost').val();

            var cruise_cost = $('#cruise_cost').val();

            var visa_cost = $('#visa_cost').val();
            var branch_admin_id = $('#branch_admin_id1').val();
            var financial_year_id = $('#financial_year_id').val();
            //Per person travel costing
            var flight_acost = $('#flight_acost').val();
            var flight_ccost = $('#flight_ccost').val();
            var flight_icost = $('#flight_icost').val();
            var train_acost = $('#train_acost').val();
            var train_ccost = $('#train_ccost').val();
            var train_icost = $('#train_icost').val();
            var cruise_acost = $('#cruise_acost').val();
            var cruise_ccost = $('#cruise_ccost').val();
            var cruise_icost = $('#cruise_icost').val();
            var other_desc = $('#other_desc').val();

            var guide_cost = $('#guide_cost').val();
            var misc_cost = $('#misc_cost').val();
            var costing_type = $('#costing_type').val();

            //Train Information

            var train_from_location_arr = [];

            var train_to_location_arr = [];

            var train_class_arr = [];

            var train_arrival_date_arr = [];

            var train_departure_date_arr = [];





            var table = document.getElementById("tbl_package_tour_quotation_dynamic_train");

            var rowCount = table.rows.length;



            for (var i = 0; i < rowCount; i++)

            {

                var row = table.rows[i];



                if (row.cells[0].childNodes[0].checked)

                {

                    var train_from_location1 = row.cells[2].childNodes[0].value;

                    var train_to_location1 = row.cells[3].childNodes[0].value;

                    var train_class = row.cells[4].childNodes[0].value;

                    var train_departure_date = row.cells[5].childNodes[0].value;

                    var train_arrival_date = row.cells[6].childNodes[0].value;







                    if (train_from_location1 == "")

                    {

                        error_msg_alert('Enter train from location in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;

                    }



                    if (train_to_location1 == "")

                    {

                        error_msg_alert('Enter train to location in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;

                    }
                    train_from_location_arr.push(train_from_location1);

                    train_to_location_arr.push(train_to_location1);

                    train_class_arr.push(train_class);

                    train_arrival_date_arr.push(train_arrival_date);

                    train_departure_date_arr.push(train_departure_date);



                }

            }



            //Plane Information  
            var plane_from_city_arr = [];
            var plane_to_city_arr = [];
            var plane_from_location_arr = [];

            var plane_to_location_arr = [];

            var airline_name_arr = [];

            var plane_class_arr = [];

            var arraval_arr = [];

            var dapart_arr = [];



            var table = document.getElementById("tbl_package_tour_quotation_dynamic_plane");

            var rowCount = table.rows.length;



            for (var i = 0; i < rowCount; i++)

            {

                var row = table.rows[i];



                if (row.cells[0].childNodes[0].checked)

                {

                    var plane_from_location1 = row.cells[2].childNodes[0].value;
                    var plane_to_location1 = row.cells[3].childNodes[0].value;
                    var airline_name = row.cells[4].childNodes[0].value;
                    var plane_class = row.cells[5].childNodes[0].value;
                    var dapart1 = row.cells[6].childNodes[0].value;
                    var arraval1 = row.cells[7].childNodes[0].value;
                    var plane_from_city = row.cells[8].childNodes[0].value;
                    var plane_to_city = row.cells[9].childNodes[0].value;



                    if (plane_from_location1 == "")

                    {

                        error_msg_alert('Enter from sector in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);

                        return false;

                    }



                    if (plane_to_location1 == "")

                    {

                        error_msg_alert('Enter to sector in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);

                        return false;

                    }




                    if (arraval1 == "")

                    {

                        error_msg_alert('Arrival Date time is required in row:' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);

                        return false;

                    }

                    if (dapart1 == "")

                    {

                        error_msg_alert("Daparture Date time is required in row:" + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);

                        return false;

                    }


                    plane_from_city_arr.push(plane_from_city);
                    plane_to_city_arr.push(plane_to_city);
                    plane_from_location_arr.push(plane_from_location1);

                    plane_to_location_arr.push(plane_to_location1);

                    airline_name_arr.push(airline_name);

                    plane_class_arr.push(plane_class);

                    arraval_arr.push(arraval1);

                    dapart_arr.push(dapart1);



                }

            }

            //Cruise Information
            var cruise_departure_date_arr = [];
            var cruise_arrival_date_arr = [];
            var route_arr = [];
            var cabin_arr = [];
            var sharing_arr = [];

            var table = document.getElementById("tbl_dynamic_cruise_quotation");
            var rowCount = table.rows.length;

            for (var i = 0; i < rowCount; i++) {
                var row = table.rows[i];
                if (row.cells[0].childNodes[0].checked) {
                    var cruise_from_date = row.cells[2].childNodes[0].value;
                    var cruise_to_date = row.cells[3].childNodes[0].value;
                    var route = row.cells[4].childNodes[0].value;
                    var cabin = row.cells[5].childNodes[0].value;
                    var sharing = row.cells[6].childNodes[0].value;

                    if (cruise_from_date == "") {
                        error_msg_alert('Enter cruise departure datetime in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }

                    if (cruise_to_date == "") {
                        error_msg_alert('Enter cruise departure datetime  in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }
                    if (route == "") {
                        error_msg_alert('Enter route in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }
                    if (cabin == "") {
                        error_msg_alert('Enter cabin in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }
                    cruise_departure_date_arr.push(cruise_from_date);
                    cruise_arrival_date_arr.push(cruise_to_date);
                    route_arr.push(route);
                    cabin_arr.push(cabin);
                    sharing_arr.push(sharing);

                }
            }

            //Hotel Information
            var package_type_arr = [];
            var city_name_arr = [];
            var hotel_name_arr = [];
            var hotel_cat_arr = [];
            var check_in_arr = [];
            var check_out_arr = [];
            var hotel_stay_days_arr = [];
            var hotel_type_arr = [];
            var package_name_arr = [];
            var total_rooms_arr = [];
            var hotel_cost_arr = [];
            var extra_bed_cost_arr = [];
            var extra_bed_arr = [];
            var hotel_meal_plan_arr = [];

            var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
            var rowCount = table.rows.length;
            for (var i = 0; i < rowCount; i++) {

                var row = table.rows[i];
                if (row.cells[0].childNodes[0].checked) {

                    var package_type = row.cells[2].childNodes[0].value;
                    var city_name = row.cells[3].childNodes[0].value;
                    var hotel_id = row.cells[4].childNodes[0].value;
                    var hotel_cat = row.cells[5].childNodes[0].value;
                    var check_in = row.cells[6].childNodes[0].value;
                    var check_out = row.cells[7].childNodes[0].value;
                    var hotel_type = row.cells[8].childNodes[0].value;
                    var hotel_stay_days1 = row.cells[9].childNodes[0].value;
                    var total_rooms = row.cells[10].childNodes[0].value;
                    var extra_bed = row.cells[11].childNodes[0].value;
                    var package_name1 = row.cells[12].childNodes[0].value;
                    var hotel_cost = row.cells[13].childNodes[0].value;
                    var package_id1 = row.cells[14].childNodes[0].value;
                    var extra_bed_cost = row.cells[15].childNodes[0].value;
                    var meal_plan = row.cells[16].childNodes[0].value;

                    if (city_name == "") {
                        error_msg_alert('Select hotel city in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }
                    if (hotel_id == "") {
                        error_msg_alert('Enter hotel in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }

                    if (hotel_stay_days1 == "") {
                        error_msg_alert('Enter hotel total days in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }

                    package_type_arr.push(package_type);
                    city_name_arr.push(city_name);
                    hotel_name_arr.push(hotel_id);
                    hotel_cat_arr.push(hotel_cat);
                    check_in_arr.push(check_in);
                    check_out_arr.push(check_out);
                    hotel_stay_days_arr.push(hotel_stay_days1);
                    hotel_type_arr.push(hotel_type);
                    total_rooms_arr.push(total_rooms);
                    extra_bed_arr.push(extra_bed);
                    package_name_arr.push(package_name1);
                    hotel_cost_arr.push(hotel_cost);
                    extra_bed_cost_arr.push(extra_bed_cost);
                    hotel_meal_plan_arr.push(meal_plan);
                }
            }

            //Transport Information
            var vehicle_name_arr = [];
            var start_date_arr = [];
            var pickup_arr = [];
            var drop_arr = [];
            var vehicle_count_arr = [];
            var transport_cost_arr1 = [];
            var package_name_arr1 = [];
            var pickup_type_arr = [];
            var drop_type_arr = [];
            var end_date_arr = [];
            var service_duration_arr = [];
            var table = document.getElementById("tbl_package_tour_quotation_dynamic_transport");

            var rowCount = table.rows.length;
            for (var i = 0; i < rowCount; i++) {

                var row = table.rows[i];
                if (row.cells[0].childNodes[0].checked) {
                    var transport_id = row.cells[2].childNodes[0].value;
                    var travel_date = row.cells[3].childNodes[0].value;
                    var end_date = row.cells[4].childNodes[0].value;
                    var service_duration = row.cells[7].childNodes[0].value;
                    var vehicle_count = row.cells[8].childNodes[0].value;
                    var transport_cost = row.cells[9].childNodes[0].value;
                    var pname = row.cells[10].childNodes[0].value;
                    var pid = row.cells[11].childNodes[0].value;

                    var pickup = row.cells[5].childNodes[0].value;
                    var drop = row.cells[6].childNodes[0].value;
                    var pickup_type = $("option:selected", $("#" + row.cells[5].childNodes[0].id)).parent()
                        .attr('value');
                    var drop_type = $("option:selected", $("#" + row.cells[6].childNodes[0].id)).parent().attr(
                        'value');

                    if (transport_id == "") {
                        error_msg_alert('Select Transport Vehicle in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        $('.accordion_content').removeClass("indicator");
                        $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                            '.accordion_content').addClass("indicator");
                        return false;
                    }
                    if (travel_date == "") {
                        error_msg_alert('Enter Travel date in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        $('.accordion_content').removeClass("indicator");
                        $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                            '.accordion_content').addClass("indicator");
                        return false;
                    }
                    if (pickup == "") {
                        error_msg_alert('Select pickup location in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        $('.accordion_content').removeClass("indicator");
                        $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                            '.accordion_content').addClass("indicator");
                        return false;
                    }
                    if (drop == "") {
                        error_msg_alert('Select drop location in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        $('.accordion_content').removeClass("indicator");
                        $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                            '.accordion_content').addClass("indicator");
                        return false;
                    }
                    vehicle_name_arr.push(transport_id);
                    start_date_arr.push(travel_date);
                    end_date_arr.push(end_date);
                    pickup_arr.push(pickup);
                    drop_arr.push(drop);
                    vehicle_count_arr.push(vehicle_count);
                    transport_cost_arr1.push(transport_cost);
                    package_name_arr1.push(pname);
                    pickup_type_arr.push(pickup_type);
                    drop_type_arr.push(drop_type);
                    service_duration_arr.push(service_duration);
                }
            }

            var table = document.getElementById("tbl_package_tour_quotation_dynamic_excursion");
            var rowCount = table.rows.length;
            var exc_date_arr_e = [];
            var city_name_arr_e = [];
            var excursion_name_arr = [];
            var transfer_option_arr = [];
            var adult_arr = [];
            var chwb_arr = [];
            var chwob_arr = [];
            var infant_arr = [];
            var excursion_amt_arr = [];
            var vehicles_arr = [];

            for (var e = 0; e < rowCount; e++) {
                var row = table.rows[e];
                if (row.cells[0].childNodes[0].checked) {
                    var exc_date = row.cells[2].childNodes[0].value;
                    var city_name = row.cells[3].childNodes[0].value;
                    var excursion_name = row.cells[4].childNodes[0].value;
                    var transfer_option = row.cells[5].childNodes[0].value;
                    var adults = row.cells[6].childNodes[0].value;
                    var chwb = row.cells[7].childNodes[0].value;
                    var chwob = row.cells[8].childNodes[0].value;
                    var infant = row.cells[9].childNodes[0].value;
                    var excursion_amount = row.cells[10].childNodes[0].value;
                    var vehicles = row.cells[15].childNodes[0].value;

                    if (exc_date == "") {
                        error_msg_alert('Select Activity date in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }
                    if (city_name == "") {
                        error_msg_alert('Select Activity city in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }
                    if (excursion_name == "") {
                        error_msg_alert('Select Activity name in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }
                    if (transfer_option == "") {
                        error_msg_alert('Select Transfer option in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }
                    exc_date_arr_e.push(exc_date);
                    city_name_arr_e.push(city_name);
                    excursion_name_arr.push(excursion_name);
                    transfer_option_arr.push(transfer_option);
                    excursion_amt_arr.push(excursion_amount);
                    adult_arr.push(adults);
                    chwb_arr.push(chwb);
                    chwob_arr.push(chwob);
                    infant_arr.push(infant);
                    vehicles_arr.push(vehicles);
                }
            }

            //Costing Information  
            var tour_cost_arr = [];
            var transport_cost_arr = [];
            var excursion_cost_arr = [];
            var basic_amount_arr = [];
            var service_charge_arr = [];
            var service_tax_subtotal_arr = [];
            var total_tour_cost_arr = [];
            var package_name_arr2 = [];
            var package_type_c_arr = [];
            var discount_in_arr = [];
            var discount_arr = [];
            
            var table = document.getElementById("tbl_package_tour_quotation_dynamic_costing");
            var rowCount = table.rows.length;

            for (var i = 0; i < rowCount; i++) {
                var row = table.rows[i];
                if (row.cells[0].childNodes[0].checked) {
                    var package_type_c = row.cells[2].childNodes[1].value;
                    var tour_cost = row.cells[3].childNodes[1].value;
                    var transport_cost = row.cells[4].childNodes[1].value;
                    var excursion_cost = row.cells[5].childNodes[1].value;
                    var basic_cost = row.cells[6].childNodes[1].value;
                    var service_tax = row.cells[7].childNodes[1].value;
                    var discount_in = row.cells[8].childNodes[1].value;
                    var discount = row.cells[9].childNodes[1].value;
                    var tax_value = row.cells[11].childNodes[1].value;
                    var service_tax_subtotal = row.cells[12].childNodes[1].value;
                    var total_tour_cost = row.cells[13].childNodes[1].value;
                    var package_name3 = row.cells[14].childNodes[1].value;

                    if (tour_cost == "") {
                        error_msg_alert('Select Hotel cost in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }
                    if (tax_apply_on == "") {
                        error_msg_alert('Select Tax Apply On in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }
                    if (tax_value == "") {
                        error_msg_alert('Select Tax in row' + (i + 1));
                        $('#btn_quotation_save').prop('disabled', false);
                        return false;
                    }
                    tour_cost_arr.push(tour_cost);
                    transport_cost_arr.push(transport_cost);
                    excursion_cost_arr.push(excursion_cost);
                    basic_amount_arr.push(basic_cost);
                    service_charge_arr.push(service_tax);
                    discount_in_arr.push(discount_in);
                    discount_arr.push(discount);
                    service_tax_subtotal_arr.push(service_tax_subtotal);
                    total_tour_cost_arr.push(total_tour_cost);
                    package_name_arr2.push(package_name3);
                    package_type_c_arr.push(package_type_c);
                }
            }
            //BSM value Costing Information  
            var bsmValues = [];
            var table = document.getElementById("tbl_package_tour_quotation_dynamic_costing");
            var rowCount = table.rows.length;

            for (var i = 0; i < rowCount; i++) {
                var row = table.rows[i];
                var bsmvaluesEach = [];

                if (row.cells[0].childNodes[0].checked) {
                    var basic_show = $(row.cells[6].childNodes[2]).find('span').text();
                    var service_show = $(row.cells[7].childNodes[2]).find('span').text();
                    var tax_apply_on = row.cells[10].childNodes[1].value;
                    var tax_value = row.cells[11].childNodes[1].value;

                    bsmvaluesEach.push({
                        "basic": 'basic',
                        "service": 'service',
                        'tax_apply_on': tax_apply_on,
                        'tax_value': tax_value
                    });
                    bsmValues.push(bsmvaluesEach);
                }
            }
            //Adult & Child Costing Information  
            var c_package_id_arr = [];
            var adult_cost_arr = [];
            var infant_cost_arr = [];
            var child_with_arr = [];
            var child_without_arr = [];

            var table = document.getElementById("tbl_package_tour_quotation_adult_child");
            var rowCount = table.rows.length;
            for (var i = 0; i < rowCount; i++) {
                var row = table.rows[i];
                var adult_cost = row.cells[1].childNodes[0].value;
                var child_with = row.cells[2].childNodes[0].value;
                var child_without = row.cells[3].childNodes[0].value;
                var infant_cost = row.cells[4].childNodes[0].value;
                var c_package_id = row.cells[5].childNodes[0].value;

                adult_cost_arr.push(adult_cost);
                infant_cost_arr.push(infant_cost);
                child_with_arr.push(child_with);
                child_without_arr.push(child_without);
                c_package_id_arr.push(c_package_id);
            }

            var package_id_arr1 = [];
            var incl_arr = [];
            var excl_arr = [];

            $('input[name="custom_package"]:checked').each(function() {

                package_id_arr1.push($(this).val());
                var package_id = $(this).val();
                //Incl & Excl
                var table = document.getElementById("dynamic_table_incl" + package_id);
                var rowCount = table.rows.length;
                for (var i = 0; i < rowCount; i++) {
                    var row = table.rows[i];
                    var inclusion = $('#inclusions' + package_id).val();
                    var exclusion = $('#exclusions' + package_id).val();

                    incl_arr.push(inclusion);
                    excl_arr.push(exclusion);
                }
            });

            var attraction_arr = [];
            var program_arr = [];
            var stay_arr = [];
            var meal_plan_arr = [];
            var package_p_id_arr = [];

            for (var j = 0; j < package_id_arr1.length; j++) {
                var table = document.getElementById("dynamic_table_list_p_" + package_id_arr1[j]);
                var rowCount = table.rows.length;
                for (var i = 0; i < rowCount; i++) {
                    var row = table.rows[i];
                    if (row.cells[0].childNodes[0].checked) {
                        var attraction = row.cells[2].childNodes[0].value;
                        var program = row.cells[3].childNodes[0].value;
                        var stay = row.cells[4].childNodes[0].value;
                        var meal_plan = row.cells[5].childNodes[0].value;
                        var package_id1 = row.cells[7].childNodes[0].value;

                        if (program == "") {
                            error_msg_alert('Daywise program is mandatory in row' + (i + 1));
                            $('#btn_quotation_save').prop('disabled', false);
                            return false;
                        }
                        attraction_arr.push(attraction);
                        program_arr.push(program);
                        stay_arr.push(stay);
                        meal_plan_arr.push(meal_plan);
                        package_p_id_arr.push(package_id1);
                    }
                }
            }

            var price_str_url = $("#upload_url").val();
            var pckg_daywise_url = $('#pckg_daywise_url').val();
            var currency_code = $('#currency_code').val();
            var discount = $('#discount').val();
            var base_url = $('#base_url').val();

            $("#vi_confirm_box").vi_confirm_box({
                callback: function(result) {
                    if (result == "yes") {
                        $('#btn_quotation_save').button('loading');
                        $('#btn_quotation_save').prop('disabled', false);
                        $.ajax({

                            type: 'post',

                            url: base_url +
                                'controller/package_tour/quotation/quotation_save.php',

                            data: {
                                enquiry_id: enquiry_id,
                                tour_name: tour_name,
                                from_date: from_date,
                                to_date: to_date,
                                total_days: total_days,
                                customer_name: customer_name,user_id:user_id,
                                email_id: email_id,
                                mobile_no: mobile_no,
                                country_code:country_code,
                                total_adult: total_adult,
                                total_infant: total_infant,
                                total_passangers: total_passangers,
                                children_without_bed: children_without_bed,
                                children_with_bed: children_with_bed,
                                quotation_date: quotation_date,
                                booking_type: booking_type,
                                train_cost: train_cost,
                                flight_cost: flight_cost,
                                visa_cost: visa_cost,
                                train_from_location_arr: train_from_location_arr,
                                train_to_location_arr: train_to_location_arr,
                                train_class_arr: train_class_arr,
                                train_arrival_date_arr: train_arrival_date_arr,
                                train_departure_date_arr: train_departure_date_arr,
                                plane_from_city_arr: plane_from_city_arr,
                                plane_to_city_arr: plane_to_city_arr,
                                plane_from_location_arr: plane_from_location_arr,
                                plane_to_location_arr: plane_to_location_arr,
                                airline_name_arr: airline_name_arr,
                                plane_class_arr: plane_class_arr,
                                arraval_arr: arraval_arr,
                                dapart_arr: dapart_arr,
                                cruise_departure_date_arr: cruise_departure_date_arr,
                                cruise_arrival_date_arr: cruise_arrival_date_arr,
                                route_arr: route_arr,
                                cabin_arr: cabin_arr,
                                sharing_arr: sharing_arr,
                                package_type_arr: package_type_arr,
                                city_name_arr: city_name_arr,
                                hotel_name_arr: hotel_name_arr,
                                hotel_cat_arr: hotel_cat_arr,
                                hotel_type_arr: hotel_type_arr,
                                hotel_stay_days_arr: hotel_stay_days_arr,
                                package_name_arr: package_name_arr,
                                total_rooms_arr: total_rooms_arr,
                                hotel_cost_arr: hotel_cost_arr,
                                extra_bed_arr: extra_bed_arr,
                                extra_bed_cost_arr: extra_bed_cost_arr,
                                meal_plan_arr:meal_plan_arr,
                                check_in_arr: check_in_arr,
                                check_out_arr: check_out_arr,
                                vehicle_name_arr: vehicle_name_arr,
                                start_date_arr: start_date_arr,
                                end_date_arr: end_date_arr,
                                pickup_arr: pickup_arr,
                                drop_arr: drop_arr,
                                vehicle_count_arr: vehicle_count_arr,
                                transport_cost_arr1: transport_cost_arr1,
                                package_name_arr1: package_name_arr1,
                                pickup_type_arr: pickup_type_arr,
                                drop_type_arr: drop_type_arr,
                                service_duration_arr:service_duration_arr,
                                tour_cost_arr: tour_cost_arr,
                                excursion_cost_arr: excursion_cost_arr,
                                adult_arr: adult_arr,
                                chwb_arr: chwb_arr,
                                chwob_arr: chwob_arr,
                                infant_arr: infant_arr,
                                vehicles_arr:vehicles_arr,
                                basic_amount_arr: basic_amount_arr,
                                service_charge_arr: service_charge_arr,
                                service_tax_subtotal_arr: service_tax_subtotal_arr,
                                total_tour_cost_arr: total_tour_cost_arr,
                                package_name_arr2: package_name_arr2,
                                transport_cost_arr1: transport_cost_arr1,
                                transport_cost_arr: transport_cost_arr,
                                package_id_arr: package_id_arr1,
                                login_id: login_id,
                                emp_id: emp_id,
                                city_name_arr_e: city_name_arr_e,
                                excursion_name_arr: excursion_name_arr,
                                exc_date_arr_e: exc_date_arr_e,
                                transfer_option_arr: transfer_option_arr,
                                excursion_amt_arr: excursion_amt_arr,
                                guide_cost: guide_cost,
                                cruise_cost: cruise_cost,
                                misc_cost: misc_cost,
                                attraction_arr: attraction_arr,
                                program_arr: program_arr,
                                stay_arr: stay_arr,
                                hotel_meal_plan_arr: hotel_meal_plan_arr,
                                package_p_id_arr: package_p_id_arr,
                                branch_admin_id: branch_admin_id,
                                c_package_id_arr: c_package_id_arr,
                                package_type_c_arr: package_type_c_arr,
                                discount_in_arr:discount_in_arr,discount_arr:discount_arr,
                                adult_cost_arr: adult_cost_arr,
                                infant_cost_arr: infant_cost_arr,
                                child_with_arr: child_with_arr,
                                child_without_arr: child_without_arr,
                                price_str_url: price_str_url,
                                incl_arr: incl_arr,
                                excl_arr: excl_arr,
                                financial_year_id: financial_year_id,
                                pckg_daywise_url: pckg_daywise_url,
                                costing_type: costing_type,
                                bsmValues: bsmValues,
                                currency_code: currency_code,
                                discount: discount,
                                flight_acost: flight_acost,
                                flight_ccost: flight_ccost,
                                flight_icost: flight_icost,
                                train_acost: train_acost,
                                train_ccost: train_ccost,
                                train_icost: train_icost,
                                cruise_acost: cruise_acost,
                                cruise_ccost: cruise_ccost,
                                cruise_icost: cruise_icost,
                                other_desc: other_desc
                            },
                            success: function(message) {

                                $('#btn_quotation_save').button('reset');
                                $('#btn_quotation_save').prop('disabled', false);
                                var msg = message.split('--');
                                if (msg[0] == "error") {
                                    error_msg_alert(msg[1]);
                                } else {
                                    $('#vi_confirm_box').vi_confirm_box({

                                        false_btn: false,

                                        message: message,

                                        true_btn_text: 'Ok',

                                        callback: function(data1) {

                                            if (data1 == "yes") {

                                                $('#btn_quotation_save')
                                                    .button('reset');
                                                $('#quotation_save_modal')
                                                    .modal('hide');
                                                $('#btn_quotation_save')
                                                    .prop('disabled',
                                                        false);
                                                window.location.href =
                                                    base_url +
                                                    'view/package_booking/quotation/home/index.php';
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    } else {
                        $('#btn_quotation_save').button('reset');
                        $('#btn_quotation_save').prop('disabled', false);
                    }
                }
            });
        }
    });
</script>