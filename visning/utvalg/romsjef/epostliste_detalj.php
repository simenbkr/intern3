<?php

require_once(__DIR__ . '/../topp_utvalg.php');


//var_dump($behandla);


?>


    <div class="container">

        <div class="col-lg-12">

            <h1>Utvalget » Romsjef » Epostlister » <?php echo $epostlista; ?></h1>

            <p>
                Her kan du se en oversikt over samtlige medlemmer av <?php $epostlista; ?>, slik at du lett kan fjerne
                de som ikke skal stå på den (f.eks tidligere beboere).
            </p>
            <hr>

            <table class="table table-bordered table-responsive">

                <thead>
                <tr>
                    <th>Epost</th>
                    <th>Beboer?</th>
                    <th>Meld av</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($behandla as $email) { ?>
                    <tr id="<?php echo $email[1]; ?>">
                        <td><?php echo $email[1]; ?></td>
                        <td><?php echo $email[0] ? 'Ja' : 'Ulik e-post som på internsiden eller ikke en (aktiv) beboer.'; ?></td>
                        <td><button class="btn btn-danger" onclick="del('<?php echo $email[1]; ?>')">Meld av</button></td>
                    </tr>
                <?php } ?>


                </tbody>


            </table>

        </div>

    </div>


    <script>
        function del(id) {
            $.ajax({
                url: '?a=utvalg/romsjef/epost/' + '<?php echo $epostlista; ?>' + "/" + id,
                type: 'DELETE',
                success: function (result) {
                    $("#" + id + " ." + group.split("@"[0])).html(result);
                }
            });
        }
    </script>
<?php

require_once(__DIR__ . '/../../static/bunn.php');