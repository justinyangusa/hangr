<!DOCTYPE html>
<html>
<head>
	<title>hangr</title>
	<link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="http://daneden.github.io/animate.css/animate.min.css" />
    <link href='http://fonts.googleapis.com/css?family=Muli' rel='stylesheet' type='text/css'>
</head>

<body>
	<header class="header" style="display:none">
		<img id="logo"src="hangr-logo.jpg" alt="logo"/>
	</header>

	<div class="portal" style="display:none">
		<div class="left">
			<p style="display:none">
				<b>hangr</b> is a social media platform that connects friends who are available to hangout. 
				We act as a service to help plan various "hangouts", whether it be short term, instantaneous meetings or long term events and invitations.
			</p>
			<a href="#" style="display:none">Hang Out Now</a>
		</div>
		<div class="right">
			<p style="display:none">
				<b>hangr</b> is a social media platform that connects friends who are available to hangout. 
				We act as a service to help plan various "hangouts", whether it be short term, instantaneous meetings or long term events and invitations.
			</p>
			<a href="#" style="display:none">Hang Out Later</a>
		</div>
	</div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>
    	$(window).load(function(){
		    $('.portal').fadeIn('slow');
		    $('.header').show().addClass('animated fadeInDown');
		    $('.left p').show().addClass('animated fadeInUp');
		    $('.right p').show().addClass('animated fadeInUp');
		    $('.left a').show().addClass('animated bounceInUp');
		    $('.right a').show().addClass('animated bounceInUp');
		});
    </script>
</body>

</html>