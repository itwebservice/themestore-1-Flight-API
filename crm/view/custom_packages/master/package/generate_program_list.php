<?php
include '../../../../model/model.php';
?>
<div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
    <legend>Tour Itinerary</legend>
    <table id="dynamic_table_list" style="width:100%" name="dynamic_table_list">
        <?php
		$total_days = $_POST['total_days'];
		for ($i = 1; $i <= $total_days; $i++) {
		?>
        <tr>
            <td class='col-md-3 pad_8'><input type="text" id="special_attaraction<?php echo $i; ?>"
                    name="special_attaraction" class="form-control mg_bt_10" placeholder="*Special Attraction"
                    title="Special Attraction" onchange="validate_spaces(this.id);validate_spattration(this.id);"
                    value="">
            </td>
            <td class='col-md-6 pad_8' style="max-width: 594px;overflow: hidden;position: relative;"><textarea id="day_program<?php echo $i; ?>" name="day_program" class="form-control mg_bt_10 day_program" placeholder="*Day<?php echo $i; ?> Program" title="Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" value=""></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
            </td>
			
            <td class='col-md-2 pad_8'><input type="text" id="overnight_stay<?php echo $i; ?>" name="overnight_stay"
                    class="form-control mg_bt_10" placeholder="*Overnight Stay"
                    onchange="validate_spaces(this.id);validate_onstay(this.id);" title="Overnight Stay" value="">
            </td>
            <td class='col-md-1 pad_8'><select id="meal_plan<?php echo $i; ?>" title="Meal Plan" name="meal_plan"
                    class="form-control mg_bt_10" style="width:140px">
                    <?php get_mealplan_dropdown(); ?>
            </td>
            <td class='col-md-1 pad_8'><button type="button" class="btn btn-excel btn-sm" title="Add Itinerary"
                    id="itinerary<?php echo $i; ?>"
                    onclick="add_itinerary('dest_name_s','special_attaraction<?php echo $i; ?>','day_program<?php echo $i; ?>','overnight_stay<?php echo $i; ?>','Day-<?= $i ?>')"><i
                        class="fa fa-plus"></i></button>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>