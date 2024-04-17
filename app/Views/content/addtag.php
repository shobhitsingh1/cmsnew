<script type="text/javascript">
$(document).ready(function(){
	$(".CheckBoxClass").change(function(){
		if($(this).is(":checked")){
			$(this).next("label").addClass("LabelSelected");
		}else{
			$(this).next("label").removeClass("LabelSelected");
		}
	});
	$(".RadioClass").change(function(){
		if($(this).is(":checked")){
			$(".RadioSelected:not(:checked)").removeClass("RadioSelected");
			$(this).next("label").addClass("RadioSelected");
		}
	});	
});
</script>
<script>
$(function() {
	var zIndexNumber = 1000;
	$('div').each(function() {
		$(this).css('zIndex', zIndexNumber);
		zIndexNumber -= 10;
	});
});
</script>
<?php //print_r($query_tags); 
$i = 1;
if ($query_tags->num_rows() > 0):
foreach ($query_tags->result() as $row_tags): ?>

	
	<div class="sub_btn_1" style="z-index: 260;">
		<input id="<?php echo $row_tags->id; ?>"  name="<?php echo $tag_type; ?>[]" value="<?php echo $row_tags->id; ?>" type="checkbox" class="CheckBoxClass"/>
		<label id="Label<?php echo $row_tags->id; ?>" for="<?php echo $row_tags->id; ?>" class="CheckBoxLabelClass"><?php echo $row_tags->title; ?></label>
	</div>
	<?php $i++; ?>

<?php endforeach; ?>

<?php endif; ?>

