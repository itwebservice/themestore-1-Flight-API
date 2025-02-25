<style>
.style_text
{
	position: absolute;
    right: 15px;
    display: flex;
    gap: 15px;
    background: #f5f5f5;
    padding: 0px 14px;
    top: 0px;
}

</style>

<form id="frm_tour_master_update" name="frm_tour_master_save" method="POST">
    <div class="app_panel" style="padding-top: 30px;">

        <div class="container-fluid">
            <div class="app_panel_content Filter-panel">
                <input type="hidden" id="txt_tour_id" name="txt_tour_id" value="<?php echo $tour_id; ?>">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mg_bt_10">
                        <select id="cmb_tour_type" name="cmb_tour_type" class="form-control" title="Tour Type" disabled>
                            <option value="<?php echo $tour_info['tour_type'] ?>" selected>
                                <?php echo $tour_info['tour_type'] ?></option>
                            <option value="Domestic">Domestic</option>
                            <option value="International">International</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 mg_bt_10">
                        <input type="text" id="txt_tour_name" name="txt_tour_name" class="form-control"
                            value="<?php echo $tour_info['tour_name'] ?>" placeholder="Tour Name"
                            onchange="package_name_validation(this.id);(this.id)" title="Tour Name" <?= $readable ?> />
                    </div>
                    <div class="col-md-3 col-sm-6 mg_bt_10 ">
                        <select id="dest_name_s" name="dest_name_s" title="Select Destination" class="form-control"
                            style="width:100%" disabled required>
                            <?php $sq_query1 = mysqli_fetch_assoc(mysqlQuery("select dest_id,dest_name from destination_master where dest_id = '$tour_info[dest_id]'")); ?>
                            <option value="<?php echo $sq_query1['dest_id']; ?>"><?php echo $sq_query1['dest_name']; ?>
                            </option>
                            <option value="">*Destination</option>
                            <?php
                            $sq_query = mysqlQuery("select * from destination_master where status != 'Inactive'");
                            while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>
                            <option value="<?php echo $row_dest['dest_id']; ?>"><?php echo $row_dest['dest_name']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-3 mg_bt_10_xs">
                        <select id="dest_image1" name="dest_image1" title="Destination Image" class="form-control">
                            <?php
                            if ($tour_info['dest_image'] != '0') {
                                $row_gallary = mysqli_fetch_assoc(mysqlQuery("select * from gallary_master where entry_id = '$tour_info[dest_image]'"));
                                $url = $row_gallary['image_url'];
                                $pos = strstr($url, 'uploads');
                                $entry_id = $row_gallary['entry_id'];
                                if ($pos != false) {
                                    $newUrl1 = preg_replace('/(\/+)/', '/', $row_gallary['image_url']);
                                    $newUrl = BASE_URL . str_replace('../', '', $newUrl1);
                                } else {
                                    $newUrl =  $row_gallary['image_url'];
                                }
                                $image_url = explode('/', $newUrl);
                                $count = sizeof($image_url) - 1;
                            ?>
                            <option value="<?= $tour_info['dest_image'] ?>"><?= $image_url[$count] ?></option>
                            <?php } ?>

                            <option value="">Select Image</option>
                            <?php
                            $query = " select * from gallary_master where dest_id = '$tour_info[dest_id]'";
                            $count = 0;
                            $sq_gallary = mysqlQuery($query);
                            while ($row_gallary = mysqli_fetch_assoc($sq_gallary)) {
                                $url = $row_gallary['image_url'];
                                $pos = strstr($url, 'uploads');
                                $entry_id = $row_gallary['entry_id'];
                                if ($pos != false) {
                                    $newUrl1 = preg_replace('/(\/+)/', '/', $row_gallary['image_url']);
                                    $newUrl = BASE_URL . str_replace('../', '', $newUrl1);
                                } else {
                                    $newUrl =  $row_gallary['image_url'];
                                }
                                $image_url = explode('/', $newUrl);
                                $count = sizeof($image_url) - 1;
                            ?>
                            <option value="<?= $entry_id ?>"><?= $image_url[$count] ?></option>
                            <?php } ?> ?>

                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <select class="<?= $active_inactive_flag ?>" name="active_flag1" id="active_flag1"
                            title="Status">
                            <option value="<?php echo $tour_info['active_flag']; ?>">
                                <?php echo $tour_info['active_flag']; ?></option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="panel panel-default panel-body app_panel_style mg_tp_20 mg_bt_20">
                    <div class="row">
                        <div class="col-md-12 text-right mg_bt_10">
                            <button type="button" class="btn btn-excel" title="Add Row" onclick="addRow('tbl_dynamic_tour_group')"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tbl_dynamic_tour_group" name="tbl_dynamic_tour_group"
                                    class="table border_0 no-marg">
                                    <?php
                                    $sq_tour_group = mysqlQuery("select * from tour_groups where tour_id='$tour_id'");
                                    $count = 1;
                                    while ($row = mysqli_fetch_assoc($sq_tour_group)) {
                                    ?>
                                    <tr>
                                        <td><input class="css-checkbox" id="chk_tour_group<?php echo $count . "m" ?>"
                                                type="checkbox" disabled checked><label class="css-label"
                                                for="chk_tour_group<?php echo $count . "m" ?>"> <label></td>
                                        <td><input class="form-control" maxlength="15" value="<?php echo $count ?>"
                                                type="text" name="username" placeholder="Sr. No." disabled /></td>
                                        <td><input class="form-control" type="text"
                                                id="txt_from_date<?php echo $count . "m" ?>"
                                                name="txt_from_date<?php echo $count . "m" ?>"
                                                value="<?php echo date("d-m-Y", strtotime($row['from_date'])) ?>"
                                                title="From Date" placeholder="*From Date" onchange="get_to_date('txt_from_date<?php echo $count . 'm' ?>','txt_to_date<?php echo $count . 'm' ?>');" /></td>
                                        <td><input class="form-control" type="text"
                                                id="txt_to_date<?php echo $count . "m" ?>"
                                                name="txt_to_date<?php echo $count . "m" ?>"
                                                value="<?php echo date("d-m-Y", strtotime($row['to_date'])) ?>"
                                                title="To Date" placeholder="*To Date" onchange="validate_validDate('txt_from_date<?php echo $count . 'm' ?>','txt_to_date<?php echo $count . 'm' ?>');" /></td>
                                        <td><input class="form-control" onchange="validate_balance(this.id);"
                                                type="text" id="txt_capacity<?php echo $count . "m" ?>"
                                                name="txt_capacity<?php echo $count . "m" ?>"
                                                onchange="validate_balance(this.id)"
                                                value="<?php echo $row['capacity'] ?>" placeholder="Tour Capacity"
                                                title="Tour Capacity" title="Tour Capacity" />
                                        </td>
                                        <td class="hidden"><input class="form-control hidden"
                                                onchange="validate_balance(this.id);" type="text"
                                                id="txt_tour_group_id<?php echo $count . "m" ?>"
                                                name="txt_tour_group_id<?php echo $count . "m" ?>"
                                                value="<?php echo $row['group_id'] ?>" placeholder="Tour Id"
                                                title="Tour Id" /></td>
                                    </tr>
                                    <script>
                                    $("#txt_to_date<?php echo $count . 'm' ?>").datetimepicker({
                                        timepicker: false,
                                        format: 'd-m-Y'
                                    });
                                    $("#txt_from_date<?php echo $count . 'm' ?>").datetimepicker({
                                        timepicker: false,
                                        format: 'd-m-Y'
                                    });
                                    </script>
                                    <?php
                                        $count++;
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                    <legend>Tour Itinerary</legend>
                        <div class="row">
                            <div class="col-md-12" id="div_list1">
                                <table style="width:100%;margin: 0 !important;" id="dynamic_table_list1"
                                    name="dynamic_table_list">
                                    <?php
                                    $count = 1;
                                    $sq_pckg_a = mysqlQuery("select * from group_tour_program where tour_id = '$tour_id'");
                                    while ($sq_pckg1 = mysqli_fetch_assoc($sq_pckg_a)) {
                                    ?>
                                    <tr>
                                        <td class='col-md-1 pad_8'><input type="text" id="day<?php echo $count; ?>-u" name="day"
                                                class="form-control mg_bt_10" placeholder="Day <?php echo $count; ?>"
                                                title="Day" value="" disabled>
                                        <td class='col-md-3 pad_8' style='width:100px'><input type="text"
                                                id="special_attaraction<?php echo $count; ?>-u"
                                                name="special_attaraction" class="form-control mg_bt_10"
                                                placeholder="Special Attraction" title="Special Attraction"
                                                onchange="validate_spaces(this.id);validate_spattration(this.id);"
                                                value="<?php echo $sq_pckg1['attraction']; ?>"></td>
                                        <!-- <td class='col-md-6 pad_8' style="max-width: 594px;overflow: hidden;"><textarea
                                                id="day_program<?php echo $count; ?>-u" name="day_program"
                                                class="form-control mg_bt_10"
                                                placeholder="Day<?php echo $count + 1; ?> Program"
                                                title="Day-wise Program" rows="3"
                                                onchange="validate_spaces(this.id);validate_dayprogram(this.id);"
                                                value="<?php echo $sq_pckg1['day_wise_program']; ?>"><?php echo $sq_pckg1['day_wise_program']; ?></textarea>
                                        </td> -->
                                        <td class='col-md-6 pad_8' style="max-width: 594px;overflow: hidden;position: relative;"><textarea id="day_program<?php echo $count; ?>-u" name="day_program" class="form-control mg_bt_10 day_program" placeholder="*Day<?php echo $count + 1; ?> Program" title="Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" value="<?php echo $sq_pckg1['day_wise_program']; ?>" style='width:100%'><?php echo $sq_pckg1['day_wise_program']; ?></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
                                        </td>
                                        <td class='col-md-2 pad_8' style='width:100px'><input type="text"
                                                id="overnight_stay<?php echo $count; ?>-u" name="overnight_stay"
                                                class="form-control mg_bt_10" placeholder="Overnight Stay"
                                                onchange="validate_spaces(this.id);validate_onstay(this.id);"
                                                title="Overnight Stay" value="<?php echo $sq_pckg1['stay']; ?>"></td>
                                        <td class='col-md-1 pad_8' style='width:100px'><select
                                                id="meal_plan<?php echo $count; ?>" title="Meal Plan" name="meal_plan"
                                                style="width:125px;" class="form-control">
                                                <?php if ($sq_pckg1['meal_plan'] != '') { ?>
                                                <option value="<?= $sq_pckg1['meal_plan'] ?>">
                                                    <?= $sq_pckg1['meal_plan'] ?>
                                                </option>
                                                <?php } ?>
                                                <?php get_mealplan_dropdown(); ?>
                                            </select></td>
                                        <td class='col-md-1 pad_8'><button type="button" class="btn btn-excel"
                                                title="Add Itinerary" id="itineraryu<?php echo $count; ?>"
                                                onClick="add_itinerary('dest_name_s','special_attaraction<?php echo $count; ?>-u','day_program<?php echo $count; ?>-u','overnight_stay<?php echo $count; ?>-u','Day-u<?= $count ?>')"><i
                                                    class="fa fa-plus"></i></button>
                                        </td>
                                        <td class="hidden"><input type="text"
                                                value="<?php echo $sq_pckg1['entry_id']; ?>">
                                        </td>
                                    </tr>
                                    <?php
                                        $count++;
                                    }  ?>
                                </table>
                            </div>
                        </div>
                </div>

                <div class="row mg_bt_10 mg_tp_20 text-center">
                    <button class="btn btn-sm btn-info ico_right days" id="btn_update">Next&nbsp;&nbsp;<i
                            class="fa fa-arrow-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>

$(document).on("click", ".style_text_b, .style_text_u", function() {
    var wrapper = $(this).data("wrapper");
    
    // Get the textarea element
    var textarea = $(this).parents('.style_text').siblings('.day_program')[0];
    
    // Ensure textarea exists and selectionStart/selectionEnd are supported
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;

        // Get the selected text
        var selectedText = textarea.value.substring(start, end);

        // Wrap the selected text with the wrapper (e.g., ** for bold, __ for underline)
        var wrappedText = wrapper + selectedText + wrapper;

        // Insert the wrapped text back into the textarea
        textarea.value = textarea.value.substring(0, start) + wrappedText + textarea.value.substring(end);

        // Adjust the cursor position after wrapping
        textarea.selectionStart = start;
        textarea.selectionEnd = end + wrapper.length * 2;
		var text=textarea.value;
		 var content = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

		// Replace markdown-style underline (__text__) with <u> tags
		content = content.replace(/__(.*?)__/g, '<u>$1</u>');
		textarea.value =content;
		//console.log(content);    
});


$('#dest_name_s,#dest_image1').select2();
/////////////********** Tour Master Information Update start ***********************************
$(function() {
    $('#frm_tour_master_update').validate({

        rules: {
            cmb_tour_type: {
                required: true
            },
            txt_tour_name: {
                required: true
            },
            txt_bus_type: {
                required: true
            },
            txt_tour_cost: {
                required: true,
                number: true
            },
            txt_children_cost: {
                required: true,
                number: true
            },
            txt_infant_cost: {
                required: true,
                number: true
            },
            with_bed_cost: {
                required: true,
                number: true
            },
            txt_special_note: {
                required: true
            },
            active_flag: {
                required: true
            },
        },
        submitHandler: function(form) {

            var result = package_name_validation('txt_tour_name');
            if(!result) { error_msg_alert('Tour name should not allow special character.'); return false; }
            
            var valid_state = table_info_validate();
            if (valid_state == false) {
                return false;
            }
            var table = document.getElementById("dynamic_table_list1");
            var rowCount = table.rows.length;

            for (var i = 0; i < rowCount; i++) {
                var row = table.rows[i];
                var day_program = row.cells[2].childNodes[0].value;
                if (day_program == "") {
                    error_msg_alert("Day-wise program important");
                    return false;
                }
                var flag1 = validate_spattration(row.cells[1].childNodes[0].id);
                var flag2 = validate_dayprogram(row.cells[2].childNodes[0].id);
                var flag3 = validate_onstay(row.cells[3].childNodes[0].id);
                if (!flag1 || !flag2 || !flag3) {
                    return false;
                }
            }

            $('#tab1_head').addClass('done');
            $('#tab2_head').addClass('active');
            $('.bk_tab').removeClass('active');
            $('#tab2').addClass('active');
            $('html, body').animate({
                scrollTop: $('.bk_tab_head').offset().top
            }, 200);

            return false;

        }
    });
});




function table_info_validate() {
    g_validate_status = true;
    var validate_message = "";

    //Special attraction table
    var table = document.getElementById("dynamic_table_list1");
    var rowCount = table.rows.length;
    for (var i = 0; i < rowCount; i++) {
        var row = table.rows[i];
        validate_dynamic_empty_fields(row.cells[1].childNodes[0]);
        validate_dynamic_empty_fields(row.cells[2].childNodes[0]);
        validate_dynamic_empty_fields(row.cells[3].childNodes[0]);


        // if(row.cells[2].childNodes[0].value==""){ 
        //       validate_message += "Enter Day"+(i+1)+"Program in row"+(i+1)+"<br>";
        // }

    }

    //Tour group table
    var from_date = new Array();
    var to_date = new Array();
    var capacity = new Array();
    var tour_group_id = new Array();

    var table = document.getElementById("tbl_dynamic_tour_group");
    var rowCount = table.rows.length;
    var latest_date = "";

    for (var i = 0; i < rowCount; i++) {
        var row = table.rows[i];

        if (row.cells[0].childNodes[0].checked) {
            var from_date1 = row.cells[2].childNodes[0].value;
            var to_date1 = row.cells[3].childNodes[0].value;
            var capacity1 = row.cells[4].childNodes[0].value;
            var tour_group_id1 = row.cells[5].childNodes[0].value;

            if (from_date1 == "" || to_date1 == "") {
                error_msg_alert('From date and To Date is required' + (i + 1));
                return false;
            }

            if (capacity1 == "") {
                error_msg_alert('Capacity is required' + (i + 1));
                return false;
            }
            var edate = from_date1.split('-');
            e_date = new Date(edate[2], edate[1] - 1, edate[0]).getTime();
            var edate1 = to_date1.split('-');
            e_date1 = new Date(edate1[2], edate1[1] - 1, edate1[0]).getTime();

            var from_date_ms = new Date(e_date).getTime();
            var to_date_ms = new Date(e_date1).getTime();

            if (from_date_ms > to_date_ms) {
                error_msg_alert('Date should not be greater than valid to date at row ' + (i + 1));
                return false;
            }

            from_date.push(from_date1);
            to_date.push(to_date1);
            capacity.push(capacity1);
            tour_group_id.push(tour_group_id1);
        }
    }

}
/////////////********** Tour Master Information Update end ***********************************

function tour_date_generate() {
    var count = $("#txt_tour_date_generate").val();

    for (var i = 0; i <= count; i++) {
        $("#txt_from_date" + i + 'm').datepicker({
            inline: true,
            dateFormat: "dd-mm-yy"
        });
        $("#txt_to_date" + i + 'm').datepicker({
            inline: true,
            dateFormat: "dd-mm-yy"
        });
    }
}
tour_date_generate();
</script>