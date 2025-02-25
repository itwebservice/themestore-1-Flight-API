<?php 
include "../../../model/model.php";

$sms_message_id = $_POST['sms_message_id'];

$sq_sms_message_info = mysqli_fetch_assoc(mysqlQuery("select * from sms_message_master where sms_message_id='$sms_message_id'"));
?>
<div class="modal fade" id="sms_message_update_modal"  role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Update Text</h4>
      </div>
      <div class="modal-body">

		<form id="frm_sms_message_update">

		<input type="hidden" id="sms_message_id" name="sms_message_id" value="<?= $sms_message_id ?>">

      	<div class="row mg_bt_10">
      		<div class="col-md-12">
      			<textarea id="message1" name="message1"  onchange="validate_spaces(this.id);" placeholder="*Message Text" title="Message Text"><?= $sq_sms_message_info['message'] ?></textarea>
      		</div>
        </div>
        <div class="row text-center">
      		<div class="col-md-12">
      			<button class="btn btn-sm btn-success"><i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;Update</button>
      		</div>
      	</div>

      	</form>
        
      </div>      
    </div>
  </div>
</div>

<script>
$('#sms_message_update_modal').modal('show');

$(function(){
  $('#frm_sms_message_update').validate({
    rules:{ 
        sms_message_id : { required:true },
        message1 : { required:true , maxlength:160 }
    },
    submitHandler:function(form){

      var sms_message_id = $('#sms_message_id').val();
      var message = $('#message1').val();
      var base_url = $('#base_url').val();

      $.ajax({
        type:'post',
        url:base_url+'controller/promotional_sms/messages/sms_message_update.php',
        data: { sms_message_id : sms_message_id, message : message },
        success:function(result){
          msg_alert(result);
          $('#sms_message_update_modal').modal('hide');
          $('#sms_message_update_modal').on('hidden.bs.modal', function () {
			    sms_message_list_reflect();
		  });
        }
      });

    }
  });
});
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>