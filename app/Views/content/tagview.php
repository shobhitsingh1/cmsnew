
<?php foreach ($cssFiles as $cssFile): ?>
    <link rel="stylesheet" href="<?= base_url($cssFile) ?>">
<?php endforeach; ?>
<?php foreach ($jsFiles as $jsFile): ?>
    <script src="<?= base_url($jsFile) ?>"></script>
<?php endforeach; ?>
<script type="text/javascript">

//function to set the selected value as default
function setDateYearDefault(defValue) {
    $('#date_year option').each(function () {
       // alert(defValue);
        if (($(this).attr('value')) === defValue) {
            $(this).attr('selected', 'selected');
        }
    });
}
function setQuarterDefault(defValue) {
    $('#date_quarter option').each(function () {
       // alert(defValue);
        if (($(this).attr('value')) === defValue) {
            $(this).attr('selected', 'selected');
        }
    });
}
	$(document).ready(function(){
    
    
        $('#reset').click(function(){
        $('#Search').val('');
        $('#ID').val('');
        $('#date_year').val('d');
        $('#date_quarter').val('d');
        $('#from_date').val('');
        $('#to_date').val('');
        
    
     
    
    });
    $( "#from_date" ).datepicker({
    showOn: "button",
     buttonImageOnly: true,
	changeYear: true,
	yearRange: "1999:2030",
    
    buttonImage: '<?php echo  base_url() ?>/public/assests/images/Icon-2.png'
    
     
	
	});
    //datepicker({ showOn: 'button', buttonImageOnly: true, buttonImage: 'images/ui-icon-calendar.png' })
	$( "#to_date" ).datepicker({
	changeYear: true,
	yearRange: "1999:2030",
    buttonImage: '<?php echo  base_url() ?>public/assests/images/Icon-2.png',
    buttonImageOnly: true,
    showOn: "button"
	
	});
    
    
   <?php if(ISSET($GET['date_year'])): ?>
     setDateYearDefault("<?php echo$GET['date_year'] ?>");
      <?php else: ?>
     setDateYearDefault("d");
    <?php endif; ?> 
    <?php if(ISSET($GET['date_quarter'])): ?>
     setQuarterDefault("<?php echo $GET['date_quarter'] ?>");
    <?php else: ?>
     setQuarterDefault("d");
    <?php endif; ?>
	
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
	$( "#date_year" ).combobox();
	$( "#date_quarter" ).combobox();
	
	
	
	
	
    

	

	
	$('#exportBtn').click(function(){
		var ids = new Array();
		var group1 = '';
		var group2 = '';
		$("input[name='checkAll[]']:checked").each(function(i, e) {
			ids.push($(this).val());
		});
		var label1 = $('#Label1').attr('class');
		var label2 = $('#Label2').attr('class');
		if( label1 == 'CheckBoxLabelClass LabelSelected'){
			group1 = "HT"
		}
		if( label2 == 'CheckBoxLabelClass LabelSelected'){
			group2 = "HA"
		}
		
    var ID ='';
    if($('#ID')){
        ID = $('#ID').val();
     
     }
     
     var from_date = ''; 
    if($('#from_date')){
        from_date = $('#from_date').val();
     
    }
    
    var to_date = ''
    if($('#to_date')){
        to_date = $('#to_date').val();
     
    } 
    
    var Search = '';
     if($('#Search')){
        Search = $('#Search').val();
     
     }
    
    var data = "ID="+ ID+"&Search="+Search+"&from_date="+from_date+"&to_date="+to_date;
    jQuery.download('tagview/download',data);
	
	
	
	});
	jQuery.download = function(url, data, method){
	//url and data options required
	if( url && data ){ 
		//data can be string of parameters or array/object
		data = typeof data == 'string' ? data : jQuery.param(data);
		//split params into form inputs
		var inputs = '';
		jQuery.each(data.split('&'), function(){ 
			var pair = this.split('=');
			inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
		});
		//send request
		jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
		.appendTo('body').submit().remove();
	};
};
	
 
});

