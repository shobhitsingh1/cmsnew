<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
<?php use App\Models\Tags;
use App\Models\User;

foreach ($cssFiles as $cssFile): ?>
    <link rel="stylesheet" href="<?= base_url($cssFile) ?>">
<?php endforeach; ?>
<?php foreach ($jsFiles as $jsFile): ?>
    <script src="<?= base_url($jsFile) ?>"></script>
<?php endforeach; ?>

<script type="text/javascript">

// Numeric only control handler
jQuery.fn.ForceNumericOnly =
function()
{
    return this.each(function()
    {
        $(this).keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
            return (
                key == 8 || 
                key == 9 ||
                key == 13 ||
                key == 46 ||
                key == 110 ||
                key == 190 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};
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
       // $('#ID').ForceNumericOnly();
        $('#reset').click(function(){
       
        $('#Search').val('');
        $('#ack_search').val('');
        $('#ID').val('');
        $('#date_year').val('');
        $('#date_quarter').val('');
        $('#from_date').val('');
        $('#to_date').val('');
        $('input:checkbox').attr('checked',false);
        //$('.custom-combobox-input').val('');
        $('.CheckBoxLabelClass').removeClass('LabelSelected');
        $(".multiselect").multiselect('uncheckAll');
    
    });
    
    $('#filter_counter_span').html($('#filter_counter').val());
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
	$(".multiselect").multiselect({selectedText:"# Selected Items"},{create: function(){ 
    $(this).next().width(220);
    $(this).next().height(25);
    }}).multiselectfilter();
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
	
	$( "#from_date" ).datepicker({
	changeYear: true,
	yearRange: "1999:2030",
    buttonImage: '<?php echo  base_url() ?>public/assests/images/Icon-2.png',
     buttonImageOnly: true,
     showOn: "button",
	
	});
	$( "#to_date" ).datepicker({
	changeYear: true,
	yearRange: "1999:2030",
    buttonImage: '<?php echo  base_url() ?>public/assests/images/Icon-2.png',
     buttonImageOnly: true,
     showOn: "button"
	
	});
	
	$('.btn_2').click(function(){
    
       // $(".multiselect").multiselect("uncheckall");
        $(".multiselect").find('option').removeAttr("selected")
    
    });
	 });
	 

	 
$(document).ready(function() {

//var checked = [];
/*$("input[name='counter[]']").click(function(){
var tag_counter = 0;
var counter_checkbox= 0;
    var counter_checkbox = $("input[name='counter[]']:checked").length;
    if(counter_checkbox > 0)
    {
         
    }

});

$("input[name='tag_counter[]']").click(function(){
var tag_counter = 0;
var counter_checkbox= 0;
    var tag_checkbox = $("input[name='tag_counter[]']:checked").length;
    if(tag_checkbox > 0)
    {
         var counter_checkbox = $("input[name='counter[]']:checked").length;
        alert(counter_checkbox);
    
    }
       

});*/

$('.tagsCount').click(function(){
   // alert($('input.keywordCouner').filter(':checked').length);
    if($('input.keywordCouner').filter(':checked').length > 0){
         $('input.tagsCount').removeAttr('checked');
        alert("Please select one count type"); 
        }
    
});

$('.keywordCouner').click(function(){
    if($('input.tagsCount').filter(':checked').length > 0){
        $('input.keywordCouner').removeAttr('checked');
        alert("Please select one count type "); 
        }
    
});    
$("#selecctall").click(function(){
      var checked_status = this.checked;
      $("input[name='checkAll[]']").each(function(){
        this.checked = checked_status;
      });
    });
	

	
	$('#exportBtn').click(function(){
		var ids = new Array();
		var group1 = '';
		var group2 = '';
		var group3 = '';
		$("input[name='checkAll[]']:checked").each(function(i, e) {
			ids.push($(this).val());
		});
		var label1 = $('#Label1').attr('class');
		var label2 = $('#Label2').attr('class');
		var label3 = $('#Label3').attr('class');
		if( label1 == 'CheckBoxLabelClass LabelSelected'){
			group1 = "HT"
		}
		if( label2 == 'CheckBoxLabelClass LabelSelected'){
			group2 = "HA"
		}
		if( label3 == 'CheckBoxLabelClass LabelSelected'){
			group3 = "HID"
		}
		var sortby = $('#sortby').val();
        var sortedBy = $('input:radio[name=sortedBy]:checked').val();
		var data = "checkBoxList="+ ids.join()+"&group1="+group1+"&group2="+group2+"&group3="+group3+"&sortby="+sortby+"&sortedBy="+sortedBy;
		jQuery.download('library/download',data);
	
	
	
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
	
 $(".colorbox2").colorbox({iframe:true,innerWidth:"800px", innerHeight:"600px"});   
 
 $('#sortby').change(function(){
    var sortby = $('#sortby').val();
    var sortedBy = $('input:radio[name=sortedBy]:checked').val();
    $('#frm2').append('<input type="hidden" name="sortby" value="'+sortby+'" />');  
    $('#frm2').append('<input type="hidden" name="sortedBy" value="'+sortedBy+'" />');
    $('#frm2').append('<input type="hidden" name="View" value="View" />');    
   /* $('#frm2').bind('submit',function(){
       
    });*/
    $('#frm2').submit()
    //alert("sortby"+sortby+"sortedBy"+sortedBy);
 
 
 });

 $('.rf').change(function(){
    var sortby = $('#sortby').val();
    var sortedBy = $('input:radio[name=sortedBy]:checked').val();
    $('#frm2').append('<input type="hidden" name="sortby" value="'+sortby+'" />');  
    $('#frm2').append('<input type="hidden" name="sortedBy" value="'+sortedBy+'" />');
    $('#frm2').append('<input type="hidden" name="View" value="View" />');    
   /* $('#frm2').bind('submit',function(){
       
    });*/
    $('#frm2').submit()
    //alert("sortby"+sortby+"sortedBy"+sortedBy);
 
 
 });
});

</script>
<style>
.ui-corner-all, .ui-corner-top, .ui-corner-right, .ui-corner-tr
{
 border-top-right-radius: 4px;
    margin-top: -4px;
} 

</style>
<?php
$this->tagsModel = new Tags();
$this->usersModel = new User();
?>
			<div class="body_main">
				<div class="main">
					 <form action="<?= base_url('library.php'); ?>" name="frm2" id="frm2" method="POST">
                	<div class="body_left">
                        
                    	<div class="sidebar_widgit">
                        	<h2>SEARCH BY:</h2>
                           
                           	  <input name="Search" type="text" class="textbox" id="Search"  placeholder="Devotional Text" value="<?php echo (ISSET($GET))?$GET['Search']:'' ?>"/>
                              <input name="ack_search" type="text" class="textbox" id="ack_search"  placeholder="Acknowledgment Text" value="<?php echo (ISSET($GET))?$GET['ack_search']:'' ?>"/>
                              <input name="ID" type="text" class="textbox" id="ID"  placeholder="ID Number" value="<?php echo (ISSET($GET))?$GET['ID']:'' ?>"/> 
                              <div class="select_con" style="padding-bottom:10px;">
                               <div class='selectBox'>
                                    <select id="date_year" name="date_year"  placeholder="Year">
                                        <option value="d" disabled="disabled">Year</option>
										
										
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
                        <div class="sidebar_widgit">
                        	<h2>KEYWORD COUNT</h2>
                            		<div class="count">
                                    <?php
                                    $count_var = array();
                                    if(ISSET( $GET['counter'])){
                                        
                                        $count_var = (array)$GET['counter'];
                                    }
                                   // print_r($count_var);
                                    ?>
                            		<input id="CheckBox25" name="counter[]" type="checkbox" value="2" class="CheckBoxClass keywordCouner" <?php echo (in_array('2',$count_var)==true)?"checked='checked'":'' ?>/>
									<label id="Label25" for="CheckBox25" class="CheckBoxLabelClass <?php echo (in_array('2',$count_var)==true)?"LabelSelected":'' ?>">2</label>
                                    </div>
                                    <div class="count">
                                    <input id="CheckBox26" name="counter[]" type="checkbox" value="3" class="CheckBoxClass keywordCouner" <?php echo (in_array('3',$count_var)==true)?"checked='checked'":'' ?>  />
									<label id="Label26" for="CheckBox26" class="CheckBoxLabelClass <?php echo (in_array('3',$count_var)==true)?"LabelSelected":'' ?>">3</label>
                                    </div>
                                   <div class="count">
                                    <input id="CheckBox27" name="counter[]" type="checkbox" value="4" class="CheckBoxClass keywordCouner" <?php echo (in_array('4',$count_var)==true)?"checked='checked'":'' ?> />
									<label id="Label27" for="CheckBox27" class="CheckBoxLabelClass <?php echo (in_array('4',$count_var)==true)?"LabelSelected":'' ?>" >4</label>
                                    </div>
                                   <div class="count">
                                    <input id="CheckBox28" name="counter[]" type="checkbox" value="5" class="CheckBoxClass keywordCouner" <?php echo (in_array('5',$count_var)==true)?"checked='checked'":'' ?> />
									<label id="Label28" for="CheckBox28" class="CheckBoxLabelClass <?php echo (in_array('5',$count_var)==true)?"LabelSelected":'' ?>" >5+</label>
                                    </div>
                                   
                                   <div class="clear"></div> 
                                    
                                    
                        </div>
                        <?php 
                         $Tags_array = array();
                        if(ISSET($GET['Tags'])){
                            $Tags_array = (array)$GET['Tags'];
                         }
                                                                
                                    //print_r($Tags_array);
                                    ?>
                        <div class="sidebar_widgit">
                        	<h2>Tags</h2>
                            <div id="div_tag_name">
									<select size="5" class="multiselect"   id="tt" name="Tags[]" title="Tags" multiple="multiple" >
									<?php //print_r($query_tags); 
									$tags = array();
									$i = 1;
                                    
									if (count($query_tags) > 0):
										foreach ($query_tags as $row_tags):
                                           
											$tags[$row_tags->id] =  $row_tags->title;
                                             $sel = '';
                                            if(in_array($row_tags->id,$Tags_array) == true){
                                                $sel = "selected='selected'";
                                            }
										?>
									<option value="<?php echo $row_tags->id; ?>" <?php echo $sel?>><?php echo (strlen($row_tags->title) > 50)?substr($row_tags->title,0,50)."..":$row_tags->title; ?></option>
										<?php endforeach; ?>
									<?php endif; ?>	
									</select>
								</div>
                            
                            
                        </div>
                         <?php 
                         $books_array = array();
                        if(ISSET($GET['books'])){
                            $books_array = (array)$GET['books'];
                         }
                                                                
                                    //print_r($Tags_array);
                                    ?>
                        <div class="sidebar_widgit">
                        	<h2>Books</h2>
                            <div id="div_book_name">
									<select size="5"  class="multiselect"  id="bt" name="books[]" title="Books" multiple="multiple" >
									<?php //print_r($query_tags); 
									
									if (count($query_books) > 0):
										foreach ($query_books as $row_tags):
											$tags[$row_tags->id] =  $row_tags->title;
                                             $sel = '';
                                            if(in_array($row_tags->id,$books_array) == true){
                                                $sel = "selected='selected'";
                                            }
										?>
									<option value="<?php echo $row_tags->id; ?>" <?php echo  $sel ?>><?php echo (strlen($row_tags->title) > 50)?substr($row_tags->title,0,50)."..":$row_tags->title; ?></option>
										<?php endforeach; ?>
									<?php endif; ?>	
									</select>
								
								
								
									</div>
                            
                        </div>
                        <?php
                        $author_array = array();
                        if(ISSET($GET['author'])){
                            $author_array = (array)$GET['author'];
                         } ?>
                        <div class="sidebar_widgit">
                        	<h2>Authors</h2>
                            <div id="div_author_name">
								<select size="5"  class="multiselect"  id="at" name="author[]" title="Author" multiple="multiple" >
									<?php //print_r($query_tags); 
									
									if (count($query_author) > 0):
										foreach ($query_author as $row_tags):
											$tags[$row_tags->id] =  $row_tags->title;
                                            $sel = '';
                                            if(in_array($row_tags->id,$author_array) == true){
                                                $sel = "selected='selected'";
                                            }
										?>
									<option value="<?php echo $row_tags->id; ?>" <?php echo $sel ?>><?php echo (strlen($row_tags->title) > 50)?substr($row_tags->title,0,50)."..":$row_tags->title; ?></option>
										<?php endforeach; ?>
									<?php endif; ?>	
									</select>
								
									</div>
                            
                        </div>
                        <div class="sidebar_widgit">
                        	<h2>TAG COUNT</h2>
                            		<div class="count">
                                    <?php
                                    $count_tag = array();
                                    if(ISSET( $GET['tag_counter'])){
                                        
                                        $count_tag = (array)$GET['tag_counter'];
                                    }
                                   // print_r($count_var);
                                    ?>
                            		<input id="CheckBox29" name="tag_counter[]" type="checkbox" value="2" class="CheckBoxClass tagsCount" <?php echo (in_array('2',$count_tag)==true)?"checked='checked'":'' ?>/>
									<label id="Label29" for="CheckBox29" class="CheckBoxLabelClass <?php echo (in_array('2',$count_tag)==true)?"LabelSelected":'' ?>">2</label>
                                    </div>
                                    <div class="count">
                                    <input id="CheckBox30" name="tag_counter[]" type="checkbox" value="3" class="CheckBoxClass tagsCount" <?php echo (in_array('3',$count_tag)==true)?"checked='checked'":'' ?>  />
									<label id="Label30" for="CheckBox30" class="CheckBoxLabelClass <?php echo (in_array('3',$count_tag)==true)?"LabelSelected":'' ?>">3</label>
                                    </div>
                                   <div class="count">
                                    <input id="CheckBox31" name="tag_counter[]" type="checkbox" value="4" class="CheckBoxClass tagsCount" <?php echo (in_array('4',$count_tag)==true)?"checked='checked'":'' ?> />
									<label id="Label31" for="CheckBox31" class="CheckBoxLabelClass <?php echo (in_array('4',$count_tag)==true)?"LabelSelected":'' ?>" >4</label>
                                    </div>
                                   <div class="count">
                                    <input id="CheckBox32" name="tag_counter[]" type="checkbox" value="5" class="CheckBoxClass tagsCount" <?php echo (in_array('5',$count_tag)==true)?"checked='checked'":'' ?> />
									<label id="Label32" for="CheckBox32" class="CheckBoxLabelClass <?php echo (in_array('5',$count_tag)==true)?"LabelSelected":'' ?>" >5+</label>
                                    </div>
                                   
                                   <div class="clear"></div> 
                                    
                                    
                        </div>
                        
                         <div class="select_con">
                              	<div class="sub_btn_1 left"><input name="View"  id="View" type="submit" value="View" class="btn_1" /></div>
                                <div class="sub_btn_1 right"><input name="reset" id="reset" type="button" value="Reset" class="btn_2" /></div>
                                <div class="clear"></div>
                              </div>
                        <div class="clear"></div>
                    </div>
					 </form>
                    <div class="body_right">
					<?php if(ISSET($query_devotional)) :?>
                    	<div class="right_top_con" style="height:13px;">
                        	<input id="selecctall" type="checkbox" class="CheckBoxClass" />
							<label id="Label19" for="selecctall" class="CheckBoxLabelClass" style=" float: left;width: 69px;"><span>Select All</span></label>
                            <span class="hd_text_new" style="float:right;padding-right:5px;margin-top:-2.5px;">
                            <select name="sortby" id="sortby">
                            <?php 
                                $cSelected = '';
                                $rChecked = '';
                                $dChecked = "checked='checked'";
                            if(ISSET($GET['sortby'])) :
                                $cSelected = $GET['sortby'];
                                $rChecked = $GET['sortedBy'];
                                $dChecked = '';
                            endif;
                            $rs = '';
                            if(($cSelected != 'id') && ($cSelected != 'devotional_date')){
                                
                               $rs = "selected='selected'";     
                            
                            
                            }
                            ?>
                             
                            <option value="id" <?php echo ($cSelected == 'id')?"selected='selected'":'' ?> <?php echo $rs ?>>ID</option>
                            <option value="devotional_date" <?php echo ($cSelected == 'devotional_date')?"selected='selected'":'' ?>>DEVOTIONAL DATE</option>
                           
                           </select>
                           <input type="radio" name="sortedBy" class="rf" value="asc" <?php echo ($rChecked == 'asc')?"checked='checked'":'' ?> <?php echo $dChecked ?> > ASC
                           <input type="radio" name="sortedBy" class="rf" value="desc" <?php echo ($rChecked == 'desc')?"checked='checked'":'' ?>> DESC
                            </span>
                        </div>
						
						<?php 
                        $filter_counter = 0;
                        $counter_found = 0;
                        if($query_devotional > 0):
							foreach ($query_devotional as $row_devotional):
                                $vs = '';
                              
                               if((count($count_var) > 0) && (count($count_tag) == 0)){
                                     $counter_found =  $this->tagsModel->getStringCount($parameter, $row_devotional->text);
                                    if(in_array('5',$count_var) == true){
                                    
                                        $count_var = array_merge($count_var,array(6,7,8,9,10,11,12,13,14,15,16,17,18,19,20));
                                    
                                    }
                                    if(in_array($counter_found,$count_var) == false){
                                        $vs = "style='display:none;'";
                                       
                                    
                                    }else{
                                         $filter_counter++;
                                    
                                    }
                                }else if((count($count_var) == 0) && (count($count_tag) > 0)){
                                    
                                   $counter_found =  $this->tagsModel->getTagsCount($GET, $row_devotional);
                                   if(in_array('5',$count_tag) == true){
                                    
                                        $count_tag = array_merge($count_tag,array(6,7,8,9,10,11,12,13,14,15,16,17,18,19,20));
                                    
                                    }
                                    if(in_array($counter_found,$count_tag) == false){
                                        $vs = "style='display:none;'";
                                       
                                    
                                    }else{
                                         $filter_counter++;
                                    
                                    }
                                
                                }else if((count($count_var) == 0) && (count($count_tag) == 0)){
                                
                                    $filter_counter++;
                                
                                
                                }
                                
                              
                                
                                
                              //  print_r($GET);
                               // print "<pre>";
                               // print_r($row_devotional);
                                
                                   /* foreach($count_var as $counter_key => $counter_val){
                                   
                                        if($counter_val ==  $counter_found){
                                            
                                        
                                        
                                        }
                                    
                                    }
                                    
                                }*/
                               
                            
                            
                            ?>
                        <div class="right_top_con2" <?php echo $vs ?>>
                        	<div class="selct_hd">
							
							
                        	<input  id="CheckBox<?php echo $row_devotional->id ?>"  type="checkbox" class="" name="checkAll[]" value="<?php echo $row_devotional->id ?>" />
							<label id="Label1<?php echo $row_devotional->id ?>" for="CheckBox<?php echo $row_devotional->id ?>" class=""><span style="color:#FFFFFF;"><strong>ID: <?php echo  $row_devotional->id ?></strong></span></label>
                            </div>
							<?php if($row_devotional->series_id > 0) : ?>
                            <div class="hd_text"><a href="<?php echo base_url();?>library/libraryseries.php?id=<?php echo $row_devotional->series_id ?>"  class="colorbox2"><?php echo  date("l, F d, Y",strtotime($row_devotional->devotional_date)) ?></a></div>
                            <?php else: ?>
							 <div class="hd_text"><a href="<?php echo base_url();?>library/librarysingle.php?id=<?php echo $row_devotional->id ?>"  class="colorbox2"><?php echo  date("l, F d, Y",strtotime($row_devotional->devotional_date)) ?></a></div>
							<?php endif; ?>	
                            
                            <?php
							if(!empty($row_devotional->series_id)) : ?>
							<div class="ico"><img src="<?php echo  base_url() ?>public/assests/images/icon_1.png" width="19" height="19" align="absmiddle" alt="Series ID: <?php echo $row_devotional->series_id ?>" title="Series ID: <?php echo $row_devotional->series_id ?>" />&nbsp;&nbsp;&nbsp;<?php echo ($counter_found>0)?$counter_found:'' ?></div>
							<?php else : ?>
							<div class="ico"><?php echo ($counter_found>0)?$counter_found:'' ?></div>
							<?php endif; ?>
                            
							
							
							
							<div class="clear"></div>
                        </div>
                        <div class="content_con" <?php echo $vs ?>>
                        	<p ><span class="boldHeading"><?php echo  html_entity_decode($row_devotional->title) ?></span><br />
							<p><span style="text-decoration:underline;"><?php echo  html_entity_decode($row_devotional->subtitle) ?></span><br />
                            <span><?php echo  html_entity_decode($row_devotional->text); ?> </span><br />
                            <hr  style="border-bottom: dotted 1px #000;background-color:transparent;padding-top:10px;"></hr>
							<?php
								$all_tags = array();
								$str_tags = 'N/A';
								if($row_devotional->tag_ids != '')
								$all_tags['tags'] = $this->tagsModel->getTagsName($row_devotional->tag_ids);
								if($row_devotional->book_ids != '')
								$all_tags['books'] = $this->tagsModel->getTagsName($row_devotional->book_ids);
								if($row_devotional->author_ids != '')
								$all_tags['authors'] = $this->tagsModel->getTagsName($row_devotional->author_ids);
                                
                               // echo count($all_tags);
								if(count($all_tags) > 0){
									$str_tags = implode(",",$all_tags);
								
								}
								
							
							
							
							?>
							<p style="padding-top:10px;word-wrap: break-word;"><span style="font-weight: bold;">Tags</span>: <?php print $this->tagsModel->getTagsName($row_devotional->tag_ids) ?><br />
							<span style="font-weight: bold;">Books</span>: <?php print $this->tagsModel->getTagsName($row_devotional->book_ids); ?><br />
                            <span style="font-weight: bold;">Authors</span>: <?php print $this->tagsModel->getTagsName($row_devotional->author_ids); ?><br />
                            <span style="font-weight: bold;">Acknowledgements</span>: <?php echo  $row_devotional->acknowledgements ?></p>
							<p><span style="font-weight: bold;">Submitted By</span>: <?php  echo  $this->usersModel->getUserName($row_devotional->user_id) ?></p>
							
                        </div>
                        <?php endforeach; ?>
						<?php endif; ?>
						
                        
                        
                        
                        <div class="right_top_con" style="height:13px;">
                        	<input type="hidden" id="filter_counter" value="<?php echo $filter_counter; ?>">
                            <span class="hd_text" style="float:right;"><?php //echo $this->pagination->create_links(); ?></span>
                        </div>
                        
                        <?php endif; ?>
                    </div>
                    
                    <div class="clear"></div>
                </div>
			</div>