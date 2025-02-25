<form id="frm_tab_3">

    <div class="app_panel">
        <div class="container-fluid mg_tp_10">
            <div class="app_panel_content no-pad">
                <div class="">
                    <div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                        <legend>Accommodation details</legend>
                        <?php
                        $count_ht = 0;
                        $sq_hotel_entries = mysqli_num_rows(mysqlQuery("select * from package_hotel_accomodation_master where booking_id='$booking_id'"));
                        if ($sq_hotel_entries == 0) {
                            include_once("../booking_save/tab_3/hotel_table_row.php");
                        } else { ?>
                        <div class="row" style="margin-top: 5px">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-excel btn-sm" onClick="addRow('tbl_package_hotel_infomration')" title="Add row"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="row mg_tp_10">
                            <div class="col-xs-12">
                                <div class="table-responsive">
                                    <table id="tbl_package_hotel_infomration"
                                        class="table table-bordered table-hover pd_bt_51 table-striped no-marg"
                                        style="width: 1475px;">
                                        <?php
                                        $sq_hotel_acc = mysqlQuery("select * from package_hotel_accomodation_master where booking_id='$booking_id'");
                                        while ($row_hotel_acc = mysqli_fetch_assoc($sq_hotel_acc)) {
                                            $count_ht++;
                                            ?>
                                        <tr>
                                            <td><input id="check-btn-hotel-acm-1" type="checkbox"
                                                    onchange="calculate_hotel_expense()" checked disabled></td>
                                            <td><input maxlength="15" type="text" name="username"
                                                    value="<?= $count_ht ?>" placeholder="Sr. No." disabled /></td>
                                            <td><select id="city_name1<?php echo $count_ht . "_h" ?>" class="city_name"
                                                    name="city_name1<?php echo $count_ht . "_h" ?>" style="width:150px"
                                                    title="Select City Name" class="form-control app_select2"
                                                    onchange="hotel_name_list_load1(this.id)">
                                                    <?php
                                                            $sq_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_hotel_acc[city_id]'"));
                                                            ?>
                                                    <option value="<?php echo $sq_city['city_id'] ?>">
                                                        <?= $sq_city['city_name'] ?></option>
                                                </select></td>
                                            <?php if($room_category_switch == 'No' ){?>
                                            <td><select id="hotel_name1<?php echo $count_ht . "_h" ?>"
                                                    name="hotel_name1<?php echo $count_ht . "_h" ?>" style="width:150px"
                                                    title="Select Hotel Name">
                                                    <option value="">Hotel Name</option>
                                                    <?php
                                                            $sq_hotel = mysqlQuery("select * from hotel_master where city_id = " . $row_hotel_acc['city_id']);
                                                            while ($rows =  mysqli_fetch_assoc($sq_hotel)) {
                                                                if ($row_hotel_acc['hotel_id'] == $rows['hotel_id']) {
                                                                    $selected = "selected";
                                                                } else {
                                                                    $selected = "";
                                                                }
                                                            ?>
                                                    <option value="<?php echo $rows['hotel_id'] ?>" <?= $selected ?>>
                                                        <?= $rows['hotel_name'] ?></option>
                                                    <?php } ?>
                                                </select></td>
                                            <?php }else {?>
                                            <td><select id="hotel_name1<?php echo $count_ht . "_h" ?>"
                                                    name="hotel_name1<?php echo $count_ht . "_h" ?>" style="width:150px"
                                                    title="Select Hotel Name" onchange="hotel_type_load_cate2(this.id);">
                                                    <option value="">Hotel Name</option>
                                                    <?php
                                                            $sq_hotel = mysqlQuery("select * from hotel_master where city_id = " . $row_hotel_acc['city_id']);
                                                            while ($rows =  mysqli_fetch_assoc($sq_hotel)) {
                                                                if ($row_hotel_acc['hotel_id'] == $rows['hotel_id']) {
                                                                    $selected = "selected";
                                                                } else {
                                                                    $selected = "";
                                                                }
                                                            ?>
                                                    <option value="<?php echo $rows['hotel_id'] ?>" <?= $selected ?>>
                                                        <?= $rows['hotel_name'] ?></option>
                                                    <?php } ?>
                                                </select></td>
                                            <?php }?>
                                            <td><input class="form-control app_datetimepicker" type="text"
                                                    id="txt_hotel_from_date<?php echo $count_ht . "_h" ?>"
                                                    placeholder="Check-In DateTime"
                                                    onchange="get_to_datetime(this.id,'txt_hotel_to_date<?php echo $count_ht . '_h' ?>')"
                                                    value="<?php echo date("d-m-Y H:i", strtotime($row_hotel_acc['from_date'])) ?>"
                                                    title="Check-In DateTime" style="width:170px"></td>
                                            <td><input class="form-control app_datetimepicker" type="text"
                                                    id="txt_hotel_to_date<?php echo $count_ht . "_h" ?>"
                                                    placeholder="Check-Out DateTime"
                                                    onchange="validate_validDatetime('txt_hotel_from_date<?php echo $count_ht . '_h' ?>' ,'txt_hotel_to_date<?php echo $count_ht . '_h' ?>')"
                                                    value="<?php echo date("d-m-Y H:i", strtotime($row_hotel_acc['to_date'])) ?>"
                                                    title="Check-Out DateTime" style="width:170px"></td>
                                            <td><input type="text" id="txt_room1" name="txt_room1" placeholder="Room(s)"
                                                    value="<?php echo $row_hotel_acc['rooms'] ?>" title="Room(s)"
                                                    style="width:110px"></td>
                                            <?php if($room_category_switch == 'No' ){?>
                                            <td><select name="txt_catagory1" id="txt_catagory1" title="Category"
                                                    class="form-control app_select2" style="width:180px">
                                                    <option value="<?= $row_hotel_acc['catagory'] ?>">
                                                        <?= $row_hotel_acc['catagory'] ?></option>
                                                    <?php get_room_category_dropdown(); ?>
                                                </select></td>
                                            <?php } else{?>
                                            <td><select name="txt_catagory1" id="txt_catagory<?php echo $count_ht . "_h" ?>" title="Category"
                                                    class="form-control app_select2" style="width:180px">
                                                    <option value="<?= $row_hotel_acc['catagory'] ?>">
                                                        <?= $row_hotel_acc['catagory'] ?></option>
                                                </select></td>
                                            <?php }?>
                                            <td><select title="Meal Plan" id="cmb_meal_plan<?= $count_ht ?>_t"
                                                    name="cmb_meal_plan" title="Meal Plan" style="width:130px">
                                                    <?php
                                                    if($row_hotel_acc['meal_plan'] != ''){ ?>
                                                        <option value="<?= $row_hotel_acc['meal_plan'] ?>"><?= $row_hotel_acc['meal_plan'] ?></option>
                                                    <?php } ?>
                                                    <?php get_mealplan_dropdown(); ?>
                                                </select></td>
                                            <td><input type="text" id="txt_extra_bed<?= $count_ht ?>_t" name="txt_extra_bed<?= $count_ht ?>_t" placeholder="Extra Bed(s)" title="Extra Bed(s)" value="<?= $row_hotel_acc['room_type'] ?>" style="width: 110px;"></td>
                                            <td><input type="text" id="txt_hotel_acm_confirmation_no<?= $count_ht ?>_t"
                                                    name="txt_hotel_acm_confirmation_no" placeholder="Confirmation no"
                                                    onchange=" validate_specialChar(this.id)"
                                                    value="<?= $row_hotel_acc['confirmation_no'] ?>"
                                                    title="Confirmation no" style="width:120px"></td>
                                            <td style="display:none"><input type="text"
                                                    value="<?php echo $row_hotel_acc['id'] ?>"></td>
                                        </tr>
                                        <script>
                                        $('#txt_hotel_from_date<?php echo $count_ht . "_h" ?>, #txt_hotel_to_date<?php echo $count_ht . "_h" ?>')
                                            .datetimepicker({
                                                format: 'd-m-Y H:i'
                                            });
                                        </script>
                                        <?php } ?>
                                    </table>
                                    <input type="hidden" id="txt_generate_hotel_acc_date"
                                        name="txt_generate_hotel_acc_date" value="<?php echo $count_ht ?>">
                                </div>
                            </div>
                        </div><?php } ?>
                    </div>
                    <div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                        <legend class="booking-section-heading main_block">Transport details</legend>
                        <?php
                        $count_tt = 0;
                        $sq_trans_entries = mysqli_num_rows(mysqlQuery("select * from package_tour_transport_master where booking_id='$booking_id'"));
                        if ($sq_trans_entries == 0) {
                            include_once("../booking_save/tab_3/transport_table_row.php");
                        } else { ?>
                        <div class="row" style="margin-top: 5px">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-excel btn-sm"
                                    onClick="addRow('tbl_package_transport_infomration','');destinationLoading('.pickup_from', 'Pickup Location');destinationLoading('.drop_to', 'Pickup Location');"
                                    title="Add Row"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="row mg_tp_10">
                            <div class="col-xs-12">
                                <div class="table-responsive">
                                    <table id="tbl_package_transport_infomration"
                                        class="table table-bordered table-hover pd_bt_51 table-striped no-marg"
                                        style="width: 1475px">
                                        <?php
                                            $sq_trans_acc = mysqlQuery("select * from package_tour_transport_master where booking_id='$booking_id'");
                                            while ($row_trans_acc = mysqli_fetch_assoc($sq_trans_acc)) {

                                                $count_tt++;
                                                $plabel = '';
                                                $dlabel = '';
                                                // Pickup
                                                if ($row_trans_acc['pickup_type'] == 'city') {
                                                    $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_trans_acc[pickup]'"));
                                                    $pickup = $row['city_name'];
                                                    $plabel = 'City Name';
                                                } else if ($row_trans_acc['pickup_type'] == 'hotel') {
                                                    $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_trans_acc[pickup]'"));
                                                    $pickup = $row['hotel_name'];
                                                    $plabel = 'Hotel Name';
                                                } else {
                                                    $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_trans_acc[pickup]'"));
                                                    $airport_nam = clean($row['airport_name']);
                                                    $airport_code = clean($row['airport_code']);
                                                    $pickup = $airport_nam . " (" . $airport_code . ")";
                                                    $plabel = 'Airport Name';
                                                }
                                                //Drop-off
                                                if ($row_trans_acc['drop_type'] == 'city') {
                                                    $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_trans_acc[drop]'"));
                                                    $drop = $row['city_name'];
                                                    $dlabel = 'City Name';
                                                } else if ($row_trans_acc['drop_type'] == 'hotel') {
                                                    $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_trans_acc[drop]'"));
                                                    $drop = $row['hotel_name'];
                                                    $dlabel = 'Hotel Name';
                                                } else {
                                                    $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_trans_acc[drop]'"));
                                                    $airport_nam = clean($row['airport_name']);
                                                    $airport_code = clean($row['airport_code']);
                                                    $drop = $airport_nam . " (" . $airport_code . ")";
                                                    $dlabel = 'Airport Name';
                                                }
                                            ?>
                                        <tr>
                                            <td><input id="check-btn-tr-acm-1" type="checkbox" checked disabled></td>
                                            <td><input maxlength="15" type="text" name="username"
                                                    value="<?= $count_tt ?>" placeholder="Sr. No." disabled /></td>
                                            <td><select name="vehicle_name1<?= $count_tt ?>-u"
                                                    id="vehicle_name1<?= $count_tt ?>-u" title="Vehicle Name"
                                                    style="width:200px">
                                                    <?php
                                                            $sq_transport = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_master where entry_id='$row_trans_acc[transport_bus_id]'"));
                                                            ?>
                                                    <option value="<?= $sq_transport['entry_id'] ?>">
                                                        <?= $sq_transport['vehicle_name'] ?></option>
                                                    <option value="">Select Vehicle</option>
                                                    <?php
                                                            $sq_transport_buses = mysqlQuery("select * from b2b_transfer_master where status!='Inactive' order by vehicle_name asc");
                                                            while ($row_transport_bus = mysqli_fetch_assoc($sq_transport_buses)) {
                                                            ?>
                                                    <option value="<?= $row_transport_bus['entry_id'] ?>">
                                                        <?= $row_transport_bus['vehicle_name'] ?></option>
                                                    <?php } ?>
                                                </select></td>
                                            <td><input type="text" id="txt_tsp_from_date<?= $count_tt ?>-u"
                                                    name="txt_tsp_from_date<?= $count_tt ?>-u" placeholder="Start Date/Time"
                                                    title="Start Date/Time" style="width: 170px"
                                                    value="<?= get_datetime_user($row_trans_acc['transport_from_date']) ?>"
                                                    class="form-control app_datepicker"
                                                    onchange="get_to_datetime(this.id,'txt_tsp_end_date<?= $count_tt ?>-u')">
                                            </td>
                                            <td><input type="text" id="txt_tsp_end_date<?= $count_tt ?>-u"
                                                    name="txt_tsp_end_date<?= $count_tt ?>-u" placeholder="End Date/Time"
                                                    title="End Date/Time" style="width: 170px"
                                                    value="<?= get_datetime_user($row_trans_acc['transport_end_date']) ?>"
                                                    class="form-control app_datepicker"
                                                    onchange="validate_validDatetime('txt_tsp_from_date<?= $count_tt ?>-u','txt_tsp_end_date<?= $count_tt ?>-u')">
                                            </td>
                                            <td><select name="pickup_from<?= $count_tt ?>-u"
                                                    id="pickup_from<?= $count_tt ?>-u" data-toggle="tooltip"
                                                    style="width:250px;" title="Pickup Location"
                                                    class="form-control app_select2 pickup_from">
                                                    <optgroup value='<?= $row_trans_acc['pickup_type'] ?>'
                                                        label="<?= $plabel ?>">
                                                        <option
                                                            value="<?= $row_trans_acc['pickup_type'] . '-' . $row_trans_acc['pickup'] ?>">
                                                            <?= $pickup ?></option>
                                                    </optgroup>
                                                </select></td>
                                            <td><select name="drop_to<?= $count_tt ?>-u" id="drop_to<?= $count_tt ?>-u"
                                                    style="width:250px;" data-toggle="tooltip" title="Drop-off Location"
                                                    class="form-control app_select2 drop_to">
                                                    <optgroup value='<?= $row_trans_acc['drop_type'] ?>'
                                                        label="<?= $dlabel ?>">
                                                        <option
                                                            value="<?= $row_trans_acc['drop_type'] . '-' . $row_trans_acc['drop'] ?>">
                                                            <?= $drop ?></option>
                                                    </optgroup>
                                                </select></td>
                                            <td><select name="duration-<?= $count_tt ?>-u" id="duration-<?= $count_tt ?>-u" style="width:170px;" title="*Service Duration" data-toggle="tooltip" class="form-control app_select2">
                                                <?php
                                                $row = mysqli_fetch_assoc(mysqlQuery("select entry_id,duration from service_duration_master where duration='$row_trans_acc[service_duration]'"));
                                                ?>
                                                <option value="<?= $row['entry_id'] ?>"><?= $row['duration'] ?></option>
                                                <option value="">*Service Duration</option>
                                                <?php echo get_service_duration_dropdown(); ?>
                                                </select></td>
                                            <td><input type="text" id="no_vehicles<?= $count_tt ?>-u"
                                                    name="no_vehicles<?= $count_tt ?>-u" placeholder="No.Of vehicles"
                                                    title="No.Of vehicles" style="width:150px"
                                                    value="<?= $row_trans_acc['vehicle_count'] ?>"></td>
                                            <td style="display:none"><input type="text" value="<?php echo $row_trans_acc['entry_id'] ?>"></td>
                                        </tr>
                                        <script>
                                        $("#txt_tsp_from_date<?= $count_tt ?>-u,#txt_tsp_end_date<?= $count_tt ?>-u,#txt_tsp_to_date<?= $count_tt ?>-u")
                                            .datetimepicker({
                                                timepicker: true,
                                                format: "d-m-Y H:i"
                                            });
                                            $('#duration-<?= $count_tt ?>-u').select2();
                                        </script>
                                        <?php } ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                        <legend class="booking-section-heading main_block">Activity details</legend>
                        <?php
                        $count_et = 0;
                        $sq_exc_entries = mysqli_num_rows(mysqlQuery("select * from package_tour_excursion_master where booking_id='$booking_id'"));
                        if ($sq_exc_entries == 0) {
                            include_once("../booking_save/tab_3/excursion_table_row.php");
                        } else { ?>
                        <div class="row" style="margin-top: 5px">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-excel btn-sm"
                                    onClick="addRow('tbl_package_exc_infomration');city_lzloading('.city_name');"
                                    title="Add Row"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="row mg_tp_10">
                            <div class="col-xs-12">
                                <div class="table-responsive">
                                    <table id="tbl_package_exc_infomration"
                                        class="table table-bordered table-hover pd_bt_51 table-striped no-marg"
                                        style="width: 1140px;">
                                        <?php
                                            $sq_exc_acc = mysqlQuery("select * from package_tour_excursion_master where booking_id='$booking_id'");
                                            while ($row_exc_acc = mysqli_fetch_assoc($sq_exc_acc)) {
                                                $count_et++;
                                                $sq_ex = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_tariff where entry_id='$row_exc_acc[exc_id]'"));
                                            ?>
                                        <tr>
                                            <td><input id="check-btn-exc" type="checkbox" checked disabled></td>
                                            <td><input maxlength="15" type="text" name="username"
                                                    value="<?= $count_et ?>" placeholder="Sr. No." disabled /></td>
                                            <td><input type="text" id="exc_date-1<?= $count_et ?>"
                                                    name="exc_date-1<?= $count_et ?>" placeholder="Activity Date & Time"
                                                    title="Activity Date & Time" class="app_datetimepicker"
                                                    value="<?= get_datetime_user($row_exc_acc['exc_date']) ?>"
                                                    style="width:200px"></td>
                                            <td><select id="city_name-1<?= $count_et ?>" class="form-control city_name"
                                                    name="city_name-1<?= $count_et ?>" title="City Name"
                                                    style="width:100%" onchange="get_excursion_list(this.id);">
                                                    <?php
                                                            $sq_transport = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_exc_acc[city_id]'"));
                                                            ?>
                                                    <option value="<?= $sq_transport['city_id'] ?>">
                                                        <?= $sq_transport['city_name'] ?></option>
                                                </select>
                                            </td>
                                            <td><select id="excursion-1<?= $count_et ?>" class="form-control"
                                                    title="Activity Name" name="excursion-1<?= $count_et ?>"
                                                    style="width:200px">
                                                    <option value="">*Activity Name</option>

                                                    <?php
                                                            $sq_exc = mysqlQuery("select * from excursion_master_tariff where city_id = " . $row_exc_acc['city_id']);
                                                            while ($rows =  mysqli_fetch_assoc($sq_exc)) {
                                                                if ($row_exc_acc['exc_id'] == $rows['entry_id']) {
                                                                    $selected = "selected";
                                                                } else {
                                                                    $selected = "";
                                                                }
                                                            ?>

                                                    <option value="<?php echo $rows['entry_id'] ?>" <?= $selected ?>>
                                                        <?php echo $rows['excursion_name'] ?></option>
                                                    <?php } ?>
                                                </select></td>
                                            <td><select name="transfer_option-1<?= $count_et ?>"
                                                    id="transfer_option-1<?= $count_et ?>" data-toggle="tooltip"
                                                    class="form-control app_select2" title="Transfer Option"
                                                    style="width:200px">
                                                    <option value="<?= $row_exc_acc['transfer_option'] ?>">
                                                        <?= $row_exc_acc['transfer_option'] ?></option>
                                                    <option value="Private Transfer">Private Transfer</option>
                                                    <option value="Without Transfer">Without Transfer</option>
                                                    <option value="Sharing Transfer">Sharing Transfer</option>
                                                    <option value="SIC">SIC</option>
                                                </select></td>
                                            <td><input type="number" id="adult-1<?= $count_et ?>"
                                                    name="adult-1<?= $count_et ?>" placeholder="Adult(s)"
                                                    title="Adult(s)" style="width:100px"
                                                    onchange="validate_balance(this.id);"
                                                    value="<?= $row_exc_acc['adult'] ?>">
                                            </td>
                                            <td><input type="number" id="child-1<?= $count_et ?>"
                                                    name="child-1<?= $count_et ?>" placeholder="Child With-Bed"
                                                    title="Child With-Bed" style="width:150px"
                                                    onchange="validate_balance(this.id);"
                                                    value="<?= $row_exc_acc['chwb'] ?>">
                                            </td>
                                            <td><input type="number" id="childwo-1<?= $count_et ?>"
                                                    name="childwo-1<?= $count_et ?>" placeholder="Child Without-Bed"
                                                    title="Child Without-Bed" style="width:150px"
                                                    onchange="validate_balance(this.id);"
                                                    value="<?= $row_exc_acc['chwob'] ?>">
                                            </td>
                                            <td><input type="number" id="infant-1<?= $count_et ?>"
                                                    name="infant-1<?= $count_et ?>" placeholder="Infant(s)"
                                                    title="Infant(s)" style="width:100px"
                                                    onchange="validate_balance(this.id);"
                                                    value="<?= $row_exc_acc['infant'] ?>"></td>
                                            <td style="display:none"><input type="text"
                                                    value="<?php echo $row_exc_acc['entry_id'] ?>"></td>
                                        </tr>
                                        <script>
                                        $('#city_name-1<?= $count_et ?>').select2();
                                        $('#exc_date-1<?= $count_et ?>').datetimepicker({
                                            format: 'd-m-Y H:i'
                                        });
                                        </script>
                                        <?php } ?>
                                    </table>
                                </div>
                            </div>
                        </div>


                        <?php } ?>

                        <div class="panel panel-default main_block bg_light pad_8 text-center mg_bt_150" style="background-color: #fff; border: none;">
                            <div class="text-center">
                                <div class="col-xs-12">
                                    <button class="btn btn-sm btn-info ico_left" type="button"
                                        onclick="back_to_tab_2()"><i
                                            class="fa fa-arrow-left"></i>&nbsp;&nbsp;Previous</button>&nbsp;&nbsp;&nbsp;
                                    <button class="btn btn-sm btn-info ico_right"
                                        onclick="calculate_train_expense('tbl_train_travel_details_dynamic_row');calculate_cruise_expense('tbl_dynamic_cruise_package_booking');calculate_plane_expense('tbl_plane_travel_details_dynamic_row')">Next&nbsp;&nbsp;<i
                                            class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</form>

<?= end_panel() ?>
<script>
$('#transport_bus_id').select2();
$(document).ready(function() {
    city_lzloading('.city_name');
    destinationLoading(".pickup_from", 'Pickup Location');
    destinationLoading(".drop_to", 'Drop-off Location');
})

function generating_hotel_acc_date() {
    var count = $("#txt_generate_hotel_acc_date").val();
    for (var i = 0; i <= count; i++) {
        $("#txt_hotel_from_date" + i + "_h").datetimepicker({
            format: "d-m-Y H:i"
        });
        $("#txt_hotel_to_date" + i + "_h").datetimepicker({
            format: "d-m-Y H:i"
        });

        $("#city_name1" + i + "_h").select2();
    }
}
generating_hotel_acc_date();

function disabled_transport_details(id) {
    var id = $('#transport_agency_id').val();
    if (id != 'N/A') {
        $("#transport_bus_id").prop({
            disabled: '',
            value: ''
        });
        $('#txt_tsp_from_date').prop({
            disabled: '',
            value: ''
        });
        $("#txt_tsp_to_date").prop({
            disabled: '',
            value: ''
        });
        $('#txt_tsp_total_amount').prop({
            disabled: '',
            value: ''
        });
    } else {
        $("#transport_bus_id").prop({
            disabled: 'disabled',
            value: ''
        });
        $('#txt_tsp_from_date').prop({
            disabled: 'disabled',
            value: ''
        });
        $("#txt_tsp_to_date").prop({
            disabled: 'disabled',
            value: ''
        });
        $('#txt_tsp_total_amount').prop({
            disabled: 'disabled',
            value: ''
        });
    }
}

/**Hotel Name load start**/
function hotel_name_list_load1(id) {
    var city_id = $("#" + id).val();
    var count = id.substring(10);
    $.get("../../booking/inc/hotel_name_load.php", {
        city_id: city_id
    }, function(data) {
        $("#hotel_name1" + count).html(data);
    });
}
//roomcategory load
function hotel_type_load_cate2(id)
{
  var hotel_id = $("#"+id).val();
  var count = id.substring(11);
  $.get( "../../booking/inc/hotel_category.php" , { hotel_id : hotel_id } , function ( data ) {
        $ ("#txt_catagory"+count).html( data ) ;  
  } ) ;   
}
</script>

<script src="../js/tab_3.js"></script>