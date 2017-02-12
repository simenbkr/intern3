<?php
require_once('topp_journal.php');
require_once('topp.php');
?>
    <script>
        function avslutt(id) {
            $.ajax({
                type: 'POST',
                url: '?a=journal/signering/',
                data: "avslutt=1&beboerId=" + id,
                method: 'POST',
                success: function (html) {
                    $('body').html(html);
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }
    </script>
    <div class="container">
        <h1>Journal » Signering</h1>
        <hr>
        <div class="col-lg-6">
            <h2><?php echo $vakta->getFulltNavn(); ?> sitter <?php echo $denne_vakta->getVaktnr(); ?>. vakt nå. (<?php echo date('Y-m-d',strtotime($denne_vakta->getDato()));?>)</h2>
            <table class="table table-bordered table-responsive">
                <tr>
                    <th>Status</th>
                    <th>Øl</th>
                    <th>Cider</th>
                    <th>Carlsberg</th>
                    <th>Rikdom</th>
                </tr>
                <tr>
                    <td>Mottatt</td>
                    <td><?php echo $denne_vakta->getOl()['mottatt']; ?></td>
                    <td><?php echo $denne_vakta->getCider()['mottatt']; ?></td>
                    <td><?php echo $denne_vakta->getCarlsberg()['mottatt']; ?></td>
                    <td><?php echo $denne_vakta->getRikdom()['mottatt']; ?></td>
                </tr>
                <tr>
                    <td>Påfyll</td>
                    <td><?php echo $denne_vakta->getOl()['pafyll']; ?></td>
                    <td><?php echo $denne_vakta->getCider()['pafyll']; ?></td>
                    <td><?php echo $denne_vakta->getCarlsberg()['pafyll']; ?></td>
                    <td><?php echo $denne_vakta->getRikdom()['pafyll']; ?></td>
                </tr>
                <tr>
                    <td>Krysset</td>
                    <td><?php echo $denne_vakta->getOl()['utavskap']; ?></td>
                    <td><?php echo $denne_vakta->getCider()['utavskap']; ?></td>
                    <td><?php echo $denne_vakta->getCarlsberg()['utavskap']; ?></td>
                    <td><?php echo $denne_vakta->getRikdom()['utavskap']; ?></td>
                </tr>
                <tr>
                    <td>Avlevert</td>
                    <td><?php echo $denne_vakta->getOl()['avlevert']; ?></td>
                    <td><?php echo $denne_vakta->getCider()['avlevert']; ?></td>
                    <td><?php echo $denne_vakta->getCarlsberg()['avlevert']; ?></td>
                    <td><?php echo $denne_vakta->getRikdom()['avlevert']; ?></td>
                </tr>
            </table>
            <input type="submit" value="Avslutt vakt" onclick="avslutt(<?php echo $vakta->getId(); ?>)" class="btn btn-block btn-warning">
        </div>
    </div>
<?php
require_once('bunn.php');
?>
