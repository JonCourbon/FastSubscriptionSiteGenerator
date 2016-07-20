<?php
/*
 * fastSubscriptionSiteGenerator.php
 * 
 * Part of the FastSubscriptionSiteGenerator library
 * 
 * Copyright 2016 JCourbon <jonathan.courbon@udamail.fr>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Page d'installation</title>
	<meta charset="utf-8"/>
	
	<script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui.js"></script>
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/jquery-ui.css">
	
	<script>
		function addExample(name){
			$.ajax({
                url: 'examples/'+name+'.json',
                type: 'GET',
                success: function(result) {
					jsonString = JSON.stringify(result, null, '\t');
                    $('#content').val(jsonString);
                }
                });
		}
	</script>
</head>
<body>
	
<?php
	include("includes/functions.php");
	removeFrontBackFiles();
?>
	
 <form action="buildWebsite.php" method="POST">
	 	<fieldset>
		<legend>INFORMATIONS</legend>
		<p><label for="title">
			    Website title</label>
			    <input name="title" type="text" placeholder="Website" value="Mon super site"/>
		</p>
		<p><label for="description">
			    Description</label><br/>
			    <textarea name="description" cols="100" >djmezjdfmezjfrzemjmojomzerj<br/>zejpdzejrpj</textarea>
		</p>
		<p><label for="footer">
			    Footer content</label><br/>
			    <textarea name="footer" cols="100" >Copyright toto</textarea>
		</p>				
		</fieldset>
		<fieldset>
			<legend>DATABASE</legend>
		<p><label for="host">
			    Host</label>
			    <input name="host" type="text" placeholder="localhost" value="localhost"/>
		</p>
		<p><label for="port">
			    Port</label>
			    <input name="port" type="text" placeholder="" />
		</p>
		<p><label for="dbname">
			    Database name</label>
			    <input name="dbname" type="text" placeholder="test" value="test"/>
		</p>
		<p><label for="login">
			    Login</label>
			    <input name="login" type="text" placeholder="root" value="root" />
		</p>
		<p><label for="password">
			    Password</label>
			    <input name="password" type="password" placeholder="" />
		</p>
		<hr/>
		<p><label for="tname">
			    Table name</label>
			    <input name="tname" type="text" placeholder="" value="Candidate"/>
		</p>
		</fieldset>
				<fieldset>
			<legend>BACKEND</legend>
		<p><label for="loginbe">
			    Login to access back-end</label>
			    <input name="loginbe" type="text" placeholder="admin" value="admin"/>
		</p>
		</fieldset>
	<fieldset>
		<legend>CONTENT</legend>	 
<div>
	<label class="boxed" onclick="addExample('base')">Base sample</label> <br/>
	<label class="boxed" onclick="addExample('fullsubscription')">Subscription sample</label> <br/>
    <label class="boxed" onclick="addExample('subscriptionregistered')">Subscription with registered user sample</label>
</div>

		<textarea rows="20" cols="100" id="content" name="content">

		</textarea>
	</fieldset>
	<fieldset>
		<legend>PERSONALIZATION</legend>
		<p><label for="valid_msg">
			    Message when data are stored</label><br/>
			    <textarea name="valid_msg" cols="100" >Yes ! </textarea>
		</p>
		<p><label for="error_msg">
			    Message in case of error</label><br/>
			    <textarea name="error_msg" cols="100" >Error => contact us !</textarea>
		</p>				
		</fieldset>
		<fieldset>
			<input type="submit" name="BtnSubmit" value="Generate website (database + front-end + back-end)">
		</fieldset>
</form> 

</body>
</html>





