<?php
    session_start();

    if ($_SESSION['login'] != true)
    {
	    echo "<script>window.location.replace('index.php');</script>";
    }

	if (isset($_POST['save'])){
		$postedData = $_POST["myField"];
		$tempData = str_replace("\\", "",$postedData);
		$cleanData = json_decode($tempData);

		//var_dump($cleanData);
		$serializedArray = serialize($cleanData);

		// Connect to MySQL database
		$con = mysqli_connect("fdb4.biz.nf","1238336_lhs","lhsgrad13","1238336_lhs");
	    if (mysqli_connect_errno($con)){
	        echo "Failed: " . mysqli_connect_error();
	    }

	    // get username
	    $user = $_SESSION['user'];

	    mysqli_query($con, "DELETE FROM calendar WHERE name='$user'");
	    mysqli_query($con, "INSERT INTO calendar (name, times) VALUES ('$user','$serializedArray')");

	    $result = mysqli_query($con, "SELECT times FROM calendar WHERE name='$user'");
		$row = mysqli_fetch_row($result);
	    $selectData = json_encode(unserialize($row[0]));
	}
	else if (isset($_POST['acceptreq']))
	{
		// Connect to MySQL database
		$con = mysqli_connect("fdb4.biz.nf","1238336_lhs","lhsgrad13","1238336_lhs");
	    if (mysqli_connect_errno($con)){
	        echo "Failed: " . mysqli_connect_error();
	    }

	    $resply=0;

	    if ($_POST['accept']=='yes')
	    	$resply = 1;
	    else if ($_POST['accept']=='no')
	    	$resply = -1;
	    else
	    	$resply = 0;
	    mysqli_query($con, "UPDATE messages SET response=$resply WHERE notes='$_POST[msg]'");
	}
	else if (isset($_POST['bam']))
	{
		// Connect to MySQL database
		$con = mysqli_connect("fdb4.biz.nf","1238336_lhs","lhsgrad13","1238336_lhs");
	    if (mysqli_connect_errno($con)){
	        echo "Failed: " . mysqli_connect_error();
	    }

	    mysqli_query($con, "DELETE FROM messages WHERE notes='$_POST[detectdelete]'");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>hangr</title>
    <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  	<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
	<link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <link rel="stylesheet" href="http://daneden.github.io/animate.css/animate.min.css" />
    <link href='http://fonts.googleapis.com/css?family=Muli' rel='stylesheet' type='text/css' />
</head>

<body>
	<header class="header">
		<a href="portal.php"><img id="logo"src="hangr-logo.jpg" alt="logo"/></a>
	</header>

	<div id="calbody">
		<form action="calendar.php" method="POST" name="form" id="calendar">
			<h2>Availability</h2>
			<br/>
			<h3>Start Time: </h3><input type="text" id="startdate" class="datepicker" data-format="YYYY-MM-DD HH:mm" data-template="MM / DD  / YYYY   HH : mm" name="startdate" value="2014-07-12 01:50">
			<br/>
			<h3 style="margin-top: 25px;">End Time: </h3><input type="text" id="enddate" class="datepicker" data-format="YYYY-MM-DD HH:mm" data-template="MM / DD  / YYYY   HH : mm" name="enddate" value="2014-07-12 02:20">
			<br/>
			<input type="button" name="add" class="add" value="add" onclick="addToArray()">
			<p id="error"></p>
			<br/>
			<div id="whitelist">No available dates set. Click to add more.</div>
			<br/>
			<input type="button" name="remove" class="remove" value="remove" onclick="removeFromArray()">
			<br/>
			<br/>
			<input type="hidden" id="myField" name="myField" value="" />
			<input type="submit" name="save" class="save" value="save">
		</form>

		<br/><br/>

		<div id="feed">
			<h2>Friends Feed</h2>
			<div id="friends"></div>
		</div>

		<div id="popup" style="display:none">
			<form action="calendar.php" method="POST" name="something" id="invite">
				<input type="button" value="close X" name="close" id="close" onclick="close()">
				<h2>Invite</h2>
				Start Time: <input type="text" id="invitestart" name="invitestart" value="" readonly>
				<br/>
				End Time: <input type="text" id="inviteend" name="inviteend" value="" readonly>
				<br/>
				From: <input type="text" id="fro" name="fro" value="" readonly>
				<br/>
				To: <input type="text" id="tod" name="tod" value="" readonly>
				<br/>
				<textarea rows="4" cols="50" id="message" name="message"></textarea>
				<br/>
				<input type="submit" name="invite" class="invite" value="invite">
				<p id="inviteerror"></p>
			</form>
		</div>

		<?php
		if (isset($_POST['invite'])){
			if (!isset($_POST['invitestart']) || $_POST['invitestart'] == "" || !isset($_POST['message']) || $_POST['message'] == "")
			{
				echo "<script>document.getElementById('inviteerror').innerHTML = 'Please fill out all fields.'</script>";
			}
			else{
				$con = mysqli_connect("fdb4.biz.nf","1238336_lhs","lhsgrad13","1238336_lhs");
			    if (mysqli_connect_errno($con)){
			        echo "Failed: " . mysqli_connect_error();
			    }
			    mysqli_query($con, "INSERT INTO messages (start, end, fro, tod, notes) VALUES ('$_POST[invitestart]','$_POST[inviteend]','$_POST[fro]','$_POST[tod]','$_POST[message]')");
			}
		}
		?>
		<div id="invitediv">
			<h2>Sent Invites</h2>
			<?php
				$con = mysqli_connect("fdb4.biz.nf","1238336_lhs","lhsgrad13","1238336_lhs");
			    if (mysqli_connect_errno($con)){
			        echo "Failed: " . mysqli_connect_error();
			    }

			    // get username
			    $user = $_SESSION['user'];

			    $theres = mysqli_query($con, "SELECT * FROM messages WHERE fro='$user'");
				while($row = mysqli_fetch_array($theres))
				{
					$reply = '';
					$background = "style='color:black'";

					if ($row['response'] == 0)
					{
						$reply = 'No response yet.';
					}
					else if ($row['response'] == -1)
					{
						$reply = 'No.';
						$background = "style='color:red'";
					}
					else if ($row['response'] == 1)
					{
						$reply = 'Yes!';
						$background = "style='color:green'";
					}

					$delete = "<form name='delete' id='delete' action='calendar.php' method='POST'><input type='hidden' name='detectdelete' value='".$row['notes']."'><input type='submit' name='bam' id='bam' value='delete'></form>";
					$sentMsg = "<div class='inviteentry'><p ".$background.">Start Time: ".$row['start']." | End Time: ".$row['end']." | Invitee: ".$row['tod']." | Message: ".$row['notes']." | Response: ".$reply." | ".$delete."</p></div>";
					echo $sentMsg;
				}
			?>
		</div>

		<div id="inboxdiv">
			<h2>Inbox</h2>
			<?php
				$con = mysqli_connect("fdb4.biz.nf","1238336_lhs","lhsgrad13","1238336_lhs");
			    if (mysqli_connect_errno($con)){
			        echo "Failed: " . mysqli_connect_error();
			    }

			    // get username
			    $user = $_SESSION['user'];

			    $theres = mysqli_query($con, "SELECT * FROM messages WHERE tod='$user'");
				while($row = mysqli_fetch_array($theres))
				{
					$background = "style='color:black'";
					if ($row['response'] == 0){
						// possible overflow error by storing message in html value attribute.
						$yesorno = "<form name='yesorno' id='yesorno' action='calendar.php' method='POST'><input type='radio' name='accept' value='yes'>Yes<input type='radio' name='accept' value='no'>No<input type='hidden' name='msg' id='msg' value='".$row['notes']."'><input type='submit' name='acceptreq' id='acceptreq' value='reply'></form>";
					}
					else if ($row['response'] == 1){
						// possible overflow error by storing message in html value attribute.
						$yesorno = "<form name='yesorno' id='yesorno' action='calendar.php' method='POST'><input type='radio' checked='checked' name='accept' value='yes'>Yes<input type='radio' name='accept' value='no'>No<input type='hidden' name='msg' id='msg' value='".$row['notes']."'><input type='submit' name='acceptreq' id='acceptreq' value='update'></form>";
						$background = "style='color:green'";
					}
					else if ($row['response'] == -1){
						// possible overflow error by storing message in html value attribute.
						$yesorno = "<form name='yesorno' id='yesorno' action='calendar.php' method='POST'><input type='radio' name='accept' value='yes'>Yes<input type='radio' checked='checked' name='accept' value='no'>No<input type='hidden' name='msg' id='msg' value='".$row['notes']."'><input type='submit' name='acceptreq' id='acceptreq' value='update'></form>";
						$background = "style='color:red'";
					}
					$sentMsg = "<div class='inboxmsg'><p ".$background.">Start Time: ".$row['start']." | End Time: ".$row['end']." | From: ".$row['fro']." | Message: ".$row['notes']." | Response: ".$yesorno."</p></div>";
					echo $sentMsg;
				}
			?>
		</div>
	</div>

	<script>
		// fills the list from the database when page loads
    	$(window).load(function(){
			updateList();
		});

		var user = "<?php echo $_SESSION['user'] ?>";

    	// settings for the datepickers
		$(function(){
		    $('.datepicker').combodate({
			    minYear: 2014,
			    maxYear: 2015,
			    minuteStep: 10
			});  
		});

		// get list from MySQL database, do not do a hard initialization
		<?php
			// Connect to MySQL database
			$con = mysqli_connect("fdb4.biz.nf","1238336_lhs","lhsgrad13","1238336_lhs");
		    if (mysqli_connect_errno($con)){
		        echo "Failed: " . mysqli_connect_error();
		    }

		    $user = $_SESSION['user'];

		    $result = mysqli_query($con, "SELECT times FROM calendar WHERE name='$user'");
			$totalrows = mysqli_num_rows($result);

			if ($totalrows > 0)
			{
				$row = mysqli_fetch_row($result);
		    	$selectData = json_encode(unserialize($row[0]));

				echo "var list = ". $selectData . ";";
			}
			else
			{
				echo "var list = [];";
			}
		?>

		$('#close').click(function(){
			$('#popup').hide();
		});

		function addToArray(){
			var startUnix = Date.parse(document.getElementById("startdate").value).getTime()/1000;
			var endUnix = Date.parse(document.getElementById("enddate").value).getTime()/1000;
			var currentUnix = (new Date).getTime()/1000;
			var check = true;

			for (i=0; i<list.length; i++)
			{
				arrayStart = Date.parse(list[i][0]).getTime()/1000;
				arrayEnd = Date.parse(list[i][1]).getTime()/1000;

				if (startUnix >= arrayStart && startUnix < arrayEnd)
				{
					document.getElementById("error").innerHTML = 'That time overlaps with an existing entry. Please try again.';
					check = false;
					break;
				}
				else if (endUnix > arrayStart && endUnix <= arrayEnd)
				{
					document.getElementById("error").innerHTML = 'That time overlaps with an existing entry. Please try again.';
					check = false;
					break;
				}
				else if (arrayStart >= startUnix && arrayStart < endUnix)
				{
					document.getElementById("error").innerHTML = 'That time overlaps with an existing entry. Please try again.';
					check = false;
					break;
				}
				else if (arrayEnd > startUnix && arrayEnd <= endUnix)
				{
					document.getElementById("error").innerHTML = 'That time overlaps with an existing entry. Please try again.';
					check = false;
					break;
				}
			}

			if (check)
			{
				if (endUnix <= startUnix)
				{
					document.getElementById("error").innerHTML = 'You cannot start when/before you end. Please try again.';
				}
				else if (endUnix <= currentUnix || startUnix <= currentUnix)
				{
					document.getElementById("error").innerHTML = 'That time has already passed. Please try again.';
				}
				else
				{
					document.getElementById("error").innerHTML = '';
					// array object to be stored in one slot of list array, thus making a two dimensional array
					var item = [ document.getElementById("startdate").value, document.getElementById("enddate").value ];
					list.push( item );
					
					updateList();
				}
			}
		}

		function removeFromArray(){
			var len = list.length;
			var subtractor = 0;

			for (i=0; i<len; i++)
			{
				if (document.getElementById('' + i).checked) 
				{
					list.splice((i-subtractor),1);
					subtractor++;
					len;
				}
			}

			updateList();
		}

		function deletePast(){
			var len = list.length;
			var subtractor = 0;
			var currentUnix = (new Date).getTime()/1000;

			for (i=0; i<len; i++)
			{
				if (Date.parse(list[i][0]).getTime()/1000 < currentUnix) 
				{
					list.splice((i-subtractor),1);
					subtractor++;
					len;
				}
			}
		}

		function updateList()
		{
			deletePast();
			// updates "remove" list with all elements in the array
			if (list.length >= 1)
			{
				var templist = [];
				// chronologically sort list array
				templist.push(list[0]);
				for (i=1; i<list.length; i++)
				{
					var count = 0;
					for (j=0; j<templist.length; j++)
					{
						if ( Date.parse(list[i][0]).getTime()/1000 > Date.parse(templist[j][0]).getTime()/1000  )
						{
							count++;
						}
						else
						{
							break;
						}
					}
					templist.splice(count, 0, list[i]);
				}
				list = templist;
				document.getElementById("whitelist").innerHTML = '<input type="checkbox" name="list" id="' + 0 + '"><p>' + list[0] + '</p><br/>';
			}	
			else
			{
				document.getElementById("whitelist").innerHTML = 'no content. click to add more.';
			}
			for (i=1; i<list.length; i++)
			{
				document.getElementById("whitelist").innerHTML += '<input type="checkbox" name="list" id="' + i + '"><p>' + list[i] + '</p><br/>';
			}

			// rewrite array in a JSON string format that can be passed and converted into php when submitted
			var st = JSON.stringify(list);
			document.getElementById('myField').value = st;
		}

		function invite( arraypos )
		{
			$('#popup').fadeIn('slow');
			document.getElementById('invitestart').value = sort[arraypos][0];
			document.getElementById('inviteend').value = sort[arraypos][1];
			document.getElementById('fro').value = user;
			document.getElementById('tod').value = sort[arraypos][2];
		}
		 
		var sort = [];
	</script>
	<script src="http://vitalets.github.io/combodate/momentjs/moment.min.2.5.0.js"></script> 
	<script src="http://vitalets.github.io/combodate/combodate.js"></script> 
	<script src="date.js"></script> 


	<?php
		// Connect to MySQL database
		$con = mysqli_connect("fdb4.biz.nf","1238336_lhs","lhsgrad13","1238336_lhs");
	    if (mysqli_connect_errno($con)){
	        echo "Failed: " . mysqli_connect_error();
	    }

	    $res = mysqli_query($con, "SELECT name FROM calendar");
		while($row = mysqli_fetch_array($res))
		{
			$name = $row['name'];
     	    if ($name != $_SESSION['user'])
     	    {
     	    	$otherTimes = mysqli_query($con, "SELECT times FROM calendar WHERE name='$name'");
	     	    $row = mysqli_fetch_row($otherTimes);
		    	$selectData = json_encode(unserialize($row[0]));

		    	echo "<script>var storage = ". $selectData . ";</script>";?>

		    	<script>
		    		for (i=0; i<storage.length; i++)
		    		{
		    			storage[i].push(<?php echo "'".$name."'" ?>);
		    		}

		    		if (!(sort.length >= 0))
		    		{
		    			sort.push(storage[0]);
		    			i=1;
		    		}
		    		else
		    		{
		    			i=0;
		    		}
		    		for (; i<storage.length; i++)
					{
						var count = 0;
						for (j=0; j<sort.length; j++)
						{
							if ( Date.parse(storage[i][0]).getTime()/1000 > Date.parse(sort[j][0]).getTime()/1000  )
							{
								count++;
							}
							else
							{
								break;
							}
						}
						var currentUnix = (new Date).getTime()/1000;
						if (Date.parse(storage[i][0]).getTime()/1000 >= currentUnix)
							sort.splice(count, 0, storage[i]);
						else
							count--;
					}
	    		</script>

		    	<?php
     	    }
		}

	?>
	<script>
		for (i=0; i<sort.length; i++)
		{
			var sub = sort[i][0].substring(0,11);
			if (sub!=prev){
				document.getElementById("friends").innerHTML += "<h3>"+sub+"</h3>";
			}
			var prev = sub;
			//var str = "'invite('"+sort[i][0]+"', '"+sort[i][1]+"', '"+sort[i][2]+"')'";
			document.getElementById("friends").innerHTML += "<p>Start Time: " + sort[i][0] +" | End Time: " + sort[i][1] +" | User: " + sort[i][2] +"<button onclick='invite("+sort.indexOf(sort[i])+")'>Invite!</button></p>";
		}
	</script>

</body>

</html>