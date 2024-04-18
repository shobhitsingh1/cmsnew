<link href="<?= base_url('/public/assests/css/style.css') ?>" rel="stylesheet" type="text/css" media="all"/>

<link rel="stylesheet" href="<?= base_url('/public/assests/css/jquery-ui.css') ?>" type="text/css"/>
<link rel="stylesheet" href="<?= base_url('/public/assests/css/Selectyze.jquery.css') ?>" type="text/css"/>
<link href="<?php echo base_url() ?>public/assests/css/jquery.multiselect_new.css" rel="stylesheet" type="text/css"
      media="all"/>
<link href="<?php echo base_url() ?>public/assests/css/jquery.multiselect.filter.css" rel="stylesheet"
      type="text/css" media="all"/>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js?"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js?"></script>

<script type="text/javascript" src="<?php echo base_url() ?>public/assests/js/jquery.multiselect_new.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>public/assests/js/jquery.multiselect.filter.js"></script>
<script type="text/javascript" src="<?= base_url('/public/assests/js/Selectyze.jquery.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('/public/assests/js/jquery-divslidetoggle.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('/public/assests/js/jquery.lavalamp.min.js') ?>"></script>

<link href="<?php echo  base_url() ?>public/assests/css/colorbox.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo  base_url() ?>public/assests/js/jquery.colorbox.js"></script>
<script type="text/javascript">
    $(document).ready(function () {


        <?php if(!empty($user_data['header_text_color'])):?>
        $(".header_nav ul li a").css({"color": "<?php echo $user_data['header_text_color'] ?>"});
        $(".logout ul li a").css({"color": "<?php echo $user_data['header_text_color'] ?>"});

        $(".hd_text a").css({"color": "<?php echo $user_data['header_text_color'] ?>"});
        $(".hd_text a:visited").css({"color": "<?php echo $user_data['header_text_color'] ?>"});
        $(".right_top_con2").css({"color": "<?php echo $user_data['header_text_color'] ?>"});
        $("#ui-id-1").css({"color": "<?php echo $user_data['header_text_color'] ?>"});
        <?php endif; ?>

        <?php if(!empty($user_data['header_bg_color'])):?>
        $(".header_top").css({"background-color": "<?php echo $user_data['header_bg_color'] ?>"});
        $(".right_top_con2").css({"background-color": "<?php echo $user_data['header_bg_color'] ?>"});
        $(".ui-dialog-titlebar.ui-widget-header.ui-corner-all.ui-helper-clearfix.ui-draggable-handle").css({"background-color": "<?php echo $user_data['header_bg_color'] ?>"});


        <?php endif; ?>

        <?php if(!empty($user_data['field_heading_text_color'])):?>
        $(".body_right h2").css({"color": "<?php echo $user_data['field_heading_text_color'] ?>"});
        $(".body_right h3").css({"color": "<?php echo $user_data['field_heading_text_color'] ?>"});
        $(".sidebar_widgit h2").css({"color": "<?php echo $user_data['field_heading_text_color'] ?>"});
        $("#create_series").css({"color": "<?php echo $user_data['field_heading_text_color'] ?>"});
        $(".admin_form_text_user").css({"color": "<?php echo $user_data['field_heading_text_color'] ?>"});
        $(".header_mid h2").css({"color": "<?php echo $user_data['field_heading_text_color'] ?>"});





        <?php endif; ?>

        <?php if(!empty($user_data['field_txt_color'])):?>
        $(".CheckBoxLabelClass").css({"color": "<?php echo $user_data['field_txt_color'] ?>"});
        $(".RadioLabelClass").css({"color": "<?php echo $user_data['field_txt_color'] ?>"});
        $(".search_con h3").css({"color": "<?php echo $user_data['field_txt_color'] ?>"});

        <?php endif; ?>



        <?php if(!empty($user_data['devotional_bg_color'])):?>
        $(".content_con").css({"background-color": "<?php echo $user_data['devotional_bg_color'] ?>"});
        <?php endif; ?>

        <?php if(!empty($user_data['devotional_text_color'])):?>
        $(".content_con p").css({"color": "<?php echo $user_data['devotional_text_color'] ?>"});
        <?php endif; ?>
        <?php if(!empty($user_data['button_text_color'])):?>
        $(".btn_1").css({"color": "<?php echo $user_data['button_text_color'] ?>"});
        $(".btn_plus").css({"color": "<?php echo $user_data['button_text_color'] ?>"});
        $(".abs").css({"color": "<?php echo $user_data['button_text_color'] ?>"});
        <?php endif; ?>
        <?php if(!empty($user_data['button_bg_color'])):?>
        $(".btn_1").css({"background-color": "<?php echo $user_data['button_bg_color'] ?>"});
        $(".btn_plus").css({"background-color": "<?php echo $user_data['button_bg_color'] ?>"});
        $(".abs").css({"background-color": "<?php echo $user_data['button_bg_color'] ?>"});
        <?php endif; ?>
        <?php if(!empty($user_data['export_text_color'])):?>
        $(".ex_btn").css({"color": "<?php echo $user_data['export_text_color'] ?>"});
        $(".btn_2").css({"color": "<?php echo $user_data['export_text_color'] ?>"});



        <?php endif; ?>
        <?php if(!empty($user_data['export_bg_color'])):?>
        $(".ex_btn").css({"background-color": "<?php echo $user_data['export_bg_color'] ?>"});
        $(".btn_2").css({"background-color": "<?php echo $user_data['export_bg_color'] ?>"});


        <?php endif; ?>
        <?php if(!empty($user_data['fonts'])):?>
        $(".content_con p").css({"font-family": "<?php echo $user_data['fonts'] ?>"});

        <?php endif; ?>
//user color Nav Mouse bg color
        <?php if(!empty($user_data['user_color'])):?>
        $("ul#menu li.backLava").css({"background-color": "<?php echo $user_data['user_color'] ?>"});

        <?php endif; ?>


    });