function sendtoLibrary(val,id){
  	
	var form = document.createElement("form");
	form.setAttribute("method", 'post');
	form.setAttribute("action", "<?php echo base_url().'library.php' ?>");
    //alert(id);
    var tag_new = new Array();
    tag_new[0] = id;
    //tag = [id];
	 var hiddenField1 = document.createElement("input");
	 hiddenField1.setAttribute("type", "hidden");
	 hiddenField1.setAttribute("name", val);
	 hiddenField1.setAttribute("value", tag_new);
	 form.appendChild(hiddenField1);
     var Search = '';
     if($('#Search')){
        Search = $('#Search').val();
     
     }
      var hiddenField2 = document.createElement("input");
	 hiddenField2.setAttribute("type", "hidden");
	 hiddenField2.setAttribute("name", 'Search');
	 hiddenField2.setAttribute("value", Search);
	 form.appendChild(hiddenField2);
     var ID ='';
     if($('#ID')){
        ID = $('#ID').val();
     
     }
     var hiddenField5 = document.createElement("input");
	 hiddenField5.setAttribute("type", "hidden");
	 hiddenField5.setAttribute("name", 'ID');
	 hiddenField5.setAttribute("value", ID);
	 form.appendChild(hiddenField5);
    var from_date = ''; 
    if($('#from_date')){
        from_date = $('#from_date').val();
     
    }
     
      var hiddenField3 = document.createElement("input");
	 hiddenField3.setAttribute("type", "hidden");
	 hiddenField3.setAttribute("name", 'from_date');
	 hiddenField3.setAttribute("value", from_date);
	 form.appendChild(hiddenField3);
     
     var to_date = ''
    if($('#to_date')){
        to_date = $('#to_date').val();
     
    } 
     
      var hiddenField4 = document.createElement("input");
	 hiddenField4.setAttribute("type", "hidden");
	 hiddenField4.setAttribute("name", 'to_date');
	 hiddenField4.setAttribute("value", to_date);
	 form.appendChild(hiddenField4);
     var hiddenField7 = document.createElement("input");
	 hiddenField7.setAttribute("type", "hidden");
	 hiddenField7.setAttribute("name", 'View');
	 hiddenField7.setAttribute("value", 'View');
	 form.appendChild(hiddenField7);
     
	 document.body.appendChild(form);   
	 form.submit();


}


   
</script>
<style>
   .ui-corner-right{
    border-bottom-right-radius: 4px;
    height: 25px;
    margin-top: -3px;
}
</style>

<?php
use App\Models\Tags;
use App\Models\User;

