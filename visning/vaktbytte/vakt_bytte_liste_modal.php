<?php

/* @var \intern3\Vakt $vakt */

?>

<h3><b><?php echo $vakt->medToString(); ?></b></h3>
<form action="?a=vakt/bytte/leggtil/<?php echo $vakt->getId();?>" method="POST">

    <div class="radio">
        <label><input type="radio" class="radio-inline" name="byttes" value="byttes" checked="checked">Byttes</label>
        <label><input type="radio" class="radio-inline" name="byttes" value="gibort">Gis bort</label>
    </div>

    Passord?
    <div class="radio">
        <label><input type="radio" class="radio-inline" name="passord" value="no"
                      onclick="setPassord(0, <?php echo $vakt->getId(); ?>)" checked="checked">Uten passord</label>
        <label><input type="radio" class="radio-inline" name="passord" value="yes"
                      onclick="setPassord(1, <?php echo $vakt->getId(); ?>)">Med passord</label>
    </div>

    <p><input type="password" class="form-control" name="passordtekst" placeholder="Passord"
              id="<?php echo $vakt->getId(); ?>" style="display:none;"></p>


    <p><textarea class="form-control" name="merknad" placeholder="Merknad"></textarea></p>

    <button class="btn btn-primary">Legg ut!</button>
</form>


<script>
    function setPassord(val, el){
        if(val == 1){
            document.getElementById(el).style.display = 'block';
        } else {
            document.getElementById(el).style.display = 'none';
        }
    }
    
</script>