</script>
<script type="text/javascript">
$(document).ready(function(){
 

 
});
function single(Id) { 
// parent.$.colorbox({ href: '<?php echo base_url();?>library/librarysingle.php?id=' + Id , iframe: true, innerWidth:"800px", innerHeight:"600px", overlayClose: true, escKey: true, onLoad: function() { $('.colorbox').hide() }, onComplete: function() { $('.colorbox').show() } });
  parent.$.colorbox({ href: '<?php echo base_url();?>library/librarysingle.php?id=' + Id , iframe: true, innerWidth:"800px", innerHeight:"600px", onLoad: function() { $('.colorbox').hide() }, onComplete: function() { $('.colorbox').show() } });
}
</script>
</head>
 <?php

 use App\Models\Tags;
 use App\Models\User;

 $this->tagsModel = new Tags();
 $this->usersModel = new User();
 ?>
<body >
	<!--START CINTAINER-->
	
                	
                    <div class="body_right_mid">
                    	<?php if(count($query_devotional) > 0):
							foreach ($query_devotional as $row_devotional): ?>
                      <div class="right_top_con2">
                        	<div class="selct_hd">
							<label id="Label120"  class=""><span  class="hd_text" style="margin-left:-5px;font-size:14px;"><strong>ID:<?php echo  $row_devotional->id ?></strong></span></label>
                            </div>
							<input type="hidden" name="d_date" class="d_date_class" value="<?php echo $row_devotional->devotional_date; ?>">
                            <div class="hd_text"><a onclick="single(<?php echo $row_devotional->id; ?> );"  style="cursor:pointer;" class="colorbox"><?php echo  date("l, F d, Y",strtotime($row_devotional->devotional_date)) ?></a></div>
                            <?php
							if(!empty($row_devotional->series_id)) : ?>
							<div class="ico"><img src="<?php echo  base_url() ?>public/assests/images/icon_1.png" width="19" height="19" alt="Series ID: <?php echo $row_devotional->series_id ?>" title="Series ID: <?php echo $row_devotional->series_id ?>" /></div>
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
								$all_tags[] = $this->tagsModel->getTagsName($row_devotional->tag_ids);
								if($row_devotional->book_ids != '')
								$all_tags[] = $this->tagsModel->getTagsName($row_devotional->book_ids);
								if($row_devotional->author_ids != '')
								$all_tags[] = $this->tagsModel->getTagsName($row_devotional->author_ids);
								if(count($all_tags) > 0){
									$str_tags = implode(",",$all_tags);
								
								}
							
							
							
							?>
							<p style="padding-top:10px;word-wrap: break-word;"><span style="font-weight: bold;">Tags</span>: <?php print $this->tagsModel->getTagsName($row_devotional->tag_ids) ?><br />
							<span style="font-weight: bold;">Books</span>: <?php print $this->tagsModel->getTagsName($row_devotional->book_ids); ?><br />
                            <span style="font-weight: bold;">Authors</span>: <?php print $this->tagsModel->getTagsName($row_devotional->author_ids); ?><br />
                            <span style="font-weight: bold;">Acknowledgements</span>: <?php echo  $row_devotional->acknowledgements ?></p>
							<p><span style="font-weight: bold;">Submitted By</span>: <?php  echo  $this->usersModel->getUserName($row_devotional->user_id) ?></p>
							<input type="hidden" id="devotional_id" name="devotional_id" value="<?php echo $row_devotional->id ?>">
							</p>
							
                        </div>
                        <?php endforeach; ?>
						<?php endif; ?>
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                    </div>
                    <div class="clear"></div>
            
			<!--END BODY MAIN-->
		