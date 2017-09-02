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
      <div class="tilbakemelding">
          <?php if (isset($_SESSION['success']) && isset($_SESSION['msg'])) { ?>

              <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  <?php echo $_SESSION['msg']; ?>
              </div>
              <p></p>
              <?php
          } elseif (isset($_SESSION['error']) && isset($_SESSION['msg'])) { ?>
              <div class="alert alert-danger fade in" id="danger" style="display:table; margin: auto; margin-top: 5%">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  <?php echo $_SESSION['msg']; ?>
              </div>
              <p></p>
              <?php
          }
          unset($_SESSION['success']);
          unset($_SESSION['error']);
          unset($_SESSION['msg']);
          ?></div>
  	<h1 style="text-align: center;">Singsaker Studenterhjem</h1>
  	<h1 style="font-size: 45px; text-align: center;">Internside</h1>
  	<p>[ Logg inn ] [ <a href="?a=logginn/passord">Glemt passord</a> ]</p>
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
