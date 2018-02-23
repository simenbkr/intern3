<?php
require_once ('topp_vinkjeller.php');
require_once('topp.php');

/* @var $regel \intern3\Vinregel */

?>
<style>
  body {
    background-color: #444341;
    color: #FFF;
  }
</style>
<div class="container">
    
    <div class="col-lg-12">
        <p>
        <?php echo $regel->getTekst(); ?>
        </p>
    </div>
    
</div>

<?php
require_once ('bunn.php');
?>
