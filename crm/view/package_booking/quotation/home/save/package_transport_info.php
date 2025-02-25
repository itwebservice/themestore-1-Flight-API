<?php
include_once('../../../../../model/model.php');

//Get selected currency rate
global $currency;
$sq_to = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$currency'"));
$to_currency_rate = $sq_to['currency_rate'];

$package_id_arr = $_POST['package_id_arr'];
$total_adult = $_POST['total_adult'];
$from_date = get_date_db($_POST['from_date']);
$transport_info_arr = array();
$total_cost = 0;
for($i=0; $i<sizeof($package_id_arr); $i++){
	
	$sq_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id='$package_id_arr[$i]'"));
	$sq_transport = mysqlQuery("select * from custom_package_transport where package_id='$package_id_arr[$i]'");

	while($row_transport = mysqli_fetch_assoc($sq_transport)){

		$service_duration = '';
		$row_tariff_master1 = mysqlQuery("select * from b2b_transfer_tariff where 1 and vehicle_id='$row_transport[vehicle_name]' order by tariff_id desc");
		while($row_tariff_master = mysqli_fetch_assoc($row_tariff_master1)){

			$currency_id = $row_tariff_master['currency_id'];
			$sq_from = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$currency_id'"));
			$from_currency_rate = $sq_from['currency_rate'];
			$tariff_count = mysqli_num_rows(mysqlQuery("select * from b2b_transfer_tariff_entries where tariff_id='$row_tariff_master[tariff_id]' and pickup_type = '$row_transport[pickup_type]' and drop_type = '$row_transport[drop_type]' and pickup_location = '$row_transport[pickup]' and drop_location = '$row_transport[drop]' and (from_date <='$from_date' and to_date>='$from_date')"));
			if($tariff_count != 0){
				$sq_tariff = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_tariff_entries where tariff_id='$row_tariff_master[tariff_id]' and pickup_type = '$row_transport[pickup_type]' and drop_type = '$row_transport[drop_type]' and pickup_location = '$row_transport[pickup]' and drop_location = '$row_transport[drop]' and (from_date <='$from_date' and to_date>='$from_date')"));
				$row1 = mysqli_fetch_assoc(mysqlQuery("select duration from service_duration_master where entry_id='$sq_tariff[service_duration]'"));
				$service_duration = $row1['duration'];
				$tariff_data = json_decode($sq_tariff['tariff_data']);
				$total_cost = $tariff_data[0]->total_cost;
				break;
			}else{
				$total_cost = 0;
				break;
			}
		}
		$q_transport = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_master where entry_id='$row_transport[vehicle_name]'"));
		$seating_capacity = $q_transport['seating_capacity'];
		$total_vehicles = ($seating_capacity > 0 ) ? ceil(intval($total_adult) / intval($seating_capacity)) : 0;
		// Pickup
		if($row_transport['pickup_type'] == 'city'){
			$row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_transport[pickup]'"));
			$pickup = $row['city_name'];
			$pickup_id = $row['city_id'];
		}
		else if($row_transport['pickup_type'] == 'hotel'){
			$row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_transport[pickup]'"));
			$pickup_id = $row['hotel_id'];
			$pickup = $row['hotel_name'];
		}
		else{
			$row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_transport[pickup]'"));
			$airport_nam = clean($row['airport_name']);
			$airport_code = clean($row['airport_code']);
			$pickup = $airport_nam." (".$airport_code.")";
			$pickup = $pickup;
			$pickup_id = $row['airport_id'];
		}
		// Drop
		if($row_transport['drop_type'] == 'city'){
			$row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_transport[drop]'"));
			$drop = $row['city_name'];
			$drop_id = $row['city_id'];
		}
		else if($row_transport['drop_type'] == 'hotel'){
			$row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_transport[drop]'"));
			$drop = $row['hotel_name'];
			$drop_id = $row['hotel_id'];
		}
		else{
			$row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_transport[drop]'"));
			$airport_nam = clean($row['airport_name']);
			$airport_code = clean($row['airport_code']);
			$drop = $airport_nam." (".$airport_code.")";
			$drop = $drop;
			$drop_id = $row['airport_id'];
		}

		$arr1 = array(
			'bus_name' => $q_transport['vehicle_name'],
			'bus_id' => $q_transport['entry_id'],
			'package_name' => $sq_package['package_name'],
			'package_id' => $sq_package['package_id'],
			'pickup' => $pickup,
			'pickup_id' => $pickup_id,
			'drop'=> $drop,
			'drop_id'=> $drop_id,
			'total_cost'=>$total_cost,
			'pickup_type' => $row_transport['pickup_type'],
			'drop_type'=> $row_transport['drop_type'],
			'total_vehicles'=>$total_vehicles,
			'duration'=> $service_duration
		);	
		array_push($transport_info_arr, $arr1);
	}
}
echo json_encode($transport_info_arr);
?>