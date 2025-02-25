<?php
global $show_entries_switch;
$role = $_SESSION['role'];
$emp_id = $_SESSION['emp_id'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$q = "select * from branch_assign where link='tasks/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
$cur_datetime = date('d-m-Y H:i');
?>
<input type="hidden" id="branch_admin_id1" name="branch_admin_id1" value="<?= $branch_admin_id ?>">
<div class="modal fade" id="tasks_save_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">New Task</h4>
      </div>
      <div class="modal-body">

        <form id="frm_task_save">

          <div class="row mg_bt_10">
            <div class="col-md-12">
              <textarea name="task_name" id="task_name" placeholder="*Task Name" onchange="validate_spaces(this.id);" title="Task Name"></textarea>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-6 mg_bt_10">
              <input type="text" id="due_date" name="due_date" placeholder="*Due Date & Time" title="Due Date & Time" value="<?= $cur_datetime ?>">
            </div>
            <div class="col-sm-6 mg_bt_10">
              <select name="assign_to" id="assign_to" style="width: 100%" title="Assign To">
                <option value="">*Assign To</option>
                <?php
                $q = "select emp_id,first_name,last_name from emp_master where active_flag='Active' order by first_name desc";
                
                $sq_emp = mysqlQuery($q);
                while ($row_emp = mysqli_fetch_assoc($sq_emp)) {
                ?>
                  <option value="<?= $row_emp['emp_id'] ?>"><?= $row_emp['first_name'] . ' ' . $row_emp['last_name'] ?></option>
                <?php
                } ?>
              </select>
            </div>
            <div class="col-sm-6 mg_bt_10">
              <select name="remind" id="remind" title="Reminder">
                <option value="">*Reminder</option>
                <option value="None">None</option>
                <option value="On Due Time">On Due Time</option>
                <option value="Before 15 Mins">Before 15 Mins</option>
                <option value="Before 30 Mins">Before 30 Mins</option>
                <option value="Before 1 Hour">Before 1 Hour</option>
                <option value="Before 1 Day">Before 1 Day</option>
              </select>
            </div>
            <div class="col-sm-6 mg_bt_10">
              <select name="remind_by" id="remind_by" title="Remind By">
                <option value="">Remind By </option>
                <option value="Email And SMS">Email And SMS</option>
                <option value="Email">Email</option>
                <option value="SMS">SMS</option>
              </select>
            </div>
            <div class="col-sm-6 mg_bt_10">
              <select name="task_type" id="task_type" title="Task Type" onchange="tasks_type_reference_reflect(this.id, 'frm_task_save')">
                <option value="">*Task Type</option>
                <option value="Group Tour">Group Tour</option>
                <option value="Package Tour">Package Tour</option>
                <option value="Enquiry">Enquiry</option>
                <option value="Other">Other</option>

              </select>
            </div>
            <div class="col-sm-6 mg_bt_10 hidden booking_id">
              <select id="booking_id" name="booking_id" style="width:100%">
                <option value="">*Select Booking ID</option>
                <?php
                $query = "select * from package_tour_booking_master where 1 and delete_status='0' ";
                include "../../model/app_settings/branchwise_filteration.php";
                $sq_booking = mysqlQuery($query);
                while ($row_booking = mysqli_fetch_assoc($sq_booking)) {

                  $pass_count = mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_booking[booking_id]'"));
                  $cancle_count = mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_booking[booking_id]' and status='Cancel'"));
                  if ($pass_count != $cancle_count) {
                    $date = $row_booking['booking_date'];
                    $yr = explode("-", $date);
                    $year = $yr[0];
                    $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
                    if ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') {
                ?>
                      <option value="<?php echo $row_booking['booking_id'] ?>"><?php echo get_package_booking_id($row_booking['booking_id'], $year) . "-" . " " . $sq_customer['company_name']; ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $row_booking['booking_id'] ?>"><?php echo get_package_booking_id($row_booking['booking_id'], $year) . "-" . " " . $sq_customer['first_name'] . " " . $sq_customer['last_name']; ?></option>
                <?php
                    }
                  }
                }
                ?>
              </select>
            </div>
            <div class="col-md-6 hidden enquiry_id">

              <select name="enquiry_id" id="enquiry_id" title="Enquiry" style="width:100%">
                <option value="">*Select Enquiry</option>
                <?php
                if ($role == 'Admin') {
                  $sq_enquiry = mysqlQuery("select * from enquiry_master where status!='Disabled' order by enquiry_id desc");
                } else {
                  if ($branch_status == 'yes') {
                    if ($role == 'Branch Admin' || $role == 'Accountant' || $role_id > '7') {
                      $sq_enquiry = mysqlQuery("select * from enquiry_master where status!='Disabled' and branch_admin_id='$branch_admin_id' order by enquiry_id desc");
                    } elseif ($role != 'Admin' && $role != 'Branch Admin' && $role_id != '7' && $role_id < '7') {
                      if($show_entries_switch == 'No'){
                        $q = "select * from enquiry_master where assigned_emp_id='$emp_id' and status!='Disabled' order by enquiry_id desc";  
                      }
                      else{
                        if($role == 'Backoffice'){

                          $q = "select * from enquiry_master where assigned_emp_id in(select emp_id from emp_master where branch_id='$branch_admin_id') and status!='Disabled' order by enquiry_id desc";
                        }else{
                          $q = "select * from enquiry_master where assigned_emp_id='$emp_id' and status!='Disabled' order by enquiry_id desc";
                        }
                      }
                      
                      $sq_enquiry = mysqlQuery($q);
                    }
                  }
                  elseif ($branch_status != 'yes' && ($role == 'Branch Admin' || $role_id == '7')) {

                    $sq_enquiry = mysqlQuery("select * from enquiry_master where status!='Disabled' order by enquiry_id desc");
                  }
                  elseif ($role != 'Admin' && $role != 'Branch Admin' && $role_id != '7' && $role_id < '7') {
                    
                    if($show_entries_switch == 'No'){
                      $q = "select * from enquiry_master where assigned_emp_id='$emp_id' and status!='Disabled' order by enquiry_id desc";  
                    }
                    else{
                      if($role == 'Backoffice'){

                        $q = "select * from enquiry_master where assigned_emp_id in(select emp_id from emp_master where branch_id='$branch_admin_id') and status!='Disabled' order by enquiry_id desc";
                      }else{
                        $q = "select * from enquiry_master where assigned_emp_id='$emp_id' and status!='Disabled' order by enquiry_id desc";
                      }
                    }
                    $sq_enquiry = mysqlQuery($q);
                  }
                }
                while ($row_enquiry = mysqli_fetch_assoc($sq_enquiry)) {

                  $sq_enq1 = mysqli_fetch_assoc(mysqlQuery("SELECT followup_status FROM `enquiry_master_entries` WHERE `enquiry_id` = '$row_enquiry[enquiry_id]' ORDER BY `entry_id` DESC"));
                  if ($sq_enq1['followup_status'] != 'Dropped') {
                ?>
                    <option value="<?= $row_enquiry['enquiry_id'] ?>">Enq:<?= $row_enquiry['enquiry_id'] ?>-<?= $row_enquiry['name'] ?></option>
                <?php }
                } ?>
              </select>
            </div>
          </div>

          <div class="row mg_bt_20 hidden tour_group_id">
            <div class="col-md-6">
              <select style="width:100%" id="tour_id" name="tour_id" onchange="tour_group_dynamic_reflect(this.id, 'tour_group_id');">
                <option value=""> *Select Tour </option>
                <?php
                $sq = mysqlQuery("select tour_id,tour_name from tour_master where active_flag='Active'");
                while ($row = mysqli_fetch_assoc($sq)) {
                  echo "<option value='$row[tour_id]'>" . $row['tour_name'] . "</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-6">
              <select name="tour_group_id" id="tour_group_id" title="Select Tour Date" style="width:100%">
                <option value="">*Select Tour Date</option>
              </select>
            </div>
          </div>
          <div class="row text-center mg_tp_20">
            <div class="col-md-12">
              <button class="btn btn-sm btn-success" id="task_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  $('#cmb_booking_id, #enquiry_id, #tour_id, #tour_group_id,#assign_to').select2();
  $('#due_date').datetimepicker({
    format: 'd-m-Y H:i'
  });
  $(function() {
    $('#frm_task_save').validate({
      rules: {
        task_name: {
          required: true
        },
        due_date: {
          required: true
        },
        assign_to: {
          required: true
        },
        remind: {
          required: true
        },
        task_type: {
          required: true
        },
      },
      submitHandler: function(form) {
        var task_name = $('#task_name').val();
        var due_date = $('#due_date').val();
        var assign_to = $('#assign_to').val();
        var remind = $('#remind').val();
        var remind_by = $('#remind_by').val();
        var branch_admin_id = $('#branch_admin_id1').val();
        var task_type = $('#task_type').val();

        if (task_type == "Other") {
          var task_type_field_id = "";
        }
        if (task_type == "Group Tour") {
          var task_type_field_id = $('#tour_group_id').val();
          if (task_type_field_id == "") {
            error_msg_alert("Please select tour date");
            return false;
          }
        }
        if (task_type == "Package Tour") {
          var task_type_field_id = $('#booking_id').val();;
          if (task_type_field_id == "") {
            error_msg_alert("Please select booking");
            return false;
          }
        }
        if (task_type == "Enquiry") {
          var task_type_field_id = $('#enquiry_id').val();
          if (task_type_field_id == "") {
            error_msg_alert("Please select enquiry");
            return false;
          }
        }

        var base_url = $('#base_url').val();
        $('#task_save').button('loading');
        if (remind_by != "None") {
          whatsapp_send(task_name, due_date, assign_to, base_url);
        }
        $.ajax({
          type: 'post',
          url: base_url + 'controller/tasks/task_save.php',
          data: {
            task_name: task_name,
            due_date: due_date,
            assign_to: assign_to,
            remind: remind,
            remind_by: remind_by,
            task_type: task_type,
            task_type_field_id: task_type_field_id,
            branch_admin_id: branch_admin_id
          },
          success: function(result) {
            msg_alert(result);
            notification_count_update();
            $('#tasks_save_modal').modal('hide');
            reset_form('frm_task_save');
            $('#task_save').button('reset');
            tasks_list_reflect();
          }
        });
      }
    });
  });
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>