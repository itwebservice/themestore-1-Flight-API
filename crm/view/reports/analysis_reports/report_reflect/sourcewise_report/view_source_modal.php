<?php
include "../../../../../model/model.php"; 

$id = $_POST['id'];
$sq_query = mysqlQuery("SELECT * FROM enquiry_master Inner JOIN enquiry_master_entries on enquiry_master.enquiry_id = enquiry_master_entries.enquiry_id  inner join emp_master on enquiry_master.assigned_emp_id=emp_master.emp_id where enquiry_master.reference_id='".$id."' and enquiry_master_entries.status != 'False' ".$_SESSION['dateqry']);
$sq_count = mysqli_num_rows($sq_query);
?>
<div class="modal fade" id="_wise_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-left" id="myModalLabel">Source Wise Enquiry Details</h4>
            </div>
            <div class="modal-body profile_box_padding">
                <div class="row">
                    <div class="col-md-12">
                     
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Sr No.</th>
                                    <th scope="col">Enquiry Date</th>
                                    <th scope="col">Customer name</th>
                                    <th scope="col">Tour Type</th>
                                    <th scope="col">Tour Name</th>
                                    <th scope="col">Enquiry Type</th>
                                    <th scope="col">Allocate_to</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Budget</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if($sq_count > 0)
                                    {
                                        $count =1;
                                        $budget=0;
                                       
                                        while($db = mysqli_fetch_assoc($sq_query))
                                        {
                                            $cust_user_name = '';
                                            if($db['user_id'] != 0){ 
                                                $row_user = mysqli_fetch_assoc(mysqlQuery("Select name from customer_users where user_id =" . $db['user_id']));
                                                $cust_user_name = ' ('.$row_user['name'].')';
                                            }
                                            if($db['enquiry_type'] == 'Flight Ticket')
                                            {
                                            $tourdetail  = json_decode($db['enquiry_content'],true);
                                                foreach($tourdetail as $detail)
                                                {
                                                    $budget += floatval($detail['budget']);
                                                }
                                            }
                                            else{
                                            $tourdetail  = json_decode($db['enquiry_content'],true);
                                            foreach($tourdetail as $dc)
                                                 {
                                                  if($dc['name'] == 'budget')
                                                  {
                                                      $budget = (int)$dc['value'];
                                                  }  
                                                  if($dc['name'] == 'tour_name')
                                                  {
                                                    
                                                    $tourname = $dc['value'];
                                                  }
                                                  
                                                }
                                            }
                                            $style = "";
                                            if($db['followup_status'] == 'Dropped')
                                            {
                                                $style = 'bg-danger';
                                            }
                                            if($db['followup_status'] == 'Converted')
                                            {
                                                $style = 'bg-success';
                                            }   

                                         
                                        ?>
                                      
                                <tr class="<?php echo $style; ?>">
                                    <td><?php echo $count++;  ?></td>
                                    <td><?php  
                                       $source = $db['enquiry_date'];
                                       $date = new DateTime($source);
                                       echo $date->format('d-m-Y');
                                    ?> </td>
                                    <td><?php   echo $db['name'].$cust_user_name; ?></td>
                                    <td><?php   echo $db['enquiry_type'] ;?></td>
                                    <td><?php   
                                    
                                    if($db['enquiry_type'] == 'Package Booking' || $db['enquiry_type'] == 'Group Booking')
                                    {
                                    echo $tourname;
                                    }
                                    else
                                    {
                                        echo 'NA';
                                    }
                                    
                                    ?></td>
                                    <td><?php   echo !empty($db['followup_stage']) ?$db['followup_stage'] : 'N/A' ; ?></td>
                                    <td><?php   echo $db['first_name'] .' '.$db['last_name']; ?></td>
                                    <td><?php   echo $db['followup_status']; ?></td>
                                    <td><?php    echo $budget; $budget=0; ?></td>

                                </tr>
                                <?php  }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#_wise_modal').modal('show');
</script>