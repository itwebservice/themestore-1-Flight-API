<?php
include "../../../model/model.php";
$id = $_POST['id'];
$sq_info = mysqli_fetch_assoc(mysqlQuery("select * from fourth_coming_attraction_master where id='$id'"));
?>
<form id="frm_fourth_coming_attraction_update">

<input type="hidden" id="id" name="id" value="<?= $id ?>">

	<div class="modal fade" id="fouth_coming_attractions_update_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel">Update Sightseeing Attraction</h4>
		</div>
		<div class="modal-body">
		
		<div class="row">
			<div class="col-md-6 mg_bt_10">
				<input class="form-control" type="text" id="txt_title1"  onchange="validate_spaces(this.id);fname_validate(this.id);" name="txt_title1" placeholder="*Title" title="Title" value="<?= $sq_info['title'] ?>">	
			</div>
			<div class="col-md-6 mg_bt_10">
				<input type="text" class="form-control" id="txt_valid_date1" name="txt_valid_date1" placeholder="*Valid Till" value="<?= date('d-m-Y', strtotime($sq_info['valid_date'])) ?>" title="Valid Till"/>    
			</div>
			<div class="col-md-12 mg_bt_10">
				<textarea class="form-control"  onchange="validate_spaces(this.id);validate_limit(this.id);" id="txt_description1" name="txt_description1" placeholder="*Description" title="Description" ><?= $sq_info['description'] ?></textarea>	
			</div>
			</div>
		<div class="row mg_bt_10">
			<div class="col-md-12"> 
				<div class="div-upload">
					<div id="hotel_upload_btn" class="upload-button1"><span>Upload Images</span></div>
					<span id="id_proof_status" ></span>
					<ul id="files" ></ul>
					<input type="hidden" id="hotel_upload_url" name="hotel_upload_url">
				</div>
				<button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note : Upload Image size below 300KB, resolution : 900X450. (Upload Maximum 3 images)"><i class="fa fa-question-circle"></i></button>
			</div>
		</div>
		<div id="images"></div>
			<div class="row text-center"> 
				<div class="col-md-12">
					<button id="update_attr" class="btn btn-sm btn-success"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Update</button>
				</div>	
			</div>
		</div>
	</div>
	</div>
</div>    

</form>      

<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>
<script>
	$('#txt_valid_date1').datetimepicker({ timepicker:false, format:'d-m-Y', minDate: new Date() });
	$('#fouth_coming_attractions_update_modal').modal('show');

	upload_hotel_pic_attch();
	function upload_hotel_pic_attch()
	{
		var btnUpload=$('#hotel_upload_btn');

		$(btnUpload).find('span').text('Upload Images');
		$("#hotel_upload_url").val('');
		
		new AjaxUpload(btnUpload, {
		action: 'upload_images.php',
		name: 'uploadfile',
		onSubmit: function(file, ext){  

			if (! (ext && /^(jpg|png|jpeg)$/.test(ext))){ 
			error_msg_alert('Only JPG, PNG files are allowed');
			return false;
			}
			$(btnUpload).find('span').text('Uploading...');
		},
		onComplete: function(file, response){
			
			var msg = response.split('--');
	        if(msg[0]==="error"){
				error_msg_alert(msg[1]);          
				$(btnUpload).find('span').text('Upload Images');
				return false;
	        }else{
				$(btnUpload).find('span').text('Uploaded');
				$("#hotel_upload_url").val(response);
				upload_pic();
	        }
	    }
	    });
	}

	function upload_pic()
	{
		var base_url = $("#base_url").val();
		var upload_url = $('#hotel_upload_url').val();
		var fourth_id = $("#id").val();

		$.ajax({
			type:'post',
			url: base_url+'controller/attractions_offers_enquiry/attraction_img_update.php',
			data:{ upload_url : upload_url, fourth_id : fourth_id },
			success:function(result)
			{
				msg_alert(result);
			}
		});
	}

	function delete_image(image_id,entry_id){
		
		var base_url = $("#base_url").val();
		$("#vi_confirm_box").vi_confirm_box({
			callback: function(result){
				if(result=="yes"){
				$.ajax({
						type:'post',
						url: base_url+"controller/attractions_offers_enquiry/fourth_coming_attraction_master_img.php",
						data:{ image_id : image_id },
						success:function(result)
						{
						success_msg_alert(result);
						load_images(entry_id);
						}
				});    
				} }
		});
	}
	function load_images(fourth_id){

		$.ajax({
				type:'post',
				url: 'fourth_coming_attraction_images.php',
				data:{ fourth_id : fourth_id },
				success:function(result)
				{
					$('#images').html(result);
				}
		});
	}
	load_images(<?= $id ?>);

	$(function(){
	  $('#frm_fourth_coming_attraction_update').validate({
	    rules:{
	            txt_title1 : { required : true },
	            txt_description1 : { required : true },
	    },
	    submitHandler:function(form){

			$('#update_attr').prop('disabled',true);
			$('#update_attr').button('loading');
	        var base_url = $("#base_url").val();
	        var id = $("#id").val();
	        var title = $("#txt_title1").val();
	        var valid_date = $("#txt_valid_date1").val();
	        var description = $("#txt_description1").val();

	        $.post( 
	                 base_url+"controller/attractions_offers_enquiry/fourth_coming_attraction_master_update_c.php",
	                 { id : id, title : title, valid_date : valid_date, description : description },
	                 function(data) {  
	                        msg_alert(data);
							$('#update_attr').prop('disabled',false);
							$('#update_attr').button('reset');
	                        $('#fouth_coming_attractions_update_modal').modal('hide');
	                        fourth_coming_attractions_list_reflect();
	                 });
	    }
	  });
	});
</script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>