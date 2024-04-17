<html lang="en">

<head>

	<meta charset="utf-8">

	<title>CMS LOGIN</title>
    <link rel="stylesheet" href="<?= base_url('public/assests/css/login-form.css') ?>" media="screen">

    <style>
        body {
            background: url(<?= base_url('public/assests/images/bg.png') ?>) center;
            margin: 0 auto;
            width: 960px;
            padding-top: 139px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font: bold 14px Arial;
        }

        .footer a {
            color: #999;
            text-decoration: none;
        }

        .login-form {
            margin: 50px auto;
        }
    </style>
    <meta name="robots" content="noindex,follow" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script src="<?= base_url('/public/assests/js/jquery.validate.js') ?>"></script>
    <script src="<?= base_url('/public/assests/js/login-form.js') ?>"></script>

<script type="text/javascript">
	$(document).ready(function(){
		
		
		$("#frmLogin").validate();	
		});
</script>
</head>

<body >



<div class="login-form">

	<h1>CMS Login Form</h1>
    <div class="error"><?php echo (ISSET($error))?$error:''; ?></div>
	<form name="frmLogin" id="frmLogin" method="post" action="<?= base_url('checklogin')?>" autocomplete="off">

		<input type="text" id="userid" name="userid" placeholder="username" required maxlength="20" required>
		
		<input type="password" name="usrpwd" id="usrpwd" placeholder="password" required maxlength="20" required>
		<br/>
		<span style="margin-top:20px;">
			<input type="checkbox" name="checkbox">
			<label for="checkbox"> Remember Me</label>
		</span>
		
		<input type="submit" name ='cmdLogin' value="log in">

	</form>

</div>



		


</body>

</html>