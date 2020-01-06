<?php
/* @var \intern3\HelgaGjest $gjest */
?>

<?php /*<form action="?a=helga/endregjest/<?php echo $gjest->getId(); ?>" */ ?>
<form action="?a=helga/endre" method="post">
    <input type="hidden" name="id" value="<?php echo $gjest->getId(); ?>"

    <table class="table table-responsive table-bordered">

        <tr>
            <td>Navn: </td>
            <td><?php echo $gjest->getNavn(); ?></td>
        </tr>

        <tr>
            <td>E-post: </td>
            <td><input class="form-control" placeholder="<?php echo $gjest->getEpost(); ?>" type="text" name="epost"/></td>
        </tr>

    </table>

    <button class="btn btn-primary" type="submit">Endre</button>

</form>