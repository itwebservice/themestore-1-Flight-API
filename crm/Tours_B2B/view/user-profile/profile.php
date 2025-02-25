<?php
include '../../../model/model.php';
$register_id = $_SESSION['register_id'];
$agent_flag = isset($_SESSION['agent_flag']) ? $_SESSION['agent_flag'] : '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$sq_query = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM `b2b_registration` where register_id='$register_id'"));
//Include header
include '../../layouts/header2.php';
$username = $encrypt_decrypt->fnDecrypt($sq_query['username'], $secret_key);
$password = $encrypt_decrypt->fnDecrypt($sq_query['password'], $secret_key);

if($agent_flag == '1'){
  $profile_user_name = $sq_query['cp_first_name'].' '.$sq_query['cp_last_name'];
  $profile_email_id = $sq_query['email_id'];
  $profile_mobile_no = $sq_query['mobile_no'];
}else{
  $sq_user = mysqli_fetch_assoc(mysqlQuery("SELECT full_name,email_id,mobile_no FROM `b2b_users` where id='$user_id'"));
  $profile_user_name = $sq_user['full_name'];
  $profile_email_id = $sq_user['email_id'];
  $profile_mobile_no = $sq_user['mobile_no'];
}
?>
  <link rel="stylesheet" href="<?php echo BASE_URL ?>Tours_B2B/css/datatables.min.css" />
  <input type='hidden' value="<?=$sq_query['customer_id'] ?>" id="customer_id"/>
  <input type="hidden" id="whatsapp_switch" value="<?= $whatsapp_switch ?>" >
    <!-- ********** Component :: Page Title ********** -->
    <div class="c-pageTitleSect">
      <div class="container">
        <div class="row">
          <div class="col-md-7 col-12">

            <!-- *** Search Head **** -->
            <div class="searchHeading">
              <span class="pageTitle m0">User Profile</span>
            </div>
            <!-- *** Search Head End **** -->
          </div>

          <div class="col-md-5 col-12 c-breadcrumbs">
            <ul>
              <li>
                <a href="<?= $b2b_index_url ?>">Home</a>
              </li>
              <li class="st-active">
                <a href="javascript:void(0)">User Profile</a>
              </li>
            </ul>
          </div>

        </div>
      </div>
    </div>
    <!-- ********** Component :: Page Title End ********** -->
    <!-- ********** Component :: User Profile ********** -->
    <div class="c-containerDark">
      <div class="container">
        <div class="row">
          <div class="col-md-4 col-12">

            <div class="c-userBlock">

              <!-- User Block -->
              <div class="u_pInfo">
                <img src="<?php echo BASE_URL ?>Tours_B2B/images/user.svg" alt="" />
                <h2 class="c-heading"><?php echo $sq_query['company_name']; ?></h2>
                <h4 class="c-heading sm"><?php echo $sq_query['accounting_name']; ?></h4>
              </div>
              <!-- User Block End -->

              <!-- User Info -->
              <div class="clearfix u_pData">
                <div class="row infoRow no-gutters">
                  <div class="col-4"><span class="lbl_sm">Name</span></div>
                  <div class="col-8"><span class="lbl_md"><?php echo $profile_user_name; ?></span></div>
                </div>
                <div class="row infoRow no-gutters">
                  <div class="col-4"><span class="lbl_sm">Email ID</span></div>
                  <div class="col-8"><span class="lbl_md"><?php echo $profile_email_id; ?></span></div>
                </div>
                <div class="row infoRow no-gutters">
                  <div class="col-4"><span class="lbl_sm">Contact Number</span></div>
                  <div class="col-8"><span class="lbl_md"><?php echo $profile_mobile_no; ?></span></div>
                </div>
              </div>
              <!-- User Info End -->

              <div class="clearfix u_links nav" id="myTab" role="tablist">
                <?php
                if($agent_flag == '1'){ ?>
                  <button class="u_tab active" id="tab-1" data-toggle="tab" href="#home1" role="tab">
                    <i class="icon itours-student-card"></i>
                    Profile Details
                  </button>
                  <button class="u_tab" id="tab-5" data-toggle="tab" href="#home5" role="tab">
                    <i class="icon itours-user-1"></i>
                    Users
                  </button>
                  <button class="u_tab" id="tab-6" data-toggle="tab" href="#home6" role="tab">
                    <i class="icon itours-financial"></i>
                    Markup & Tax
                  </button>
                <?php } ?>
                <?php
                $active_status = ($agent_flag == '0') ? 'active' : '';
                ?>
                <button class="u_tab  <?= $active_status ?>" id="tab-4" data-toggle="tab" href="#home4" role="tab">
                  <i class="icon itours-user-1"></i>
                  Quotation Summary
                </button>
                <button class="u_tab" id="tab-2" data-toggle="tab" href="#home2" role="tab">
                  <i class="icon itours-user-1"></i>
                  Booking Summary
                </button>
                <?php
                if($agent_flag == '1'){ ?>
                  <button class="u_tab" id="tab-3" data-toggle="tab" href="#home3" role="tab">
                    <i class="icon itours-financial"></i>
                    Account Ledgers
                  </button>
                <?php } ?>
              </div>

            </div>
          </div>

          <!-- ** User Profile Details ** -->
          <div class="col-md-8 col-12">

            <div class="c-userBlock st-details  tab-content">

            <?php
                if($agent_flag == '1'){ ?>
              <!-- Tab 1 -->
              <div class="tabDetails tab-pane fade show active" id="home1" role="tabpanel">
                <?php include_once('tab1.php'); ?>
              </div>
              <!-- Tab 1 End -->

              <!-- Tab 2 -->
              <div class="tabDetails tab-pane fade" id="home5" role="tabpanel">
                <?php include_once('tab5.php'); ?>
              </div>
              <!-- Tab 2 End -->

              <!-- Tab 3 -->
              <div class="tabDetails tab-pane fade" id="home6" role="tabpanel">
                <?php include_once('tab6.php'); ?>
              </div>
              <!-- Tab 3 End -->
              <?php } ?>
              <?php
              $active_status = ($agent_flag == '0') ? 'show active' : '';
              ?>

              <!-- Tab4 -->
              <div class="tabDetails tab-pane fade <?= $active_status ?>" id="home4" role="tabpanel">
                <?php include_once('tab4.php'); ?>
              </div>
              <!-- Tab 4 End -->

              <!-- Tab 5 -->
              <div class="tabDetails tab-pane fade" id="home2" role="tabpanel">
                <?php include_once('tab2.php'); ?>
              </div>
              <!-- Tab 5 End -->

              <?php
                if($agent_flag == '1'){ ?>
                <!-- Tab 6 -->
                <div class="tabDetails tab-pane fade" id="home3" role="tabpanel">
                  <?php include_once('tab3.php'); ?>
                </div>
                <!-- Tab 6 End -->
              <?php } ?>

            </div>

          </div>
          <!-- ** User Profile Details End ** -->

        </div>
      </div>
    </div>
    <!-- ********** Component :: User Profile End ********** -->

