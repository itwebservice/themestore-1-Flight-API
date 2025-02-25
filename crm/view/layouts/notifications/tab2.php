<div class="notification_scroller notification_scroller_task">
	<ul class="no-pad">
		<?php
		$q = "select branch_status from branch_assign where link='tasks/index.php'";
		$sq_count = mysqli_num_rows(mysqlQuery($q));
		$sq = mysqli_fetch_assoc(mysqlQuery($q));
		$branch_status1 = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
		if($role!='Admin' && $role!='Branch Admin'){
			if($role_id==2 || $role_id==3 || $role_id==4 || $role_id==7){
				$sq_task = mysqlQuery("select * from tasks_master where task_status='Created' and emp_id='$emp_id' order by task_id desc limit 5");
			}
			else{
			$query = "SELECT x.* FROM 
				(
				(SELECT emp_id, task_name,task_status,status_date,due_date,created_at FROM tasks_master as tcr WHERE tcr.task_status = 'Created' and tcr.emp_id='$emp_id'";
				if($branch_status1=='yes'){
					$query .=" and tcr.branch_admin_id='$branch_admin_id'";
				}
				$query .= " order by tcr.created_at desc limit 5)";
				
				$query .= "UNION ALL
				
				(SELECT emp_id, task_name,task_status,status_date,due_date,created_at FROM tasks_master as tco WHERE tco.task_status = 'Completed' and tco.emp_id!='$emp_id'";
				if($branch_status1=='yes'){
					$query .=" and tco.branch_admin_id='$branch_admin_id'";
				}
				$query .= " order by tco.status_date desc limit 5) order by created_at desc limit 5 ) x ";
				$sq_task = mysqlQuery($query);
			}
			while($row_task = mysqli_fetch_assoc($sq_task)){
				$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_task[emp_id]'"));
				?>
				<li class="single_notification">
					<h5 class="single_notification_text no-marg"><?= $row_task['task_name'].'('.$sq_emp['first_name'].' '.$sq_emp['last_name'].')' ?></h5>
					<p class="single_notification_date_time no-marg"><?= date('d-m-Y H:i',strtotime($row_task['due_date'])).'('.$row_task['task_status'].')' ?></p>
				</li>
				<?php }
		}
		else{
			if($role=="Branch Admin"){
				$query = "SELECT x.* FROM (
					(SELECT emp_id, task_name,task_status,status_date,due_date,created_at FROM tasks_master as tcr WHERE tcr.task_status = 'Created' and tcr.emp_id='$emp_id'";
					if($branch_status1=='yes'){
						$query .=" and tcr.branch_admin_id='$branch_admin_id'";
					}
					$query .= " order by tcr.created_at desc limit 5)";
					
					$query .= "UNION ALL
					
					(SELECT emp_id, task_name,task_status,status_date,due_date,created_at FROM tasks_master as tco WHERE tco.task_status = 'Completed' and tco.emp_id!='$emp_id' ";
					if($branch_status1=='yes'){
						$query .=" and tco.branch_admin_id='$branch_admin_id'";
					}
					$query .= " order by tco.status_date desc limit 5) order by created_at desc limit 5 )x ";
			}
			else{
				$query = "select * from tasks_master where 1 and task_status='Completed' order by status_date desc limit 5";
			}
			$sq_task = mysqlQuery($query);
			while($row_task = mysqli_fetch_assoc($sq_task)){
				$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_task[emp_id]'"));
				?>
				<li class="single_notification">
					<h5 class="single_notification_text no-marg"><?= $row_task['task_name'].'('.$sq_emp['first_name'].' '.$sq_emp['last_name'].')' ?></h5>
					<p class="single_notification_date_time no-marg"><?= date('d-m-Y H:i',strtotime($row_task['due_date'])).'('.$row_task['task_status'].')' ?></p>
				</li>
			<?php }
		} ?>
	</ul>

	<div class="all_notification">
		<?php $path="view/tasks/index.php"; ?>
		<a href= <?= BASE_URL.$path ?> target="_blank">View All Notifications</a>
	</div>

</div>