<form id="frm_tab4">

    <div class="app_panel">

        <div class="container">
            <div class="row">
                <div class="col-md-12 app_accordion">
                    <div class="panel-group main_block" id="accordion" role="tablist" aria-multiselectable="true">

                        <!-- Accordian-1 Start --><!-- Group Costing -->
                        <div class="accordion_content main_block mg_bt_20">

                            <div class="panel panel-default main_block">
                                <div class="panel-heading main_block" role="tab" id="headingl1">
                                    <div class="Normal main_block" role="button" data-toggle="collapse"
                                        data-parent="#accordion" href="#collapsel1" aria-expanded="true"
                                        aria-controls="collapsel1" id="collapsedl1">
                                        <div class="col-md-12"><span>Group Costing</span></div>
                                    </div>
                                </div>
                                <div id="collapsel1" class="panel-collapse collapse in main_block" role="tabpanel"
                                    aria-labelledby="headingl1">
                                    <div class="panel-body">

                                        <div class="row">

                                            <div class="col-xs-12">
                                                <h3 class="editor_title">Land Cost</h3>
                                                <div class="panel panel-default panel-body app_panel_style">
                                                    <div class="row mg_bt_20_sm_xs">
                                                        <div class="col-xs-12">

                                                            <div class="table-responsive">
                                                                <table id="tbl_package_tour_quotation_dynamic_costing"
                                                                    name="tbl_package_tour_quotation_dynamic_costing" class="table no-marg border_0"
                                                                    disabled>
                                                                    <?php
                                                                    $count = 0;
                                                                    $sq_q_costing = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id' ");
                                                                    while ($row_q_costing = mysqli_fetch_assoc($sq_q_costing)) {

                                                                        $count++;

                                                                        $add_class1 = '';
                                                                        if ($role == 'B2b') {
                                                                            $add_class1 = "hidden";
                                                                        } else {
                                                                            $add_class1 = "text";
                                                                        }
                                                                        $basic_cost = $row_q_costing['basic_amount'];
                                                                        $service_charge = $row_q_costing['service_charge'];
                                                                        $bsmValues = json_decode($row_q_costing['bsmValues']);
                                                                        $service_tax_amount = 0;
                                                                        if ($row_q_costing['service_tax_subtotal'] !== 0.00 && ($row_q_costing['service_tax_subtotal']) !== '') {
                                                                            $service_tax_subtotal1 = explode(',', $row_q_costing['service_tax_subtotal']);
                                                                            for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
                                                                                $service_tax = explode(':', $service_tax_subtotal1[$i]);
                                                                                $service_tax_amount = $service_tax_amount + $service_tax[2];
                                                                            }
                                                                        }

                                                                        foreach ($bsmValues[0] as $key => $value) {
                                                                            switch ($key) {
                                                                                case 'basic':
                                                                                    $basic_cost = ($value != "") ? $basic_cost + $service_tax_amount : $basic_cost;
                                                                                    $inclusive_b = $value;
                                                                                    break;
                                                                                case 'service':
                                                                                    $service_charge = ($value != "") ? $service_charge + $service_tax_amount : $service_charge;
                                                                                    $inclusive_s = $value;
                                                                                    break;
                                                                            }
                                                                        }
                                                                        $readonly = isset($inclusive_d) ? 'readonly' : '';
                                                                        if($bsmValues[0]->tax_apply_on == '1') {
                                                                            $tax_apply_on = 'Basic Amount';
                                                                        }
                                                                        else if($bsmValues[0]->tax_apply_on == '2') { 
                                                                            $tax_apply_on = 'Service Charge';
                                                                        }
                                                                        else if($bsmValues[0]->tax_apply_on == '3') { 
                                                                            $tax_apply_on = 'Total';
                                                                        }else{
                                                                            $tax_apply_on = '';
                                                                        }
                                                                    ?>
                                                                    <tr>
                                                                        <td class="header_btn hidden" style="display:none;"><small>&nbsp;</small><input class="css-checkbox" id="chk_costing<?= $count ?>" type="checkbox" checked disabled><span class="css-label" for="chk_costing<?= $count ?>"></span></td>
                                                                        <td class="header_btn hidden" style="display:none;">
                                                                            <small>&nbsp;</small><input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No." class="form-control" disabled /><span>SR.NO</span></td>
                                                                        <td><small>&nbsp;</small><input type="text" id="package_type-<?= $count ?>" name="package_type-" placeholder="Package Type" title="Package Type" style="width:150px" value="<?= $row_q_costing['package_type'] ?>" readonly><span>Package Type</span></td>
                                                                        <td class="header_btn"><small>&nbsp;</small><input type="text"
                                                                                id="tour_cost-<?= $count ?>" name="tour_cost"
                                                                                placeholder="Hotel Cost" title="Hotel Cost"
                                                                                onchange="validate_balance(this.id);quotation_cost_calculate1(this.id);"
                                                                                value="<?php echo $row_q_costing['tour_cost']; ?>"
                                                                                style="width:100px"><span>Hotel Cost</span></td>

                                                                        <td class="header_btn"><small>&nbsp;</small><input type="text"
                                                                                id="transport_cost-<?= $count ?>" name="transport_cost"
                                                                                placeholder="Transport Cost" title="Transport Cost"
                                                                                onchange="validate_balance(this.id);quotation_cost_calculate1(this.id)"
                                                                                value="<?php echo $row_q_costing['transport_cost']; ?>"
                                                                                style="width:100px"><span>Transport Cost</span></td>

                                                                        <td class="header_btn"><small>&nbsp;</small><input type="text"
                                                                                id="excursion_cost-<?= $count ?>" name="excursion_cost"
                                                                                onchange="quotation_cost_calculate1(this.id); validate_balance(this.id)"
                                                                                placeholder="Activity Cost" title="Activity Cost"
                                                                                value="<?= $row_q_costing['excursion_cost'] ?>"
                                                                                style="width:150px"><span>Activity Cost</span></td>

                                                                        <td class="header_btn"><small id="basic_show-"
                                                                                style="color:#000000">&nbsp;</small><input type="<?= $add_class1 ?>"
                                                                                id="basic_amount-<?= $count ?>" name="basic_amount" onchange="quotation_cost_calculate1(this.id);get_business(this.id,'true');validate_balance(this.id)"
                                                                                placeholder="Basic Amount" title="Basic Amount" style="width:100px"
                                                                                value="<?= $row_q_costing['basic_amount'] ?>" readonly><span>Basic
                                                                                Amount</span></td>

                                                                        <td class="header_btn"><small id="service_show-"
                                                                                style="color:#000000">&nbsp;</small><input type="<?= $add_class1 ?>"
                                                                                id="service_charge-<?= $count ?>" name="service_charge"
                                                                                onchange="get_business(this.id,'false');quotation_cost_calculate1(this.id); validate_balance(this.id)" style="width:150px" placeholder="Service charge" title="Service charge" value="<?= $row_q_costing['service_charge'] ?>"><span>Service charge</span></td>
                                                                        <td class="header_btn"><small>&nbsp;</small><select title="Discount In" id="discount_in-<?= $count ?>" name="discount_in-" class="form-control" onchange="quotation_cost_calculate1(this.id);get_business(this.id,'true');" style="width: 150px!important;">
                                                                            <option value="<?= $row_q_costing['discount_in']?>"><?= $row_q_costing['discount_in']?></option>
                                                                            <?php if($row_q_costing['discount_in'] != 'Percentage'){ ?>
                                                                                <option value="Percentage">Percentage</option>
                                                                            <?php } ?>
                                                                            <?php if($row_q_costing['discount_in'] != 'Flat'){ ?>
                                                                                <option value="Flat">Flat</option>
                                                                            <?php } ?>
                                                                            </select> <span>Discount In</span></td>
                                                                        <td class="header_btn"><small>&nbsp;</small><input type="<?= $add_class1 ?>" id="discount_amt-<?= $count ?>" name="discount_amt-" onchange="quotation_cost_calculate1(this.id); get_business(this.id,'false');validate_balance(this.id)" placeholder="Discount" title="Discount" value="<?= $row_q_costing['discount'] ?>" style="width:100px"><span>Discount</span></td>
                                                                        <td class="header_btn"><small id="tax_apply_show-" style="color:#000000">&nbsp;</small><select title="Tax Apply On" id="atax_apply_on-<?= $count ?>" name="atax_apply_on-<?= $count ?>" class="form-control" onchange="quotation_cost_calculate1(this.id);get_business(this.id,'false');" style="width: 150px!important;">
                                                                        <option value="<?php echo $bsmValues[0]->tax_apply_on ?>"><?php echo $tax_apply_on ?></option>
                                                                                <option value="">*Tax Apply On</option>
                                                                                <option value="1">Basic Amount</option>
                                                                                <option value="2">Service Charge</option>
                                                                                <option value="3">Total</option>
                                                                            </select><span>Tax Apply On</span></td>
                                                                        <td class="header_btn"><small id="tax_show-" style="color:#000000">&nbsp;</small><select title="Select Tax" id="tax_value1-<?= $count ?>" name="tax_value1-<?= $count ?>" class="form-control" onchange="quotation_cost_calculate1(this.id);get_business(this.id,'false');" style="width: 250px!important;">
                                                                            <option value="<?php echo $bsmValues[0]->tax_value ?>"><?php echo $bsmValues[0]->tax_value ?></option>
                                                                            <option value="">*Select Tax</option>
                                                                            <?php get_tax_dropdown('Income') ?>
                                                                            </select><span>Select Tax</span></td>
                                                                        <td class="header_btn"><small>&nbsp;</small><input type="text"
                                                                                id="service_tax_subtotal-<?= $count ?>" name="service_tax_subtotal"
                                                                                readonly placeholder="Tax Amount" title="Tax Amount"
                                                                                value="<?= $row_q_costing['service_tax_subtotal'] ?>"
                                                                                style="width:250px"><span>Tax Amount</span></td>

                                                                        <td class="header_btn"><small>&nbsp;</small><input type="text"
                                                                                id="total_tour_cost-<?= $count ?>"
                                                                                class="amount_feild_highlight text-right" name="total_tour_cost"
                                                                                placeholder="Total Cost" title="Total Cost"
                                                                                value="<?= $row_q_costing['total_tour_cost'] ?>"
                                                                                style="width: 100px;" readonly><span>Total Cost</span></td>

                                                                        <td class="header_btn"><small>&nbsp;</small><input type="text"
                                                                                id="package_name1-<?= $count ?>" name="package_name1"
                                                                                placeholder="Package Name" title="Package Name"
                                                                                value="<?php echo '0'; ?>" style="display: none" readonly>
                                                                        </td>
                                                                        <td class="header_btn"><input type="hidden"
                                                                                value="<?= $bsmValues[0]->tax_apply_on ?>" id="atax_apply_on-<?= $count ?>"></td>

                                                                        <td class="header_btn"><input type="hidden"
                                                                                value="<?= $row_q_costing['id'] ?>"></td>

                                                                    </tr>

                                                                    <?php

                                                                    }

                                                                    ?>

                                                                </table>

                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row mg_tp_10">
                                            <div class="col-xs-12">
                                                <h3 class="editor_title">Travel Cost</h3>
                                                <div class="panel panel-default panel-body app_panel_style">
                                                    <!-- Other costs -->
                                                    <div class="row">
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Flight Cost</span>
                                                            <input type="text" id="flight_cost1" value="<?php echo $sq_quotation['flight_cost']; ?>"
                                                                name="flight_cost" placeholder="Flight Cost" title="Flight Cost"
                                                                onchange="validate_balance(this.id)">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Train Cost</span>
                                                            <input type="text" id="train_cost1" name="train_cost"
                                                                value="<?php echo $sq_quotation['train_cost']; ?>" placeholder="Train Cost"
                                                                title="Train Cost" onchange="validate_balance(this.id)">
                                                        </div>
                                                        <div class="col-md-4 header_btn mg_bt_10">
                                                            <span>Cruise Cost</span>
                                                            <input type="text" id="cruise_cost1" name="cruise_cost1" placeholder="Cruise Cost"
                                                                value="<?php echo $sq_quotation['cruise_cost']; ?>" title="Cruise Cost"
                                                                onchange="validate_balance(this.id)">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Accordian-1 End --><!-- Group Costing -->
                        <!-- Accordian-2 Start --><!-- Per person Costing -->
                        <div class="accordion_content main_block">

                            <div class="panel panel-default main_block">
                                <div class="panel-heading main_block" role="tab" id="headingl_2">
                                    <div class="Normal main_block" role="button" data-toggle="collapse"
                                        data-parent="#accordion" href="#collapsel2" aria-expanded="true"
                                        aria-controls="collapsel2" id="collapsedl2">
                                        <div class="col-md-12"><span>Per Person Costing</span></div>
                                    </div>
                                </div>
                                <div id="collapsel2" class="panel-collapse collapse main_block" role="tabpanel"
                                    aria-labelledby="headingl_2">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <h3 class="editor_title">Land Cost</h3>
                                                <div class="panel panel-default panel-body app_panel_style">
                                                    <!-- Adult & child cost -->
                                                    <?php
                                                    $count = 0;
                                                    $countp = 1;
                                                    $sq_cost_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id'"));
                                                    ?>
                                                    <input type="hidden" id="sq_ppcost_count" value="<?= $sq_cost_count ?>" />
                                                    <?php
                                                    $sq_q_costing1 = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id' ");
                                                    while ($row_q_costing1 = mysqli_fetch_assoc($sq_q_costing1)) {

                                                        $id = $row_q_costing1['id'];
                                                    ?>
                                                    <div class="row mg_tp_10">
                                                        <div class="col-md-3">
                                                            <span>Package Type</span>
                                                            <input type="text" id="package_type<?= $countp ?>" onchange="validate_balance(this.id);"
                                                                name="package_type" placeholder="Package Type" title="Package Type"
                                                                value="<?= $row_q_costing1['package_type'] ?>" readonly>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <span>Adult Cost</span>
                                                            <input type="text" id='adult_cost1<?= $countp ?>' onchange="validate_balance(this.id);"
                                                                name="adult_cost1" placeholder="Adult Cost" title="Adult Cost"
                                                                value="<?= $row_q_costing1['adult_cost'] ?>">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <span>Child with Bed Cost</span>
                                                            <input type="text" id="child_with1<?= $countp ?>" onchange="validate_balance(this.id);"
                                                                name="child_with1" placeholder="Child with Bed Cost" title="Child with Bed Cost"
                                                                value="<?= $row_q_costing1['child_with'] ?>">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <span>Child w/o Bed Cost</span>
                                                            <input type="text" id="child_without1<?= $countp ?>"
                                                                onchange="validate_balance(this.id);" name="child_without1"
                                                                placeholder="Child w/o Bed Cost" title="Child w/o Bed Cost"
                                                                value="<?= $row_q_costing1['child_without'] ?>">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <span>Infant Cost</span>
                                                            <input type="text" id="infant_cost1<?= $countp ?>" onchange="validate_balance(this.id);"
                                                                name="infant_cost1" placeholder="Infant Cost" title="Infant Cost"
                                                                value="<?= $row_q_costing1['infant_cost'] ?>">
                                                        </div>
                                                        <input type="hidden" id="entry_id1<?= $countp ?>" value="<?= $row_q_costing1['id'] ?>">
                                                    </div>
                                                    <?php $countp++;
                                                    } ?>
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
                                                            <input type="number" id="flight_acost1" name="flight_acost" placeholder="Flight Adult Cost" title="Flight Adult Cost" value="<?php echo $sq_quotation['flight_acost']; ?>">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Flight Child Cost</span>
                                                            <input type="number" id="flight_ccost1" name="flight_ccost" placeholder="Flight Child Cost" title="Flight Child Cost" value="<?php echo $sq_quotation['flight_ccost']; ?>">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Flight Infant Cost</span>
                                                            <input type="number" id="flight_icost1" name="flight_icost" placeholder="Flight Infant Cost" title="Flight Infant Cost" value="<?php echo $sq_quotation['flight_icost']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="row mg_tp_10">
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Train Adult Cost</span>
                                                            <input type="number" id="train_acost1" name="train_acost" placeholder="Train Adult Cost" title="Train Adult Cost" value="<?php echo $sq_quotation['train_acost']; ?>">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Train Child Cost</span>
                                                            <input type="number" id="train_ccost1" name="train_ccost" placeholder="Train Child Cost" title="Train Child Cost" value="<?php echo $sq_quotation['train_ccost']; ?>">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Train Infant Cost</span>
                                                            <input type="number" id="train_icost1" name="train_icost" placeholder="Train Infant Cost" title="Train Infant Cost" value="<?php echo $sq_quotation['train_icost']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="row mg_tp_10">
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Cruise Adult Cost</span>
                                                            <input type="number" id="cruise_acost1" name="cruise_acost" placeholder="Cruise Adult Cost" title="Cruise Adult Cost" value="<?php echo $sq_quotation['cruise_acost']; ?>">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Cruise Child Cost</span>
                                                            <input type="number" id="cruise_ccost1" name="cruise_ccost" placeholder="Cruise Child Cost" title="Cruise Child Cost" value="<?php echo $sq_quotation['cruise_ccost']; ?>">
                                                        </div>
                                                        <div class="col-md-4 header_btn col-xs-12 mg_bt_10">
                                                            <span>Cruise Infant Cost</span>
                                                            <input type="number" id="cruise_icost1" name="cruise_icost" placeholder="Cruise Infant Cost" title="Cruise Infant Cost" value="<?php echo $sq_quotation['cruise_icost']; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            </div></div></div>
            <div class="row">
                <div class="col-xs-12">
                    <h3 class="editor_title">Other Costing</h3>
                    <div class="panel panel-default panel-body app_panel_style">
                        <div class="col-md-3 header_btn col-xs-12 mg_bt_10">
                            <span>Visa Cost</span>
                            <input type="text" id="visa_cost1" value="<?php echo $sq_quotation['visa_cost']; ?>"
                                name="visa_cost" placeholder="Visa Cost" title="Visa Cost"
                                onchange="validate_balance(this.id)">
                        </div>
                        <div class="col-md-3 header_btn mg_bt_10">
                            <span>Guide Cost</span>
                            <input type="text" id="guide_cost1" name="guide_cost1" placeholder="Guide Cost"
                                value="<?php echo $sq_quotation['guide_cost']; ?>" title="Guide Cost"
                                onchange="validate_balance(this.id)">
                        </div>
                        <div class="col-md-3 header_btn mg_bt_10">
                            <span>Miscellaneous Cost</span>
                            <input type="text" id="misc_cost1" name="misc_cost1" placeholder="Miscellaneous Cost"
                                value="<?php echo $sq_quotation['misc_cost']; ?>" title="Miscellaneous Cost"
                                onchange="validate_balance(this.id)">
                        </div>
                        <div class="col-md-3 header_btn mg_bt_10">
                            <span>Miscellaneous Description</span>
                            <textarea id="other_desc1" name="other_desc1" placeholder="Miscellaneous Description" title="Miscellaneous Description"><?php echo $sq_quotation['other_desc']; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-6 col-sm-12 mg_bt_10 hidden">
                    <input type="hidden" id="discount1" name="discount" placeholder="Discount" title="Discount"
                        value="<?= $sq_quotation['discount'] ?>" />
                </div>
                <div class="col-md-3 col-sm-12">
                    <select name="costing_type1" id="costing_type1" title="Select Costing type" class="form-control">
                        <?php $costing_type = ($sq_quotation['costing_type'] == 1) ? 'Group Costing' : 'Per Person costing'; ?>
                        <option value="<?= $sq_quotation['costing_type'] ?>"><?= $costing_type ?></option>
                        <option value="1">Group Costing</option>
                        <option value="2">Per Person costing</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                    <select name="currency_code1" id="currency_code1" title="Currency" style="width:100%"
                        data-toggle="tooltip" required>
                        <?php
						$sq_currencyd = mysqli_fetch_assoc(mysqlQuery("SELECT `id`,`currency_code` FROM `currency_name_master` WHERE id=" . $sq_quotation['currency_code']));
						?>
                        <option value="<?= $sq_currencyd['id'] ?>"><?= $sq_currencyd['currency_code'] ?></option>
                        <option value=''>*Select Currency</option>
                        <?php
						$sq_currency = mysqlQuery("select * from currency_name_master order by currency_code");
						while ($row_currency = mysqli_fetch_assoc($sq_currency)) {
						?>
                        <option value="<?= $row_currency['id'] ?>"><?= $row_currency['currency_code'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-12">
                    <div class="div-upload">
                        <div id="price_structure1" class="upload-button1"><span>Price Structure</span></div>
                        <span id="photo_status"></span>
                        <ul id="files"></ul>
                        <input type="hidden" id="upload_url1" name="upload_url1"
                            value="<?= $sq_quotation['price_str_url'] ?>">
                    </div>
	                <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note : Only Excel or Word files are allowed."><i class="fa fa-question-circle"></i></button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-6 col-sm-12"></div>
                <div class="col-md-6 col-sm-12">
                    <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note : Group Costing or Per person costing to display on quotation."><i class="fa fa-question-circle"></i></button>
                </div>
            </div>
            <div class="row mg_tp_20 mg_bt_20 text-center">
                <div class="col-xs-12">
                    <button class="btn btn-info btn-sm ico_left" type="button" onclick="switch_to_tab3()"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Previous</button>
                    &nbsp;&nbsp;
                    <button class="btn btn-sm btn-success" id="btn_quotation_update"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Update</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?= end_panel(); ?>
<script>
$('#currency_code1').select2();
function get_business(id, flag, change = false) {
    var offset = id.split('-');
    get_auto_values('quotation_date', 'basic_amount-' + offset[1], 'payment_mode', 'service_charge-' + offset[1],'markup', 'update', flag, 'markup', 'discount_amt-'+ offset[1], offset[1], change);
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

upload_price_struct();
function upload_price_struct() {
    var btnUpload = $('#price_structure1');
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
                return false;
            } else {
                $(btnUpload).find('span').text('Uploaded');
                success_msg_alert('File is uploaded!');
                $("#upload_url1").val(response);
            }
        }
    });
}


