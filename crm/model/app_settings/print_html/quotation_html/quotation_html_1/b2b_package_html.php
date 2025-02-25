<?php
//Generic Files
include "../../../../model.php"; 
include "printFunction.php";
global $app_quot_img,$app_quot_format;

$package_id=$_GET['package_id'];
$sq_pckg = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$package_id'"));
$sq_dest = mysqli_fetch_assoc(mysqlQuery("select * from destination_master where dest_id='$sq_pckg[dest_id]'"));
?>

<section class="headerPanel main_block">
  <div class="headerImage">
    <img src="<?= getFormatImg($app_quot_format,$sq_pckg['dest_id'])?>" class="img-responsive"  >
    <div class="headerImageOverLay"></div>
    <!-- style="height:180px !important;" -->
  </div>

  <!-- header -->
  <section class="print_header main_block side_pad mg_tp_30">
    <div class="col-md-4 no-pad">
      <div class="print_header_logo">
        <img src="<?= $admin_logo_url ?>" class="img-responsive mg_tp_10">
      </div>
    </div>
    <div class="col-md-4 no-pad text-center mg_tp_30">
      <span class="title"><i class="fa fa-pencil-square-o"></i> PACKAGE TOUR</span>
    </div>

    <?php 
    include "standard_header_html.php";
    ?>

  </section>

      <!-- Package -->
      <section class="print_sec main_block side_pad mg_tp_30">
        <div class="section_heding">
          <h2>PACKAGE DETAILS</h2>
          <div class="section_heding_img">
            <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="print_info_block">
              <ul class="main_block">
                <li class="col-md-6 mg_tp_10 mg_bt_10"><span>DESTINATION :</span> <?= $sq_dest['dest_name'] ?></li>
              </ul>
              <ul class="main_block">
                <li class="col-md-6 mg_tp_10 mg_bt_10"><span>PACKAGE NAME :</span> <?= $sq_pckg['package_name'] ?></li>
                <li class="col-md-6 mg_tp_10 mg_bt_10"><span>PACKAGE CODE :</span> <?=  $sq_pckg['package_code'] ?></li>
              </ul>
              <ul class="main_block">
                <li class="col-md-6 mg_tp_10 mg_bt_10"><span>TOTAL DAYS :</span> <?= $sq_pckg['total_days'] ?></li>
                <li class="col-md-6 mg_tp_10 mg_bt_10"><span>TOTAL NIGHTS :</span> <?= $sq_pckg['total_nights'] ?></li>
              </ul>
            </div>
          </div>
        </div>
        <!-- Hotel -->
        <?php
        $sq_hotelc = mysqli_num_rows(mysqlQuery("select * from custom_package_hotels where package_id='$package_id'"));
        if($sq_hotelc!=0){?>
          <div class="section_heding mg_tp_20">
            <h2>HOTEL DETAILS</h2>
            <div class="section_heding_img">
              <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-bordered no-marg" id="tbl_emp_list">
                <thead>
                  <tr class="table-heading-row">
                    <th>City</th>
                    <th>Hotel Name</th>
                    <th>Total Nights</th>
                  </tr>
                </thead>
                <tbody> 
                <?php $sq_hotel = mysqlQuery("select * from custom_package_hotels where package_id='$package_id'");
                while($row_hotel = mysqli_fetch_assoc($sq_hotel)){
                  $hotel_name = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$row_hotel[hotel_name]'"));
                  $city_name = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_hotel[city_name]'"));
                ?>
                <tr>
                    <?php
                    $sql = mysqli_fetch_assoc(mysqlQuery("select * from hotel_vendor_images_entries where hotel_id='$row_hotel[hotel_name]'"));
                    $sq_count_h = mysqli_num_rows(mysqlQuery("select * from custom_package_hotels where package_id='$package_id' "));
                    if($sq_count_h ==0){
                      $download_url =  BASE_URL.'images/dummy-image.jpg';
                    }
                    else{  
                          $image = $sql['hotel_pic_url']; 
                          $download_url = preg_replace('/(\/+)/','/',$image);
                    }
                    ?>
                    <td><?php echo $city_name['city_name']; ?></td>
                    <td><?php echo $hotel_name['hotel_name'].$similar_text; ?></td>
                    <td><?php echo $row_hotel['total_days']; ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
          </div>
        <?php } ?>
        <!-- Transport -->
        <?php
        $sq_hotelc = mysqli_num_rows(mysqlQuery("select * from custom_package_transport where package_id='$package_id'"));
        if($sq_hotelc!=0){?>
          <div class="section_heding mg_tp_10">
            <h2>Transport DETAILS</h2>
            <div class="section_heding_img">
              <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-bordered no-marg" id="tbl_emp_list">
                <thead>
                      <tr class="table-heading-row">
                        <th>VEHICLE</th>
                        <th>Pickup</th>
                        <th>Drop</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      $count = 0;
                      $sq_hotel = mysqlQuery("select * from custom_package_transport where package_id='$package_id'");
                      while($row_hotel = mysqli_fetch_assoc($sq_hotel)){
                        $transport_name = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_master where entry_id ='$row_hotel[vehicle_name]'"));
                        // Pickup
                        if($row_hotel['pickup_type'] == 'city'){
                          $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_hotel[pickup]'"));
                          $pickup = $row['city_name'];
                        }
                        else if($row_hotel['pickup_type'] == 'hotel'){
                          $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_hotel[pickup]'"));
                          $pickup = $row['hotel_name'];
                        }
                        else{
                          $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_hotel[pickup]'"));
                          $airport_nam = clean($row['airport_name']);
                          $airport_code = clean($row['airport_code']);
                          $pickup = $airport_nam." (".$airport_code.")";
                          $html = '<optgroup value="airport" label="Airport Name"><option value="'.$row['airport_id'].'">'.$pickup.'</option></optgroup>';
                        }
                        // Drop
                        if($row_hotel['drop_type'] == 'city'){
                          $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_hotel[drop]'"));
                          $drop = $row['city_name'];
                        }
                        else if($row_hotel['drop_type'] == 'hotel'){
                          $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_hotel[drop]'"));
                          $drop = $row['hotel_name'];
                        }
                        else{
                          $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_hotel[drop]'"));
                          $airport_nam = clean($row['airport_name']);
                          $airport_code = clean($row['airport_code']);
                          $drop = $airport_nam." (".$airport_code.")";
                          $html = '<optgroup value="airport" label="Airport Name"><option value="'.$row['airport_id'].'">'.$pickup.'</option></optgroup>';
                        }
                        ?>
                        <tr>
                          <td><?= $transport_name['vehicle_name'].$similar_text ?></td>
                          <td><?= $pickup ?></td>
                          <td><?= $drop ?></td>
                        </tr>
                      <?php } ?>
                    </tbody>
              </table>
            </div>
          </div>
          </div>
        <?php } ?>
        </section>
        
        <!-- Tour Itinenary -->
        <section class="print_sec main_block side_pad mg_tp_30">
          <div class="section_heding">
            <h2>TOUR ITINERARY</h2>
            <div class="section_heding_img">
              <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="print_itinenary main_block no-pad no-marg">          
              <?php 
              $count = 1;
              $sq_package_program = mysqlQuery("select * from custom_package_program where package_id = '$package_id'");
              while($row_itinarary = mysqli_fetch_assoc($sq_package_program)){
              ?>
                <section class="print_single_itinenary main_block">
                  <div class="print_itinenary_count print_info_block">DAY - <?php echo $count++; ?></div>
                  <div class="print_itinenary_desciption print_info_block">
                    <div class="print_itinenary_attraction">
                      <span class="print_itinenary_attraction_icon"><i class="fa fa-map-marker"></i></span>
                      <samp class="print_itinenary_attraction_location"><?= $row_itinarary['attraction'] ?></samp>
                    </div>
                    <p><?= $row_itinarary['day_wise_program'] ?></p>
                  </div>
                  <div class="print_itinenary_details">
                    <div class="print_info_block">
                      <ul class="main_block no-pad">
                        <li class="col-md-12 mg_tp_10 mg_bt_10"><span><i class="fa fa-bed"></i> : </span><?= $row_itinarary['stay'] ?></li>
                        <li class="col-md-12 mg_tp_10 mg_bt_10"><span><i class="fa fa-cutlery"></i> : </span><?= $row_itinarary['meal_plan'] ?></li>
                      </ul>
                    </div>
                  </div>
                </section>
                <?php } ?>
              </div>
            </div>
          </div>
        </section>
  


      <!-- Inclusion -->
      <section class="print_sec main_block side_pad mg_tp_30">
        <div class="row">
          <?php if($sq_pckg['inclusions']!= ' '){?>
            <div class="col-md-6">
              <div class="section_heding">
                <h2>Inclusions</h2>
                <div class="section_heding_img">
                  <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
                </div>
              </div>
              <div class="print_text_bolck">
                <?= $sq_pckg['inclusions'] ?>
              </div>
            </div>
          <?php } ?> 


          <!-- Exclusion -->
          <?php if($sq_pckg['exclusions']!= ' '){?>
            <div class="col-md-6">
              <div class="section_heding">
                <h2>Exclusions</h2>
                <div class="section_heding_img">
                  <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
                </div>
              </div>
              <div class="print_text_bolck">
                <?= $sq_pckg['exclusions'] ?>
              </div>
            </div>
          <?php } ?> 

          <!-- Note -->
          <?php if($sq_pckg['note']!= ''){?>
            <div class="col-md-12">
              <div class="section_heding">
                <h2>Note</h2>
                <div class="section_heding_img">
                  <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
                </div>
              </div>
              <div class="print_text_bolck">
                <?= $sq_pckg['note'] ?>
              </div>
            </div>
          <?php } ?> 

        </div>
      </section>
  </body>
</html>