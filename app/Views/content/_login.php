<html lang="en">

<head>

	<meta charset="utf-8">

	<title>CMS LOGIN</title>

	<link rel="stylesheet" href="<?php echo  base_url() ?>application/assests/css/login-form.css" media="screen">

	<style>body{background:url(<?php echo  base_url() ?>application/assests/images/bg.png) center;margin: 0 auto;width: 960px;padding-top: 50px}.footer{margin-top:50px;text-align:center;color:#666;font:bold 14px Arial}.footer a{color:#999;text-decoration:none}.login-form{margin: 50px auto;}</style>
<meta name="robots" content="noindex,follow" />
</head>

<body>



<div class="login-form">

	<h1>Login Form</h1>

	<form action="#">

		<input type="text" name="username" placeholder="username">
		
		<input type="password" name="password" placeholder="password">
		
		<span>
			<input type="checkbox" name="checkbox">
			<label for="checkbox">remember</label>
		</span>
		
		<input type="submit" value="log in">

	</form>

</div>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="<?php echo  base_url() ?>application/assests/js/login-form.js"></script>

</body>

</html>