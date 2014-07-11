<!DOCTYPE html>
<html>
<head>
	<title>hangr</title>
	<link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <link rel="stylesheet" href="http://daneden.github.io/animate.css/animate.min.css" />
    <link href='http://fonts.googleapis.com/css?family=Muli' rel='stylesheet' type='text/css' />
</head>

<body>
	<header class="header" style="display:none">
		<img id="logo"src="hangr-logo.jpg" alt="logo"/>
	</header>

	<div class="content" style="display:none">
		<div class="left" style="display:none">
			<p>
				<b>hangr</b> is a social media platform that allows users to find friends who are available to hang out. Plan events and invite friends, or find available friends and instantly arrange hang outs!
			</p>
		</div>
		<div class="right" style="display:none">
			<!--<form action="index.php" method="POST">
				<input type="text" id="username" placeholder="username"/>
				<input type="password" id="password" placeholder="password"/>
				<input type="submit" id="submit" value="Log In"/>
				<input type="submit" id="signup" value="Sign Up"/>
			</form>-->
			<fb:login-button scope="public_profile" onlogin="checkLoginState();">
			</fb:login-button><br>
		</div>
	</div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>
    	$(window).load(function(){
		    $('.content').fadeIn('slow');
		    $('.header').show().addClass('animated fadeInDown');
		    $('.left').show().addClass('animated fadeInLeft');
		    $('.right').show().addClass('animated fadeInRight');
		});
    </script>
</body>

</html>
