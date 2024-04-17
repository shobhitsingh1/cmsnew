<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CMS-Login</title>
<link rel="stylesheet" href="<?php echo  base_url() ?>application/assests/css/global.css" type="text/css" />
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="<?php echo  base_url() ?>application/assests/js/jquery.validate.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		
		
		$("#frmLogin").validate();	
		});
</script>		

</head>

<body>
<!--Top Container -->
    <div id="login_container">
    	<div class="login_wrapper">
        	<div class="clear">
				
            	
				
                <div class="login_area" style="float:left;">
						<div class="error_div"><?php echo (ISSET($error))?$error:''; ?></div>
                	<div class="clear">
                    	<form name="frmLogin" id="frmLogin" method="post" action="index.php" autocomplete="off">                        	
                        	<div class="login_improw">
                                <input name="userid" id="userid" type="text" class="con_imptext2" placeholder="Username" maxlength="20" required/>
            	 		 	</div>
                            <div class="clear"><img src="<?php echo  base_url() ?>application/assests/images/spacer.gif" width="1" height="22" alt="" /></div>
                            <div class="login_improw">
                                <input name="usrpwd" id="usrpwd" type="password" class="con_imptext2" value="" placeholder="Password" required maxlength="20" autocomplete="off" />
                                
            	 		 	</div>
                            <div class="clear"><img src="<?php echo  base_url() ?>application/assests/images/spacer.gif" width="1" height="22" alt="" /></div>
                            <div class="clear" align="center">
                            	<input type="hidden" name="postMe" id="postMe" value="Y" />  
                                <input type="submit" name="cmdLogin" id="cmdLogin" value="" class="login" />                            	
                            </div>
                            <h2></h2>                        
                		</form>
                	</div>
            	</div>
            </div>
      </div>
    </div>
<!--Top Container -->
<div id="bottom_container">

    <div class="wrapper">

        <div class="clear"><img src="<?php echo  base_url() ?>application/assests/images/footer_logo.png" alt="" /></div>

        <div class="clear">

            <p>1.<span style="display:none;">_</span>866.249.6885 • Monday–Friday 8 a.m. – 5 p.m. EST </p>

        </div>

    </div>

</div>
</body>
</html>