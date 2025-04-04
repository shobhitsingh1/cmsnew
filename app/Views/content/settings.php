<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
<?php foreach ($cssFiles as $cssFile): ?>
    <link rel="stylesheet" href="<?= base_url($cssFile) ?>">
<?php endforeach; ?>
<?php foreach ($jsFiles as $jsFile): ?>
    <script src="<?= base_url($jsFile) ?>"></script>
<?php endforeach; ?>
<script type="text/javascript">
	$(document).ready(function(){
		
	
		$('#resetBtn').click(function(){
		
			$('#header_text_color').val('#c1c1c1');
			$('#header_bg_color').val('#58595b');
			$('#field_heading_text_color').val('#58595b');
			$('#field_txt_color').val('#58595b');
			$('#page_bg_color').val('#c1c1c1');
			$('#devotional_bg_color').val('#e8e8e8');
			$('#devotional_text_color').val('#58595b');
			$('#button_bg_color').val('#9b9b9c');
			$('#button_text_color').val('#fff');
			$("#export_bg_color").val('#f47b20');
			$("#export_text_color").val('#fff');
			$("#user_color").val('#fff');
			$("#fonts option:selected").text('Arial, Helvetica, sans-serif');
			//$("#fonts").val('#fff');
				//$('#frmSetting').submit();
			
		
		});
	});
	$(document).ready( function() {
			
            $('.minicolors').each( function() {
                //
                // Dear reader, it's actually very easy to initialize MiniColors. For example:
                //
                //  $(selector).minicolors();
                //
                // The way I've done it below is just for the demo, so don't get confused 
                // by it. Also, data- attributes aren't supported at this time. Again, 
                // they're only used for the purposes of this demo.
                //
				$(this).minicolors({
					control: $(this).attr('data-control') || 'hue',
					defaultValue: $(this).attr('data-defaultValue') || '',
					inline: $(this).attr('data-inline') === 'true',
					letterCase: $(this).attr('data-letterCase') || 'lowercase',
					opacity: $(this).attr('data-opacity'),
					position: $(this).attr('data-position') || 'bottom left',
					change: function(hex, opacity) {
						var log;
						try {
							log = hex ? hex : 'transparent';
							if( opacity ) log += ', ' + opacity;
							console.log(log);
						} catch(e) {}
					},
					theme: 'default'
				});
                
            });
			
		});
	
</script>
<?php

	$array_fornts = array("Impact, Charcoal, 
	sans-serif"," 'Palatino Linotype', 
	'Book Antiqua', Palatino, serif",
	"Tahoma, Geneva, sans-serif",
	"Century Gothic, sans-serif",
	"'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
	"'Arial Black', Gadget, sans-serif",
	"'Times New Roman', Times, serif",
	"'Arial Narrow', sans-serif",
	"Verdana, Geneva, sans-serif",
	"Copperplate / Copperplate Gothic Light, sans-serif",
	"'Lucida Console', Monaco, monospace",
	"Gill Sans / Gill Sans MT, sans-serif",
	"'Trebuchet MS', Helvetica, sans-serif",
	"'Courier New', Courier, monospace",
	"Arial, Helvetica, sans-serif",
	"Georgia, Serif"
	);
	
	
	


