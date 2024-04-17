 <link href="<?php echo  base_url() ?>application/assests/css/colorbox.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo  base_url() ?>application/assests/js/jquery.colorbox.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
 

 
});
function single(Id) { 
// parent.$.colorbox({ href: '<?php echo base_url();?>library/librarysingle.php?id=' + Id , iframe: true, innerWidth:"800px", innerHeight:"600px", overlayClose: true, escKey: true, onLoad: function() { $('.colorbox').hide() }, onComplete: function() { $('.colorbox').show() } });
  parent.$.colorbox({ href: '<?php echo base_url();?>library/librarysingle.php?id=' + Id , iframe: true, innerWidth:"800px", innerHeight:"600px", onLoad: function() { $('.colorbox').hide() }, onComplete: function() { $('.colorbox').show() } });
}
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
                            <div class="hd_text"><a onclick="single(<?php echo $row_devotional->id; ?> );"  style="cursor:pointer;" class="colorbox"><?php echo  date("l, F d, Y",strtotime($row_devotional->devotional_date)) ?></a></div>
                            <?php
							if(!empty($row_devotional->series_id)) : ?>
							<div class="ico"><img src="<?php echo  base_url() ?>application/assests/images/icon_1.png" width="19" height="19" alt="Series ID: <?php echo $row_devotional->series_id ?>" title="Series ID: <?php echo $row_devotional->series_id ?>" /></div>
							<?php else : ?>
							<div class="ico"></div>
							<?php endif; ?>
                            <div class="clear"></div>
                        </div>
						
                        <div class="content_con">
                        	<p><span class="boldHeading"><?php echo  html_entity_decode($row_devotional->title) ?></span><br />
							<p><span style="text-decoration:underline;"><?php echo  html_entity_decode($row_devotional->subtitle) ?></span><br />
                            <span><?php echo  (strlen($row_devotional->text) > 400)?substr(html_entity_decode($row_devotional->text),0,398)."..":html_entity_decode($row_devotional->text) ?> </span><br />
                            <hr  style="border-bottom: dotted 1px #000;background-color:transparent;"></hr>
							<?php
								$str_tags  = 'N/A';
								$all_tags = array();
								if($row_devotional->tag_ids != '')
								$all_tags[] = $this->Tags->getTagsName($row_devotional->tag_ids);
								if($row_devotional->book_ids != '')
								$all_tags[] = $this->Tags->getTagsName($row_devotional->book_ids);
								if($row_devotional->author_ids != '')
								$all_tags[] = $this->Tags->getTagsName($row_devotional->author_ids);
								if(count($all_tags) > 0){
									$str_tags = implode(",",$all_tags);
								
								}
							
							
							
							?>
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
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                    </div>
                    <div class="clear"></div>
            
			<!--END BODY MAIN-->
		