<?php //echo anchor("create_user", "Create Users", "id='create_users'"); 

				if(count($admin_data) > 0){
                    foreach ($admin_data as $admin) {
                        $user = $admin;
                    }
				
				}
				?>
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
		
		
		$("#frmCreateUser").validate({
		rules: {
			user_name: {
				required: true,
				minlength: 2,
				remote:"checkuser"
			},
			user_password: {
				
				required: true,
				minlength: 5
			},
			conf_password: {
				required: true,
				minlength: 5,
				equalTo: "#user_password"
			},
			user_email: {
				required: true,
				email: true
			}
		},
		messages: {
			
			user_name: {
				required: "Please enter a username",
				minlength: "Your username must consist of at least 2 characters",
				remote:"Username already in System. "
			},
			user_password: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long"
			},
			conf_password: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long",
				equalTo: "Please enter the same password as above"
			},
			user_email: "Please enter a valid email address"
			
		}
	});
	$("#frmUpdateUser").validate({
		rules: {
			user_name: {
				required: true,
				minlength: 2
				
			},
			user_password: {
				
				
				minlength: 5
			},
			conf_password: {
				
				minlength: 5,
				equalTo: "#user_password"
			},
			user_email: {
				required: true,
				email: true
			}
			
			
		},
		messages: {
			
			user_name: {
				required: "Please enter a username",
				minlength: "Your username must consist of at least 2 characters"
				
			},
			user_password: {
				
				minlength: "Your password must be at least 5 characters long"
			},
			conf_password: {
				
				minlength: "Your password must be at least 5 characters long",
				equalTo: "Please enter the same password as above"
			},
			user_email: "Please enter a valid email address"
			
		}
	});
	});	
</script>		
<div class="body_main">
	<div class="main">
		<div class="body_right_new">
			<div class="search_con" style="z-index:761;">
				<h3>Create Users</h3>
				

				<div class="clear"></div>
				<div class="border_btm"></div>
			</div>
			<div class="scroll_bg" style="background-color:#fff;height:450px;">
				<div class="scroll-pane" style="height:650px;width:680px;margin-left: -60px;padding-top: 44px;">
						<?php if(!ISSET($user->id)): ?>
						<form name="frmCreateUser" id="frmCreateUser" method="POST" action="create_users.php">
						<?php else: ?>
						<form name="frmUpdateUser" id="frmUpdateUser" method="POST" action="create_users.php">
						<?php endif; ?>
                                <div class="left">
                                    <div class="admin_form_text_user">Username:</div>
									<input type="hidden" name="user_id" value="<?php echo (ISSET($user->id))?$user->id:'' ?>">
                                    <input name="user_name" id="user_name" value="<?php echo (ISSET($user->user_name))?$user->user_name:'' ?>" <?php echo (ISSET($user->user_name))?"readonly":'' ?> class="textbox_2" maxlength="50" type="text" required>
                                </div>
                                <div class="left">
                                    <div class="admin_form_text_user">User Email:</div>
                                    <input name="user_email" id="user_email" value="<?php echo (ISSET($user->user_email))?$user->user_email:'' ?>" class="textbox_2" maxlength="100" type="text" required>
                                </div>
                                <div class="left">
                                    <div class="admin_form_text_user">Password:</div>
                                    <input name="user_password" id="user_password" value="" class="textbox_2" maxlength="20" type="password" >
                                </div>
                                
                                <div class="left" style="width:100%">
                                    <div class="admin_form_text_user"><strong>Confirm Password</strong>:</div>
                                    <input name="conf_password" id="conf_password" value="" class="textbox_2" maxlength="20" type="password" >
                                </div>
								
									<div class="left" style="display:none;">
										<div class="admin_form_text_user" ><strong>User Role</strong>:</div>
											<select name="user_role" id="user_role" class="selectyze2" >
												<option value="">Select</option>
												<option value="Designer">Designer</option>
												<option value="Production">Production</option>
												<option value="Coordinator">Coordinator</option>
											</select>
									</div>

									<?php 
									$c = '';
									if (ISSET($user->super_admin)){
										if($user->super_admin == '1'){
											$c = "checked='checked'";
										}
									}
									
									?>
									<div class="left" >
										<div class="admin_form_text_user"><strong>Admin</strong>:</div>
										<input name="user_privileges" id="user_privileges" value="1"  style="margin-top: 11px;" class="admin_form_input_checkbox" type="checkbox" <?php echo $c ?> >
									</div>                            
									<div class="left" style="margin-top:37px;width:108px;">                                
										<?php if(!ISSET($user->id)): ?>
										<input name="cmdSaveAdminUser" id="cmdSaveAdminUser" value="Submit" class="btn_1" type="submit">
										<?php else: ?>
										<input name="cmdUpdateAdminUser" id="cmdUpdateAdminUser" value="Update" class="btn_1" type="submit">
										<?php endif; ?>
									</div>
							</form>	
					</div>
			</div>



		</div>   

	<div class="clear"></div>
	</div>
</div>