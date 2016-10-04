<?php

require_once('topp.php');

if (isset($visError)) {
?>
<div style="margin-top: 15%">
  <div class="alert alert-danger" id="error"  style="display:table; margin: auto;">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Du har ikke tilgang til denne siden!
  </div>
<?php
}
?>
  <div style="display:table; margin: auto; margin-top: <?php if (isset($visError)) { echo '5%'; } else { echo '20%'; } ?>">
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
  				<td><input type="submit" class="btn btn-primary" value="Logg inn"></td>
  			</tr>
  		</table>
  	</form>
  </div>
</div>
<?php

require_once('bunn.php');

?>
