<?php
include "../../../model.php";
include "../print_functions.php";
require("../../../../classes/convert_amount_to_word.php");
global $currency_code, $currency;

$booking_id = $_GET['booking_id'];
$quotation_id = $_GET['quotation_id'];
$branch_status = $_GET['branch_status'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$year = $_GET['year'];

$credit_card_charges = $_GET['credit_card_charges'];
$charge = ($credit_card_charges != '') ? $credit_card_charges : 0;

$package_booking_info = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_booking_master where booking_id='$booking_id' "));
$branch_admin_id = ($_SESSION['branch_admin_id'] != '') ? $_SESSION['branch_admin_id'] : $package_booking_info['branch_admin_id'];
$branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_admin_id'"));

$sq_terms_cond = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Package Sale' and active_flag ='Active'"));
$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id'"));

$inclusions = ($package_booking_info['quotation_id'] == 0) ? $package_booking_info['inclusions'] : $sq_quotation['inclusions'];
$exclusions = ($package_booking_info['quotation_id'] == 0) ? $package_booking_info['exclusions'] : $sq_quotation['exclusions'];

$tour_name = $package_booking_info['tour_name'];
$from_date = date("d-m-Y", strtotime($package_booking_info['tour_from_date']));
$to_date = date("d-m-Y", strtotime($package_booking_info['tour_to_date']));
//Total days
$total_days1 = strtotime($package_booking_info['tour_to_date']) - strtotime($package_booking_info['tour_from_date']);
$total_days = round($total_days1 / 86400);

$_SESSION['generated_by'] = $app_name;
$booking_date = get_datetime_user($package_booking_info['booking_date']);

$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id = '$package_booking_info[customer_id]'"));
if ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') {
    $customer_name = $sq_customer['company_name'];
} else {
    $customer_name = $sq_customer['first_name'] . ' ' . $sq_customer['middle_name'] . ' ' . $sq_customer['last_name'];
}

$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id= '$package_booking_info[emp_id]'"));
$booker_name1 = $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];
if ($package_booking_info['emp_id'] == '0') {
    $booker_name = 'Admin';
} else {
    $booker_name = $booker_name1;
}
if($package_booking_info['quotation_id'] == 0){
    $sq_total_members = mysqli_num_rows(mysqlQuery("select traveler_id from package_travelers_details where booking_id='$booking_id'"));
}else{
    $sq_total_members = $sq_quotation['total_passangers'];
}

$roundoff = $package_booking_info['roundoff'];
$basic_cost1 = $package_booking_info['basic_amount'];
$service_charge = $package_booking_info['service_charge'];
$net_amount = $package_booking_info['net_total'];
$bsmValues = json_decode($package_booking_info['bsm_values']);

$tax_show = '';
$newBasic = $basic_cost1;
$name = '';
//////////////////Service Charge Rules
$service_tax_amount = 0;
if ($package_booking_info['tour_service_tax_subtotal'] !== 0.00 && ($package_booking_info['tour_service_tax_subtotal']) !== '') {
    $service_tax_subtotal1 = explode(',', $package_booking_info['tour_service_tax_subtotal']);
    for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
        $service_tax = explode(':', $service_tax_subtotal1[$i]);
        $service_tax_amount +=  $service_tax[2];
        $name .= $service_tax[0]  . $service_tax[1] . ', ';
    }
}
$service_tax_amount_show = currency_conversion($currency, $package_booking_info['currency_code'], $service_tax_amount);

if ($bsmValues[0]->service != '') {   //inclusive service charge
    $newBasic = $basic_cost1;
    $newSC = $service_tax_amount + $service_charge;
} else {
    // $tax_show = $service_tax_amount;
    $tax_show =  rtrim($name, ', ') . ' : ' . $currency_code . ' ' . ($service_tax_amount);
    $newSC = $service_charge;
}

////////////Basic Amount Rules
if ($bsmValues[0]->basic != '') { //inclusive basic

    $tax_show = '';
} else {
}
$net_amount1 = currency_conversion($currency, $package_booking_info['currency_code'], $net_amount);
?>

