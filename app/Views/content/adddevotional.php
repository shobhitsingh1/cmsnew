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
		
		
		$(".colorbox2").colorbox({iframe:true,innerWidth:"800px", innerHeight:"600px"}); 
		
	
		
		$('.selectyze2').Selectyze({
			theme : 'select2'
		});
		var el = $("#tt").multiselect({selectedText:"# Selected Keywords"},{create: function(){ $(this).next().width(220); }}).multiselectfilter(),
		tagItem = $('#tag_name');
		
				
		$('#tag_click').click(function(){
			var tag_name = $.trim($('#tag_name').val());
			if(tag_name == ''){
				alert("Please enter tag name");
			
			}else{
			
				 $.ajax({
				 url:"devotional/addtag",
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
				 url:"devotional/addtag",
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
				 url:"devotional/addtag",
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
		 $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Show All Items" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " didn't match any item" )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.autocomplete( "instance" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
		
		
		
	});
	function ltrim(stringToTrim) {

		 return stringToTrim.replace(/^\s+/,"");

		}

		function rtrim(stringToTrim) {

		 return stringToTrim.replace(/\s+$/,"");

		}
	function check_date(){
		
		if($('#devotional').val() != ''){
			var devotional_lines = $.trim($('#devotional').val()).split('\n');
			//alert(devotional_lines[0]);
			var date_quarter = $('#date_quarter :selected').text();
			//alert("date_quarter"+date_quarter);
			var date_year = $('#date_devo :selected').text();
			var d = $.datepicker.parseDate("DD MM dd, yy",  devotional_lines[0]+", "+date_year);
			var datestrInNewFormat = $.datepicker.formatDate( "yy-mm-dd", d);
			var New_datestrInNewFormat = $.datepicker.formatDate( "DD, MM dd, yy", d);
			//alert(datestrInNewFormat);
			
			var d_date = new Array();
			$('.d_date_class').each(function(){
			   d_date.push($(this).val());
			});
			
			if(jQuery.inArray( datestrInNewFormat, d_date) > '-1'){
				
				var r = confirm("Do you want to overwrite "+New_datestrInNewFormat+" ?");
				if (r == true) {
					$("#frm1").submit();
					//return true;
				} 		
			}else {
					$("#frm1").submit();
			}	
		
		}else{
		
		return false;
		}
	}
	 $(function() {
		var dialog, form,
		// From http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-%28type=email%29
		
		series_count = $( "#series_count" ),
		allFields = $( [] ).add( series_count ),
		tips = $( ".validateTips" );
		function updateTips( t ) {
			tips
			.text( t )
			.addClass( "ui-state-highlight" );
			setTimeout(function() {
			tips.removeClass( "ui-state-highlight", 1500 );
			}, 500 );
		}
		
		function checkRegexp( o, regexp, n ) {
		if ( !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			updateTips( n );
			return false;
			} else {
			return true;
			}
		}
		function addUser() {
			var valid = true;
			allFields.removeClass( "ui-state-error" );
			
			valid = valid && checkRegexp( series_count, /^([2-9])+$/, "Allowed field only allow : 0-9" );
			if ( valid ) {
				$('#series_processing').val(series_count.val());
				var start_p = $('#start_processing').val(1);
				
				$('#create_series').html("(Series) Processing "+start_p.val()+" of "+series_count.val());
				dialog.dialog( "close" );
			}
			return valid;
		}
		dialog = $( "#dialog-form" ).dialog({
			autoOpen: false,
			draggable: true,
		
		  width: 300,
		  height: 'auto',
		  modal: true,
			buttons: {
				"Create Series": addUser,
				Cancel: function() {
					dialog.dialog( "close" );
				}
			},
			close: function() {
				form[ 0 ].reset();
				allFields.removeClass( "ui-state-error" );
				}
		});
		form = dialog.find( "form" ).on( "submit", function( event ) {
			event.preventDefault();
			addUser();
		});
		$( "#create_series" ).click( function() {
			dialog.dialog( "open" );
		});
	});
	 $(function() {
    $( "#date_quarter" ).combobox();
    $( "#date_devo" ).combobox();
  });
</script>
<style>
.ui-autocomplete-input {
    height: 17px;
    width: 110px;
}
</style>

<!--START BODY MAIN-->
			
			<?php

            use App\Models\Tags;
            use App\Models\User;

            $this->tagsModel = new Tags();
            $this->usersModel = new User();
            ?>
			<form action="devotional/adddevotional" name="frm1" id="frm1" method="post">
			<div class="body_main">
				<div class="main">
                	<div class="body_left">
                    	<div class="sidebar_widgit" style=" height: 1000px;">
                        	<h2  >SELECT QUARTER:</h2>
                           
                              
                              <div class="select_con">
                               <div class='selectBox1'>
                                    <select id="date_quarter" name="date_quarter" placeholder="Quarter" >
                                    
									 <option value="d" disabled="disabled" selected="selected" >Quarter</option>
										<?php  for($i=94; $i <= 99; $i++): ?>
										<?php $quarter_array = array('03','06','09','12');
											foreach($quarter_array as $quarter_val): ?>
											<?php $y = ($i < 10)? "0".$i:$i ?>
											<option  value="<?php echo $quarter_val.$y ?>"><?php echo $quarter_val.$y ?></option>
										<?php endforeach; ?>
									<?php endfor;  ?>
										<option  value="0300">0300</option>
										<option  value="0600">0600</option>
										<option  value="0900">0900</option>
										<option  value="1200">1200</option>
										<?php for($i=1; $i <= 32; $i++): ?>
										<?php $quarter_array = array('03','06','09','12');
											foreach($quarter_array as $quarter_val): ?>
											<?php $y = ($i < 10)? "0".$i:$i ?>
											<option  value="<?php echo $quarter_val.$y ?>"><?php echo $quarter_val.$y ?></option>
										<?php endforeach; ?>
									<?php endfor;  ?>
									
								</select> 
                           	  </div> 
                            </div>
                             <br/>   
                            <h2  >SELECT YEAR:</h2>
                           
                              <div class="select_con" style="padding-bottom:10px;" >
                               <div class='selectBox1'>
                                  
                           	 
							  <select id="date_devo" name="date_devo" placeholder="Year" >
                               <option value="d" disabled="disabled" selected="selected">Year</option>
									<?php for($i=1994; $i <= 2035; $i++): ?>
										<option  value=<?php echo $i ?>><?php echo $i ?></option>
									
									<?php endfor;  ?>
									
								</select> 
								</div>
                              </div>
                                <div class="save" style="margin-left:16px;">
                            	<input name="View" type="button"  onclick="check_date();" value="Assign" class="btn_1" />
                            </div>
                            </form>
                        </div>
                        
                       
                        
                        
                        
                        <div class="clear"></div>
                    </div>
                    <div class="body_right">
                        <h2>Devotional</h2>
						<h2 style="float:right;margin-top:-30px;" ><a id='create_series' ><?php echo (empty($session_data['series_processing']))? "Create Series" :"(Series) Processing ".($session_data['start_processing']+1)." of ".$session_data['series_processing']; ?></a><input type="hidden" name="series_processing" id="series_processing" value="<?php echo (!empty($session_data['series_processing']))? $session_data['series_processing'] :'' ?>"><input type="hidden" id="start_processing" name="start_processing" value="<?php echo (!empty($session_data['start_processing']))? ($session_data['start_processing']+1) :'' ?>" ></h2>
                        <textarea name="devotional" id="devotional" cols="" rows="" class="textarea"></textarea>
                        <h2>Acknowledgements</h2>
                        <textarea name="acknowledgements" cols="" rows="" class="textarea"></textarea>
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
									if (count($query_tags) > 0):
										foreach ($query_tags as $row_tags):
											$tags[$row_tags->id] =  $row_tags->title;
										?>
									<option value="<?php echo $row_tags->id; ?>"><?php echo (strlen($row_tags->title) > 50)?substr($row_tags->title,0,50)."..":$row_tags->title; ?></option>
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
									
									if (count($query_books) > 0):
										foreach ($query_books as $row_tags):
											$tags[$row_tags->id] =  $row_tags->title;
										?>
									<option value="<?php echo $row_tags->id; ?>"><?php echo (strlen($row_tags->title) > 50)?substr($row_tags->title,0,50)."..":$row_tags->title; ?></option>
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
									
									if (count($query_author) > 0):
										foreach ($query_author as $row_tags):
											$tags[$row_tags->id] =  $row_tags->title;
										?>
									<option value="<?php echo $row_tags->id; ?>"><?php echo (strlen($row_tags->title) > 50)?substr($row_tags->title,0,50)."..":$row_tags->title; ?></option>
										<?php endforeach; ?>
									<?php endif; ?>	
									</select>
								
									</div>
									
									
                            </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <h3>Recent History</h3>
						           	
						
						<?php if(count($query_devotional) > 0):
							foreach ($query_devotional as $row_devotional): ?>
                      <div class="right_top_con2">
                        	<div class="selct_hd">
							<label id="Label120"  class=""><span  class="hd_text" style="margin-left:-5px;font-size:14px;"><strong>ID:<?php echo  $row_devotional->id ?></strong></span></label>
                            </div>
							<input type="hidden" name="d_date" class="d_date_class" value="<?php echo $row_devotional->devotional_date; ?>">
                            <?php if($row_devotional->series_id > 0) : ?>
                            <div class="hd_text"><a href="<?php echo base_url();?>library/libraryseries.php?id=<?php echo $row_devotional->series_id ?>"  class="colorbox2"><?php echo  date("l, F d, Y",strtotime($row_devotional->devotional_date)) ?></a></div>
                            <?php else: ?>
							 <div class="hd_text"><a href="<?php echo base_url();?>library/librarysingle.php?id=<?php echo $row_devotional->id ?>"  class="colorbox2"><?php echo  date("l, F d, Y",strtotime($row_devotional->devotional_date)) ?></a></div>
							<?php endif; ?>	
                            <?php
							if(!empty($row_devotional->series_id)) : ?>
							<div class="ico"><img src="<?php echo  base_url() ?>public/assests/images/icon_1.png" width="19" height="19" alt="Series ID: <?php echo $row_devotional->series_id ?>" title="Series ID: <?php echo $row_devotional->series_id ?>" /></div>
							<?php else : ?>
							<div class="ico"></div>
							<?php endif; ?>
                            <div class="clear"></div>
                        </div>
						<?php //print_r($tags);
						$tags_key_value = array();
						$tags_author_key_value = array();
						$tags_key_value3 = array();
						$row_devotional->tag_ids = trim($row_devotional->tag_ids);
							if($row_devotional->tag_ids != ''){
								$tags_arr = @explode(",",$row_devotional->tag_ids); 
								if(count($tags_arr) > 0){
									foreach($tags_arr as $tags_value){
										$tags_key_value[] = $tags[$tags_value];
									
									}
								}
							}
							$row_devotional->author_ids = trim($row_devotional->author_ids);
							//print $row_devotional->author_ids; 
							if($row_devotional->author_ids != ''){
								$tags_author_arr = @explode(",",$row_devotional->author_ids); 
								if(count($tags_author_arr)>0){
									foreach($tags_author_arr as $tags_value){
										$tags_author_key_value[] = $tags[$tags_value];
									
									}
								}
							}
							$row_devotional->book_ids = trim($row_devotional->book_ids);
							if($row_devotional->book_ids != ''){
								$tags_arr3 = @explode(",",$row_devotional->book_ids); 
								if(count($tags_arr3) > 0){
									foreach($tags_arr3 as $tags_value){
										$tags_key_value3[] = $tags[$tags_value];
									
									}
								}
							}
						$tags_str = implode(",",array_merge($tags_key_value,$tags_author_key_value,$tags_key_value3));
							

						?>
                        <div class="content_con">
                        	<p><span class="boldHeading"><?php echo  html_entity_decode($row_devotional->title) ?></span><br />
							<p><span style="text-decoration:underline;"><?php echo  html_entity_decode($row_devotional->subtitle) ?></span><br />
                            <span><?php echo  (strlen($row_devotional->text) > 400)?substr(html_entity_decode($row_devotional->text),0,398)."..":html_entity_decode($row_devotional->text) ?> </span><br />
                           <hr  style="border-bottom: dotted 1px #000;background-color:transparent;padding-top:10px;"></hr>
							<p style="padding-top:10px; word-wrap: break-word;"><span style="font-weight: bold;">Tags</span>: <?php   $this->tagsModel->getTagsName($row_devotional->tag_ids) ?><br />
							<span style="font-weight: bold;">Books</span>: <?php print  $this->tagsModel->getTagsName($row_devotional->book_ids); ?><br />
                            <span style="font-weight: bold;">Authors</span>: <?php print  $this->tagsModel->getTagsName($row_devotional->author_ids); ?><br />
                            <span style="font-weight: bold;">Acknowledgements</span>: <?php echo  $row_devotional->acknowledgements ?></p>
							<p><span style="font-weight: bold;">Submitted By</span>: <?php  echo   $this->usersModel->getUserName($row_devotional->user_id) ?></p>
							
                        </div>
                        <?php endforeach; ?>
						<?php endif; ?>
                        
                        
                        
                        
                    </div>
                    <div class="clear"></div>
                </div>
			</div>
			</form>
				<div id="dialog-form" title="Create New Series">
						
						<form id="frm2">
							<fieldset>
								<label for="name"># of Days In Series</label>
								<input type="text" name="series_count" id="series_count" maxlength="1" ui-widget-content ui-corner-all">
								
								<!-- Allow form submission with keyboard without duplicating the dialog button -->
								<input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
							</fieldset>
						</form>
					</div>
