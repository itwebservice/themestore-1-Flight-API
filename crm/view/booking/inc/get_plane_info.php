<?php
include_once('../../../model/model.php');

$group_id = $_POST['group_id'];
$plane_info_arr = array();

$sq_group = mysqli_fetch_assoc(mysqlQuery("select * from tour_groups where group_id='$group_id'"));

$query = "select * from group_tour_plane_entries where tour_id='$sq_group[tour_id]'";
$sq_train = mysqlQuery($query);

	while($row_train = mysqli_fetch_assoc($sq_train)){	
		$sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$row_train[from_city]'")); 	
		$sq_city1 = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$row_train[to_city]'"));	
		$arrival_date = date('d-m-Y H:i', strtotime($sq_group['from_date']));
		$departure_date = date('d-m-Y H:i', strtotime($sq_group['from_date']));
		$arr = array(
			'from_city_id' => $row_train['from_city'],
			'to_city_id' => $row_train['to_city'],
			'from_city' => $sq_city['city_name'],
			'to_city' => $sq_city1['city_name'],
			'from_location' => $row_train['from_location'],
			'to_location' => $row_train['to_location'],
			'airline_name' => $row_train['airline_name'],	
			'class' => $row_train['class'],
			'arraval_time' => $arrival_date,
			'dapart_time' => $departure_date,
			'from_location' =>$row_train['from_location'],
			'to_location' =>$row_train['to_location']
		);
		array_push($plane_info_arr, $arr);
	}


echo json_encode($plane_info_arr);
?>