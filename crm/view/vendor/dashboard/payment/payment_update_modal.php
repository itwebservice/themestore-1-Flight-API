<?php 
include_once('../../../../model/model.php');
include_once('../../inc/vendor_generic_functions.php');
$payment_id = $_POST['payment_id'];
$sq_payment = mysqli_fetch_assoc(mysqlQuery("select * from vendor_payment_master where payment_id='$payment_id'"));

$sq_est = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where estimate_id='$sq_payment[estimate_id]'"));

$sq_paid = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as payment_amount from vendor_payment_master where  estimate_id='$sq_payment[estimate_id]'"));
$total_paid = $sq_paid['payment_amount'];
$cancel_est = $sq_est['cancel_amount'];

$row_estimate = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where estimate_id = '$sq_payment[estimate_id]'"));
$vendor_type_val = get_vendor_name($row_estimate['vendor_type'], $row_estimate['vendor_type_id']);
$estimate_type_val = get_estimate_type_name($row_estimate['estimate_type'], $row_estimate['estimate_type_id']);
$date = $row_estimate['purchase_date'];
$yr = explode("-", $date);
$year = $yr[0];
$estimate_id = get_vendor_estimate_id($row_estimate['estimate_id'],$year)." : ".$vendor_type_val."(".$row_estimate['vendor_type'].") : ".$estimate_type_val;
$balance_amount = 0;
if($sq_est['purchase_return'] == '1'){
  if($total_paid > 0){
    if($cancel_est >0){
      if($total_paid > $cancel_est){
        $balance_amount += 0;
      }else{
        $balance_amount += $cancel_est - $total_paid;
      }
    }else{
      $balance_amount += 0;
    }
  }
  else{
    $balance_amount += $cancel_est;
  }
}else if($sq_est['purchase_return'] == '2'){
  $cancel_estimate = json_decode($sq_est['cancel_estimate']);
  $balance_amount += (($sq_est['net_total'] - floatval($cancel_estimate[0]->net_total)) + $cancel_est) - $total_paid;
}
else{
  $balance_amount += $sq_est['net_total'] - $total_paid;
}
$enable = ($sq_payment['payment_mode']=="Cash" || $sq_payment['payment_mode']=="Credit Card" || $sq_payment['payment_mode']=="Debit Note" || $sq_payment['payment_mode']=="Advance") ? "disabled" : "";
?>
<div class="modal fade" id="payment_update_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document" style="margin-top:20px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Payment Update</h4>
      </div>
      <div class="modal-body">
        

        <form id="frm_vendor_payment_update">

        <input type="hidden" id="payment_id_update" name="payment_id_update" value="<?= $payment_id ?>">
        <input type="hidden" id="payment_old_value" name="payment_old_value" value="<?= $sq_payment['payment_amount'] ?>">
        <input type="hidden" id="payment_old_mode" name="payment_old_mode" value="<?= $sq_payment['payment_mode'] ?>">
        <input type="hidden" id="balance_amount" name="balance_amount" value="<?= $balance_amount ?>">

          <div class="panel panel-default panel-body app_panel_style mg_tp_20 feildset-panel">
          <legend>Purchase Details</legend>

          <div class="row mg_bt_10">
              <div class="col-md-6">
                <input type="text" value="<?= $estimate_id ?>" readonly/>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <select name="vendor_type1" id="vendor_type1" title="Supplier Type" onchange="vendor_type_data_load(this.value, 'div_vendor_type_content1', '1')" disabled>
                  <option value="<?= $sq_payment['vendor_type'] ?>"><?= $sq_payment['vendor_type'] ?></option>
                  <?php 
                    $sq_vendor = mysqlQuery("select * from vendor_type_master order by vendor_type");
                    while($row_vendor = mysqli_fetch_assoc($sq_vendor)){
                      ?>
                      <option value="<?= $row_vendor['vendor_type'] ?>"><?= $row_vendor['vendor_type'] ?></option>
                      <?php
                    }
                  ?>
                </select>
              </div>
              <div id="div_vendor_type_content1" style="pointer-events: none;"></div>
              <script>
                vendor_type_data_load('<?= $sq_payment['vendor_type'] ?>', 'div_vendor_type_content1', '1', <?= $sq_payment['vendor_type_id'] ?>);
              </script>
              <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                  <select name="estimate_type3" id="estimate_type1" title="Purchase Type" onchange="payment_for_data_load(this.value, 'div_payment_for_content1', '1','')" disabled>
                    <option value="<?= $sq_payment['estimate_type'] ?>"><?= $sq_payment['estimate_type'] ?></option>
                    <option value="">Purchase Type</option>
                    <?php 
                    $sq_estimate_type = mysqlQuery("select * from estimate_type_master order by id");
                    while($row_estimate = mysqli_fetch_assoc($sq_estimate_type)){
                      ?>
                      <option value="<?= $row_estimate['estimate_type'] ?>"><?= $row_estimate['estimate_type'] ?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div>
                <div id="div_payment_for_content1" style="pointer-events: none;"></div>
              <script>
                payment_for_data_load('<?= $sq_payment['estimate_type'] ?>', 'div_payment_for_content1', '1', <?= $sq_payment['estimate_type_id'] ?>);
              </script>
            </div>

          </div>

          <div class="panel panel-default panel-body app_panel_style mg_tp_20 feildset-panel">
          <legend>Update Payment</legend>

             <div class="row mg_bt_20">                      
              <div class="col-md-4">
                <input type="text" id="payment_date1" name="payment_date1" class="form-control" placeholder="Date" title="Payment Date" value="<?= date('d-m-Y', strtotime($sq_payment['payment_date'])) ?>" readonly>
              </div>  
              <div class="col-md-4">
                <input type="text" id="payment_amount1" name="payment_amount1" class="form-control" placeholder="Amount" title="Payment Amount" value="<?= $sq_payment['payment_amount'] ?>" onchange="validate_balance(this.id)">
              </div>             
              <div class="col-md-4">
                <select name="payment_mode1" id="payment_mode1" class="form-control" title="Payment Mode" onchange="payment_master_toggles(this.id, 'bank_name1', 'transaction_id1', 'bank_id1')" disabled>
                  <option value="<?= $sq_payment['payment_mode'] ?>"><?= $sq_payment['payment_mode'] ?></option>                  
                    <option value="">*Payment Mode</option>
                    <option value="Cash"> Cash </option>
                    <option value="Cheque"> Cheque </option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="NEFT"> NEFT </option>
                    <option value="RTGS"> RTGS </option>
                    <option value="IMPS"> IMPS </option>
                    <option value="DD"> DD </option>
                    <option value="Online"> Online </option>
                    <option value="Debit Note"> Debit Note </option>
                    <option value="Advance">Advance</option>
                    <option value="Other"> Other </option>
                </select>
              </div>
            </div>
            <div class="row mg_bt_10">
              <div class="col-md-4">
                <input type="text" id="bank_name1" name="bank_name1" class="form-control bank_suggest" placeholder="Bank Name" title="Bank Name" value="<?= $sq_payment['bank_name'] ?>" <?= $enable ?>>
              </div>
              <div class="col-md-4">
                <input type="number" id="transaction_id1" onchange="validate_balance(this.id);" name="transaction_id1" class="form-control" placeholder="Cheque No/ID" title="Cheque No/ID" value="<?= $sq_payment['transaction_id'] ?>" <?= $enable ?>>
              </div>
               <div class="col-md-4">
                <select name="bank_id1" id="bank_id1" title="Debitor Bank" <?= $enable ?> disabled>
                  <?php 
                  $sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from bank_master where bank_id='$sq_payment[bank_id]'"));
                  if($sq_bank['bank_id'] != ''){
                  ?>
                  <option value="<?= $sq_bank['bank_id'] ?>"><?= $sq_bank['bank_name'] ?></option>
                  <?php  } get_bank_dropdown('Debitor Bank'); ?>
                </select>
              </div>
            </div>  
            <div class="row">
              <div class="col-md-8">
                  <div class="div-upload pull-left" id="div_upload_button1">
                      <div id="payment_evidence_upload1" class="upload-button1"><span>Payment Evidence</span></div>
                      <span id="payment_evidence_status1" ></span>
                      <ul id="files" ></ul>
                      <input type="hidden" id="payment_evidence_url1" name="payment_evidence_url1" value="<?= $sq_payment['payment_evidence_url'] ?>">
                  </div>
                </div>
            </div>         

          </div>

          <div class="row text-center">
              <div class="col-md-12">
                <button class="btn btn-sm btn-success" id="btn_update"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Update</button>
              </div>
          </div>

        </form>



      </div>      
    </div>
  </div>
</div>
<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>
<script>
$('#payment_update_modal').modal('show');

payment_evidence_upload(1);
function payment_evidence_upload(offset='')
{
    var btnUpload=$('#payment_evidence_upload'+offset);
    var status=$('#payment_evidence_status'+offset);
    // status.text('Payment Evidence');
    new AjaxUpload(btnUpload, {
      action: 'payment/upload_payment_evidence.php',
      name: 'uploadfile',
      onSubmit: function(file, ext){

        var id_proof_url = $("#payment_evidence_url"+offset).val();
          
        if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
          status.text('Only JPG, PNG files are allowed');
          //return false;
        }
        status.text('Uploading...');
      },
      onComplete: function(file, response){
        //On completion clear the status
		status.text('');
        //Add uploaded file to list
        if(response==="error"){          
          alert("File is not uploaded.");     
          //$('<li></li>').appendTo('#files').html('<img src="./uploads/'+file+'" alt="" /><br />'+file).addClass('success');
        } else{
          ///$('<li></li>').appendTo('#files').text(file).addClass('error');
        	$("#payment_evidence_url"+offset).val(response);
        	status.text('Uploaded');
        	msg_alert('File uploaded!');
        }
      }
    });

}
$(function(){
  $('#frm_vendor_payment_update').validate({
      rules:{
              payment_amount1 : { required: true, number:true },
              payment_date1 : { required: true },
              payment_mode1 : { required : true }, 
              bank_id1 : { required : function(){  if($('#payment_mode1').val()!="Cash"){ return true; }else{ return false; }  }  },     
      },
      submitHandler:function(form){

              $('#btn_update').prop('disabled',true);
              var payment_id = $('#payment_id_update').val();

              var vendor_type = $('#vendor_type1').val();
              var vendor_type_id = get_vendor_type_id('vendor_type1', '1');

              var estimate_type = $('#estimate_type1').val();
              var estimate_type_id = get_estimate_type_id('estimate_type1','1');

              var payment_amount = $('#payment_amount1').val();
              var payment_date = $('#payment_date1').val();
              var payment_mode = $('#payment_mode1').val();
              var bank_name = $('#bank_name1').val();
              var transaction_id = $('#transaction_id1').val();
              var bank_id = $('#bank_id1').val();
              var payment_evidence_url = $('#payment_evidence_url1').val();
              var payment_old_value = $('#payment_old_value').val();
              var payment_old_mode = $('#payment_old_mode').val();
              var balance_amount = $('#balance_amount').val();

              if(!check_updated_amount(payment_old_value,payment_amount)){
                $('#btn_update').prop('disabled',false);
                error_msg_alert("You can update payment to 0 only!");
                return false;
              }

              if(payment_amount > payment_old_value){
                var balance_paying = parseFloat(payment_amount) - parseFloat(payment_old_value);
                if(parseFloat(balance_paying) > parseFloat(balance_amount)){
                  error_msg_alert('Payment should not be more than purchase amount!');
                  $('#btn_update').prop('disabled',false);
                  return false; 
                }
              }
              
              $.ajax({
                type:'post',
                url: base_url()+'controller/vendor/dashboard/payment/payment_update.php',
                data:{ payment_id : payment_id, vendor_type : vendor_type, vendor_type_id : vendor_type_id,estimate_type : estimate_type,estimate_type_id : estimate_type_id, payment_amount : payment_amount, payment_date : payment_date, payment_mode : payment_mode, bank_name : bank_name, transaction_id : transaction_id, bank_id : bank_id, payment_evidence_url : payment_evidence_url,payment_old_value : payment_old_value,payment_old_mode : payment_old_mode },
                success:function(result){
                  msg_alert(result);
                  $('#payment_update_modal').modal('hide');
                  $('#btn_update').prop('disabled',false);
                  $('#payment_update_modal').on('hidden.bs.modal', function(){
                    payment_list_reflect();
                  });
                },
                error:function(result){
                  console.log(result.responseText);
                }
              });


      }
  });
});
</script>  
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>