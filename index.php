// Just my first kinda iteration, got back late tonight so no time to work on it, but thought I would pass it up to here to all have a look at.
// The CSS is not a worry at this stage, but that will be my fun little thing to do in my downtime.
// Please feel free to let me know if i'm missing anything when it comes to linking the database.

<?php 
	//this section will be used to hold php instructions
	//page name: index.php - including style, user controls and searching - this page has been updated to expand the search to include sName, sMgrID and sEmail
	//
	//Helpful suggestions:
	//developing in javascript can be rewarding - however, due to the nature of browsers errors can sometimes be hard to see.
	//although some browsers will be different, if you are using chrome - it is suggested that you enable the developer tools
	//to do this, (in chrome) click the 3 vertical dots menu (normally top right) and click more tools/developer tools
	//for information on using the developer tools search "chrome developer tools" or see: https://developers.google.com/web/tools/chrome-devtools/
	
	
	//it is suggested that you use the existing php database modules (already available if you have installed a MAMP (or similar) development 
	//environment. although there are several possible options for using the MYSQLi (MySQL Improved) extension we will focus on the ‘object orientated’ 
	//approach (by using this common industry standard approach you will be able to quickly access and utilise a collection of prebuilt functions 
	//that will simplify the development of your system and allow it to be integrated more easily with other possible frameworks in the future).
	//
	//for more information on the mysqli (extension), its use and functions etc - please see: https://www.php.net/manual/en/book.mysqli.php

	
	//create some database connection variables - this makes it easier to read the code there are 3 basic details needed:
	//1. the address of the server - as our database server is on the same machine as our web server we can use 'localhost'.
	//2. the name of the user that EVERYONE who accesses this page will use to authentic a request to connect and execute database instructions.	
	//3. the password used for the "EVERYONE" user name.
	
	//because PHP variables don’t specify type (e.g. INT, etc) they're declared using "$"
	//for readability and to more easily associate them with specific parts of the software we have prefixed the letters "db_" but that is not essential
	$db_server="localhost"; 
	$db_username="all_web_users";
	$db_password="password123";
	
	$db_database="crux_database"; //in addition to details on the server, user and password - we also need to specify the database to use
	
	
	//use php's mysqli extension to authentic and establish a connection to the database server holding the information 
	//create a variable to "hold" the database connection and execute/use and following or helpful MySQL functions (another name for a function is “method”)
	$db_connection_object = new mysqli($db_server, $db_username, $db_password, $db_database); 

	//check to see if any errors were generated after executing the previous instruction
	if ($db_connection_object->connect_error) {
		
		//die is a php function that displays a message and causes the program to terminate immediately
		//this is helpful remember, if we can’t connect we won’t be able to do much
		die("MySQL Connection: " . $db_connection_object->connect_error); 
	} 
	
	
	//we can added a feature to this page to recover any value provided by the searchbox form
	//this feature allows the user to search the user table
	$search=$_POST["searchbox"];


	//prepare a valid sql instruction
	$db_sql = " SELECT `sID`, `sName`, `sMgrID`, `sEmail` FROM staff WHERE `sEmail` LIKE '%$search%' OR `sName` LIKE '%$search%' OR `sMgrID` LIKE '%$search%' ; ";

	//use the query function (method) to execute the sql instruction and store the results in the db_results variable
	$db_results = $db_connection_object->query($db_sql);

	
	
	//once we have completed all our current sql instructions we should close the connection (this helps the server manage its resources)
	$db_connection_object->close();


