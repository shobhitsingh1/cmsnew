<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<?php $random = ""; ?>
	<meta name="last-modified" content="" />
	<meta name="description" lang="en" content="" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-script-type" content="text/javascript" />
<title><?php echo $title ?></title>
<link href="<?= base_url('/public/assests/css/style.css')?><?php echo $random ?>" rel="stylesheet" type="text/css" media="all" />
<?php
	  if(!isset($user_data['username'])){
			redirect("/");
		}
?>
<link rel="stylesheet" href="<?= base_url('/public/assests/css/jquery-ui.css')?><?php echo $random ?>" type="text/css" />
<link rel="stylesheet" href="<?= base_url('/public/assests/css/Selectyze.jquery.css')?><?php echo $random ?>" type="text/css" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js?<?php echo $random ?>"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js?<?php echo $random ?>"></script>


<script type="text/javascript" src="<?= base_url('/public/assests/js/Selectyze.jquery.js')?><?php echo $random ?>"></script>
<script type="text/javascript" src="<?= base_url('/public/assests/js/jquery-divslidetoggle.js')?><?php echo $random ?>"></script>
<script type="text/javascript" src="<?= base_url('/public/assests/js/jquery.lavalamp.min.js')?><?php echo $random ?>"></script>


<script type="text/javascript">

$(document).ready(function(){
$('ul#menu').lavaLamp();
$('ul#menu2').lavaLamp();
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

	
<?php if(!empty($user_data['header_text_color'])):?>
	$(".header_nav ul li a").css({"color":"<?php echo $user_data['header_text_color'] ?>"});
	$(".logout ul li a").css({"color":"<?php echo $user_data['header_text_color'] ?>"});
	
	$(".hd_text a").css({"color":"<?php echo $user_data['header_text_color'] ?>"});
	$(".hd_text a:visited").css({"color":"<?php echo $user_data['header_text_color'] ?>"});
	$(".right_top_con2").css({"color":"<?php echo $user_data['header_text_color'] ?>"});
	$("#ui-id-1").css({"color":"<?php echo $user_data['header_text_color'] ?>"});	
	
	
	
	
	
<?php endif; ?>	



<?php if(!empty($user_data['header_bg_color'])):?>
	$(".header_top").css({"background-color":"<?php echo $user_data['header_bg_color'] ?>"});
	$(".right_top_con2").css({"background-color":"<?php echo $user_data['header_bg_color'] ?>"});
	$(".ui-dialog-titlebar.ui-widget-header.ui-corner-all.ui-helper-clearfix.ui-draggable-handle").css({"background-color":"<?php echo $user_data['header_bg_color'] ?>"});
	
	
<?php endif; ?>	

<?php if(!empty($user_data['field_heading_text_color'])):?>
	$(".body_right h2").css({"color":"<?php echo $user_data['field_heading_text_color'] ?>"});
	$(".body_right h3").css({"color":"<?php echo $user_data['field_heading_text_color'] ?>"});
	$(".sidebar_widgit h2").css({"color":"<?php echo $user_data['field_heading_text_color'] ?>"});
	$("#create_series").css({"color":"<?php echo $user_data['field_heading_text_color'] ?>"});
	$(".admin_form_text_user").css({"color":"<?php echo $user_data['field_heading_text_color'] ?>"});
	$(".header_mid h2").css({"color":"<?php echo $user_data['field_heading_text_color'] ?>"});
	
	
	

	
<?php endif; ?>	

<?php if(!empty($user_data['field_txt_color'])):?>
	$(".CheckBoxLabelClass").css({"color":"<?php echo $user_data['field_txt_color'] ?>"});
	$(".RadioLabelClass").css({"color":"<?php echo $user_data['field_txt_color'] ?>"});
	$(".search_con h3").css({"color":"<?php echo $user_data['field_txt_color'] ?>"});
	
<?php endif; ?>	

<?php if(!empty($user_data['page_bg_color'])):?>
	$("body").css({"background-color":"<?php echo $user_data['page_bg_color'] ?>"});
<?php endif; ?>

<?php if(!empty($user_data['devotional_bg_color'])):?>
	$(".content_con").css({"background-color":"<?php echo $user_data['devotional_bg_color'] ?>"});
<?php endif; ?>

<?php if(!empty($user_data['devotional_text_color'])):?>
	$(".content_con p").css({"color":"<?php echo $user_data['devotional_text_color'] ?>"});
<?php endif; ?>	
<?php if(!empty($user_data['button_text_color'])):?>
	$(".btn_1").css({"color":"<?php echo $user_data['button_text_color'] ?>"});
	$(".btn_plus").css({"color":"<?php echo $user_data['button_text_color'] ?>"});
    $(".abs").css({"color":"<?php echo $user_data['button_text_color'] ?>"});
<?php endif; ?>	
<?php if(!empty($user_data['button_bg_color'])):?>
	$(".btn_1").css({"background-color":"<?php echo $user_data['button_bg_color'] ?>"});
	$(".btn_plus").css({"background-color":"<?php echo $user_data['button_bg_color'] ?>"});
    $(".abs").css({"background-color":"<?php echo $user_data['button_bg_color'] ?>"});
<?php endif; ?>	
<?php if(!empty($user_data['export_text_color'])):?>
	$(".ex_btn").css({"color":"<?php echo $user_data['export_text_color'] ?>"});
	$(".btn_2").css({"color":"<?php echo $user_data['export_text_color'] ?>"});
	
	
	
<?php endif; ?>	
<?php if(!empty($user_data['export_bg_color'])):?>
	$(".ex_btn").css({"background-color":"<?php echo $user_data['export_bg_color'] ?>"});
	$(".btn_2").css({"background-color":"<?php echo $user_data['export_bg_color'] ?>"});
	
	
<?php endif; ?>	
<?php if(!empty($user_data['fonts'])):?>
	$(".content_con p").css({"font-family":"<?php echo $user_data['fonts'] ?>"});

<?php endif; ?>	
//user color Nav Mouse bg color
<?php if(!empty($user_data['user_color'])):?>
	$("ul#menu li.backLava").css({"background-color":"<?php echo $user_data['user_color'] ?>"});

<?php endif; ?>	


});
</script>
<script>
/*$(function() {
	var zIndexNumber = 1000;
	$('div').each(function() {
		$(this).css('zIndex', zIndexNumber);
		zIndexNumber -= 10;
	});
});*/
</script>

