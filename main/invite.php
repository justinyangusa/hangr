<?php
    session_start();

	if (isset($_POST['invite'])){
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
</body>

</html>