<!-- header -->
<section class="print_header main_block" style="margin-bottom: 0 !important;">
    <div class="col-md-6 no-pad">
        <span class="title"><i class="fa fa-file-text"></i> CONFIRMATION FORM</span>
        <div class="print_header_logo">
            <img src="<?php echo $admin_logo_url; ?>" class="img-responsive mg_tp_10">
        </div>
    </div>
    <div class="col-md-6 no-pad">
        <div class="print_header_contact text-right">
            <span class="title"><?php echo $app_name; ?></span><br>
            <p><?php echo ($branch_status == 'yes') ? $branch_details['address1'] . ',' . $branch_details['address2'] . ',' . $branch_details['city'] : $app_address ?>
            </p>
            <p class="no-marg"><i class="fa fa-phone" style="margin-right: 5px;"></i>
                <?php echo ($branch_status == 'yes') ?
                    $branch_details['contact_no'] : $app_contact_no ?></p>
            <p><i class="fa fa-envelope" style="margin-right: 5px;"></i>
                <?php echo ($branch_status == 'yes' && $branch_details['email_id'] != '') ? $branch_details['email_id'] : $app_email_id;; ?>
            </p>
        </div>
    </div>
</section>

<!-- print-detail -->
<section class="print_sec main_block">
    <div class="row">
        <div class="col-md-12">
            <div class="print_info_block">
                <ul class="main_block noType">
                    <li class="col-md-3 mg_tp_10 mg_bt_10">
                        <div class="print_quo_detail_block">
                            <i class="fa fa-calendar" aria-hidden="true"></i><br>
                            <span>BOOKING DATE</span><br>
                            <?= $booking_date ?><br>
                        </div>
                    </li>
                    <li class="col-md-3 mg_tp_10 mg_bt_10">
                        <div class="print_quo_detail_block">
                            <i class="fa fa-hourglass-half" aria-hidden="true"></i><br>
                            <span>DURATION</span><br>
                            <?php echo ($total_days) . 'N/' . ($total_days + 1) . 'D'; ?><br>
                        </div>
                    </li>
                    <li class="col-md-3 mg_tp_10 mg_bt_10">
                        <div class="print_quo_detail_block">
                            <i class="fa fa-users" aria-hidden="true"></i><br>
                            <span>TOTAL GUEST (s)</span><br>
                            <?php echo $sq_total_members; ?><br>
                        </div>
                    </li>
                    <li class="col-md-3 mg_tp_10 mg_bt_10">
                        <div class="print_quo_detail_block">
                            <i class="fa fa-tags" aria-hidden="true"></i><br>
                            <span>PRICE</span><br>
                            <?= $net_amount1 ?><br>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Package -->