?>
<div class="body_main">
	<div class="main">
		<div class="body_right_new">
			<div class="search_con" style="z-index:761;">
				<h3>Color Scheme</h3>
				

				<div class="clear"></div>
				<div class="border_btm"></div>
			</div>
			<div class="scroll_bg" style="background-color:#fff;height:450px;">
				<div class="scroll-pane" style="height:650px;width:680px;">
							<form name="frmSetting" id="frmSetting" method="post" action="settings.php">
								<?php $row = $query_admin_users[0];
									
								?> 
                                <div class="left">
                                    <div class="admin_form_text_user"><strong>Header Text Color </strong> : </div>
									
                                    <input name="header_text_color" id="header_text_color" data-position="top right" class="minicolors" maxlength="10"  data-control="saturation"  type="text" value="<?php echo (ISSET($row->header_text_color))?$row->header_text_color:'#c1c1c1' ?>">
                                </div>
                                <div class="left">
                                    <div class="admin_form_text_user">Header Background Color: </div>
                                    <input name="header_bg_color" id="header_bg_color"  data-position="top right" value="<?php echo (ISSET($row->header_bg_color))?$row->header_bg_color:'#58595b' ?>" class="minicolors" maxlength="10" type="text">
                                </div>
                                <div class="left">
                                    <div class="admin_form_text_user"><strong>Nav Mouseover Color</strong> : </div>
                                    <input name="user_color" data-position="top right" id="user_color" value="<?php echo (ISSET($row->user_color))?$row->user_color:'#fff' ?>" class="minicolors" maxlength="10" type="text">
                                </div>
                                <div class="left">
                                    <div class="admin_form_text_user"> <strong>Field Heading Text Color</strong> : </div>
                                    <input name="field_heading_text_color" id="field_heading_text_color" data-position="top right" value="<?php echo (ISSET($row->field_heading_text_color))?$row->field_heading_text_color:'#58595b' ?>" class="minicolors" maxlength="10" type="text">
                                </div>
								<div class="left">
                                    <div class="admin_form_text_user"><strong>Field Text Color </strong> : </div>
                                    <input name="field_txt_color" id="field_txt_color" data-position="top right" value="<?php echo (ISSET($row->field_txt_color))?$row->field_txt_color:'#58595b' ?>" class="minicolors" maxlength="10" type="text">
                                </div>
                                
                                <div class="left">
                                    <div class="admin_form_text_user"><strong>Page Background Color</strong> : </div>
                                    <input name="page_bg_color" id="page_bg_color" data-position="top right" value="<?php echo (ISSET($row->page_bg_color))?$row->page_bg_color:'#c1c1c1' ?>" class="minicolors" maxlength="10" type="text">
                                </div>
								<div class="left">
                                    <div class="admin_form_text_user"><strong>Devotional Background Color</strong> : </div>
                                    <input name="devotional_bg_color" data-position="top right" id="devotional_bg_color" value="<?php echo (ISSET($row->devotional_bg_color))?$row->devotional_bg_color:'#e8e8e8' ?>" class="minicolors" maxlength="10" type="text">
                                </div>
								<div class="left">
                                    <div class="admin_form_text_user"><strong>Devotional Text Color</strong> : </div>
                                    <input name="devotional_text_color" data-position="top right" id="devotional_text_color" value="<?php echo (ISSET($row->devotional_text_color))?$row->devotional_text_color:'#58595b' ?>" class="minicolors" maxlength="10" type="text">
                                </div>
								<div class="left">
                                    <div class="admin_form_text_user"><strong>Button Text Color </strong> : </div>
                                    <input name="button_text_color" data-position="top right" id="button_text_color" value="<?php echo (ISSET($row->button_text_color))?$row->button_text_color:'#fff' ?>" class="minicolors" maxlength="10" type="text">
                                </div>
								<div class="left">
                                    <div class="admin_form_text_user"><strong>Button background Color</strong> : </div>
                                    <input name="button_bg_color" data-position="top right" id="button_bg_color" value="<?php echo (ISSET($row->button_bg_color))?$row->button_bg_color:'#9b9b9c' ?>" class="minicolors" maxlength="10" type="text">
                                </div>
								<div class="left">
                                    <div class="admin_form_text_user"><strong>Export background Color</strong> : </div>
                                    <input name="export_bg_color" data-position="top right" id="export_bg_color" value="<?php echo (ISSET($row->export_bg_color))?$row->export_bg_color:'#f47b20' ?>" class="minicolors" maxlength="10" type="text">
                                </div>
								
								<div class="left">
                                    <div class="admin_form_text_user"><strong>Export/Reset Text Color</strong> : </div>
                                    <input name="export_text_color" data-position="top right" id="export_text_color" value="<?php echo (ISSET($row->export_text_color))?$row->export_text_color:'#fff' ?>" class="minicolors" maxlength="10" type="text">
                                </div>
								
								<div class="left">
                                    <div class="admin_form_text_user"><strong>Devotional Font:</strong> : </div>
                                    <select name="fonts" id="fonts" style="width:300px;margin-top:1px;height:24px;">
									<?php foreach($array_fornts as $v) { 
										$sel = '';
										if(empty($row->fonts)){
											$row->fonts = 'Arial, Helvetica, sans-serif';
										}
										?>
										<?php if($row->fonts == $v): ?>
										<?php $sel = "selected='selected'"; ?>
										<?php endif; ?>
										<option value="<?php echo $v ?>" <?php echo $sel ?>><?php echo str_replace("'",'',$v) ?></option>
									<?php } ?>
									</select>	
                                </div>
								
								
								<div class="save" style="width:157px;margin-left:248px;" >
									<input type="button"  id="resetBtn" class="btn_2" name="btnreset" value="reset" style="width:72px;">
									
									<input type="submit" class="btn_1" name="btnsubmit" value="submit" style="width:65px;">
                                </div>	
							</form>
					</div>
			</div>



		</div>   

	<div class="clear"></div>
	</div>
</div>