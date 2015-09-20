<?php

require_once('topp.php');

?>
<div style="display:table; margin: auto; margin-top: 20%;">
	<h1 style="text-align: center;">Singsaker Studenterhjem</h1>
	<h1 style="font-size: 45px; text-align: center;">Internside</h1>
	<p>[ Logg inn ] [ <a href="?a=logginn/registrer">Registrer</a> ] [ <a href="?a=logginn/passord">Glemt passord</a> ]</p>
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<table class="table borderless">
			<tr>
				<th>Epost</th>
				<td><input type="text" name="brukernavn"></td>
			</tr>
			<tr>
				<th>Passord</th>
				<td><input type="password" name="passord"></td>
			</tr>
			<tr>
				<td> </td>
				<td><input type="submit" class="btn-primary" value="Logg inn"></td>
			</tr>
		</table>
	</form>
</div>
<?php

require_once('bunn.php');

?>
