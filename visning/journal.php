<?php
require_once('topp_journal.php');
require_once('topp.php');
?>

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
<!-- ?a=journal/logout for å logge ut. -->
<div class="container">
   <h1>Journal</h1>
   <hr>
   <div class="col-lg-12">
  <h1>
      <ul>
          <a href="?a=journal/krysseliste" type="button" class="btn btn-sm btn-info">Krysseliste</a>
          <a href="?a=journal/signering" type="button" class="btn btn-sm btn-success">Signering</a>
          <a href="?a=journal/pafyll" type="button" class="btn btn-sm btn-warning">Påfyll</a>
          <a href="?a=journal/vaktbytte" type="button" class="btn btn-sm btn-primary">Vaktbytte</a>
      </ul></h1>
  </div>
<?php

require_once('bunn.php');

?>