<div id="update_modal_div"></div>
<div id="booking_view_modal"></div>
<?php include '../../layouts/footer.php';?>
<script type="text/javascript" src="<?php echo BASE_URL ?>Tours_B2B/js/datatables.min.js"></script>
<script src="index.js"></script>
<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>
<script>
$('#currency_id,#iata_status,#city,#country').select2();
$('#from_date_filter, #to_date_filter').datetimepicker({ timepicker:false,format:'d-m-Y' });
$('#from_date_f, #to_date_f,#qfrom_date_filter, #qto_date_filter').datetimepicker({ timepicker:false,format:'d-m-Y' });
upload_logo_proof();
function upload_logo_proof(){
    var btnUpload=$('#logo_upload_btn1');
    var base_url=$('#base_url').val();
    // $(btnUpload).find('span').text('Company Logo');
    new AjaxUpload(btnUpload, {
      action: base_url+'view/b2b_customer/inc/upload_logo.php',
      name: 'uploadfile',
      onSubmit: function(file, ext){  

        if (! (ext && /^(png|jpg|jpeg)$/.test(ext))){ 
         error_msg_alert('Only PNG,JPG or JPEG files are allowed');
         return false;
        }
        $(btnUpload).find('span').text('Uploading...');
      },
      onComplete: function(file, response){
       // alert(response);
        if(response=="error1"){
            $(btnUpload).find('span').text('Company Logo');
            error_msg_alert('Maximum size exceeds');
            return false;
        }
        else if(response==="error"){
          error_msg_alert("File is not uploaded.");
          $(btnUpload).find('span').text('Upload');
        }
        else{
          $(btnUpload).find('span').text('Uploaded');
          $("#logo_upload_url").val(response);
        }
      }
    });
}
upload_address_proof();
function upload_address_proof(){
    var btnUpload=$('#address_upload_btn1');
    var base_url=$('#base_url').val();
    // $(btnUpload).find('span').text('Address Proof');    

    new AjaxUpload(btnUpload, {
      action: base_url+'view/b2b_customer/inc/upload_address_proof.php',
      name: 'uploadfile',
      onSubmit: function(file, ext){  

        if (! (ext && /^(jpg|png|jpeg|pdf)$/.test(ext))){ 
          error_msg_alert('Only PDF,JPG or PNG files are allowed');
          return false;
        }
        $(btnUpload).find('span').text('Uploading...');
      },

      onComplete: function(file, response){

        if(response==="error"){
          error_msg_alert("File is not uploaded.");
          $(btnUpload).find('span').text('Upload');
        }
        else{
          $(btnUpload).find('span').text('Uploaded');
          $("#address_upload_url").val(response);
        }
      }
    });
}

