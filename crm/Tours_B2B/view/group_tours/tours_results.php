<?php
include '../../../model/model.php';
$tours_result_array = ($_POST['final_arr'] != NULL) ? $_POST['final_arr'] : [];
?>
<input type='hidden' value='<?= json_encode($tours_result_array,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>' id='tours_results' name='tours_results'/>

<div id='noData-container'></div>
<div id='data-container'>
</div>
<div id='pagination-container'></div>

<script>
$(document).ready(function () {
    var html  = '<div class="timeline-item"><div class="animated-background"><div class="imgDiv"></div><div class="line-1"></div><div class="line-2"></div><div class="line-3"></div><div class="line-4"></div><div class="line-5"></div></div></div><div class="timeline-item"><div class="animated-background"><div class="imgDiv"></div><div class="line-1"></div><div class="line-2"></div><div class="line-3"></div><div class="line-4"></div><div class="line-5"></div></div></div>';
    $('#data-container').html(html);
    var label = (<?= sizeof($tours_result_array)?> === 1||<?= sizeof($tours_result_array)?> === 0)? 'Tour result':'Tours results';
    document.getElementsByClassName("results_count")[0].innerHTML='<?= sizeof($tours_result_array) ?>'+' '+label;
    document.getElementsByClassName("results_count")[1].innerHTML='<?= sizeof($tours_result_array) ?>'+' '+label;
    
    var tours_results = $('#tours_results').val();
    if(tours_results!=='null' && JSON.parse(tours_results).length !== 0){
        $('#pagination-container').pagination({
            dataSource:JSON.parse(tours_results) ,
            pageSize: 20,
            isForced:true,
            callback: function(data, pagination) {
                $.post('per_page_result.php', { data: data }, function (html) {
                    $('#data-container').html(html);
                });
            }
        })
    }
    else{  
        var html  = '<div class="c-emptyList"><div class="imgDiv"><img src="../../images/search_illustration.svg" alt="" /></div><span class="infoDiv">The Tours are not found for this search. Please modify search.</span></div>';
        $('#noData-container').html(html);
        $('#data-container').html('');
    }
});
</script>