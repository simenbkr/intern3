<?php
require_once ('topp.php');
?>
    <div style="display:table; margin: auto; margin-top: 20%">

        <?php require_once ('tilbakemelding.php'); ?>

        <h1 style="text-align: center;">Singsaker Studenterhjem</h1>
        <h1 style="font-size: 45px; text-align: center;">Internside</h1>
        <p>[ <a href="?a=diverse">Logg inn</a> ] [ Glemt passord ]</p>
        <p>Skriv inn din epost under, så sender vi et nytt passord til deg.</p>
        <form method="post" action="">
            <table class="table borderless">
                <tr>
                    <th>Epost</th>
                    <td><input type="text" name="brukernavn"></td>
                </tr>
                <tr>
                    <td> </td>
                    <td><input type="submit" class="btn btn-primary" value="Send"></td>
                </tr>
            </table>
        </form>
    </div>
    </div>

<?php
require_once ('bunn.php');
?>