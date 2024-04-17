<link href="<?php echo  base_url() ?>application/assests/css/jquery.multiselect_new.css" rel="stylesheet" type="text/css" media="all" />
<link href="<?php echo  base_url() ?>application/assests/css/jquery.multiselect.filter.css" rel="stylesheet" type="text/css" media="all" />



<script type="text/javascript" src="<?php echo  base_url() ?>application/assests/js/jquery.multiselect_new.js"></script>
<script type="text/javascript" src="<?php echo  base_url() ?>application/assests/js/jquery.multiselect.filter.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		
		
		
		
	
		
	
		var el = $("#tt").multiselect({selectedText:"# Selected Keywords"},{create: function(){ $(this).next().width(220); }}).multiselectfilter(),
		tagItem = $('#tag_name');
		
				
		$('#tag_click').click(function(){
			var tag_name = $.trim($('#tag_name').val());
			if(tag_name == ''){
				alert("Please enter tag name");
			
			}else{
			
				 $.ajax({
				 url:"<?php echo  base_url() ?>devotional/addtag",
				 data:{tag_name:tag_name,tag_type:"Tags"},
				 type:'post',
				 success:function(result){
						if($.trim(result) != 'EXIST'){
							//$('#div_tag_name_1').append(result);
							//$('#tag_name').val('');
							var v = tagItem.val(), opt = $('<option />', {
								value: result,
								text: v
							});
							
							
							
							opt.appendTo( el );
							
							el.multiselect('refresh');
							$('#tag_name').val('');
						}else{
							alert(tag_name+" Already Exist as Tag Name's");
						
						}
					}
				  });
				
			
			
			}
		
		
		
		
		});
		var e2 = $("#at").multiselect({selectedText:"# Selected Authors"},{create: function(){ $(this).next().width(220); }}).multiselectfilter(),
		authorItem = $('#tag_author');
		$('#author_click').click(function(){
		
		
			var tag_name = $.trim($('#tag_author').val());
			if(tag_name == ''){
				alert("Please enter author name");
			
			}else{
			
				 $.ajax({
				 url:"<?php echo  base_url() ?>devotional/addtag",
				 data:{tag_name:tag_name,tag_type:"Author"},
				 type:'post',
				 success:function(result){
						if($.trim(result) != 'EXIST'){
						var v = authorItem.val(), opt = $('<option />', {
								value: result,
								text: v
							});
							
							
							
							opt.appendTo( e2 );
							
							e2.multiselect('refresh');
							$('#tag_author').val('');
						}else{
							alert(tag_name+" Already Exist as Author Name's");
						
						}
					}
				  });
				
			
			
			}
		
		
		
		
		});
		
		var e3 = $("#bt").multiselect({selectedText:"# Selected Books"},{create: function(){ $(this).next().width(220); }}).multiselectfilter(),
		bookItem = $('#tag_books');
		
		$('#book_click').click(function(){
			var tag_name = $.trim($('#tag_books').val());
			if(tag_name == ''){
				alert("Please enter book name");
			
			}else{
			
				 $.ajax({
				 url:"<?php echo  base_url() ?>devotional/addtag",
				 data:{tag_name:tag_name,tag_type:"books"},
				 type:'post',
				 success:function(result){
						if($.trim(result) != 'EXIST'){
							var v = bookItem.val(), opt = $('<option />', {
								value: result,
								text: v
							});
							
							
							
							opt.appendTo( e3 );
							
							e3.multiselect('refresh');
							$('#tag_books').val('');
						}else{
							alert(tag_name+" Already Exist as Book Name's");
						
						}
					}
				  });
				
			
			
			}
		
		
		
		
		});
		
		$('#save_devotional').click(function(){
			var tag_ids = $('#tt').val();
			var author_ids = $('#at').val();
			var books_ids = $('#bt').val();
			var devotional_id = $('#devotional_id').val();
			$.ajax({
				 url:"<?php echo  base_url() ?>devotional/devotionaltags",
				 data:{tag_ids:tag_ids,author_ids:author_ids,books_ids:books_ids,devotional_id:devotional_id},
				 type:'post',
				 success:function(result){
						if(result == 'ok'){
							
							parent.location.reload(); // or opener.location.href = opener.location.href;
	
						
						}
					
						
					}
				});
			
			
		
		
		});
		
		});	
