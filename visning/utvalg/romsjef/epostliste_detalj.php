<?php

require_once(__DIR__ . '/../topp_utvalg.php');

$kjonn = false;

if (in_array($epostlista, array('sing-gutter@singsaker.no', 'sing-jenter@singsaker.no'))) {
    $kjonn = true;
    $riktig_kjonn = explode('@', explode('-', $epostlista)[1])[0];
    $kjonnene = array('jenter' => 'Kvinne', 'gutter' => 'Mann');
}

?>
    <link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
    <script type="text/javascript" src="js/dataTables.js"></script>
    <script>
        $(document).ready(function () {
            var table = $('#tabellen').DataTable({
                "paging": false,
                "searching": false,
                "scrollY": "60vh",
                "scrollCollapse": true,
            });
        });
    </script>
    <div class="container">
        <div class="col-lg-12">
            <h1>Utvalget » Romsjef » Epostlister » <?php echo $epostlista; ?></h1>
            <p>
                Her kan du se en oversikt over samtlige medlemmer av <?php $epostlista; ?>, slik at du lett kan fjerne
                de som ikke skal stå på den (f.eks tidligere beboere).
            </p>
            <hr>
            <table class="table table-bordered table-responsive" id="tabellen">
                <thead>
                <tr>
                    <th>Epost</th>
                    <th>Beboer?</th>
                    <?php if ($kjonn) { ?>
                        <th>Riktig kjønn?</th> <?php } ?>
                    <th>Meld av</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($behandla as $email) { ?>
                    <tr id="<?php echo $email[1]; ?>">
                        <td><?php echo $email[1]; ?></td>
                        <td><?php echo $email[0] ? 'Ja' : 'Ulik e-post som på internsiden eller ikke en (aktiv) beboer.'; ?></td>
                        <?php if ($kjonn) { ?>
                            <?php if (is_null($email[2])) { ?>
                                <td>Ukjent</td>
                            <?php } else {
                                $bruker = $email[2];
                                ?>
                                <td><?php echo $bruker->getPerson()->getKjonn() == $kjonnene[$riktig_kjonn] ? 'Ja' : 'Nei!'; ?></td>
                            <?php } ?>
                        <?php }
                          ?>

                        <td>
                            <button class="btn btn-danger" onclick="del('<?php echo $email[1]; ?>')">Meld av</button>
                        </td>
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