<script type='text/javascript'><!--
			$(document).ready(function() {
				enableSelectBoxes();
			});
			
			function enableSelectBoxes(){
				$('div.selectBox').each(function(){
					$(this).children('span.selected').html($(this).children('div.selectOptions').children('span.selectOption:first').html());
					$(this).attr('value',$(this).children('div.selectOptions').children('span.selectOption:first').attr('value'));
					
					$(this).children('span.selected,span.selectArrow').click(function(){
						if($(this).parent().children('div.selectOptions').css('display') == 'none'){
							$(this).parent().children('div.selectOptions').css('display','block');
						}
						else
						{
							$(this).parent().children('div.selectOptions').css('display','none');
						}
					});
					
					$(this).find('span.selectOption').click(function(){
						$(this).parent().css('display','none');
						$(this).closest('div.selectBox').attr('value',$(this).attr('value'));
						$(this).parent().siblings('span.selected').html($(this).html());
					});
				});				
			}//-->
			
			

</script>
<!--		-->
<!--		--><?php //echo $_scripts; ?>
<!--		--><?php //echo $_styles; ?>
</head>

		
<body>
	<!--START CINTAINER-->
	<div class="container">
		<!--START WRAPPER-->
		<div class="wrapper">
			<!--START HEADER-->
			<div class="header">
            	<div class="header_top">
					<div class="main">
                	<div class="header_nav">
                    	<ul class="lavaLampWithImage" id='menu'>
						<?php $active_menu = $class_name ?>
                            <li  <?php echo ($active_menu == 'library')? 'class="selectedLava"':'';?>><a href="<?php echo base_url()?>library.php">&nbsp;Library&nbsp;</a></li>     
                            <li <?php echo ($active_menu == 'devotional')? 'class="selectedLava"':'';?> ><a href="<?php echo base_url()?>add_devotional.php" >&nbsp;Add Devotional&nbsp;</a></li>	
                            <li <?php echo ($active_menu == 'tagview')? 'class="selectedLava"':'';?> ><a href="<?php echo base_url()?>tagview.php" >&nbsp;Tag View&nbsp;</a></li>      
                            <li  <?php echo ($active_menu == 'settings')? 'class="selectedLava"':'';?>><a href="<?php echo base_url()?>settings.php">&nbsp;Settings&nbsp;</a></li>
							 
							<?php if($user_data['super_admin'] == 'super_admin') :?>
							<li <?php echo ($active_menu == 'users')? 'class="selectedLava"':'';?> ><a href="<?php echo base_url()?>users.php" >&nbsp;Users&nbsp;</a></li>
							 <?php else: ?>
							 <li>&nbsp;&nbsp;&nbsp;</li>
							 <?php endif ;?>
							 
							
                        </ul>
                        
                        <div class="clear"></div>
                    </div>
					<div class="logout" style="float:left;margin-left:120px;">
                    <ul>
                         <li><i><a>Welcome, <?php echo ucwords($user_data['username']); ?></a></i></li>
                        </ul>
                    </div>
                    <div class="logout">
                    	<ul >
						
                        	<li  ><a href="<?php echo base_url()?>login/logout.php">Log Out</a></li>
                        </ul>
                    </div>
                    <div class="clear"></div>
                </div>
                </div>
                
                <div class="main">
                	 <div class="header_mid_container">
					 <?php if($active_menu == 'library'): ?>
						<div class="header_left">&nbsp;</div>
                        <div class="header_mid"> <?php if(ISSET($parameter)): ?><h2><span>Search Parameters: <?php print $parameter ?></span></h2> <?php endif; ?></div>
						<div class="header_left">&nbsp;</div>
						<div class="header_mid"> <?php if(ISSET($query_devotional)): ?><h2><span> Records Found: <span id="filter_counter_span"></span></span></h2> <?php endif; ?></div>
                        <div class="header_right">
                        	<div class="ex_btn"><a id="exportBtn" >Export</a></div>
                            <div class="radio_con" style="padding-left:23px;" >
								<input id="CheckBox3" type="checkbox" class="CheckBoxClass" name="group3" value="HID" >
                                <label style="width:65px;float:left;margin-left:-133px;font-size:13px;" id="Label3" for="CheckBox3" class="CheckBoxLabelClass" >Hide ID</label>
                            	<input id="CheckBox1" type="checkbox" class="CheckBoxClass" name="group1" value="HT" >
                                <label style="width:65px;float:left;margin-left:-50px;font-size:13px;" id="Label1" for="CheckBox1" class="CheckBoxLabelClass" >Hide Tags</label>
                                <input id="CheckBox2" type="checkbox" class="CheckBoxClass"  value="HA" name="group2"/>
                                <label style="width:auto;float:left;font-size:13px;" id="Label2" for="CheckBox2" class="CheckBoxLabelClass" >Hide Acknowledgements</label>
                                <div class="clear"></div>	
                             </div>
                            </div>
                            <div class="clear"></div>
						<?php endif; ?>
                         <?php if($active_menu == 'tagview'): ?>
						<div class="header_left">&nbsp;</div>
                            <div class="header_right">
                        	<div class="ex_btn"><a id="exportBtn" >Export</a></div>
                            
                            </div>
                            <div class="clear"></div>
						<?php endif; ?>
					 </div>
                    </div>
                </div>
			</div>
			<!--END HEADER-->
			<?php echo $content ?>
			<!--END BODY MAIN-->
			<!--START FOOTER-->
			<div class="footer">
				
			</div>
			<!--END FOOTER-->
		</div>
		<!--END WRAPPER-->
	</div>
	<!--END CINTAINER-->
</body>
</html>
