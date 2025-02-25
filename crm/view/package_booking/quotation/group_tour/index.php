<?php
include "../../../../model/model.php";
/*======******Header******=======*/
require_once('../../../layouts/admin_header.php');
include_once('../inc/quotation_hints_modal.php');


$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$sq = mysqli_fetch_assoc(mysqlQuery("select branch_status from branch_assign where link='package_booking/quotation/group_tour/index.php'"));
$branch_status = $sq['branch_status'];
$financial_year_id = $_SESSION['financial_year_id'];
?>
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>">
<input type="hidden" id="whatsapp_switch" name="whatsapp_switch" value="<?= $whatsapp_switch ?>">
<?= begin_panel('Group Tour Quotation', 41) ?>
<div class="app_panel_content">
    <div class="row">
        <div class="col-md-12">
            <div id="div_id_proof_content">
                <div class="row mg_bt_20">
                    <div class="col-md-8">
                    </div>
                    <div class="col-md-4 text-right">
                        <button class="btn btn-info btn-sm ico_left" onclick="save_modal()" id="gquot_save"><i
                                class="fa fa-plus"></i>&nbsp;&nbsp;Quotation</button>
                    </div>
                </div>


                <div class="app_panel_content Filter-panel">
                    <div class="row">
                        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                            <input type="text" id="from_date_filter" name="from_date_filter" placeholder="From Date"
                                title="From Date" onchange="get_to_date(this.id,'to_date_filter');">
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                            <input type="text" id="to_date_filter" name="to_date_filter" placeholder="To Date"
                                title="To Date" onchange="validate_validDate('from_date_filter','to_date_filter');">
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                            <select name="booking_type_filter" id="booking_type_filter" title="Tour Type">
                                <option value="">Tour Type</option>
                                <option value="Domestic">Domestic</option>
                                <option value="International">International</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                            <select name="tour_name" id="tour_name_filter" title="Select Tour Name" style="width:100%">
                                <option value="">Select Tour Name</option>
                                <?php
                                $query = "select distinct(tour_name) from group_tour_quotation_master where 1 and status='1'";
                                if ($role == 'B2b' || $role == 'Sales' || $role == 'Backoffice') {
                                    $query .= " and emp_id='$emp_id'";
                                }
                                if ($branch_status == 'yes' && $role != 'Admin') {
                                    $query .= " and branch_admin_id = '$branch_admin_id'";
                                }
                                if ($branch_status == 'yes' && $role == 'Branch Admin') {
                                    $query .= " and branch_admin_id='$branch_admin_id'";
                                }
                                $query .= ' order by tour_name';
                                $sq_tour = mysqlQuery($query);
                                while ($row_tour = mysqli_fetch_assoc($sq_tour)) {
                                ?>
                                <option value="<?= $row_tour['tour_name'] ?>"><?= $row_tour['tour_name'] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                            <select name="quotation_id" id="quotation_id" title="Select Quotation" style="width:100%">
                                <option value="">Select Quotation</option>
                                <?php
                                $query = "select * from group_tour_quotation_master where 1 and financial_year_id='$financial_year_id' and status='1'";
                                if ($role == 'B2b' || $role == 'Sales' || $role == 'Backoffice') {
                                    $query .= " and emp_id='$emp_id'";
                                }
                                if ($branch_status == 'yes' && $role != 'Admin') {
                                    $query .= " and branch_admin_id = '$branch_admin_id'";
                                }
                                if ($branch_status == 'yes' && $role == 'Branch Admin') {
                                    $query .= " and branch_admin_id='$branch_admin_id'";
                                }
                                $query .= "  order by quotation_id desc";
                                $sq_quotation = mysqlQuery($query);
                                while ($row_quotation = mysqli_fetch_assoc($sq_quotation)) {
                                    $quotation_date = $row_quotation['quotation_date'];
                                    $yr = explode("-", $quotation_date);
                                    $year = $yr[0];
                                ?>
                                <option value="<?= $row_quotation['quotation_id'] ?>">
                                    <?= get_quotation_id($row_quotation['quotation_id'], $year) ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6 col-xs-12">
                            <select name="status" id="status" title="Status" style="width:100%">
                                <option value="">Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mg_tp_10">
                        <?php if ($role == 'Admin') { ?>
                        <div class="col-md-2 col-sm-6 col-xs-12 mg_bt_10_xs">
                            <select name="branch_id_filter" id="branch_id_filter1" title="Branch Name"
                                style="width: 100%">
                                <?php get_branch_dropdown($role, $branch_admin_id, $branch_status) ?>
                            </select>
                        </div>
                        <?php } ?>
                        <div class="col-md-3 col-sm-6 mg_bt_10_xs">
                            <select name="financial_year_id_filter" id="financial_year_id_filter" title="Select Financial Year">
                                <?php
                                $sq_fina = mysqli_fetch_assoc(mysqlQuery("select * from financial_year where financial_year_id='$financial_year_id'"));
                                $financial_year = get_date_user($sq_fina['from_date']).'&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;&nbsp;'.get_date_user($sq_fina['to_date']);
                                ?>
                                <option value="<?= $sq_fina['financial_year_id'] ?>"><?= $financial_year  ?></option>
                                <?php echo get_financial_year_dropdown_filter($financial_year_id); ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <button class="btn btn-sm btn-info ico_right" onclick="quotation_list_reflect()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>

                <div id="div_quotation_list_reflect" class="main_block loader_parent">
                    <div class="row mg_tp_20">
                        <div class="col-md-12 no-pad">
                            <div class="table-responsive">
                                <table id="group_table" class="table table-hover"
                                    style="width:100% !important;margin: 20px 0 !important;">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="div_quotation_form"></div>
                <div id="div_quotation_update"></div>
                <div id="div_modal_content"></div>
                <div id="backoffice_mail"></div>
            </div>
        </div>
    </div>
</div>
<?= end_panel() ?>
<script src="<?= BASE_URL ?>js/app/field_validation.js"></script>
<style>
.action_width {
    width: 200px;
}

.s_no_width {
    width: 5px !important;
}

.tooltip {
    width: 150px;
}
</style>
<script>
$('[data-toggle="tooltip"]').tooltip();
$('#quotation_id,#tour_name_filter').select2();
$('#from_date_filter, #to_date_filter').datetimepicker({
    timepicker: false,
    format: 'd-m-Y'
});

column = [{
        title: "S_No.",
        className: "s_no_width"
    },
    {
        title: "QUOTATION_ID"
    },
    {
        title: "Tour_Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
    },
    {
        title: "Customer"
    },
    {
        title: "QUOTATION_Date&nbsp;&nbsp;"
    },
    {
        title: "Amount"
    },
    {
        title: "Created_by"
    },
    {
        title: "Actions",
        className: "text-center action_width custom_action_width",
        bSortable: false
    }
];

function quotation_list_reflect() {
    $('#div_quotation_list_reflect').append('<div class="loader"></div>');
    var from_date = $('#from_date_filter').val();
    var to_date = $('#to_date_filter').val();
    var booking_type = $('#booking_type_filter').val();
    var tour_name = $('#tour_name_filter').val();
    var quotation_id = $('#quotation_id').val();
    var branch_status = $('#branch_status').val();
    var branch_id = $('#branch_id_filter1').val();
    var status = $('#status').val();
    var financial_year_id_filter = $('#financial_year_id_filter').val();

    $.post('quotation_list_reflect.php', {
        from_date: from_date,
        to_date: to_date,
        booking_type: booking_type,
        tour_name: tour_name,
        quotation_id: quotation_id,
        branch_status: branch_status,
        branch_id: branch_id,
        status: status,
        financial_year_id:financial_year_id_filter
    }, function(data) {
        pagination_load(data, column, true, false, 20, 'group_table');
        $('.loader').remove();
    })
}
quotation_list_reflect();

function quotation_clone(quotation_id) {
    var base_url = $('#base_url').val();
    $('#vi_confirm_box').vi_confirm_box({
        callback: function(data1) {
            if (data1 == "yes") {
                $.ajax({
                    type: 'post',
                    url: base_url +
                        'controller/package_tour/quotation/group_tour/quotation_clone.php',
                    data: {
                        quotation_id: quotation_id
                    },
                    success: function(result) {
                        msg_alert(result);
                        console.log(result);
                        quotation_list_reflect();
                    }
                });
            }
        }
    });
}

function save_modal() {
    $('#gquot_save').button('loading');
    var branch_status = $('#branch_status').val();
    $.post('save/index.php', {
        branch_status: branch_status
    }, function(data) {
        $('#div_quotation_form').html(data);
        $('#gquot_save').button('reset');
    });
}

function update_modal(quotation_id) {
	$('#editq-'+quotation_id).prop('disabled',true);
	$('#editq-'+quotation_id).button('loading');
    var branch_status = $('#branch_status').val();
    $.post('update/index.php', {
        quotation_id: quotation_id,
        branch_status: branch_status
    }, function(data) {
        $('#div_quotation_update').html(data);
        $('#editq-'+quotation_id).prop('disabled',false);
        $('#editq-'+quotation_id).button('reset');
    });
}
$(document).ready(function() {
    let searchParams = new URLSearchParams(window.location.search);
    if (searchParams.get('enquiry_id')) {
        save_modal();
    }
});

function quotation_email_send_backoffice_modal(quotation_id) {
	$('#email_backoffice_btn-'+quotation_id).prop('disabled',true);
	$('#email_backoffice_btn-'+quotation_id).button('loading');
    $.post('backoffice_mail.php', {
        quotation_id: quotation_id
    }, function(data) {
        $('#backoffice_mail').html(data);
        $('#email_backoffice_btn-'+quotation_id).prop('disabled',false);
        $('#email_backoffice_btn-'+quotation_id).button('reset');
    });
}

function quotation_email_send(btn_id, quotation_id) {
    $('#' + btn_id).button('loading');
    var base_url = $('#base_url').val();
    $.ajax({
        type: 'post',
        url: base_url + 'controller/package_tour/quotation/group_tour/quotation_email_send.php',
        data: {
            quotation_id: quotation_id
        },
        success: function(message) {
            msg_alert(message);
            $('#' + btn_id).button('reset');
        }
    });
    if ($('#whatsapp_switch').val() == "on") quotation_whatsapp_send(quotation_id, base_url);
}

function quotation_whatsapp_send(quotation_id, base_url) {
    $.post(base_url + 'controller/package_tour/quotation/group_tour/quotation_whatsapp.php', {
        quotation_id: quotation_id
    }, function(link) {
        window.open(link);
    });
}
</script>
<?php
/*======******Footer******=======*/
require_once('../../../layouts/admin_footer.php');
?>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>

<style>
.action_width {
    display: flex;
    padding-right: 0px !important;
}

.custom_action_width {
    width: 300px !important;
}