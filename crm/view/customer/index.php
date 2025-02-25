<?php
include "../../model/model.php";
if(isset($_SESSION['login_type'])){ session_destroy(); }  
global $app_version;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>

  <link rel="icon" href="<?= $circle_logo_url ?>" type="image/gif" sizes="16x16">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,500" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/font-awesome-4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/vi.alert.css">
  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/app/app.php">
  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/app/login.php">

  <script src="<?php echo BASE_URL ?>js/jquery-3.1.0.min.js"></script>
  <script src="<?php echo BASE_URL ?>js/bootstrap.min.js"></script>
  <script src="<?php echo BASE_URL ?>js/jquery.validate.min.js"></script>
  <script src="<?php echo BASE_URL ?>js/vi.alert.js"></script>

</head>
<body>


<form action="login_verify.php" id="frm_login" method="POST">
  <div class="main_block login_screen">
    <div class="login_wrap">
        
      <div class="logo-wrap"> <img src="<?= $circle_logo_url ?>" /> </div>
      <h3>Login to your account</h3>

      <div class="login_wrap_inner">

        <div class="row"> <div class="col-md-12"> 
              <input class="form-control" id="txt_mobile_no" name="txt_mobile_no" type="text" maxlength="30" placeholder="Mobile Number" required/>
        </div> </div><br>

        <div class="row">
            <div class="col-md-12">
              <div class="login-password-field">
                <input class="form-control" id="txt_email_id" name="txt_email_id" type="password" maxlength="30" placeholder="Email Id"/>    
                <a onclick="show_password('txt_email_id')" target="_BLANK" class="btn" title="View Password"><i class="fa fa-eye"></i></a>
              </div>
            </div>
          </div><br>

        <div class="row"> <div class="col-md-12"> 
            <button class="app_btn"><i class="fa fa-sign-in"></i>&nbsp;&nbsp;Sign In</button>
        </div> </div>


        <?php if(isset($_GET['status'])) { ?> <br><div class="alert alert-danger"> Enter proper login credentials. </div>  <?php  }  ?> 
      
      </div>

    </div>
  </div>
</form>


<script>
  $('#frm_login').validate();
  function show_password(password) {
    var x = document.getElementById(password);
    if (x.type === 'password') {
      x.type = 'text';
    }
    else {
      x.type = 'password';
    }
  }
</script>
  
</body>
</html>