function quotation_cost_calculate1(id) {
    var offset = id.split('-');
    var quotation_cost = 0;
    var tour_cost = $('#tour_cost-' + offset[1]).val();
    var transport_cost = $('#transport_cost-' + offset[1]).val();
    var excursion_cost = $('#excursion_cost-' + offset[1]).val();
    var service_tax = $('#service_tax-' + offset[1]).val();
	var discount_in = $('#discount_in-' + offset[1]).val();
	var discount_amt = $('#discount_amt-' + offset[1]).val();

    if (tour_cost == '') {
        tour_cost = 0;
    }
    if (transport_cost == '') {
        transport_cost = 0;
    }
    if (excursion_cost == '') {
        excursion_cost = 0;
    }
	if (discount_amt == '') {
		discount_amt = 0;
	}

    var sub_total = parseFloat(tour_cost) + parseFloat(transport_cost) + parseFloat(excursion_cost);
    $('#basic_amount-' + offset[1]).val(sub_total.toFixed(2));

    if (id != 'basic_amount-' + offset[1]) {
        $('#basic_amount-' + offset[1]).trigger('change');
    }

    var service_charge = $('#service_charge-' + offset[1]).val();
    var service_tax_subtotal = $('#service_tax_subtotal-' + offset[1]).val();
    if (service_charge == '') {
        service_charge = 0;
    }
	var service_tax_amount = 0;
	if (parseFloat(service_tax_subtotal) !== 0.0 && service_tax_subtotal !== '' && typeof service_tax_subtotal != 'undefined') {
		var service_tax_subtotal1 = service_tax_subtotal.split(',');
		for (var i = 0; i < service_tax_subtotal1.length; i++) {
			var service_tax = service_tax_subtotal1[i].split(':');
			service_tax_amount = parseFloat(service_tax_amount) + parseFloat(service_tax[2]);
		}
	}

	var discountable_amt = parseFloat(service_charge);
	if(discount_in == 'Percentage'){
		var discount = parseFloat(discountable_amt) * parseFloat(discount_amt) / 100;
	}
	else{
		var discount = (service_charge != 0) ? parseFloat(discount_amt) : 0;
	}
	var after_discount_amt = parseFloat(discountable_amt) - parseFloat(discount);
    var total_amt = parseFloat(sub_total) + parseFloat(service_tax_amount) + parseFloat(after_discount_amt);
    $('#total_tour_cost-' + offset[1]).val(Math.round(total_amt).toFixed(2));

}

