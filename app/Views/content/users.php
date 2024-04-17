<div class="body_main">
				<div class="main">
                    <div class="body_right_new">
                    	<div class="search_con">
                    	<h3>Users</h3>
						<h2 style="float:right">
                            <?php echo anchor(base_url("users/create_users.php"), "Create Users", "id='create_users'"); ?>
						</h2>
                        
                        <div class="clear"></div>
                        <div class="border_btm"></div>
                        </div>
                        <div class="scroll_bg" style="background-color:#fff;height:450px;">
                            <div class="scroll-pane" style="height:650px;width:680px;">
        						<div class="scroll-padding">
                                	<div class="sc_con_new">
                                    	
									<div class="admin_u_user_list" style="width:450px;">
											<ul>
											
											<?php 
                                            $i =1;
                                            if (count($query_admin_users) > 0):
												foreach ($query_admin_users as $row_tags): ?>
												
												<li>
												<div style="width:25px;float:left;padding-left:5px;">
													<?php echo $i; ?>
												</div>
												<div style="width:auto;float:left;padding-left:80px;" class='admin_form_text_user'>
												<?php echo ucwords($row_tags->user_name); ?> 
												</div>
                                                <div style="width:40px;float:right;padding-left:5px;" class='admin_form_text_user'>
												 <a href="<?php echo base_url()."users/create_users.php?id=".$row_tags->id."&action=delete" ?>">Delete</a>
												</div> 
												<div style="width:40px;float:right;padding-left:5px;" class='admin_form_text_user'>
												 <a href="<?php echo base_url()."users/create_users.php?id=".$row_tags->id ?>">Edit</a>
												</div>
                                                
												</li>
											<?php $i++; endforeach; ?>	
											<?php endif; ?>	
											</ul>
									   
									</div>
									<!--<div class="admin_u_del_btn"><a href="#"><img src="images/delete_btn3.png" alt="" /></a></div>-->
									<div class="admin_u_del_btn"></div>
								
                                        <div class="clear"></div>
                                  </div>
                                </div>
                            </div>
                        </div>
                        
                       
                       
                        
                    
                    <div class="clear"></div>
                </div>
			</div>