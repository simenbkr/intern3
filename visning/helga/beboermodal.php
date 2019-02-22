
<?php if(!$oppretta) { ?>

<form action="?a=helga/general/egendefinert" method="post">

    <table class="table table-responsive table-bordered">
        <input type="hidden" name="beboer_id" value="<?php echo $beboer->getId(); ?>"/>
        <input type="hidden" name="aar" value="<?php echo $aar;?>" />
        <tr>
            <td>Navn: </td>
            <td><?php echo $beboer->getFulltNavn(); ?></td>
        </tr>

        <tr>
            <td>Torsdag: </td>
            <td><input class="form-control" placeholder="Antall gjester denne dagen. Tom betyr standard."  type="number" name="torsdag"/></td>
        </tr>

        <tr>
            <td>Fredag: </td>
            <td><input class="form-control" placeholder="Antall gjester denne dagen. Tom betyr standard." type="number" name="fredag"/></td>
        </tr>

        <tr>
            <td>Lørdag: </td>
            <td><input class="form-control" placeholder="Antall gjester denne dagen. Tom betyr standard." type="number" name="lordag"/></td>
        </tr>


    </table>

    <button class="btn btn-primary" type="submit">Send</button>

</form>

<?php } else {
    $pers_arr = $denne_helga->medEgendefinertAntall()[$beboer->getId()];

    ?>

    <form action="?a=helga/general/egendefinert" method="post">

        <table class="table table-responsive table-bordered">
            <input type="hidden" name="beboer_id" value="<?php echo $beboer->getId(); ?>"/>
            <input type="hidden" name="aar" value="<?php echo $aar;?>" />
            <tr>
                <td>Navn: </td>
                <td><?php echo $beboer->getFulltNavn(); ?></td>
            </tr>

            <tr>
                <td>Torsdag: </td>
                <td><input class="form-control" placeholder="Antall gjester denne dagen. Tom betyr standard."
                           value="<?php echo $pers_arr['torsdag']; ?>" type="number" name="torsdag"/>
                </td>
            </tr>

            <tr>
                <td>Fredag: </td>
                <td><input class="form-control" placeholder="Antall gjester denne dagen. Tom betyr standard."
                           value="<?php echo $pers_arr['fredag']; ?>" type="number" name="fredag"/>
                </td>
            </tr>

            <tr>
                <td>Lørdag: </td>
                <td><input class="form-control" placeholder="Antall gjester denne dagen. Tom betyr standard."
                           value="<?php echo $pers_arr['lordag']; ?>" type="number" name="lordag"/>
                </td>
            </tr>


        </table>

        <button class="btn btn-primary" type="submit">Send</button>

    </form>


<?php } ?>