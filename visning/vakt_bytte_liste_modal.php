
<h3><b><?php echo $vakt->medToString(); ?></b></h3>
<form action="?a=vakt/bytte/leggtil/<?php echo $vakt->getId();?>" method="POST">
    <table class="table">
        <tr>
            <td>Byttes <input class="radio-inline" type="radio" name="byttes" value="byttes" checked></td>
            <td>Gis bort <input class="radio-inline" type="radio" name="byttes" value="gibort"></td>
        </tr>
        
        <tr>
            <td>Uten passord <input class="radio-inline" type="radio" name="passord" value="no"
                                    checked onclick="setPassord(0, <?php echo $vakt->getId();?>)"></td>
            
            <td>Med passord <input class="radio-inline" type="radio" name="passord" value="yes"
                                   onclick="setPassord(1, <?php echo $vakt->getId();?>)"></td>
            
            <td><input class="form-control" type="password" name="passordtekst" placeholder="Passord"
                       id="<?php echo $vakt->getId();?>" style="display:none;"></td>
        </tr>
        <tr>
            <td>Merknad</td>
            <td><textarea class="form-control" name="merknad"></textarea></td>
            
        </tr>
        
    </table>
    <button class="btn btn-primary">Send!</button>
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