<?php
include "../../model/model.php";
$role_id = $_POST['role_id'];
$role_name = $_POST['role_name'];
?>
<div class="panel panel-default panel-body mg_tp_20 mg_bt_-1">
	<h4>User Permissions</h4>
	<?php
	$count=0;
	$alternate = true;
	$class_name = '';
	$rank_array = array();
	$xml=simplexml_load_file("../../xml/role_management.xml");
	foreach($xml->menu_desc as $value){

	$name = $value->name;
	$link = $value->link;
	$rank = $value->rank;
	$priority = $value->priority;
	$description = $value->description;
	$icon = $value->menu_icon;

	$assigned_role = mysqli_num_rows(mysqlQuery("select * from user_assigned_roles where role_id='$role_id' and name='$name' and link='$link' "));
	if($assigned_role>=1){
		$status = "checked";
	}	
	else{
		$status = "";
	}
	if($link=="privileges/index.php"){
		$enable_status = "disabled";
	}
	else{
		$enable_status = "";
	}	
	$count++;

	if(!in_array($rank, $rank_array)){

		$bg = ($alternate) ? "#f5f5f5" : "";
		$alternate = !$alternate;
		?>
		</div>
		<div class="panel panel-default panel-body mg_bt_-1 pd_tp_5 pd_bt_0" style="background:<?= $bg ?>; padding-left:30px">
		<?php
	}

	if($priority==1){
		echo "<div class='row'><div class='col-md-12' style='padding:10px 0 0px 0;'>";
		$class_name = $name;
	}else{
		$class_name = $class_name;
	}
	$class_name = str_replace(array(' ','/'),'_',$class_name);
	?>
	<div class="col-sm-6 col-md-3 mg_bt_10">
		<input id="<?php echo 'chk_'.$count ?>" name="chk_role_mgt" data-offset="<?= $count ?>" type="checkbox" <?php echo $status; ?> <?php echo $enable_status; ?> class="<?= $class_name ?>" onclick="select_all_child_boxes(this.id,'<?= $class_name?>','<?= $priority?>')">
		&nbsp;&nbsp;<label for="<?php echo 'chk_'.$count ?>"><?php echo $name ?></label>
		<input type="hidden" id="role_name_<?= $count ?>" value="<?php echo $name ?>">
		<input type="hidden" id="role_link_<?= $count ?>" value="<?php echo $link ?>">
		<input type="hidden" id="role_rank_<?= $count ?>" value="<?php echo $rank ?>">
		<input type="hidden" id="role_priority_<?= $count ?>" value="<?php echo $priority ?>">
		<input type="hidden" id="role_description_<?= $count ?>" value="<?php echo $description ?>">
		<input type="hidden" id="role_icon_<?= $count ?>" value="<?php echo $icon ?>">
	</div>
	<?php
		if($priority==1){
			echo "</div></div>";
		}
		if(!in_array($rank, $rank_array)){
			$rank = (string)$rank;
			$rank_array[] = $rank;
		} 	
	}
	?>
</div>
<div class="panel panel-default panel-body mg_bt_10 text-center pad_8">
	<button class="btn btn-sm btn-success" id="btn_save" onclick="role_mgt_save()"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
</div>
<script>
function select_all_child_boxes(id,custom_package,priority){
	if(priority == 1){
		var checked = $('#'+id).is(':checked');
		// Select all
		if(checked){
			$('.'+custom_package).each(function() {
				$(this).prop("checked",true);
			});
		}
		else{
			// Deselect All
			$('.'+custom_package).each(function() {
				$(this).prop("checked",false);
			});
		}
	}
}
/////////////***********User roles save start******************************************************
function role_mgt_save()
{
	var base_url = $("#base_url").val(); 
	var role_id = $("#role_id").val(); 

	if(role_id==''){
		alert("Please select user role first.");
		return false;
	}

	var name = new Array();
	var link = new Array(); 
	var rank = new Array(); 
	var priority = new Array(); 
	var description = new Array(); 
	var icon = new Array(); 

	$('input[name="chk_role_mgt"]').each(function(){

		if($(this).is(':checked')){
		var offset = $(this).attr('data-offset');
		var name1 = $('#role_name_'+offset).val();
		var link1 = $('#role_link_'+offset).val();
		var rank1 = $('#role_rank_'+offset).val();
		var priority1 = $('#role_priority_'+offset).val();
		var description1 = $('#role_description_'+offset).val();
		var icon1 = $('#role_icon_'+offset).val();
		
		name.push(name1);       
		link.push(link1);       
		rank.push(rank1);       
		priority.push(priority1);       
		description.push(description1);         
		icon.push(icon1); 

		}

	});
	$('#btn_save').button('loading');
	$('#vi_confirm_box').vi_confirm_box({
		message : "Change privileges for <?= $role_name ?> ?",
		callback : function (answer){
			if(answer == "yes"){
				$.post( 
	
				base_url+"controller/group_tour/role_mgt_save.php",
				{ role_id : role_id, 'name[]' : name, 'link[]' : link, 'rank[]' : rank, 'priority[]' : priority, 'description[]' : description, 'icon[]': icon  },
				function(data) {   
					$('#btn_save').button('reset');
					msg_popup_reload(data);
					});
			}
			else{
				$('#btn_save').button('reset');
			}
		}
	});
}
/////////////***********User roles save end*****************************************************
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>