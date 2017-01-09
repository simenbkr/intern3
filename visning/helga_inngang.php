<?php
require_once('topp.php');

$dag_array = array(
    0 => 'Torsdag',
    1 => 'Fredag',
    2 => 'Lørdag'
);
$side_tittel = "Helga inngang » $dag_array[$dag_tall]";
switch ($dag_tall) {
    case 0:
        $undertittel = "[ Torsdag ] [ <a href='?a=helga/inngang/fredag'>Fredag</a> ] [ <a href='?a=helga/inngang/lordag'>Lørdag</a> ]";
        break;
    case 1:
        $undertittel = "[ <a href='?a=helga/inngang/torsdag'>Torsdag</a> ] [ Fredag ] [ <a href='?a=helga/inngang/lordag'>Lørdag</a> ]";
        break;
    case 2:
        $undertittel = "[ <a href='?a=helga/inngang/torsdag'>Torsdag</a> ] [ <a href='?a=helga/inngang/fredag'>Fredag</a> ] [ Lørdag ]";
        break;
    default:
        $undertittel = "[ Torsdag ] [ <a href='?a=helga/inngang/fredag'>Fredag</a> ] [ <a href='?a=helga/inngang/lordag'>Lørdag</a> ]";
}
?>
    <script>
        function registrer(id, verdi) {
            $.ajax({
                type: 'POST',
                url: '?a=helga/inngang',
                data: 'registrer=ok&gjestid=' + id + "&verdi=" + verdi,
                method: 'POST',
                success: function (html) {
                    $("#" + id).replaceWith($('#' + id, $(html)));
                    $("#gjester").replaceWith($("#gjester", $(html)));
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function test() {
            var shownVal = document.getElementById("tekstinput").value;
            var gjestid = document.querySelector("#gjester option[value='"+shownVal+"']").dataset.value;
            registrer(gjestid, 1);
            document.getElementById("tekstinput").value = "";
        }

    </script>
    <div class="container">

        <h1><?php echo $side_tittel; ?></h1>
        <h3><?php echo $undertittel; ?></h3>
        <br/>
        <h4>Registrer folk</h4>
        <input placeholder="Ola Nordmann" id="tekstinput" class="form-control" type="text" list="gjester" onkeydown="if (event.keyCode == 13) { test()}"><br/><br/>

        <?php
        foreach ($gjesteliste_dag_gruppert as $beboers_id => $beboers_gjester) { ?>
            <table class="table table-bordered table-responsive">
                <tr class="bg-info">
                    <td><b><?php echo $beboerliste[$beboers_id]->getFulltNavn(); ?></b></td>
                </tr>
                <?php
                foreach ($beboers_gjester as $gjest) {
                    $klassen = $gjest->getInne() == 0 ? 'bg-warning' : 'bg-success';
                    $checked = $gjest->getInne() == 0 ? 'none' : 'checked=\"checked\"';
                    $verdi = $gjest->getInne() == 0 ? 1 : 0;
                    echo "<tr class=\"$klassen\" id='" . $gjest->getId() . "'><td>" . $gjest->getNavn() . "</td>";
                    echo "<td><input type=\"checkbox\"  value=\"" . $gjest->getId() . "\" onclick='registrer(" . $gjest->getId() . ",$verdi)' $checked></td></tr>";
                }
                ?></table>
            <?php
        }
        ?>
    </div>
    <datalist id="gjester">
        <?php
        foreach ($gjesteliste_dag as $gjest) {
            if ($gjest->getInne() != 0) {
                continue;
            }
            ?>
            <option data-value="<?php echo $gjest->getId(); ?>" value="<?php echo $gjest->getNavn(); ?>"></option>
            <?php
        }
        ?>
    </datalist>
<?php
require_once('bunn.php');
?>