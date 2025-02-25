<?php
include "../../../../../model/model.php";
$q = "select * from branch_assign where link='booking/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
?>
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>" >
<div class="app_panel_content Filter-panel mg_bt_10">
    
  <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
          <select id="tour_id_filter" name="tour_id_filter" onchange="tour_group_dynamic_reflect1(this.id,'group_id_filter');" style="width:100%" title="Tour Name" class="form-control"> 
              <option value="">Tour Name</option>
              <?php
                  $sq=mysqlQuery("select tour_id,tour_name from tour_master where active_flag='Active' order by tour_name");
                  while($row=mysqli_fetch_assoc($sq))
                  {
                    echo "<option value='$row[tour_id]'>".$row['tour_name']."</option>";
                  }    
              ?>
          </select>
        </div>
      <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
          <select class="form-control" id="group_id_filter" name="group_id_filter"  title="Tour Group" onchange="travelers_booking_reflect(this.id,'tour_id_filter','booking_id_filter3');"> 
              <option value="">Tour Group</option>        
          </select>
      </div>
	    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
	      <select id="booking_id_filter3" name="booking_id_filter" style="width:100%" title="Booking ID" class="form-control" > 
	          <?php get_group_booking_dropdown($role, $branch_admin_id, $branch_status,$emp_id); ?>
	      </select>
		  </div>
      <div class="col-md-3 col-sm-6 col-xs-12 form-group">
          <button class="btn btn-sm btn-info ico_right" onclick="collection_reflect()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
      </div>
</div>
<div id="div_list" class="main_block mg_tp_20">
<div class="row"> <div class="col-md-12 no-pad"> <div class="table-responsive">
<table id="gtc_tour_report" class="table table-hover" style="margin: 20px 0 !important;">         
</table>
</div></div></div>
</div>
<script>
  $('#tour_id_filter').select2();
  var column = [
	{ title: "S_No." },
	{ title: "Refund_date" },
	{ title: "Booking_id" },
	{ title: "Passenger_name" },
	{ title: "cheque_no/id" },
	{ title: "bank_Name" },
	{ title: "Refund_Mode" },
	{ title: "Refund_Amount", className: "success" }
];
	function collection_reflect(){
		var tour_id = $('#tour_id_filter').val();
    var group_id = $('#group_id_filter').val();
    var id = $('#booking_id_filter3').val();
    var branch_status = $('#branch_status').val();
		$.post('reports_content/group_tour/refund_cancelled_traveler_report/refund_cancelled_traveler_report_filter.php', {id:id,tour_id : tour_id,group_id : group_id,branch_status:branch_status}, function(data){
      pagination_load(data, column, true, true, 20, 'gtc_tour_report',true);
	});
	}
  collection_reflect();

   function tour_group_dynamic_reflect1(id, goup_id)
{
  var base_url = $('#base_url').val();
  var tour_id=document.getElementById(id).value;  

  $.get(base_url+"view/reports/reports_content/group_tour/refund_tour_cancelation_report/tour_group_reflect.php", { tour_id : tour_id }, function(data){
      $('#'+goup_id).html(data);
  });
}
</script>