$this->tagsModel = new Tags();
$this->usersModel = new User();
?>
<div class="body_main">
				<div class="main">
                	<form action="tagview.php" name="frm2" id="frm2" method="POST">
                	<div class="body_left">
                    	<div class="sidebar_widgit">
                        	<h2>SEARCH BY:</h2>
                           
                           	  <input name="Search" type="text" class="textbox" id="Search"  placeholder="Devotional Text" value="<?php echo (ISSET($GET))?$GET['Search']:'' ?>"/>
                              <input name="ID" type="text" class="textbox" id="ID"  placeholder="ID Number" value="<?php echo (ISSET($GET))?$GET['ID']:'' ?>"/> 
                              <div class="select_con" style="padding-bottom:10px;">
                               <div class='selectBox'>
                                    <select id="date_year" name="date_year"  placeholder="Year">
                                        <option value="d" disabled="disabled">Date Year</option>
										
										
										<?php for($i=1994; $i <= 2030; $i++): ?>
										
											
											<option  value="<?php echo $i ?>"><?php echo $i ?></option>
										
									<?php endfor;  ?>
									
								</select> 
                           	  </div>
                              </div>
                              <div class="select_con" style="padding-bottom:10px;">
                               <div class='selectBox'>
                                    <div class='selectBox'>
                                    
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
                                </div>
                              
                              <div class="select_con" style="padding-bottom:10px;">
                              	<div class="date_picker left">
                                	
                                	<div class="date">
									<input type="text" id="from_date" value="<?php echo (ISSET($GET))?$GET['from_date']:'' ?>" name="from_date" class="textbox" placeholder="From Date" style="float: left; width: 67px;"></div>
                                </div>
                                <div class="date_picker right">
                                	<div class="date">
									<input type="text" id="to_date" value="<?php echo (ISSET($GET))?$GET['to_date']:'' ?>" name="to_date" class="textbox" placeholder="To Date" style="float: left; width: 67px;"></div>
                                </div>
                                <div class="clear"></div>
                              </div>
                              
                             
                              
                                
                           
                        </div>
                       
                       
                      
                        
                         <div class="select_con">
                              	<div class="sub_btn_1 left"><input name="View" type="submit" value="View" class="btn_1" /></div>
                                <div class="sub_btn_1 right"><input name="reset" type="button" id="reset" value="Reset" class="btn_2" /></div>
                                <div class="clear"></div>
                              </div>
                        <div class="clear"></div>
                    </div>
					 </form>
                    <?php 
                    $tags_array_tmp = array();
                     $author_array_tmp = array();
                     $book_array_tmp = array();
                    if(ISSET($GET)){
                   // print_r($GET);
                    if(($GET['Search'] == '')&&($GET['ID'] == '')&&(!ISSET($GET['date_year']))&&(!ISSET($GET['date_quarter']))&&($GET['from_date'] == '')&&($GET['to_date'] == '')){
                        $tags_array = array();
                        $book_array = array(); 
                        $author_array = array();
                        
                        if(ISSET($query_devotional)){
                            if(count($query_devotional) > 1){
                                 
                                  foreach ($query_devotional as $row_devotional){
                                       
                                        if($row_devotional->type == 'Tags'){
                                            $tags_array_tmp[$row_devotional->id] = $row_devotional->title;
                                        
                                        }
                                        if($row_devotional->type == 'author'){
                                              $author_array_tmp[$row_devotional->id] = $row_devotional->title;
                                        
                                        }
                                        if($row_devotional->type == 'books'){
                                              $book_array_tmp[$row_devotional->id] = $row_devotional->title;
                                        
                                        }
                                    
                                    }
                                
                                
                            }
                        }
                        
                        
                        ?>
                        <div class="body_right">
                         <?php if(count($tags_array_tmp) > 0):  ?>
                            <h3>Tags</h3>
                            <div class="scroll_bg" style="height:188px;">
                                <div class="scroll-pane">
                                    <div class="scroll-padding">
                                        <div class="sc_con">
                                            <ul>
                                            <?php if(count($tags_array_tmp) > 0):  ?>
                                             
                                            
                                            <?php foreach($tags_array_tmp as $k => $v) : ?>
                                                <?php if($k != ''): ?>
                                                <li onClick = "sendtoLibrary('Tags[]',<?php echo $k ?>);" class="abs" style="cursor:pointer;"><?php echo $v ?> (<?php echo $this->tagsModel->getTagsCounts($k) ?>)</li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                            </ul>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <?php endif; ?>
                            <?php if(count($book_array_tmp) > 0):  ?>
                            <h3>Books</h3>
                            <div class="scroll_bg" style="height:188px;">
                                <div class="scroll-pane">
                                    <div class="scroll-padding">
                                        <div class="sc_con">
                                            <ul>
                                                <?php if(count($book_array_tmp) > 0): ?>
                                            
                                            
                                            <?php foreach($book_array_tmp as $k => $v) : ?>
                                                <?php if($k != ''): ?>
                                                <li style="width:281px;cursor:pointer;" onClick = "sendtoLibrary('books[]',<?php echo $k ?>);" class="abs" ><?php echo $v ?> (<?php echo $this->tagsModel->getBookCounts($k) ?>)</li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                            </ul>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <?php endif; ?>
                             <?php if(count($author_array_tmp) > 0):  ?>
                            <h3>Authors</h3>
                            <div class="scroll_bg" style="height:188px;">
                                <div class="scroll-pane">
                                    <div class="scroll-padding">
                                        <div class="sc_con">
                                            <ul>
                                                 <?php if(count($author_array_tmp) > 0): ?>
                                               
                                            
                                                <?php foreach($author_array_tmp as $k => $v) : ?>
                                                <?php if($k != ''): ?>
                                                <li  onClick = "sendtoLibrary('author[]',<?php echo $k ?>);" class="abs" style="cursor:pointer;"><?php echo $v ?> (<?php echo $this->tagsModel->getAuthorCounts($k) ?>)</li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                            </ul>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <?php endif; ?>
                         
                         
                         
                    </div>
                    <?php }else{ ?>
                  
                    <?php 
                   
                    
                        $tags_array = array();
                        $book_array = array(); 
                        $author_array = array();
                        
                        if(ISSET($query_devotional)){
                        if(count($query_devotional) > 0){
                                foreach ($query_devotional as $row_devotional){
                                    $tags_array_tmp = array();
                                    $tags_array_tmp = explode(",",$row_devotional->tag_ids);
                                    if(count($tags_array_tmp) > 0){
                                        foreach($tags_array_tmp as $k => $v){
                                            $tags_array[] = $v;
                                        
                                        
                                        }
                                    
                                    }
                                    
                                    $book_array_tmp = array();
                                    $book_array_tmp = explode(",",$row_devotional->book_ids);
                                    if(count($book_array_tmp) > 0){
                                        foreach($book_array_tmp as $k => $v){
                                            $book_array[] = $v;
                                        
                                        
                                        }
                                    
                                    }
                                    
                                    $author_array_tmp = array();
                                    $author_array_tmp = explode(",",$row_devotional->author_ids);
                                    if(count($author_array_tmp) > 0){
                                        foreach($author_array_tmp as $k => $v){
                                            $author_array[] = $v;
                                        
                                        
                                        }
                                    
                                    }
                                
                                
                                }
                        }
                        }
                        
                        
                        ?>
                        <div class="body_right">
                         <?php if(count($tags_array) > 0):  ?>
                            <h3>Tags</h3>
                            <div class="scroll_bg" style="height:188px;">
                                <div class="scroll-pane">
                                    <div class="scroll-padding">
                                        <div class="sc_con">
                                            <ul>
                                            <?php if(count($tags_array) > 0): 
                                               
                                                $tags_unique = array_count_values($tags_array); 
                                                arsort( $tags_unique);
                                                 ?>
                                            <?php foreach($tags_unique as $k => $v) : ?>
                                                <?php if($k != ''): ?>
                                                <li onClick = "sendtoLibrary('Tags[]',<?php echo $k ?>);" class="abs" style="cursor:pointer;"><?php echo $this->tagsModel->getTagNameById($k) ?> (<?php echo $v ?>)</li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                            </ul>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <?php endif; ?>
                            <?php if(count($book_array) > 0):  ?>
                            <h3>Books</h3>
                            <div class="scroll_bg" style="height:188px;">
                                <div class="scroll-pane">
                                    <div class="scroll-padding">
                                        <div class="sc_con">
                                            <ul>
                                                <?php if(count($book_array) > 0): 
                                                $book_unique = array_count_values($book_array);
                                                arsort( $book_unique);

                                                ?>
                                            
                                            <?php foreach($book_unique as $k => $v) : ?>
                                                <?php if($k != ''): ?>
                                                <li style="width:281px;" onClick = "sendtoLibrary('books[]',<?php echo $k ?>);" class="abs" style="cursor:pointer;"><?php echo $this->tagsModel->getTagNameById($k) ?> (<?php echo $v ?>)</li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                            </ul>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <?php endif; ?>
                             <?php if(count($author_array) > 0):  ?>
                            <h3>Authors</h3>
                            <div class="scroll_bg" style="height:188px;">
                                <div class="scroll-pane">
                                    <div class="scroll-padding">
                                        <div class="sc_con">
                                            <ul>
                                                 <?php if(count($author_array) > 0): 
                                                $author_unique = array_count_values($author_array);
                                                arsort($author_unique);
                                                ?>
                                            
                                                <?php foreach($author_unique as $k => $v) : ?>
                                                <?php if($k != ''): ?>
                                                <li  onClick = "sendtoLibrary('author[]',<?php echo $k ?>);" class="abs" style="cursor:pointer;"><?php echo $this->tagsModel->getTagNameById($k) ?> (<?php echo $v ?>)</li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                            </ul>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <?php endif; ?>
                         
                         
                         
                    </div>
                    <?php }} ?>
                    
                    <div class="clear"></div>
                </div>
			</div>