?>
<!doctype html>
<html>
	<head>
		<meta charset = "utf-8">
		<title>Time Card App</title>
		<link rel="stylesheet" href="style.css">
		
		<script>
			//this section will be used to hold javascript instructions
			
			//when developing always try to adopt a style that helps you write readable code - for example, we prefix 'fn' to the names of our functions
			//to help define them as functions and (more helpfully) try to avoid duplicated names 
			function fnDeleteUser(p_sID) {
				//this function is called when the user clicks the "X" option against any listed user - the function expects 1 parameter (the user id to delete);
				if(confirm("Are you sure you want to delete user " + p_sID +"?")) { //use a javascript confirmation box to confirm first
					//the user must have clicked 'okay' to get here
					document.getElementById("delete_sID").value = p_sID; //copy the user's id that will be deleted to the delete_sID
					document.getElementById("modify_sID").value = 0; //to avoid possible issues, we reset and value in the modify_sID to 0
					
					fnApply(); //call the function to apply the add/modify request
				}
			}
			
			function fnModifyUser(p_sID, p_sName, p_sMgrID, p_sEmail) {
				//this function is called when the user clicks the 'edit' options - each option has been programmed so it passes the details for a specific user
				//the details are them used by javascript to pre-populate the user modify form 
				
				document.getElementById("modify_sID").value = p_sID; //copy the user's id to be modified to the modify_sID
				document.getElementById("delete_sID").value = "0" //to avoid possible issues, we reset and value in the delete_sID to 0
				
				document.getElementById("sName").value = p_sName; //copy the user's sName value into the sName input box
				document.getElementById("sMgrID").value = p_sMgrID; //copy the user's sMgrID value into the sMgrID input box
				document.getElementById("sEmail").value = p_sEmail; //copy the user's sEmail value into the sEmail input box
				
				//we dont submit as we give the user time to edit - if they want to submit the can click the 'apply' button
			}
			
			function fnApply() {
				//this function is called when the user clicks the "apply" button - in earlier examples the button clicked was actually a submit button
				//but by adding client-side code we can add more features and create a richer user experience
				console.log("Ready to apply"); //we can use the console.log function to send information to the javascript console, this is great for debugging and testing
				document.getElementById("modifyform").submit(); //use javascript to submit the form
			}
			
		</script>	
	</head>
		
	<body>
		<div>
			<div>
				<h2>Welcome to the Time Card App</h2>
			</div>
			
			<div>
				<form id="searchform" action="index.php" method="post">
					<input type="text" id="searchbox" name="searchbox" placeholder="search users" value="<?php echo $search; ?>">
				</form>
			</div>
			
			<table id="userlist" >
				<tr style="font-weight: bold; background-color: Gray; color: White;">
					<td style="width: 100px;">Id</td>
					<td style="width: 300px;">Name</td>
					<td>Email Address</td>
					<td>Manager Name</td>
					<td style="width: 100px;">Options</td>
				</tr>
				<?php
					if($db_results->num_rows > 0) { 
						while($row = $db_results->fetch_assoc()) {
				?>
							<tr class="highlight">
								<td><?php echo $row["sID"]; ?></td>
								<td><?php echo $row["sName"]; ?></td>
								<td><?php echo $row["sEmail"]; ?></td>
								<td><?php echo $row["sMgrID"]; ?></td>
								<td>
									<!-- 
										this is an html comment, comments are ignored by browser so developers can use them to add programming note
										this bit creates a small control panel to allow the user to target specific user records
									-->
									<input type="button" value="X" style="color: Red; font-weight: bold;" onclick="fnDeleteUser(<?php echo $row["sID"]; ?>);">
									
									<!-- 
										the modify user button passed each of the fields to the javascript function - however because we are dealing with strings
										we need to be careful not to mix the double quotes and the single quotes
										in this instance, we are using the double to hold the javascript instruction while at the same time using the single
										to delineate string values been passed.
										
									-->
									<input type="button" value="Edit" style="color: DimGrey;" 
										onclick="fnModifyUser(
											<?php echo $row["sID"]; ?>, 
											'<?php echo $row["sName"]; ?>',
											'<?php echo $row["sMgrID"]; ?>',
											'<?php echo $row["sEmail"]; ?>'
											);">
								</td>
							</tr>
				<?php 
						}
					} else {
				?>
							<tr>
								<td colspan=4>No Records Found.</td>
							</tr>
				<?php 
					}
				?>
			</table>
			
			<br>
			<form id="modifyform" action="modifyuser.php" method="post">
				<table id="adduser">
					<tr style="font-weight: bold; background-color: MediumBlue; color: GhostWhite;">
						<td colspan="2">Add / Modify User</td>
					</tr> <!-- this line is used to create a space between the existing records and the new one -->
					<tr>
						<td style="width: 400px;">					
							<input type="text" id="sName" name="sName" style="width: 180px;" placeholder="First Name">
							<input type="text" id="sMgrID" name="sMgrID" style="width: 180px;" placeholder="Manager ID">
						</td>
						<td>
							<input type="text" id="sEmail" name="sEmail" style="width: 400px;" placeholder="Email Address">
							
							<!-- 
								we have changed the "add user" submit button to an ordinary button called 'apply' because we are now using javascript to 
								submit (although if we kept the actual 'submit' button javascript throws an error, search 'javascript submit is not a function' 
								for more details when coding using javascript it is recommended that you enable the developer tools so that you can more easily 
								see errors we have also started to adopt a better naming convention for our buttons and forms to avoid accidental id duplication 
							-->
							<input type="button" id="btApply" value="Apply" onclick="fnApply();">
						</td>
					</tr>
				</table>
				<!-- 
					the following is used to allow more data to be passed to the modifyuser.php page 
					where possible, default values have been set to 0
					by adding "title" we can create hover text - text that shows when the user hovers over the object
				-->
				<input type="hidden" id="modify_sID" name="modify_sID" style="width: 180px;" value="0" title="modify sID"> 
				<input type="hidden" id="delete_sID" name="delete_sID" style="width: 180px;" value="0" title="delete sID">
				
			</form>
			
		</div>
	</body>

</html>
