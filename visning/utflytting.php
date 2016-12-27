<?php
require_once('topp.php');
?>
<div class="container">
    <?php if (isset($success)) {
        ?>
        <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Du sendte en utflyttingsmelding! Det blir trist at du flytter! :(.
        </div>
        <?php
    }
    unset($success);
    ?>
    <?php if (isset($error)) {
        ?>
        <div class="alert alert-danger fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
           Noe gikk galt, og din utflyttingmelding ble IKKE sendt!
        </div>
        <?php
    }
    unset($error);
    ?>

<h1>Utflytting</h1>

    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <table class="table borderless">
            <tr>
                <th>Utflyttsmelding</th>
                <td><textarea name="tekst" type="tekst" placeholder="Skriv her!"></textarea></td>
            </tr>
            <tr>
                <th>Passord</th>
                <td><input type="password" placeholder="Passord" name="passord"></td>
            </tr>
            <tr>
                <td> </td>
                <td><input type="submit" class="btn btn-primary" value="Send"></td>
            </tr>
        </table>
    </form>

</div>
<?php
require_once('bunn.php');
?>
