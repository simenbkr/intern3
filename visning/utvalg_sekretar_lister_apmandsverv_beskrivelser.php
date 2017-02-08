<?php
require_once ('topp_utvalg.php');
?>
<div class="container">
<h1>Utvalget » Sekretær » Åpmandsverv beskrivelser</h1>
    <hr>
    <div class="col-lg-12">
  <?php foreach($vervene as $verv){ ?>
<b><u><?php echo $verv->getNavn();?></u></b><br/><br/>
          <?php echo $verv->getBeskrivelse();?>
      <br/><br/><br/><br/>


    <?php
}
?>
    </div>
</div>

<?php
require_once ('bunn.php');
?>