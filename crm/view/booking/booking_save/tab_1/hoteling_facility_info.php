<legend>Accommodation Details</legend>
<div class="row text-center mg_bt_20">

    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
        <label>Total Passengers</label>
        <input type="text" id="txt_stay_total_seats" value="0" name="txt_stay_total_seats" title="Total Passenger"
            readonly />
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
        <label>Triple Bed Room</label>
        <select id="txt_single_bed_room" title="Single Bed Room"
            onchange="payment_details_reflected_data('tbl_member_dynamic_row')" name="txt_single_bed_room">
            <?php
            for ($i = 0; $i <= 20; $i++) {
                echo '<option value="' . $i . '">' . $i . '</option>';
            }
            ?>
        </select>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
        <label>Double Bed Room</label>
        <select id="txt_double_bed_room" title="Double Bed Room"
            onchange="payment_details_reflected_data('tbl_member_dynamic_row')" name="txt_double_bed_room">
            <?php
            for ($i = 0; $i <= 20; $i++) {
                echo '<option value="' . $i . '">' . $i . '</option>';
            }
            ?>
        </select>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
        <label>Extra Bed</label>
        <select id="txt_extra_bed" name="txt_extra_bed" title="Extra Bed"
            onchange="payment_details_reflected_data('tbl_member_dynamic_row')">
            <?php
            for ($i = 0; $i <= 20; $i++) {
                echo '<option value="' . $i . '">' . $i . '</option>';
            }
            ?>
        </select>
    </div>

    <div class="col-md-2 col-sm-6 col-xs-12 hidden">
        <label>On floor</label>
        <select id="txt_on_floor" name="txt_on_floor" title="On floor"
            onchange="payment_details_reflected_data('tbl_member_dynamic_row')">
            <?php
            for ($i = 0; $i <= 20; $i++) {
                echo '<option value="' . $i . '">' . $i . '</option>';
            }
            ?>
        </select>
    </div>

</div>

<script src="../js/tab_1_hoteling_facility.js"></script>