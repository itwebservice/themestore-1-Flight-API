<?php
include "../../../model/model.php";
/*======******Header******=======*/
// require_once('../../layouts/admin_header.php');
$emp_id = $_SESSION['emp_id']; 
$role_id = $_SESSION['role_id'];
$q = "select branch_status from branch_assign where link='leave_magt/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
?>
<input type="hidden" name="branch_status_req" id="branch_status_req" value="<?= $branch_status ?>">
<?php if($emp_id != '0' && $role_id!="1"){ ?>
<div class="row mg_bt_20">
  <div class="col-sm-12 text-right text_left_sm_xs">
      <button class="btn btn-info btn-sm ico_left" id="lreq_btn" onclick="save_modal()"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Leave Request</button>
  </div>
</div>
<?php } ?>

<div id="div_list" class="main_block"></div>
<div id="div_modal"></div>
<script src="<?php echo BASE_URL ?>js/app/field_validation.js"></script>                    

<script>
function total_days_reflect(){

    var from_date = $('#from_date').val(); 
    var to_date = $('#to_date').val();
    var edate = from_date.split("-");
    e_date = new Date(edate[2],edate[1]-1,edate[0]).getTime();
    var edate1 = to_date.split("-");
    e_date1 = new Date(edate1[2],edate1[1]-1,edate1[0]).getTime();

    var one_day=1000*60*60*24;

    var from_date_ms = new Date(e_date).getTime();
    var to_date_ms = new Date(e_date1).getTime();
    
    var difference_ms = to_date_ms - from_date_ms;
    var total_days = Math.round(Math.abs(difference_ms)/one_day); 

    total_days = parseFloat(total_days)+1;
    $('#no_of_days').val(total_days);
}
function list_reflect()
{
  var branch_status = $('#branch_status_req').val();
	$.post('leave_request/list_reflect.php',{branch_status:branch_status}, function(data){
		$('#div_list').html(data);
	});
}
list_reflect();

function save_modal(){

  $('#lreq_btn').prop('disabled',true);
  $('#lreq_btn').button('loading');
  $.post('leave_request/save_modal.php', {}, function(data){
    $('#lreq_btn').prop('disabled',false);
    $('#lreq_btn').button('reset');
    $('#div_modal').html(data);
  });
} 
</script>
<?= end_panel() ?>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<?php
/*======******Footer******=======*/
// require_once('../../layouts/admin_footer.php'); 
?>