upload_id_proof();
function upload_id_proof(){

    var btnUpload=$('#photo_upload_btn_p');
    var base_url=$('#base_url').val();
    // $(btnUpload).find('span').text('ID Proof');
    new AjaxUpload(btnUpload, {
      action: base_url+'view/b2b_customer/inc/upload_photo_proof.php',
      name: 'uploadfile',
      onSubmit: function(file, ext){  
        if (! (ext && /^(pdf|jpg|png|jpeg)$/.test(ext))){ 
          error_msg_alert('Only PDF,JPG or PNG files are allowed');
          return false;
        }
        $(btnUpload).find('span').text('Uploading...');
      },

      onComplete: function(file, response){

        if(response==="error"){          
          error_msg_alert("File is not uploaded.");
          $(btnUpload).find('span').text('Upload');
        }
        else{
          $(btnUpload).find('span').text('Uploaded');
          $("#photo_upload_url").val(response);
        }
      }
    });
}

</script>
<script>
//Booking summary
var columns = [
	{ title : "SR No." },
	{ title : "Booking_ID" },
	{ title : "Booking_Date" },
	{ title : "Total" },
	{ title : "Paid" },
	{ title : "Balance" },
	{ title : "User" },
	{ title : "Actions" }
]
function list_reflect(){
  var from_date = $('#from_date_filter').val();
  var to_date = $('#to_date_filter').val();
  var customer_id = $('#customer_id').val();
	$.post('booking_data_reflect.php', {from_date:from_date,to_date:to_date,customer_id:customer_id}, function(data){
		pagination_load(data,columns,true,"Footer",20,'tbl_list');
	});
}
list_reflect();
function booking_view_modal(booking_id){
  var base_url = $('#base_url').val();
  $('#view-'+booking_id).prop('disabled',true);
	$.post(base_url+'/view/b2b_sale/view/index.php', { booking_id : booking_id }, function(data){
		$('#booking_view_modal').html(data);
    $('#view-'+booking_id).prop('disabled',false);
	})
}

//Account Ledgers
var columns1 = [
  { title : "SR No." },
  { title : "Date" },
  { title : "Particular" },
  { title : "Debit" },
  { title : "Credit" },
]
function acc_list_reflect(){
  var from_date = $('#from_date_f').val();
  var to_date = $('#to_date_f').val();
  var customer_id = $('#customer_id').val();
  $.post('finance_data_reflect.php', {from_date:from_date,to_date:to_date,customer_id:customer_id}, function(data){
    pagination_load(data,columns1,true,"Footer",20,'acc_table');
    // console.log(data);
  });
}
acc_list_reflect();

//Quotation summary
var columns2 = [
	{ title : "SR No." },
	{ title : "Quotation_Date" },
	{ title : "Guest Name" },
	{ title : "Quotation_Amount" },
	{ title : "User" },
	{ title : "Actions",
        className: "text-center action_width" }
]
function quotlist_reflect(){
  var from_date = $('#qfrom_date_filter').val();
  var to_date = $('#qto_date_filter').val();
	$.post('quotation_data_reflect.php', {from_date:from_date,to_date:to_date}, function(data){
		pagination_load(data,columns2,false,"Footer",20,'quottbl_list');
	});
}
quotlist_reflect();
function proceed_to_direct_checkout(quotation_id,register_id){

  var base_url = $('#base_url').val();
	$.post(base_url+'controller/b2b_customer/quotation_to_checkout.php', {quotation_id:quotation_id,register_id:register_id}, function(data){
    var link_to_redirect = data.split("==");
		localStorage.setItem('cart_list_arr', (link_to_redirect[1]));
		window.open(link_to_redirect[0]);
	});
}
//Users summary
var columns5 = [
  { title : "SR_No." },
  { title : "User_Name" },
  { title : "Email_ID" },
  { title : "Mobile_No" },
  { title : "Created_at" },
  { title : "Action" }
]
function user_list_reflect(){
  $.post('user_data_reflect.php', {}, function(data){
    console.log(data);
    pagination_load(data,columns5,true,"Footer",20,'user_table');
  });
}
user_list_reflect();
function update_user(user_id){

  $.ajax({
        type:'post',
        url: 'update_user.php',
        data:{ user_id:user_id},
        success: function(message){
          $('#update_modal_div').html(message);
        }
  });
}
</script>