</script>
</head>
<body >
	<!--START CINTAINER-->
	
                	
                    <div class="body_right_mid">
                    	<?php if($query_devotional->num_rows() > 0): 
							foreach ($query_devotional->result() as $row_devotional): ?>
                      <div class="right_top_con2">
                        	<div class="selct_hd">
							<label id="Label120"  class=""><span  class="hd_text" style="margin-left:-5px;font-size:14px;"><strong>ID:<?php echo  $row_devotional->id ?></strong></span></label>
                            </div>
							<input type="hidden" name="d_date" class="d_date_class" value="<?php echo $row_devotional->devotional_date; ?>">
                            <div class="hd_text"><?php echo  date("l, F d, Y",strtotime($row_devotional->devotional_date)) ?></div>
                            <?php
							if(!empty($row_devotional->series_id)) : ?>
							<div class="ico"><img src="<?php echo  base_url() ?>application/assests/images/icon_1.png" width="19" height="19" alt="Series ID: <?php echo $row_devotional->series_id ?>" title="Series ID: <?php echo $row_devotional->series_id ?>" /></div>
							<?php else : ?>
							<div class="ico"></div>
							<?php endif; ?>
                            <div class="clear"></div>
                        </div>
						
                        <div class="content_con">
                        	<p ><span class="boldHeading"><?php echo  html_entity_decode($row_devotional->title) ?></span><br />
							<p><span style="text-decoration:underline;"><?php echo  html_entity_decode($row_devotional->subtitle) ?></span><br />
                            <span><?php echo  html_entity_decode($row_devotional->text) ?> </span><br />
                            
							
							 <hr  style="border-bottom: dotted 1px #000;background-color:transparent;padding-top:10px;"></hr>
							<p style="padding-top:10px;word-wrap: break-word;"><span style="font-weight: bold;">Tags</span>: <?php print $this->Tags->getTagsName($row_devotional->tag_ids) ?><br />
							<span style="font-weight: bold;">Books</span>: <?php print $this->Tags->getTagsName($row_devotional->book_ids); ?><br />
                            <span style="font-weight: bold;">Authors</span>: <?php print $this->Tags->getTagsName($row_devotional->author_ids); ?><br />
                            <span style="font-weight: bold;">Acknowledgements</span>: <?php echo  $row_devotional->acknowledgements ?></p>
							<p><span style="font-weight: bold;">Submitted By</span>: <?php  echo  $this->User->getUserName($row_devotional->user_id) ?></p>
							<input type="hidden" id="devotional_id" name="devotional_id" value="<?php echo $row_devotional->id ?>">
							</p>
							
                        </div>
                        <?php endforeach; ?>
						<?php endif; ?>
                        <br/>
                        <div class="popup_bottom">
                        	<div class="popup_bottom_con">
                            	<div class="sidebar_widgit">
										<h2>Tags</h2>
										<div class="btm_input">
											<div class="btn_in_left"><input name="tag_name" type="text" class="textbox_1" id="tag_name" maxlength="50" /> </div>
											 <div class="btn_in_right">
											<input type="button" class="btn_plus" value="+"  align="middle" id="tag_click" name="View">
										   </div>
											<div class="clear"></div>
										</div>
										<div id="div_tag_name">
											<select size="5"   id="tt" name="Tags[]" title="Refresh Exanmple" multiple="multiple" >
											<?php //print_r($query_tags); 
											$tags = array();
											$i = 1;
											$devotional_tags_by_id = array();
											$devotional_tags_by_id = @explode(",",$row_devotional->tag_ids);
											
											if ($query_tags->num_rows() > 0):
												foreach ($query_tags->result() as $row_tags): 
													$sel = '';
													$tags[$row_tags->id] =  $row_tags->title;
													if(in_array($row_tags->id,$devotional_tags_by_id)){
														$sel = "Selected='selected'";
													}
													
												?>
											<option value="<?php echo $row_tags->id; ?>" <?php echo $sel ?>><?php echo (strlen($row_tags->title) > 50)?substr($row_tags->title,0,50)."..":$row_tags->title; ?></option>
												<?php endforeach; ?>
											<?php endif; ?>	
											</select>
										</div>
									</div>
                            </div>
                            <div class="popup_bottom_con">
                            	<div class="sidebar_widgit">
                        	<h2>Books</h2>
                            <div class="btm_input" >
                                	<div class="btn_in_left"><input name="tag_books" type="text" class="textbox_1" id="tag_books" maxlength="50" /> </div>
									 <div class="btn_in_right">
									<input type="button" class="btn_plus" value="+" id="book_click" name="View">
									</div>
								   <div class="clear"></div>
                                </div>
								<div id="div_book_name">
									<select size="5"   id="bt" name="books[]" title="Refresh Exanmple" multiple="multiple" >
									<?php //print_r($query_tags); 
									$devotional_books_by_id = array();
									$devotional_books_by_id = @explode(",",$row_devotional->book_ids);
									if ($query_books->num_rows() > 0):
										foreach ($query_books->result() as $row_tags): 
											$sel = '';
											$tags[$row_tags->id] =  $row_tags->title;
											if(in_array($row_tags->id,$devotional_books_by_id)){
												$sel = "Selected='selected'";
											}
										?>
									<option value="<?php echo $row_tags->id; ?>" <?php echo $sel ?>><?php echo (strlen($row_tags->title) > 50)?substr($row_tags->title,0,50)."..":$row_tags->title; ?></option>
										<?php endforeach; ?>
									<?php endif; ?>	
									</select>
								
								
								
									</div>
									</div>
                            </div>
                            <div class="popup_bottom_con_last">
                            	<div class="sidebar_widgit">
                        	<h2>Authors</h2>
                            <div class="btm_input">
                                	<div class="btn_in_left"><input name="tag_author" type="text" class="textbox_1" id="tag_author" maxlength="50" /> </div>
                                    <div class="btn_in_right">
									<input type="button" class="btn_plus" value="+" id="author_click" name="View">
									</div>
                                    <div class="clear"></div>
                                </div>
								<div id="div_author_name">
								<select size="5"   id="at" name="author[]" title="Refresh Exanmple" multiple="multiple" >
									<?php //print_r($query_tags); 
									$devotional_author_by_id = array();
									$devotional_author_by_id = @explode(",",$row_devotional->author_ids);
									if ($query_author->num_rows() > 0):
										foreach ($query_author->result() as $row_tags): 
											$sel = '';
											$tags[$row_tags->id] =  $row_tags->title;
											if(in_array($row_tags->id,$devotional_author_by_id)){
												$sel = "Selected='selected'";
											}
										?>
									<option value="<?php echo $row_tags->id; ?>" <?php echo $sel ?>><?php echo (strlen($row_tags->title) > 50)?substr($row_tags->title,0,50)."..":$row_tags->title; ?></option>
										<?php endforeach; ?>
									<?php endif; ?>	
									</select>
								
									</div>
									
									
                            </div>
                            </div>
                            <div class="clear"></div>
                            
                            <div class="save">
                            	<input name="View" type="button"  id="save_devotional" value="Save" class="btn_1" />
                            </div>
                            
                        </div>
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                    </div>
                    <div class="clear"></div>
            
			<!--END BODY MAIN-->
		