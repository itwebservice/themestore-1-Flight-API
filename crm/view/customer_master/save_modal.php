<?php
include "../../model/model.php";
$client_modal_type = $_POST['client_modal_type'];
$branch_admin_id = $_SESSION['branch_admin_id'];
?>
<input type="hidden" id="client_modal_type" name="client_modal_type" value="<?= $client_modal_type ?>">
<input type="hidden" id="branch_admin_id" name="branch_admin_id" value="<?= $branch_admin_id ?>">
<input type="hidden" id="whatsapp_switch" value="<?= $whatsapp_switch ?>">
<div class="modal fade" id="customer_save_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
    data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Customer</h4>
            </div>
            <div class="modal-body">

                <form id="frm_customer_save">
                    <div class="row mg_bt_10">
                        <div class="col-md-4 col-md-offset-2 mg_bt_10">
                            <select name="cust_type" id="cust_type" class="form-control" data-toggle="tooltip"
                                onchange="corporate_fields_reflect();customer_users_reflect('save');" title="Customer Type">
                                <?php get_customer_type_dropdown(); ?>
                            </select>
                        </div>
                        <div class="col-md-4 mg_bt_10">
                            <select name="cust_source" id="cust_source" class="form-control" data-toggle="tooltip" title="Customer Source">
                                <?php get_customer_source_dropdown(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mg_bt_10">
                        <div id="corporate_fields"></div>
                    </div>
                    <div class="panel panel-default panel-body app_panel_style mg_tp_30 feildset-panel">
                        <legend>Personal Information</legend>
                        <div class="row ">
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <input type="text" id="cust_first_name" name="cust_first_name"
                                    onchange="fname_validate(this.id);" placeholder="*First Name" title="First Name">
                            </div>
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <input type="text" id="cust_middle_name" name="cust_middle_name"
                                    onchange="fname_validate(this.id);" placeholder="Middle Name" title="Middle Name">
                            </div>
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <input type="text" id="cust_last_name" name="cust_last_name"
                                    onchange="fname_validate(this.id);" placeholder="Last Name" title="Last Name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <select name="cust_gender" id="cust_gender" title="Select Gender">
                                    <option value="">Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <input type="text" id="cust_birth_date" name="cust_birth_date" placeholder="Birth Date"
                                    title="Birth Date"
                                    onchange="calculate_age_generic('cust_birth_date', 'cust_age') ; ">

                            </div>
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <input type="text" id="cust_age" name="cust_age" onchange="validate_balance(this.id);"
                                    placeholder="Age" title="Age" readonly>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10">
                                <select name="country_code" id="country_code">
                                    <?= get_country_code() ?>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10">
                                <input type="text" id="cust_contact_no" name="cust_contact_no" maxlength="15"
                                    onchange="mobile_validate(this.id)" placeholder="*Mobile No" title="Mobile No">
                            </div>
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <input type="text" id="cust_email_id" name="cust_email_id" placeholder="Email ID"
                                    title="Email ID">
                            </div>
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <input type="text" id="cust_service_tax_no" name="cust_service_tax_no"
                                    onchange="validate_alphanumeric(this.id);" placeholder="Tax No" title="Tax No"
                                    style="text-transform: uppercase;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <input type="text" id="cust_pan" onchange="validate_alphanumeric(this.id);"
                                    name="cust_pan" placeholder="Personal Identification No(PIN)" title="Personal Identification No(PIN)" style="text-transform: uppercase;">
                            </div>
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <input class="form-control" type="number" id="op_balance" name="op_balance"
                                    placeholder="*Opening Balance" title="Opening Balance" value="0">
                            </div>
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <select class="form-control" id="balance_side" name="balance_side" title="Balance Side"
                                    style="width:100%;">
                                    <option value="Debit">Debit</option>
                                    <option value="Credit">Credit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default panel-body app_panel_style mg_tp_30 feildset-panel">
                        <legend>Address Information</legend>
                        <div class="row">
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <input type="text" name="cust_address1" id="cust_address1" placeholder="Address-1"
                                    onchange="validate_address(this.id);" title="Address 1" />
                            </div>
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <input type="text" name="cust_address2" id="cust_address2"
                                    onchange="validate_address(this.id);" placeholder="Address-2" title="Address 2" />
                            </div>
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <input type="text" name="city" id="city" onchange="validate_city(this.id)"
                                    placeholder="City" title="City" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 col-xs-12 mg_bt_10">
                                <select name="cust_state" id="cust_state" title="State/Country Name"
                                    style="width : 100%">
                                    <?php get_states_dropdown() ?>
                                </select>
                            </div>
                            <div class="col-sm-4 col-xs-12">
                                <select name="cust_active_flag" id="cust_active_flag" title="Status" class="hidden">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div id="users_div"></div>

                    <div class="row text-center">
                        <div class="col-xs-12">
                            <button class="btn btn-sm btn-success" id="btn_csave"><i
                                    class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
<script src="<?php echo BASE_URL ?>js/app/field_validation.js"></script>

<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script>
$('#cust_state,#country_code').select2();
var date = new Date();
var yest = date.setDate(date.getDate() - 1);
$('#cust_birth_date').datetimepicker({
    timepicker: false,
    maxDate: yest,
    format: 'd-m-Y'
});
$('#customer_save_modal').modal('show');
$('#div_customer_update_modal').html('');
$('select').on("change", function(e) {
    $(this).valid()
});
$(function() {
    $('#frm_customer_save').validate({

        rules: {
            cust_first_name: {
                required: true
            },
            active_flag: {
                required: true
            },
            corpo_company_name: {
                required: true
            },
            cust_type: {
                required: true
            },
            cust_contact_no: {
                required: true
            },
            country_code: {
                required: true
            },
            cust_state: {
                required: true
            },
            op_balance: {
                required: true
            },
            balance_side: {
                required: true
            }
        },
        errorPlacement: function(error, element) {
            if (element.attr('id') == 'country_code') {
                error.insertAfter(element.next('span')); // select2
            } else {
                error.insertAfter(element); // default
            }
        },
        submitHandler: function(form, e) {
            $('#btn_csave').prop('disabled', true);
            var first_name = $('#cust_first_name').val();
            var middle_name = $('#cust_middle_name').val();
            var last_name = $('#cust_last_name').val();
            var gender = $('#cust_gender').val();
            var birth_date = $('#cust_birth_date').val();
            var age = $('#cust_age').val();
            var contact_no = $('#cust_contact_no').val();
            var country_code = $('#country_code').val();
            var email_id = $('#cust_email_id').val();
            var address = $('#cust_address1').val();
            var address2 = $('#cust_address2').val();
            var city = $('#city').val();
            var active_flag = $('#cust_active_flag').val();
            var service_tax_no = $('#cust_service_tax_no').val();
            var landline_no = $('#cust_landline_no').val();
            var alt_email_id = $('#cust_alt_email_id').val();
            var company_name = $('#corpo_company_name').val();
            var cust_type = $('#cust_type').val();
            var base_url = $('#base_url').val();
            var state = $('#cust_state').val();
            var cust_pan = $('#cust_pan').val();
            var op_balance = $('#op_balance').val();
            var balance_side = $('#balance_side').val();
            var branch_admin_id = $('#branch_admin_id').val();
            var cust_source = $('#cust_source').val();
            
            var user_name_array = [];
            var mobile_no_array = [];
            var email_id_array = [];
            var status_array = [];
            if(cust_type == 'B2B' || cust_type == 'Corporate'){

                var table = document.getElementById("tbl_cust_users");
                var rowCount = table.rows.length;
                for(var i=0; i<rowCount; i++){

                    var row = table.rows[i];
                    if(row.cells[0].childNodes[0].checked){  

                        var user_name = row.cells[2].childNodes[0].value;
                        var mobile_no = row.cells[3].childNodes[0].value;
                        var email_id = row.cells[4].childNodes[0].value;
                        var status = row.cells[5].childNodes[0].value;

                        if(user_name==""){
                            error_msg_alert("User name is required in row : "+(i+1));
                            $('#btn_csave').prop('disabled', false);
                            return false;
                        }

                        user_name_array.push(user_name);
                        mobile_no_array.push(mobile_no);
                        email_id_array.push(email_id);
                        status_array.push(status);
                    }
                }
            }
            $('#btn_csave').prop('disabled', true);
            $('#btn_csave').button('loading');
            $.ajax({
                type: 'post',
                url: base_url + 'controller/customer_master/customer_save.php',
                data: {
                    first_name: first_name,
                    middle_name: middle_name,
                    last_name: last_name,
                    gender: gender,
                    birth_date: birth_date,
                    age: age,
                    contact_no: contact_no,
                    email_id:email_id,
                    address: address,
                    address2: address2,
                    city: city,
                    active_flag: active_flag,
                    service_tax_no: service_tax_no,
                    landline_no: landline_no,
                    alt_email_id: alt_email_id,
                    company_name: company_name,
                    cust_type: cust_type,
                    state: state,
                    cust_pan: cust_pan,
                    op_balance: op_balance,
                    balance_side: balance_side,
                    branch_admin_id: branch_admin_id,
                    cust_source: cust_source,
                    country_code: country_code,user_name_array:user_name_array,mobile_no_array:mobile_no_array,email_id_array:email_id_array,status_array:status_array
                },
                success: function(result) {

                    $('#btn_csave').prop('disabled', true);
                    var result_arr = result.split('==');
                    var error_arr = result.split('--');
                    var client_modal_type = $('#client_modal_type').val();
                    $('#btn_csave').button('reset');
                    if (client_modal_type == "master") {
                        if (error_arr[0] == 'error') {
                            error_msg_alert(error_arr[1]);
                            $('#btn_csave').prop('disabled', false);
                            return false;
                        } else {
                            success_msg_alert(result_arr[0]);
                            $('#customer_save_modal').modal('hide');
                            customer_list_reflect();
                            if ($('#whatsapp_switch').val() == "on")
                                customer_whatsapp_send(first_name, country_code +
                                    contact_no, email_id,company_name,cust_type);
                        }
                    } else {
                        if (error_arr[0] == 'error') {
                            error_msg_alert(error_arr[1]);
                            $('#btn_csave').prop('disabled', false);
                            return false;
                        } else {
                            success_msg_alert(result_arr[0]);
                            $('#customer_save_modal').modal('hide');
                            customer_dropdown_reload(result_arr[1]);
                            if ($('#whatsapp_switch').val() == "on")
                                customer_whatsapp_send(first_name, country_code +
                                    contact_no, email_id,company_name,cust_type);
                        }
                    }
                }
            });
        }
    });

});
</script>

<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>