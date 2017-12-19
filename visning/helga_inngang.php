<?php
require_once('topp.php');
$jeg_er_dum = array(0 => 'torsdag', 1 => 'fredag', '2' => 'lordag');
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

            if (verdi == 1) {
                document.getElementById(id).classList.remove('bg-warning');
                document.getElementById(id).classList.add('bg-success');
                document.getElementById(id + "-knapp").checked = true;
            } else {
                document.getElementById(id).classList.remove('bg-success');
                document.getElementById(id).classList.add('bg-warning');
                document.getElementById(id + "-knapp").checked = false;
            }
            $.ajax({
                type: 'POST',
                url: '?a=helga/inngang/<?php echo $jeg_er_dum[$dag_tall];?>',
                data: 'registrer=ok&gjestid=' + id + "&verdi=" + verdi,
                method: 'POST',
                success: function (html) {

                      var parser = new DOMParser();
                      var response = parser.parseFromString(html, "text/html");

                      if(document.getElementById("gjester").innerHTML != response.getElementById('gjester').innerHTML) {
                          $('#gjester').replaceWith(response.getElementById('gjester'));
                      }

                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function test() {
            var shownVal = document.getElementById("tekstinput").value;
            var gjestid = document.querySelector("#gjester option[value='" + shownVal + "']").dataset.value;
            registrer(gjestid, 1);
            document.getElementById("tekstinput").value = "";
        }

        function refresh() {
            $.ajax({
                type: 'GET',
                url: '?a=helga/inngang/<?php echo $jeg_er_dum[$dag_tall];?>',
                method: 'GET',
                success: function (html) {
                    var parser = new DOMParser();
                    var response = parser.parseFromString(html, "text/html");

                    if(document.getElementById('status').innerHTML != response.getElementById('status').innerHTML){
                        $('#status').replaceWith(response.getElementById('status'));
                    }

                    if(document.getElementById('lista').innerHTML != response.getElementById('lista').innerHTML){
                        $('#lista').replaceWith(response.getElementById('lista'));
                    }
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }


        setInterval(function () {
            refresh()
        }, 500);

        setInterval(function () {
                $.ajax({
                    type: 'GET',
                    url: '?a=helga/inngang/<?php echo $jeg_er_dum[$dag_tall];?>',
                    method: 'GET',
                    success: function (html) {
                        //$("#gjester").replaceWith($('#gjester', $(html)));
                        var parser = new DOMParser();
                        var response = parser.parseFromString(html, "text/html");

                        if(document.getElementById("gjester").innerHTML != response.getElementById('gjester').innerHTML) {
                            $('#gjester').replaceWith(response.getElementById('gjester'));
                        }
                    },
                    error: function (req, stat, err) {
                        alert(err);
                    }
                });
            },
            1000
        );

    </script>

    <div class="asd">
        <h4>Registrer folk</h4>
        <input placeholder="Ola Nordmann" id="tekstinput" class="form-control" type="text" list="gjester"
               onkeydown="if (event.keyCode == 13) { test()}"><br/><br/>
    </div>


    <div class="container" id="status">

        <h1><?php echo $side_tittel; ?></h1>
        <h3><?php echo $undertittel; ?></h3>
        <div class="tekst-ting">
            <h4>I dag er det invitert <b><?php echo $antall_inviterte; ?></b>, og det er
                <b id="number"><?php echo $antall_inne; ?></b>
                inne nå.</h4>
        </div>
        <br/>
    </div>


    <div class="subcontainer" id="lista">
        <?php
        foreach ($gjesteliste_dag_gruppert as $beboers_id => $beboers_gjester) { ?>
            <table class="table table-bordered table-responsive">
                <tr class="bg-info">
                    <td>
                        <b><?php echo ($beboerliste[$beboers_id] != null) ? $beboerliste[$beboers_id]->getFulltNavn() : ''; ?></b>
                    </td>
                </tr>
                <?php
                foreach ($beboers_gjester as $gjest) {
                    /* @var \intern3\HelgaGjest $gjest */
                    $klassen = $gjest->getInne() == 0 ? 'bg-warning' : 'bg-success';
                    $checked = $gjest->getInne() == 0 ? 'none' : 'checked=\"checked\"';
                    $verdi = $gjest->getInne() == 0 ? 1 : 0;
                    echo "<tr class=\"$klassen\" id='" . $gjest->getId() .
                        "' onclick='registrer(" . $gjest->getId() . ",$verdi)'>
                        <td>" . $gjest->getNavn() . "</td>";
                    
                    echo "<td><input id=" . $gjest->getId() . "-knapp type=\"checkbox\"  value=\"" . $gjest->getId() . "\" onclick='registrer(" . $gjest->getId() . ",$verdi)' $checked></td></tr>";
                }
                ?></table>
            <?php
        }
        ?>
    </div>
    </div>

    <datalist id="gjester">
        <?php
        foreach ($gjesteliste_dag as $gjest) {
            if ($gjest->getInne() != 0) {
                continue;
            }
            ?>
            <option data-value="<?php echo $gjest->getId(); ?>"
                    value="<?php echo $gjest->getNavn(); ?>"></option>
            <?php
        }
        ?></datalist>

<?php
require_once('bunn.php');
?>