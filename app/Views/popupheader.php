<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta name="last-modified" content="" />
	<meta name="description" lang="en" content="" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-script-type" content="text/javascript" />
<title>DevoCMS</title>
<link href="<?php echo  base_url() ?>application/assests/css/style.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo  base_url() ?>application/assests/css/jquery-ui.css" type="text/css" />
<?php $user_data = $this->session->all_userdata();
	  if(empty($user_data['username'])){
			redirect("login");

		}
?>
 <script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
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
	$(".body_right_mid").css({"background-color":"<?php echo $user_data['page_bg_color'] ?>"});
	
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
<?php endif; ?>	
<?php if(!empty($user_data['button_bg_color'])):?>
	$(".btn_1").css({"background-color":"<?php echo $user_data['button_bg_color'] ?>"});
	$(".btn_plus").css({"background-color":"<?php echo $user_data['button_bg_color'] ?>"});
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