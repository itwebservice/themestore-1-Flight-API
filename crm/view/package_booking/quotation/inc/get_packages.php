<?php
include "../../../../model/model.php";
$dest_id = $_POST['dest_id'];
$count = 1;
$offset = 1;
$sq_tours = mysqlQuery("select * from custom_package_master where dest_id = '$dest_id' and status!='Inactive'");
?>


<style>
.style_text
{
	position: absolute;
    right: 15px;
    display: flex;
    gap: 15px;
    background: #f5f5f5;
    padding: 0px 14px;
    top: 0px;
}

</style>

<div class="col-md-12 app_accordion">
    <div class="panel-group main_block" id="accordion" role="tablist" aria-multiselectable="true">
        <?php
        $table_count = 0;
        while ($row_tours = mysqli_fetch_assoc($sq_tours)) {
        ?>
        <div class="package_selector">
            <input type="radio" value="<?php echo $row_tours['package_id']; ?>"
                id="<?php echo $row_tours['package_id']; ?>" name="custom_package" />
        </div>
        <div class="accordion_content package_content mg_bt_10">
            <div class="panel panel-default main_block">
                <div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
                    <div class="Normal collapsed main_block" role="button" data-toggle="collapse"
                        data-parent="#accordion" href="#collapse_<?= $count; ?>" aria-expanded="false"
                        aria-controls="collapse_<?= $count; ?>" id="collapsed_<?= $count ?>">
                        <div class="col-md-12"><span><em style="margin-left: 15px;"><?php echo $row_tours['package_name'] . ' (' . $row_tours['total_days'] . 'D/' . $row_tours['total_nights'] . 'N )' ?></em></span>
                        </div>
                    </div>
                </div>
                <div id="collapse_<?= $count ?>" class="panel-collapse collapse main_block" role="tabpanel"
                    aria-labelledby="heading_<?= $count ?>">
                    <div class="panel-body">
                        <div class="col-md-12 no-pad" id="div_list1">
                            <div class="row mg_bt_10">
                                <div class="col-xs-12 text-right text_center_xs">
                                    <button type="button" class="btn btn-excel btn-sm"
                                        onClick="addRow('dynamic_table_list_p_<?= $row_tours['package_id'] ?>','<?= $row_tours['package_id'] ?>','itinerary')"><i
                                            class="fa fa-plus"></i></button>
                                    <button type="button" class="btn btn-pdf btn-sm"
                                        onClick="deleteRow('dynamic_table_list_p_<?= $row_tours['package_id'] ?>')"><i
                                            class="fa fa-trash"></i></button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table style="width: 100%" id="dynamic_table_list_p_<?= $row_tours['package_id'] ?>"
                                    name="dynamic_table_list_p_<?= $row_tours['package_id'] ?>"
                                    class="table table-bordered table-hover table-striped no-marg pd_bt_51 mg_bt_0">
                                    <legend>Tour Itinerary</legend>
                                    <?php
                    $offset1 = 0;
                    $sq_program = mysqlQuery("select * from custom_package_program where package_id='$row_tours[package_id]'");
                    while ($row_program = mysqli_fetch_assoc($sq_program)) {
                        $offset1++;
                        ?>
                        <tr>
                            <td style="width: 50px;"><input class="css-checkbox mg_bt_10"
                                    id="chk_program<?= $offset ?>" type="checkbox" checked><label
                                    class="css-label" for="chk_program<?= $offset ?>"> <label></td>
                            <td style="width: 50px;" class="hidden"><input maxlength="15"
                                    value="<?= $offset1 ?>" type="text" name="username"
                                    placeholder="Sr. No." class="form-control mg_bt_10" disabled /></td>
                            <td style="width: 100px;"><input type="text"
                                    id="special_attaraction<?php echo $offset; ?>-u"
                                    onchange="validate_spaces(this.id);validate_spattration(this.id);"
                                    name="special_attaraction" class="form-control mg_bt_10"
                                    placeholder="*Special Attraction" title="Special Attraction"
                                    value="<?php echo $row_program['attraction']; ?>" style='width:220px'>
                            </td>
                            <!-- <td style="max-width: 594px;overflow: hidden;width:100px"><textarea
                                    id="day_program<?php echo $offset; ?>-u" name="day_program"
                                    class="form-control mg_bt_10" title="Day-wise Program" rows="3"
                                    placeholder="*Day-wise Program"
                                    onchange="validate_spaces(this.id);validate_dayprogram(this.id);"
                                    style='width:400px'
                                    value="<?php echo $row_program['day_wise_program']; ?>"><?php echo $row_program['day_wise_program']; ?></textarea>
                            </td> -->
                            <!-- <td  style="max-width: 594px;overflow: hidden;width:100px;"><textarea id="day_program<?php echo $offset; ?>-u" name="day_program" class="form-control mg_bt_10 day_program" placeholder="*Day-wise Program" title="Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" style='width:400px'
                            value="<?php echo $row_program['day_wise_program']; ?>"><?php echo $row_program['day_wise_program']; ?></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
                            </td> -->
                            <td class='col-md-6 pad_8' style="max-width: 594px;overflow: hidden;position: relative;"><textarea id="day_program<?php echo $offset; ?>-u" name="day_program" class="form-control mg_bt_10 day_program" placeholder="*Day-wise Program" title="Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" value="<?php echo $row_program['day_wise_program']; ?>"><?php echo $row_program['day_wise_program']; ?></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
                            </td>
                            <td style="width: 100px;"><input type="text"
                                    id="overnight_stay<?php echo $offset; ?>-u" name="overnight_stay"
                                    onchange="validate_spaces(this.id);validate_onstay(this.id);"
                                    class="form-control mg_bt_10" placeholder="*Overnight Stay"
                                    title="Overnight Stay" value="<?php echo $row_program['stay']; ?>"
                                    style='width:170px'></td>
                            <td><select id="meal_plan<?php echo $offset; ?>" -u title="Meal Plan"
                                    name="meal_plan" class="form-control mg_bt_10" style='width: 140px'>
                                    <?php if ($row_program['meal_plan'] != '') { ?><option
                                        value="<?php echo $row_program['meal_plan']; ?>">
                                        <?php echo $row_program['meal_plan']; ?></option>
                                    <?php } ?>
                                    <?php get_mealplan_dropdown(); ?>
                                </select></td>
                            <td class='col-md-1 pad_8'><button type="button"
                                    class="btn btn-info btn-iti btn-sm" style="border:none;"
                                    id="itinerary<?php echo $offset1; ?>" title="Add Itinerary"
                                    onClick="add_itinerary('dest_name','special_attaraction<?php echo $offset; ?>-u','day_program<?php echo $offset; ?>-u','overnight_stay<?php echo $offset; ?>-u','Day-<?= $offset1 ?>')"><i
                                        class="fa fa-plus"></i></button>
                            </td>
                            <td style="width: 100px;"><input style="display:none" type="text"
                                    name="package_id_n" value="<?php echo $row_tours['package_id']; ?>">
                            </td>
                        </tr>
                        <?php $offset++;
                    } ?>
                        </table>
                    </div>
                    <div class="row mg_tp_20">
                        <div class="col-md-6">
                            <legend>Inclusions</legend>
                        </div>
                        <div class="col-md-6">
                            <legend>Exclusions</legend>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table style="width:100%" class="no-marg"
                                id="dynamic_table_incl<?= $row_tours['package_id'] ?>"
                                name="dynamic_table_incl<?= $row_tours['package_id'] ?>">
                                <tr>
                                    <td class="col-md-6"><textarea class="feature_editor"
                                            id="inclusions<?= $row_tours['package_id'] ?>" name="inclusions"
                                            placeholder="Inclusions" title="Inclusions"
                                            rows="4"><?php echo $row_tours['inclusions']; ?></textarea></td>
                                    <td class="col-md-6"><textarea class="feature_editor"
                                            id="exclusions<?= $row_tours['package_id'] ?>" name="exclusions"
                                            placeholder="Exclusions" title="Exclusions"
                                            rows="4"><?php echo $row_tours['exclusions']; ?></textarea></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$count++;
$table_count++;
$_SESSION['id'] = $row_tours['package_id'];
} ?>
    </div>
</div>
<script>
$(document).on("click", ".style_text_b, .style_text_u", function() {
    var wrapper = $(this).data("wrapper");
    
    // Get the textarea element
    var textarea = $(this).parents('.style_text').siblings('.day_program')[0];
    console.log(textarea);
    // Ensure textarea exists and selectionStart/selectionEnd are supported
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;

        // Get the selected text
        var selectedText = textarea.value.substring(start, end);

        // Wrap the selected text with the wrapper (e.g., ** for bold, __ for underline)
        var wrappedText = wrapper + selectedText + wrapper;

        // Insert the wrapped text back into the textarea
        textarea.value = textarea.value.substring(0, start) + wrappedText + textarea.value.substring(end);

        // Adjust the cursor position after wrapping
        textarea.selectionStart = start;
        textarea.selectionEnd = end + wrapper.length * 2;
		var text=textarea.value;
		 var content = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

		// Replace markdown-style underline (__text__) with <u> tags
		content = content.replace(/__(.*?)__/g, '<u>$1</u>');
		textarea.value =content;
		//console.log(content);    
});


</script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>