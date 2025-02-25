<?php 
include "../../../../model/model.php";
$misc_id = $_POST['misc_id'];
?>
<div class="modal fade profile_box_modal" id="visa_display_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Purchase History</h4>
      </div>
      <div class="modal-body profile_box_padding">
	     <div class="row mg_bt_20">    
		  	<div class="col-xs-12">
		  		<div class="profile_box">
		            <div class="table-responsive">
                    <table  class="table table-bordered no-marg">
	                   <thead>
                         <tr class="table-heading-row">
                            <th>S_No.</th>
                            <th>Purchase_Date</th>
                            <th>Supplier_Type</th>
                            <th>Supplier Name</th>
                            <th>Amount</th>
                         </tr>
                     </thead>
                     <tbody>
                       <?php 
                       $count = 0;
                       $sq_query = mysqlQuery("SELECT * FROM vendor_estimate WHERE status!='Cancel' and estimate_type LIKE  'miscellaneous' AND estimate_type_id = '$misc_id' and delete_status='0'");

                      while($row_entry = mysqli_fetch_assoc($sq_query))
                      {
                          $Supplier_name = get_vendor_name_report($row_entry['vendor_type'] ,$row_entry['vendor_type_id']);
                          $bg = ($row_entry['status']=="Cancel") ? "danger" : "";

                          if($row_entry['purchase_return'] == 0){
                            $total_purchase = $row_entry['net_total'];
                          }
                          else if($row_entry['purchase_return'] == 2){
                            $cancel_estimate = json_decode($row_entry['cancel_estimate']);
                            $p_purchase = ($row_entry['net_total'] - floatval($cancel_estimate[0]->net_total));
                            $total_purchase = $p_purchase;
                          }
                          $count++;
                          ?>
                          <tr>
                              <td><?php echo $count; ?></td>
                              <td><?php echo get_date_user($row_entry['purchase_date']); ?></td>
                              <td><?php echo $row_entry['vendor_type']; ?></td>
                              <td><?php echo $Supplier_name; ?></td>
                              <td><?php echo number_format($total_purchase,2); ?></td>
                          </tr>
                          <?php

                        }
                       ?>
                     </tbody>
		                </table>
		            </div>
		            </div>
		        </div>  
		    </div>
		</div>	

</div>

</div>
</div>
</div>
  
</div>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script>
$('#visa_display_modal').modal('show');
</script>