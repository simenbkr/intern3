<?php
require_once('topp_utvalg.php');
?>
    <script>
        function slettHelga(aar) {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/sekretar/helga/' + aar,
                data: 'sletthelga=1&aar=' + aar,
                method: 'POST',
                success: function (data) {
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }
    </script>
    <h1>Sekretar &raquo; Helga &raquo; Endre Helga</h1>
    <div class="container">
        <form action="" method="post">
            <table class="form table table-responsive table-bordered">
                <tr>
                    <th>År:</th>
                    <td><?php echo $helgaen->getAar(); ?></td>
                </tr>
                <tr>
                    <th>Tema:</th>
                    <td><input type="text" name="tema" value="<?php echo $helgaen->getTema(); ?>"></td>
                </tr>
                <tr>
                    <th>Start dato:</th>
                    <td><input type="text" name="start" value="<?php echo $helgaen->getStartDato(); ?>"> (YYYY-MM-DD f.eks 2015-02-25)</td>
                </tr>
                <tr>
                    <td><input type="hidden" name="endre" value="<?php echo $helgaen->getAar(); ?>"></td>
                    <td><input type="submit" class="btn btn-primary" value="Endre"></td>
                </tr>
            </table>
        </form>
        <button class="btn btn-danger" onclick="slettHelga(<?php echo $helgaen->getAar();?>)">Slett denne helga</button>
    </div>
<?php
require_once('bunn.php');
?>