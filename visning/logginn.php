<?php

require_once('topp.php');

?>
<div style="display:table; margin:auto; margin-top: 20%;">
  <h1>Logg inn</h1>
  <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
  	<table class="table borderless" style="width:auto;">
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
