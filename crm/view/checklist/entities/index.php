<?php
include "../../../model/model.php";
require_once('../../layouts/admin_header.php');
?>
<?= begin_panel('Tour Checklist',44) ?>
<?php
$emp_id = $_SESSION['emp_id'];
?>
<div class="row text-right mg_tp_20 mg_bt_20"> <div class="col-md-12">
	<button class="btn btn-info btn-sm ico_left" data-toggle="tooltip" title="Add Checklist" id="checklist_save_btn_add" onclick="checklist_save();btnDisableEnable(this.id)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Checklist</button>
</div> </div>
<div class="app_panel_content Filter-panel">
<div class="row">
    <div class="col-md-3 col-sm-6 mg_bt_10_sm_xs">
        <select id="entity_for_a" name="entity_for_a" data-toggle="tooltip" style="width:100%" title="Service For" onchange="entities_list_reflect();">
            <option value="">Select Service For</option>
            <option value="Group Tour">Group Tour</option>
            <option value="Package Tour">Package Tour</option>
            <option value="Visa Booking">Visa Booking</option>
            <option value="Flight Booking">Flight Booking</option>
            <option value="Train Booking">Train Booking</option>
            <option value="Hotel Booking">Hotel Booking</option>
            <option value="Bus Booking">Bus Booking</option>
            <option value="Car Rental Booking">Car Rental Booking</option>
            <option value="Excursion Booking">Activity Booking</option>
        </select>
    </div>
</div>
</div>
<div id="div_entities_list" class="main_block mg_tp_20"></div>
<div id="div_entitiesup_list"></div>
<div id="div_view_entries"></div>

<script>
function checklist_save(){
    
	$.post('entity_save_modal.php', {}, function(data){
		$('#div_entitiesup_list').html(data);
	});
}
function entities_list_reflect(){
	var entity_for = $('#entity_for_a').val();
	$.post('entities_list_reflect.php', {entity_for : entity_for}, function(data){
		$('#div_entities_list').html(data);
	});
}

entities_list_reflect();
function update_modal(entity_id)
{
	
    $('#update_btn-'+entity_id).button('loading');
    $('#update_btn-'+entity_id).prop('disabled',true);
    $.post('entity_update_modal.php', {entity_id : entity_id}, function(data){
        $('#div_entitiesup_list').html(data);
        $('#update_btn-'+entity_id).button('reset');
        $('#update_btn-'+entity_id).prop('disabled',false);
    });
}
function view_modal(entity_id)
{
    $('#view_btn-'+entity_id).button('loading');
    $('#view_btn-'+entity_id).prop('disabled',true);
	$.post('view_to_do_name.php', {entity_id : entity_id}, function(data){
        $('#div_view_entries').html(data);
        $('#view_btn-'+entity_id).button('reset');
        $('#view_btn-'+entity_id).prop('disabled',false);
    });
}
// checklist_content_reflect();
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>



<?php
/*======******Footer******=======*/
require_once('../../layouts/admin_footer.php'); 
?>