$('#frm_tab4').validate({

    rules: {

        markup_cost1: {
            required: true,
            number: true
        },

        tour_cost1: {
            required: true,
            number: true
        },
        currency_code1: {
            required: true
        }

    },

    submitHandler: function(form, e) {

        e.preventDefault();

        $('#btn_quotation_update').prop('disabled', true);
        var quotation_id = $('#quotation_id1').val();

        var enquiry_id = $('#enquiry_id12').val();

        var package_id = $('#package_id1').val();
        var tour_name = $('#tour_name12').val();
        var from_date = $('#from_date12').val();

        var to_date = $('#to_date12').val();

        var total_days = $('#total_days12').val();

        var customer_name = $('#customer_name12').val();
        var user_id = 0;
        if($('#s_user_id').val() != 0){
            user_id = $('#user_id_u').val();
        }

        var email_id = $('#email_id12').val();
        var mobile_no = $('#mobile_no12').val();
		var country_code = $('#country_code1').val();

        var total_adult = $('#total_adult12').val();

        var total_infant = $('#total_infant12').val();

        var total_passangers = $('#total_passangers12').val();

        var children_without_bed = $('#children_without_bed12').val();

        var children_with_bed = $('#children_with_bed12').val();

        var quotation_date = $('#quotation_date').val();
        var active_flag = $('#active_flag1').val();
        var booking_type = $('#booking_type2').val();

        var train_cost = $('#train_cost1').val();

        var flight_cost = $('#flight_cost1').val();
        var cruise_cost = $('#cruise_cost1').val();
        var visa_cost = $('#visa_cost1').val();
        var guide_cost = $('#guide_cost1').val();
        var misc_cost = $('#misc_cost1').val();
        var price_str_url = $("#upload_url1").val();
        var currency_code = $('#currency_code1').val();

        var checked_programe_arr = [];
        var day_count_arr = [];
        var attraction_arr = [];
        var program_arr = [];
        var stay_arr = [];
        var meal_plan_arr = [];
        var package_p_id_arr = [];

        var table = document.getElementById("dynamic_table_list_update");
        var rowCount = table.rows.length;
        for (var i = 0; i < rowCount; i++) {
            var row = table.rows[i];
            var checked_programe = row.cells[0].childNodes[0].checked;
            var day_count = row.cells[1].childNodes[0].value;
            var attraction = row.cells[2].childNodes[0].value;
            var program = row.cells[3].childNodes[0].value;
            var stay = row.cells[4].childNodes[0].value;
            var meal_plan = row.cells[5].childNodes[0].value;
            var package_id1 = row.cells[7].childNodes[0].value;
            checked_programe_arr.push(checked_programe);
            day_count_arr.push(day_count);
            attraction_arr.push(attraction);
            program_arr.push(program);
            stay_arr.push(stay);
            meal_plan_arr.push(meal_plan);
            package_p_id_arr.push(package_id1);
        }

        //Train Information
        var train_status_arr = [];
        var train_from_location_arr = [];
        var train_to_location_arr = [];
        var train_class_arr = [];
        var train_arrival_date_arr = [];
        var train_departure_date_arr = [];
        var train_id_arr = [];

        var table = document.getElementById("tbl_package_tour_quotation_dynamic_train");
        var rowCount = table.rows.length;

        for (var i = 0; i < rowCount; i++) {
            var row = table.rows[i];
            var status = row.cells[0].childNodes[0].checked;
            var train_from_location1 = row.cells[2].childNodes[0].value;
            var train_to_location1 = row.cells[3].childNodes[0].value;
            var train_class = row.cells[4].childNodes[0].value;
            var train_departure_date = row.cells[5].childNodes[0].value;
            var train_arrival_date = row.cells[6].childNodes[0].value;

            if (row.cells[7] && row.cells[7].childNodes[0]) {
                var train_id = row.cells[7].childNodes[0].value;
            } else {
                var train_id = "";
            }

            train_status_arr.push(status);
            train_from_location_arr.push(train_from_location1);
            train_to_location_arr.push(train_to_location1);
            train_class_arr.push(train_class);
            train_arrival_date_arr.push(train_arrival_date);
            train_departure_date_arr.push(train_departure_date);
            train_id_arr.push(train_id);
        }

        //Plane Information
        var plane_status_arr = [];
        var plane_from_city_arr = [];
        var plane_to_city_arr = [];
        var plane_from_location_arr = [];
        var plane_to_location_arr = [];
        var airline_name_arr = [];
        var plane_class_arr = [];
        var arraval_arr = [];
        var dapart_arr = [];
        var plane_id_arr = [];

        var table = document.getElementById("tbl_package_tour_quotation_dynamic_plane");
        var rowCount = table.rows.length;

        for (var i = 0; i < rowCount; i++) {
            var row = table.rows[i];

            var status = row.cells[0].childNodes[0].checked;
            var plane_from_location1 = row.cells[2].childNodes[0].value;
            var plane_to_location1 = row.cells[3].childNodes[0].value;
            var airline_name = row.cells[4].childNodes[0].value;
            var plane_class = row.cells[5].childNodes[0].value;
            var dapart1 = row.cells[6].childNodes[0].value;
            var arraval1 = row.cells[7].childNodes[0].value;
            var plane_from_city = row.cells[8].childNodes[0].value;
            var plane_to_city = row.cells[9].childNodes[0].value;


            if (row.cells[10] && row.cells[10].childNodes[0]) {
                var plane_id = row.cells[10].childNodes[0].value;
            } else {
                var plane_id = "";
            }

            plane_status_arr.push(status);
            plane_from_city_arr.push(plane_from_city);
            plane_to_city_arr.push(plane_to_city);
            plane_from_location_arr.push(plane_from_location1);
            plane_to_location_arr.push(plane_to_location1);
            airline_name_arr.push(airline_name);
            plane_class_arr.push(plane_class);
            arraval_arr.push(arraval1);
            dapart_arr.push(dapart1);
            plane_id_arr.push(plane_id);
        }
        //Cruise Information
        var cruise_status_arr = [];
        var cruise_departure_date_arr = [];
        var cruise_arrival_date_arr = [];
        var route_arr = [];
        var cabin_arr = [];
        var sharing_arr = [];
        var c_entry_id_arr = [];

        var table = document.getElementById("tbl_dynamic_cruise_quotation");
        var rowCount = table.rows.length;

        for (var i = 0; i < rowCount; i++) {
            var row = table.rows[i];

            var status = row.cells[0].childNodes[0].checked;
            var cruise_from_date = row.cells[2].childNodes[0].value;
            var cruise_to_date = row.cells[3].childNodes[0].value;
            var route = row.cells[4].childNodes[0].value;
            var cabin = row.cells[5].childNodes[0].value;
            var sharing = row.cells[6].childNodes[0].value;
            var c_entry_id = row.cells[7].childNodes[0].value;

            if (c_entry_id == '') {
                c_entry_id = 0;
            } else {
                c_entry_id = row.cells[7].childNodes[0].value;
            }
            cruise_status_arr.push(status);
            cruise_departure_date_arr.push(cruise_from_date);
            cruise_arrival_date_arr.push(cruise_to_date);
            route_arr.push(route);
            cabin_arr.push(cabin);
            sharing_arr.push(sharing);
            c_entry_id_arr.push(c_entry_id);
        }

        //Hotel Information
        var package_type_arr = [];
        var hotel_status_arr = [];
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
        var hotel_id_arr = [];
        var hotel_meal_plan_arr = [];

        var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel_update");
        var rowCount = table.rows.length;

        for (var i = 0; i < rowCount; i++) {

            var row = table.rows[i];
            var status = row.cells[0].childNodes[0].checked;
            var package_type = row.cells[2].childNodes[0].value;
            var city_name = row.cells[3].childNodes[0].value;
            var hotel_id = row.cells[4].childNodes[0].value;
            var hotel_cat = row.cells[5].childNodes[0].value;
            var check_in = row.cells[6].childNodes[0].value;
            var checkout = row.cells[7].childNodes[0].value;
            var hotel_type = row.cells[8].childNodes[0].value;
            var hotel_stay_days1 = row.cells[9].childNodes[0].value;
            var total_rooms = row.cells[10].childNodes[0].value;
            var extra_bed = row.cells[11].childNodes[0].value;
            var package_name1 = row.cells[12].childNodes[0].value;
            var hotel_cost = row.cells[13].childNodes[0].value;
            var package_id1 = row.cells[14].childNodes[0].value;
            var extra_bed_cost = row.cells[15].childNodes[0].value;
            var meal_plan = row.cells[16].childNodes[0].value;

            if (row.cells[17] && row.cells[17].childNodes[0]) {
                var hotel_id1 = row.cells[17].childNodes[0].value;
            } else {
                var hotel_id1 = '';
            }

            hotel_status_arr.push(status);
            package_type_arr.push(package_type);
            city_name_arr.push(city_name);
            hotel_name_arr.push(hotel_id);
            hotel_cat_arr.push(hotel_cat);
            hotel_stay_days_arr.push(hotel_stay_days1);
            hotel_type_arr.push(hotel_type);
            total_rooms_arr.push(total_rooms);
            extra_bed_arr.push(extra_bed);
            package_name_arr.push(package_name1);
            hotel_cost_arr.push(hotel_cost);
            extra_bed_cost_arr.push(extra_bed_cost);
            hotel_id_arr.push(hotel_id1);
            check_in_arr.push(check_in);
            check_out_arr.push(checkout);
            hotel_meal_plan_arr.push(meal_plan);
        }

        //Transport Information
        var transport_status_arr = [];
        var vehicle_name_arr = [];
        var start_date_arr = [];
        var end_date_arr = [];
        var pickup_arr = [];
        var drop_arr = [];
        var vehicle_count_arr = [];
        var transport_cost_arr1 = [];
        var package_name_arr1 = [];
        var pickup_type_arr = [];
        var drop_type_arr = [];
        var transport_id_arr = [];
        var service_duration_arr = [];

        var table = document.getElementById("tbl_package_tour_quotation_dynamic_transport_u");
        var rowCount = table.rows.length;

        for (var i = 0; i < rowCount; i++) {

            var row = table.rows[i];
            var status = row.cells[0].childNodes[0].checked;
            var transport_id1 = row.cells[2].childNodes[0].value;
            var travel_date = row.cells[3].childNodes[0].value;
            var end_date = row.cells[4].childNodes[0].value;
            var pickup = row.cells[5].childNodes[0].value;
            var drop = row.cells[6].childNodes[0].value;
            var pickup_type = $("option:selected", $("#" + row.cells[5].childNodes[0].id)).parent().attr(
                'value');
            var drop_type = $("option:selected", $("#" + row.cells[6].childNodes[0].id)).parent().attr(
                'value');
            var service_duration = row.cells[7].childNodes[0].value;
            var vehicle_count = row.cells[8].childNodes[0].value;
            var transport_cost = row.cells[9].childNodes[0].value;
            var pname = row.cells[10].childNodes[0].value;
            var package_id1 = row.cells[11].childNodes[0].value;

            if (row.cells[14] && row.cells[14].childNodes[0]) {
                var transport_id = row.cells[14].childNodes[0].value;
            } else {
                var transport_id = '';
            }

            transport_status_arr.push(status);
            vehicle_name_arr.push(transport_id1);
            start_date_arr.push(travel_date);
            end_date_arr.push(end_date);
            pickup_arr.push(pickup);
            drop_arr.push(drop);
            vehicle_count_arr.push(vehicle_count);
            transport_cost_arr1.push(transport_cost);
            package_name_arr1.push(pname);
            pickup_type_arr.push(pickup_type);
            drop_type_arr.push(drop_type);
            transport_id_arr.push(transport_id);
            service_duration_arr.push(service_duration);
        }
        //Activity Info
        var table = document.getElementById("tbl_package_tour_quotation_dynamic_excursion");
        var rowCount = table.rows.length;

        var exc_status_arr = [];
        var exc_date_arr_e = [];
        var city_name_arr_e = [];
        var excursion_name_arr = [];
        var transfer_option_arr = [];
        var adult_arr = [];
        var chwb_arr = [];
        var chwob_arr = [];
        var infant_arr = [];
        var excursion_amt_arr = [];
        var excursion_id_arr = [];
        var vehicles_arr = [];

        for (var e = 0; e < rowCount; e++) {
            var row = table.rows[e];

            var status = row.cells[0].childNodes[0].checked;
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

            if (row.cells[16] && row.cells[16].childNodes[0]) {
                var excursion_id = row.cells[16].childNodes[0].value;
            } else {
                var excursion_id = "";
            }
            exc_status_arr.push(status);
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
            excursion_id_arr.push(excursion_id);
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
        var costing_id_arr = [];
        var package_type_c_arr = [];
        var discount_in_arr = [];
        var discount_arr = [];

        var table = document.getElementById("tbl_package_tour_quotation_dynamic_costing");
        var rowCount = table.rows.length;
        for (var i = 0; i < rowCount; i++) {

            var row = table.rows[i];
            var package_type_c = row.cells[2].childNodes[1].value;
            var tour_cost = row.cells[3].childNodes[1].value;
            var transport_cost = row.cells[4].childNodes[1].value;
            var excursion_cost = row.cells[5].childNodes[1].value;
            var basic_amount = row.cells[6].childNodes[1].value;
            var service_charge = row.cells[7].childNodes[1].value;
            var discount_in = row.cells[8].childNodes[1].value;
            var discount = row.cells[9].childNodes[1].value;
            var tax_apply_on = row.cells[10].childNodes[1].value;
            var tax_value = row.cells[11].childNodes[1].value;
            var service_tax_subtotal = row.cells[12].childNodes[1].value;
            var total_tour_cost = row.cells[13].childNodes[1].value;
            var package_name3 = row.cells[14].childNodes[1].value;
            var costing_id = row.cells[16].childNodes[0].value;

            if (tour_cost == "") {
                error_msg_alert('Select Tour cost in row' + (i + 1));
                $('#btn_quotation_update').prop('disabled', false);
                return false;
            }
            if (tax_apply_on == "") {
                error_msg_alert('Select Tax Apply On in row' + (i + 1));
                $('#btn_quotation_update').prop('disabled', false);
                return false;
            }
            if (tax_value == "") {
                error_msg_alert('Select Tax in row' + (i + 1));
                $('#btn_quotation_update').prop('disabled', false);
                return false;
            }

            package_type_c_arr.push(package_type_c);
            tour_cost_arr.push(tour_cost);
            transport_cost_arr.push(transport_cost);
            excursion_cost_arr.push(excursion_cost);
            basic_amount_arr.push(basic_amount);
            service_charge_arr.push(service_charge);
            discount_in_arr.push(discount_in);
            discount_arr.push(discount);
            service_tax_subtotal_arr.push(service_tax_subtotal);
            total_tour_cost_arr.push(total_tour_cost);
            package_name_arr2.push(package_name3);
            costing_id_arr.push(costing_id);
        }
        var bsmValues = [];

        var table = document.getElementById("tbl_package_tour_quotation_dynamic_costing");
        var rowCount = table.rows.length;
        for (var i = 0; i < rowCount; i++) {
            var row = table.rows[i];
            var bsmvaluesEach = [];

            if (row.cells[0].childNodes[1].checked) {
                var basic_show = $(row.cells[6].childNodes[1]).find('span').text();
                var service_show = $(row.cells[7].childNodes[1]).find('span').text();
                var tax_apply_on = row.cells[10].childNodes[1].value;
                var tax_value = row.cells[11].childNodes[1].value;
                bsmvaluesEach.push({
                    "basic": basic_show,
                    "service": service_show,
                    'tax_apply_on':tax_apply_on,
                    'tax_value':tax_value
                });
                bsmValues.push(bsmvaluesEach);
            }
        }
        // PP Costing
        var adult_cost_arr = [];
        var infant_cost_arr = [];
        var child_with_arr = [];
        var child_without_arr = [];
        var entry_id_arr = [];
        var sq_ppcost_count = $('#sq_ppcost_count').val();
        for (var i = 1; i <= sq_ppcost_count; i++) {

            var adult_cost = $('#adult_cost1' + i).val();
            var infant_cost = $('#infant_cost1' + i).val();
            var child_with = $('#child_with1' + i).val();
            var child_without = $('#child_without1' + i).val();
            var entry_id = $('#entry_id1' + i).val();

            adult_cost_arr.push(adult_cost);
            infant_cost_arr.push(infant_cost);
            child_with_arr.push(child_with);
            child_without_arr.push(child_without);
            entry_id_arr.push(entry_id);
        }
        //Per person travel costing
        var flight_acost = $('#flight_acost1').val();
        var flight_ccost = $('#flight_ccost1').val();
        var flight_icost = $('#flight_icost1').val();
        var train_acost = $('#train_acost1').val();
        var train_ccost = $('#train_ccost1').val();
        var train_icost = $('#train_icost1').val();
        var cruise_acost = $('#cruise_acost1').val();
        var cruise_ccost = $('#cruise_ccost1').val();
        var cruise_icost = $('#cruise_icost1').val();
        var other_desc = $('#other_desc1').val();

        var costing_type = $('#costing_type1').val();
        var inclusions = $('#inclusions1').val();
        var exclusions = $('#exclusions1').val();
        var image_url_id = $('#image_url_id').val();
        var pckg_daywise_url = $('#pckg_daywise_url').val();
        var image_url = $('#delete_image_url').val();
        var discount = $('#discount1').val();
        var updated_url = pckg_daywise_url + image_url;
        var base_url = $('#base_url').val();

        $("#vi_confirm_box").vi_confirm_box({
            callback: function(result) {
                if (result == "yes") {
                    $('#btn_quotation_update').button('loading');
                    $('#btn_quotation_update').prop('disabled', false);
                    $.ajax({

                        type: 'post',
                        url: base_url +
                            'controller/package_tour/quotation/quotation_update.php',
                        data: {
                            quotation_id: quotation_id,
                            package_id: package_id,
                            tour_name: tour_name,
                            from_date: from_date,
                            to_date: to_date,
                            total_days: total_days,
                            customer_name: customer_name,user_id:user_id,
                            email_id: email_id,
                            mobile_no: mobile_no,country_code:country_code,
                            total_adult: total_adult,
                            total_infant: total_infant,
                            total_passangers: total_passangers,
                            children_without_bed: children_without_bed,
                            children_with_bed: children_with_bed,
                            quotation_date: quotation_date,
                            active_flag: active_flag,
                            booking_type: booking_type,
                            train_cost: train_cost,
                            flight_cost: flight_cost,
                            cruise_cost: cruise_cost,
                            visa_cost: visa_cost,
                            train_from_location_arr: train_from_location_arr,
                            train_to_location_arr: train_to_location_arr,
                            train_class_arr: train_class_arr,
                            train_arrival_date_arr: train_arrival_date_arr,
                            train_departure_date_arr: train_departure_date_arr,
                            train_id_arr: train_id_arr,
                            plane_from_city_arr: plane_from_city_arr,
                            plane_to_city_arr: plane_to_city_arr,
                            plane_from_location_arr: plane_from_location_arr,
                            plane_to_location_arr: plane_to_location_arr,
                            plane_id_arr: plane_id_arr,
                            airline_name_arr: airline_name_arr,
                            plane_class_arr: plane_class_arr,
                            arraval_arr: arraval_arr,
                            dapart_arr: dapart_arr,
                            cruise_departure_date_arr: cruise_departure_date_arr,
                            cruise_arrival_date_arr: cruise_arrival_date_arr,
                            route_arr: route_arr,
                            cabin_arr: cabin_arr,
                            sharing_arr: sharing_arr,
                            c_entry_id_arr: c_entry_id_arr,
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
                            hotel_id_arr: hotel_id_arr,
                            hotel_meal_plan_arr:hotel_meal_plan_arr,
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
                            tour_cost_arr: tour_cost_arr,
                            basic_amount_arr: basic_amount_arr,
                            service_charge_arr: service_charge_arr,
                            service_tax_subtotal_arr: service_tax_subtotal_arr,
                            total_tour_cost_arr: total_tour_cost_arr,
                            package_name_arr2: package_name_arr2,
                            transport_cost_arr: transport_cost_arr,
                            costing_id_arr: costing_id_arr,
                            package_type_c_arr: package_type_c_arr,
                            enquiry_id: enquiry_id,
                            transport_id_arr: transport_id_arr,
                            service_duration_arr:service_duration_arr,
                            city_name_arr_e: city_name_arr_e,
                            excursion_name_arr: excursion_name_arr,
                            exc_date_arr_e: exc_date_arr_e,
                            transfer_option_arr: transfer_option_arr,
                            excursion_amt_arr: excursion_amt_arr,
                            excursion_id_arr: excursion_id_arr,
                            excursion_cost_arr: excursion_cost_arr,
                            vehicles_arr:vehicles_arr,
                            guide_cost: guide_cost,
                            misc_cost: misc_cost,
                            adult_cost: adult_cost_arr,
                            infant_cost: infant_cost_arr,
                            child_with: child_with_arr,
                            child_without: child_without_arr,
                            entry_id_arr: entry_id_arr,
                            price_str_url: price_str_url,
                            attraction_arr: attraction_arr,
                            program_arr: program_arr,
                            stay_arr: stay_arr,
                            meal_plan_arr: meal_plan_arr,
                            package_p_id_arr: package_p_id_arr,
                            inclusions: inclusions,
                            exclusions: exclusions,
                            checked_programe_arr: checked_programe_arr,
                            day_count_arr: day_count_arr,
                            costing_type: costing_type,
                            train_status_arr: train_status_arr,
                            plane_status_arr: plane_status_arr,
                            cruise_status_arr: cruise_status_arr,
                            hotel_status_arr: hotel_status_arr,
                            transport_status_arr: transport_status_arr,
                            exc_status_arr: exc_status_arr,
                            updated_url: updated_url,
                            image_url_id: image_url_id,
                            bsmValues: bsmValues,
                            currency_code: currency_code,
                            package_type_arr: package_type_arr,
                            discount_in_arr:discount_in_arr,discount_arr:discount_arr,
                            adult_arr: adult_arr,
                            chwb_arr: chwb_arr,
                            chwob_arr: chwob_arr,
                            infant_arr: infant_arr,
                            discount: discount,
                            flight_acost : flight_acost,flight_ccost:flight_ccost,flight_icost:flight_icost,train_acost:train_acost,train_ccost:train_ccost,train_icost:train_icost,cruise_acost:cruise_acost,cruise_ccost:cruise_ccost,cruise_icost:cruise_icost,other_desc:other_desc
                        },

                        success: function(message) {
                            $('#btn_quotation_update').button('reset');
                            $('#btn_quotation_update').prop('disabled', false);
                            var msg = message.split('--');
                            if (msg[0] == "error") {
                                error_msg_alert(msg[1]);
                                $('#btn_quotation_update').prop('disabled', false);
                            } else {
                                $('#vi_confirm_box').vi_confirm_box({
                                    false_btn: false,
                                    message: message,
                                    true_btn_text: 'Ok',
                                    callback: function(data1) {
                                        $('#btn_quotation_update').prop(
                                            'disabled', false);
                                        if (data1 == "yes") {
                                            $('#btn_quotation_update')
                                                .button('reset');
                                            $('#btn_quotation_update')
                                                .prop('disabled',
                                                false);
                                            $('#quotation_update_modal')
                                                .modal('hide');
                                            window.location.href =
                                                base_url +
                                                'view/package_booking/quotation/home/index.php';
                                        } else {
                                            $('#btn_quotation_update')
                                                .button('reset');
                                            $('#btn_quotation_update')
                                                .prop('disabled',
                                                false);
                                        }
                                    }
                                });
                            }
                        }
                    });
                } else {
                    $('#btn_quotation_update').button('reset');
                    $('#btn_quotation_update').prop('disabled', false);
                }
            }
        });
    }
});
</script>