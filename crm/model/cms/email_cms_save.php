<?php 
class email_cms_save{

///////////////////////***Master save start*********//////////////
function cms_save()
{
  $entry_id = $_POST['entry_id'];
  $subject = $_POST['subject'];
  $draft = $_POST['draft'];
  $signature = $_POST['signature'];
  $active_flag = $_POST['active_flag'];
  $no_of_days = $_POST['no_of_days'];

  begin_t();  
  $subject = addslashes($subject);
  $draft = addslashes($draft);
  $signature = addslashes($signature);  

  $sq_cms = mysqlQuery("update cms_master_entries set subject='$subject',draft='$draft', signature='$signature', active_flag='$active_flag' where entry_id='$entry_id'");

  $sq_cms = mysqlQuery("update cms_master set days='$no_of_days' where id='$entry_id'");
  
  if($sq_cms){
    commit_t();
    echo "The Email draft has been updated successfully!";
    exit;
  }else{
    rollback_t();
    echo "The Email draft has been not updated";
    exit;
  }
} 
///////////////////////***Master save end*********//////////////

}
?>