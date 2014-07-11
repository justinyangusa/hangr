<?php
    session_start();

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
	    $user = "user";

	    mysqli_query($con, "DELETE FROM calendar WHERE name='$user'");
	    mysqli_query($con, "INSERT INTO calendar (name, times) VALUES ('$user','$serializedArray')");

	    $result = mysqli_query($con, "SELECT times FROM calendar WHERE name='$user'");
		$row = mysqli_fetch_row($result);
	    $selectData = json_encode(unserialize($row[0]));
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>hangr</title>
    <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  	<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
</head>

<body>
	<form action="calendar.php" method="POST" name="form">
		<h2>Availability</h2>
		<br/>
		Start Time: <input type="text" id="startdate" class="datepicker" data-format="YYYY-MM-DD HH:mm" data-template="MM / DD  / YYYY   HH : mm" name="startdate" value="2014-07-09 10:50">
		<br/>
		End Time: <input type="text" id="enddate" class="datepicker" data-format="YYYY-MM-DD HH:mm" data-template="MM / DD  / YYYY   HH : mm" name="enddate" value="2014-07-09 13:20">
		<br/>
		<input type="button" name="add" class="add" value="add" onclick="addToArray()">
		<br/>
		<br/>
		<p id="error"></p>
		<br/>
		<br/>
		<div id="whitelist">no content. click to add more.</div>
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
		<p><?php
			// Connect to MySQL database
			$con = mysqli_connect("fdb4.biz.nf","1238336_lhs","lhsgrad13","1238336_lhs");
		    if (mysqli_connect_errno($con)){
		        echo "Failed: " . mysqli_connect_error();
		    }

		    $res = mysqli_query($con, "SELECT times FROM calendar WHERE name='user'");
			$rows = mysqli_fetch_row($res);
			var_dump($rows);
		?></p>
	</div>

	<script>
		// fills the list from the database when page loads
    	$(window).load(function(){
			updateList();
		});

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

		    // get username
		    $user = "user";

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

		function updateList()
		{
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
						if ( Date.parse(list[i][0]).getTime()/1000 > Date.parse(list[j][0]).getTime()/1000  )
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
				document.getElementById("whitelist").innerHTML = '<input type="checkbox" name="list" id="' + 0 + '">' + list[0] + '<br/>';
			}	
			else
			{
				document.getElementById("whitelist").innerHTML = 'no content. click to add more.';
			}
			for (i=1; i<list.length; i++)
			{
				document.getElementById("whitelist").innerHTML += '<input type="checkbox" name="list" id="' + i + '">' + list[i] + '<br/>';
			}

			// rewrite array in a JSON string format that can be passed and converted into php when submitted
			var st = JSON.stringify(list);
			document.getElementById('myField').value = st;
		}
	</script>
	<script src="http://vitalets.github.io/combodate/momentjs/moment.min.2.5.0.js"></script> 
	<script src="http://vitalets.github.io/combodate/combodate.js"></script> 
	<script src="date.js"></script> 
</body>

</html>