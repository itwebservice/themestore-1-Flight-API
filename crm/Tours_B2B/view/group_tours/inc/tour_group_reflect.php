<?php
include '../../../../model/model.php';
?>
<option value=""> *Select Tour Date </option>
<?php
$tour_id=$_GET['tour_id'];
$flag = $_GET['flag'];
$today_date = strtotime(date('Y-m-d'));
$sq = mysqlQuery("select * from tour_groups where tour_id='$tour_id' and status!='Cancel' ");

while($row=mysqli_fetch_assoc($sq))
{
     $group_id=$row['group_id'];
     $from_date=$row['from_date'];
     $to_date=$row['to_date'];

     $date1_ts = strtotime($tour_groups_array[$t]->from_date);

     $from_date=date("d-m-Y", strtotime($from_date));  
     $to_date=date("d-m-Y", strtotime($to_date)); 

     if($flag == "false"){
          $val = (int)date_diff(date_create(date("d-m-Y")),date_create($to_date))->format("%R%a");
          if($val <= 0)  continue; // skipping the ended group tours (only used group quotation)
     }
     // if($today_date < $date1_ts){
          echo "<option value='$group_id'>".$from_date." to ".$to_date."</option>";
     // }
}
?>