<section class="print_sec main_block">
    <div class="section_heding">
        <h2>BOOKING DETAILS</h2>
        <div class="section_heding_img">
            <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mg_bt_20">
            <ul class="print_info_list no-pad noType">
                <li><span>TOUR :</span> <?= $tour_name ?> </li>
                <li><span>CUSTOMER :</span> <?= $customer_name ?></li>
                <li><span>CONTACT :</span> <?= $package_booking_info['mobile_no'] ?></li>
            </ul>
        </div>
        <div class="col-md-12 mg_bt_20">
            <ul class="print_info_list no-pad noType">
                <li><span>TOUR DATE :</span> <?= $from_date . ' To ' . $to_date ?></li>
                <li><span>BOOKING ID :</span> <?= get_package_booking_id($booking_id, $year) ?></li>
                <?php if ($package_booking_info['package_type'] != '') { ?>
                    <li><span>PACKAGE TYPE :</span> <?= $package_booking_info['package_type'] ?></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</section>


<!-- Passenger -->
<section class="print_sec main_block">
    <div class="section_heding">
        <h2>PASSENGERS</h2>
        <div class="section_heding_img">
            <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered no-marg" id="tbl_emp_list">
                    <thead>
                        <tr class="table-heading-row">
                            <th>Full_Name</th>
                            <th>Gender</th>
                            <th>DOB</th>
                            <th>Age</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sq_members1 = mysqlQuery("select * from package_travelers_details where booking_id = '$booking_id'");
                        while ($row_members1 = mysqli_fetch_assoc($sq_members1)) { ?>
                            <tr>
                                <td><?php echo $row_members1['first_name'] . ' ' . $row_members1['middle_name'] . ' ' . $row_members1['last_name']; ?>
                                </td>
                                <td><?php echo $row_members1['gender']; ?></td>
                                <td><?php echo date("d-m-Y", strtotime($row_members1['birth_date'])); ?></td>
                                <td><?php echo $row_members1['age']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Accommodation -->
<?php
$sq_count = mysqli_num_rows(mysqlQuery("select * from package_hotel_accomodation_master where booking_id='$booking_id'"));
if ($sq_count != 0) {
?>
    <section class="print_sec main_block">
        <div class="section_heding">
            <h2>ACCOMMODATION</h2>
            <div class="section_heding_img">
                <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered no-marg" id="tbl_emp_list">
                        <thead>
                            <tr class="table-heading-row">
                                <th>City</th>
                                <th>Hotel_NAME</th>
                                <th>Check_In</th>
                                <th>Check_Out</th>
                                <th>Rooms</th>
                                <th>Category</th>
                                <th>Extra_Bed</th>
                                <th>Meal_plan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sq_entry = mysqlQuery("select * from package_hotel_accomodation_master where booking_id='$booking_id'");
                            while ($row_entry = mysqli_fetch_assoc($sq_entry)) {
                                $city_id = $row_entry['city_id'];
                                $hotel_id = $row_entry['hotel_id'];
                                $sq_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$city_id'"));
                                $sq_hotel_name = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$hotel_id'"));
                            ?>
                                <tr>
                                    <td><?php echo $sq_city['city_name']; ?></td>
                                    <td><?php echo $sq_hotel_name['hotel_name'] . $similar_text; ?></td>
                                    <td><?php echo get_datetime_user($row_entry['from_date']); ?></td>
                                    <td><?php echo get_datetime_user($row_entry['to_date']); ?></td>
                                    <td><?php echo $row_entry['rooms']; ?></td>
                                    <td><?php echo $row_entry['catagory']; ?></td>
                                    <td><?php echo $row_entry['room_type']; ?></td>
                                    <td><?php echo $row_entry['meal_plan']; ?></td>
                                </tr>
                            <?php
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php } ?>


<!-- transport -->
<?php
$sq_count = mysqli_num_rows(mysqlQuery("select * from package_tour_transport_master where booking_id='$booking_id'"));
if ($sq_count != 0) {
?>
    <section class="print_sec main_block">
        <div class="section_heding">
            <h2>TRANSPORT</h2>
            <div class="section_heding_img">
                <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered no-marg" id="tbl_emp_list">
                        <thead>
                            <tr class="table-heading-row">
                                <th>Vehicle</th>
                                <th>Start_Date</th>
                                <th>End_Date</th>
                                <th>Pickup</th>
                                <th>Drop</th>
                                <th>S_Duration</th>
                                <th>Vehicles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sq_entry = mysqlQuery("select * from package_tour_transport_master where booking_id='$booking_id'");
                            while ($row_entry = mysqli_fetch_assoc($sq_entry)) {
                                $q_transport = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_master where entry_id='$row_entry[transport_bus_id]'"));
                                // Pickup
                                if ($row_entry['pickup_type'] == 'city') {
                                    $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_entry[pickup]'"));
                                    $pickup = $row['city_name'];
                                } else if ($row_entry['pickup_type'] == 'hotel') {
                                    $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_entry[pickup]'"));
                                    $pickup = $row['hotel_name'];
                                } else {
                                    $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_entry[pickup]'"));
                                    $airport_nam = clean($row['airport_name']);
                                    $airport_code = clean($row['airport_code']);
                                    $pickup = $airport_nam . " (" . $airport_code . ")";
                                }
                                //Drop-off
                                if ($row_entry['drop_type'] == 'city') {
                                    $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_entry[drop]'"));
                                    $drop = $row['city_name'];
                                } else if ($row_entry['drop_type'] == 'hotel') {
                                    $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_entry[drop]'"));
                                    $drop = $row['hotel_name'];
                                } else {
                                    $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_entry[drop]'"));
                                    $airport_nam = clean($row['airport_name']);
                                    $airport_code = clean($row['airport_code']);
                                    $drop = $airport_nam . " (" . $airport_code . ")";
                                }
                            ?>
                                <tr>
                                    <td><?= $q_transport['vehicle_name'] . $similar_text ?></td>
                                    <td><?= get_datetime_user($row_entry['transport_from_date']) ?></td>
                                    <td><?= get_datetime_user($row_entry['transport_end_date']) ?></td>
                                    <td><?= $pickup ?></td>
                                    <td><?= $drop ?></td>
                                    <td><?= $row_entry['service_duration'] ?></td>
                                    <td><?= $row_entry['vehicle_count'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<!-- Activity -->
<?php
$sq_count = mysqli_num_rows(mysqlQuery("select * from package_tour_excursion_master where booking_id='$booking_id'"));
if ($sq_count != 0) {
?>
    <section class="print_sec main_block">
        <div class="section_heding">
            <h2>Activity</h2>
            <div class="section_heding_img">
                <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered no-marg" id="tbl_emp_list">
                        <thead>
                            <tr class="table-heading-row">
                                <th>Activity_date</th>
                                <th>City_Name</th>
                                <th>Activity_name</th>
                                <th>Transfer_option</th>
                                <th>Adult(s)</th>
                                <th>CWB</th>
                                <th>CWOB</th>
                                <th>Infant(s)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sq_entry = mysqlQuery("select * from package_tour_excursion_master where booking_id='$booking_id'");
                            while ($row_entry = mysqli_fetch_assoc($sq_entry)) {
                                $q_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_entry[city_id]'"));
                                $sq_ex = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_tariff where entry_id='$row_entry[exc_id]'"));
                            ?>
                                <tr>
                                    <td><?php echo get_datetime_user($row_entry['exc_date']) ?></td>
                                    <td><?= $q_city['city_name'] ?></td>
                                    <td><?= $sq_ex['excursion_name'] ?></td>
                                    <td><?= $row_entry['transfer_option'] ?> </td>
                                    <td><?= $row_entry['adult'] ?> </td>
                                    <td><?= $row_entry['chwb'] ?> </td>
                                    <td><?= $row_entry['chwob'] ?> </td>
                                    <td><?= $row_entry['infant'] ?> </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php } ?>


<?php
//Train
$sq_train = mysqli_num_rows(mysqlQuery("select booking_id from package_train_master where booking_id='$booking_id'"));
$sq_air = mysqli_num_rows(mysqlQuery("select booking_id from package_plane_master where booking_id='$booking_id'"));
if ($sq_train > 0 || $sq_air > 0) {
    $sq_train = mysqli_num_rows(mysqlQuery("select booking_id from package_train_master where booking_id='$booking_id'"));
    $train_count = 0;

    if ($sq_train > 0) { ?>
        <section class="print_sec main_block">
            <div class="section_heding">
                <h2>Train</h2>
                <div class="section_heding_img">
                    <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered no-marg" id="tbl_emp_list">
                            <thead>
                                <tr class="table-heading-row">
                                    <th>From</th>
                                    <th>To</th>
                                    <th>TRAIN</th>
                                    <th>SEATS</th>
                                    <th>CLASS</th>
                                    <th>PRIORITY</th>
                                    <th>DEPARTURE D/T</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sq_train_details = mysqlQuery("select * from package_train_master where booking_id='$booking_id'");
                                while ($row_train_details = mysqli_fetch_assoc($sq_train_details)) { ?>
                                    <tr>
                                        <td><?php echo $row_train_details['from_location']; ?></td>
                                        <td><?php echo $row_train_details['to_location']; ?></td>
                                        <td><?php echo $row_train_details['train_no']; ?></td>
                                        <td><?php echo $row_train_details['seats']; ?></td>
                                        <td><?php echo $row_train_details['train_class']; ?></td>
                                        <td><?php echo $row_train_details['train_priority']; ?></td>
                                        <td><?php echo get_datetime_user($row_train_details['date']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
<?php }
} ?>

<?php
//Flight
$sq_air = mysqli_num_rows(mysqlQuery("select booking_id from package_plane_master where booking_id='$booking_id'"));
$air_count = 0;

if ($sq_air > 0) { ?>
    <section class="print_sec main_block">
        <div class="section_heding">
            <h2>Flight</h2>
            <div class="section_heding_img">
                <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered no-marg" id="tbl_emp_list">
                        <thead>
                            <tr class="table-heading-row">
                                <th>DEPARTURE D/T</th>
                                <th>ARRIVAL D/T</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Airline</th>
                                <th>Class</th>
                                <th>SEATS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sq_air_details = mysqlQuery("select * from package_plane_master where booking_id='$booking_id'");
                            while ($row_air_details = mysqli_fetch_assoc($sq_air_details)) {
                                $sq_airline = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$row_air_details[company]'")); ?>
                                <tr>
                                    <td><?php echo get_datetime_user($row_air_details['date']); ?></td>
                                    <td><?php echo get_datetime_user($row_air_details['arraval_time']); ?></td>
                                    <td><?php echo $row_air_details['from_location']; ?></td>
                                    <td><?php echo $row_air_details['to_location']; ?></td>
                                    <td><?php echo $sq_airline['airline_name'] . ' (' . $sq_airline['airline_code'] . ')'; ?>
                                    </td>
                                    <td><?php echo $row_air_details['class']; ?></td>
                                    <td><?php echo $row_air_details['seats']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<?php
//Cruise
$sq_cruise = mysqli_num_rows(mysqlQuery("select booking_id from package_cruise_master where booking_id='$booking_id'"));
if ($sq_cruise > 0) { ?>
    <section class="print_sec main_block">
        <div class="section_heding">
            <h2>Cruise</h2>
            <div class="section_heding_img">
                <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered no-marg" id="tbl_emp_list">
                        <thead>
                            <tr class="table-heading-row">
                                <th>DEPARTURE D/T</th>
                                <th>ARRIVAL D/T</th>
                                <th>ROUTE</th>
                                <th>CABIN</th>
                                <th>SHARING</th>
                                <th>SEATS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sq_cruise_details = mysqlQuery("select * from package_cruise_master where booking_id='$booking_id'");
                            while ($row_cruise_details = mysqli_fetch_assoc($sq_cruise_details)) { ?>
                                <tr>
                                    <td><?php echo get_datetime_user($row_cruise_details['dept_datetime']); ?></td>
                                    <td><?php echo get_datetime_user($row_cruise_details['arrival_datetime']); ?></td>
                                    <td><?php echo $row_cruise_details['route']; ?></td>
                                    <td><?php echo $row_cruise_details['cabin']; ?></td>
                                    <td><?php echo $row_cruise_details['sharing']; ?></td>
                                    <td><?php echo $row_cruise_details['seats']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<!-- Inclusion -->
<section class="print_sec main_block">
    <div class="row">
        <div class="col-md-12">
            <div class="section_heding">
                <h2>Inclusions</h2>
                <div class="section_heding_img">
                    <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                </div>
            </div>
            <div class="print_text_bolck">
                <?php echo $inclusions; ?>
            </div>
        </div>
    </div>
</section>


<!-- Exclusion -->
<section class="print_sec main_block">
    <div class="row">
        <div class="col-md-12">
            <div class="section_heding">
                <h2>Exclusions</h2>
                <div class="section_heding_img">
                    <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                </div>
            </div>
            <div class="print_text_bolck">
                <?php echo $exclusions; ?>
            </div>
        </div>
    </div>
</section>

<?php
if (isset($sq_terms_cond['terms_and_conditions'])) { ?>
    <!-- Terms and Conditions -->
    <section class="print_sec main_block">
        <div class="row">
            <div class="col-md-12">
                <div class="section_heding">
                    <h2>Terms and Conditions</h2>
                    <div class="section_heding_img">
                        <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                    </div>
                </div>
                <div class="print_text_bolck">
                    <span><?= $sq_terms_cond['terms_and_conditions'] ?></span>
                </div>
            </div>
        </div>
    </section>
<?php } ?>


<!-- Booking Summary -->
<section class="print_sec main_block">
    <div class="row">
        <div class="col-md-12">
            <div class="section_heding">
                <h2>Booking Summary</h2>
                <div class="section_heding_img">
                    <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
                </div>
            </div>
            <div class="row">
                <div class="col-md-7 mg_bt_20">
                    <ul class="print_info_list no-pad noType">
                        <li><span>BOOKING DATE :</span> <?= get_datetime_user($package_booking_info['booking_date']) ?>
                        </li>
                    </ul>
                </div>
                <div class="col-md-5 mg_bt_20">
                    <ul class="print_info_list no-pad noType">
                        <li><span>DUE DATE :</span>
                            <?php echo ($package_booking_info['due_date'] != '1970-01-01') ? get_date_user($package_booking_info['due_date']) : get_date_user($package_booking_info['tour_to_date']); ?>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="print_text_bolck">
                        <span><?= $package_booking_info['special_request'] ?></span>
                    </div>
                </div>
            </div>
</section>

<!-- Payment Detail -->
<?php

$total_hotel_expense = ($package_booking_info['total_hotel_expense'] != "") ? $package_booking_info['total_hotel_expense'] : 0;
$total_travel_expense = ($package_booking_info['total_travel_expense'] != "") ? $package_booking_info['total_travel_expense'] : 0;

$newBasic1 = currency_conversion($currency, $package_booking_info['currency_code'], ($package_booking_info['subtotal']));
$charge1 = currency_conversion($currency, $package_booking_info['currency_code'], $charge);
$tcs_tax = currency_conversion($currency, $package_booking_info['currency_code'], $package_booking_info['tcs_tax']);
$roundoff = currency_conversion($currency, $package_booking_info['currency_code'], $roundoff);
$tds = currency_conversion($currency, $package_booking_info['currency_code'], $package_booking_info['tds']);

$tcs_show=currency_conversion($currency, $package_booking_info['currency_code'],$package_booking_info['tcs_per']);
?>
<section class="print_sec main_block">
    <div class="section_heding">
        <h2>PAYMENT DETAILS</h2>
        <div class="section_heding_img">
            <img src="<?php echo BASE_URL . 'images/heading_border.png'; ?>" class="img-responsive">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="print_amount_block">
                <ul class="main_block no-pad text-right noType">
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>BASIC AMOUNT : </span><?php echo $newBasic1; ?></li>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>ROUNDOFF : </span><?= $roundoff ?></li>
                </ul>
                <ul class="main_block no-pad text-right noType">
                </ul>
                <ul class="main_block no-pad text-right noType">
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>TAX :
                        </span><?php echo str_replace(',', '', $name) . $service_tax_amount_show; ?></li>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>NET AMOUNT : </span><?php echo $net_amount1; ?></li>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>TCS<?= '(' .$tcs_tax  . '%)' ?>
                            : </span><?= $tcs_show ?></li>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>CREDIT CARD CHARGES : </span><?= $charge1 ?></li>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>TDS : </span><?= $tds ?></li>
                    <li class="col-md-6 mg_tp_10 mg_bt_10"><span>DUE DATE :
                        </span><?php echo ($package_booking_info['due_date'] != '1970-01-01') ? get_date_user($package_booking_info['due_date']) : get_date_user($package_booking_info['tour_to_date']); ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-6">
            <div class="print_amount_block">
                <ul class="main_block no-pad text-right noType">
                    <li class="col-md-12 mg_tp_10 mg_bt_10 font_5"><span>TOTAL AMOUNT :
                        </span><?php echo $net_amount1; ?></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 text-center">
            <div class="print_quotation_creator">
                <span>CUSTOMER'S SIGNATURE</span><br>
            </div>
        </div>
        <div class="col-md-7 text-right">
            <div class="print_quotation_creator text-center">
                <span>BOOKED BY </span><br><?php echo $booker_name; ?>
            </div>
        </div>
    </div>
</section>
</body>

</html>