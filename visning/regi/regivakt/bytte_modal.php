<h3><b>Regivakt <?php echo $rv->medToString(); ?></b> - <?php echo $rv->getNokkelord() ;?></h3>
<form action="?a=regi/regivakt/leggtil/<?php echo $rv->getId(); ?>" method="POST">

    <div class="radio">
        <label><input type="radio" class="radio-inline" name="byttes" value="byttes" checked="checked">Byttes</label>
        <label><input type="radio" class="radio-inline" name="byttes" value="gibort">Gis bort</label>
    </div>

    Passord?
    <div class="radio">
        <label><input type="radio" class="radio-inline" name="passord" value="no"
                      onclick="setPassord(0, <?php echo $rv->getId(); ?>)" checked="checked">Uten passord</label>
        <label><input type="radio" class="radio-inline" name="passord" value="yes"
                      onclick="setPassord(1, <?php echo $rv->getId(); ?>)">Med passord</label>
    </div>

    <p><input type="password" class="form-control" name="passordtekst" placeholder="Passord"
              id="<?php echo $rv->getId(); ?>" style="display:none;"></p>


    <p><textarea class="form-control" name="merknad" placeholder="Merknad"></textarea></p>

    <button class="btn btn-primary">Legg ut!</button>
</form>


<script>
    function setPassord(val, el) {
        if (val == 1) {
            document.getElementById(el).style.display = 'block';
        } else {
            document.getElementById(el).style.display = 'none';
        }
    }

</script>