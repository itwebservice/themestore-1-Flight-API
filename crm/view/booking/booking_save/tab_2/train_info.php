<div class="row">
    <div class="col-sm-10 col-xs-12 col-sm-push-2 text-right">
        <button type="button" class="btn btn-excel" title="Add Row" onclick="addRow('tbl_train_travel_details_dynamic_row');city_lzloading('.train_from', '*From', true);city_lzloading('.train_to', '*To', true);"><i class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-pdf btn-sm" title="Delete Row" onclick="deleteRow('tbl_train_travel_details_dynamic_row'); calculate_train_expense('tbl_train_travel_details_dynamic_row')"><i class="fa fa-trash"></i></button>
        <!-- Code to upload button -->
        <div class="div-upload mg_bt_10_sm_xs" id="div_upload_button">
            <div id="train_upload" class="upload-button"><span>Ticket</span></div><span id="train_status" ></span>
            <ul id="files" ></ul>
            <input type="hidden" id="txt_train_upload_dir" name="txt_train_upload_dir">
        </div>   
    </div>
    <div class="col-sm-2 col-xs-12 col-sm-pull-10 mg_bt_10_sm_xs">
        <input type="checkbox" class="css-checkbox" id="chk_train_select_all" onchange="select_all('tbl_train_travel_details_dynamic_row',this.id); calculate_train_expense('tbl_train_travel_details_dynamic_row')" checked>&nbsp;&nbsp;
        <label for="chk_train_select_all">Select All</label>&nbsp;&nbsp;&nbsp;
        <button type="button" class="btn btn-excel btn-sm" title="Add City" onclick="city_ssave_modal()"><i class="fa fa-plus"></i></button> 
    </div>
</div>  

<div class="row mg_bt_30"> <div class="col-xs-12"> <div class="table-responsive">
    
    <table id="tbl_train_travel_details_dynamic_row" name="tbl_train_travel_details_dynamic_row" class="table table-bordered table-hover bg_white no-marg-sm">
        <tr>
            <td><input class="css-checkbox" id="check-btn-train-1" type="checkbox" onchange="calculate_train_expense('tbl_train_travel_details_dynamic_row')" ><label for="check-btn-train-1" class="css-label check-label-align"></label></td>

            <td><input maxlength="15" type="text" id="tr_train_sr_no" name="tr_train_sr_no" value="1" placeholder="Sr.No." disabled/></td>

            <td><input type="text" id="txt_train_date1" name="txt_train_date1"  value="<?= date('d-m-Y H:i') ?>" title="Departure Date & Time" placeholder="*Departure Date & Time" style="width:156px"/></td>

            <td><select id="txt_train_from_location1" name="txt_train_from_location1" title="From Location" onchange="validate_location('txt_train_to_location1' , 'txt_train_from_location1')" class="app_select2 train_from" style="width:120px">
            </select></td>

            <td><select id="txt_train_to_location1" name="txt_train_to_location1" title="To Location" onchange="validate_location('txt_train_from_location1' , 'txt_train_to_location1')" class="app_select2 train_to" style="width:120px">
            </select></td>

            <td><input type="text" id="txt_train_no1" name="txt_train_no1" title="Train Name & No"  placeholder="Train Name & No" style="width:100px"/></td>

            <td><input type="text" id="txt_train_total_seat1" name="txt_train_total_seat1" placeholder="Total Seats" title="Total Seats" maxlength="3" onchange="validate_balance(this.id);" style="width:100px"/></td>

            <td><input type="text" id="txt_train_amount1" name="txt_train_amount1"  class="text-right" placeholder="*Amount" title="Amount" onchange="calculate_train_expense('tbl_train_travel_details_dynamic_row');validate_balance(this.id);" style="width:120px"/></td>

            <td style="width:100px"><select class="empty" id="txt_train_class1" title="Class" name="txt_train_class1" style="width:90px"> 
                    <option value="">Class</option>
                    <option value="1A">1A</option>
                    <option value="2A">2A</option>
                    <option value="3A">3A</option>
                    <option value="FC">FC</option>
                    <option value="CC">CC</option>
                    <option value="SL">SL</option>
                    <option value="2S">2S</option>
                </select> </td>
            <td style="width:108px"><select class="empty" id="cmb_train_priority1" title="Priority" name="cmb_train_priority1" style="width:100px"> 
                    <option value="" selected>Priority</option>
                    <option value="General"> General </option>
                    <option value="Tatkal"> Tatkal </option>
                    <option value="Self"> Self </option>
                </select> </td>
        </tr>
    </table>

</div> </div> </div>                    
                        
    <div class="row hidden">
        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
            <label>Subtotal</label>
            <input type="text" id="txt_train_expense" name="txt_train_expense"  title="Subtotal" class="text-right" value="0.00" readonly />            
        </div>
        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
            <label>Service Charge</label>
            <input type="text" id="txt_train_service_charge" name="txt_train_service_charge"  class="text-right" value="0.00" title="Service Charge" onchange="validate_balance(this.id); calculate_total_train_expense()" />            
        </div>
        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
            <label>Tax</label>
            <select name="train_taxation_id" id="train_taxation_id" title="Tax" onchange="generic_tax_reflect(this.id, 'train_service_tax', 'calculate_total_train_expense');">
                
            </select>
            <input type="hidden" name="train_service_tax" id="train_service_tax" value="0">            
        </div>
        <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
            <label>Tax Amount</label>
            <input type="text" id="train_service_tax_subtotal" name="train_service_tax_subtotal" placeholder="0.00" title="Tax Amount" readonly>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-12">
            <label>Total</label>
            <input type="text" id="txt_train_total_expense" name="txt_train_total_expense"  title="Total" class="text-right amount_feild_highlight" value="0.00" readonly />            
        </div>
    </div>
    <script type="text/javascript">
    //$('#txt_train_date1').datetimepicker({ format:'d-m-Y H:i' });
    $(document).ready(function(){
        city_lzloading(".train_from", "*From", true);
        city_lzloading(".train_to", "*To", true);
    });
</script>