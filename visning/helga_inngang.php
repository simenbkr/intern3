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
            
            var elem = document.getElementById(id);

            if (verdi == 1) {
                
                elem.classList.remove('bg-warning');
                elem.classList.add('bg-success');
                document.getElementById(id + "-knapp").checked = true;
                
                elem.onclick = function() { registrer(id, 0); };
                document.getElementById(id + "-knapp").onclick = function() { registrer(id, 0); };
                
            } else {
                elem.classList.remove('bg-success');
                elem.classList.add('bg-warning');
                document.getElementById(id + "-knapp").checked = false;
                elem.onclick = function() {registrer(id, 1); };
                document.getElementById(id + "-knapp").onclick = function() { registrer(id, 1); };
            }

            $.ajax({
                type: 'POST',
                url: '?a=helga/inngang/<?php echo $jeg_er_dum[$dag_tall];?>',
                data: 'registrer=ok&gjestid=' + id + "&verdi=" + verdi,
                method: 'POST',
                success: function (html) {
                    reloadGjest();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
            refreshNum();
        }

        function test() {
            var shownVal = document.getElementById("tekstinput").value;
            var gjestid = document.querySelector("#gjester option[value='" + shownVal + "']").dataset.value;
            registrer(gjestid, 1);
            document.getElementById("tekstinput").value = "";
        }

        function refreshNum(){
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
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
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
                    reloadAvkryss();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }


        setInterval(function () {
            refresh()
        }, 3000);

        setInterval(function () {
                $.ajax({
                    type: 'GET',
                    url: '?a=helga/inngang/<?php echo $jeg_er_dum[$dag_tall];?>',
                    method: 'GET',
                    success: function (html) {
                        reloadAvkryss();
                    },
                    error: function (req, stat, err) {
                        alert(err);
                    }
                });
            },
            10000
        );

        $(document).ready(function(){
            refreshNum();
            reloadGjest();
            reloadAvkryss();
        })

        function reloadGjest(){
            $("#gjesteliste").load("?a=helga/gjesteliste/" + '<?php echo $jeg_er_dum[$dag_tall];?>');
        }

        function reloadAvkryss(){
            $("#gjestavkryss").load("?a=helga/gjestavkryss/" + '<?php echo $jeg_er_dum[$dag_tall];?>');
        }

    </script>

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

    <div class="asd">
        <h4>Registrer folk</h4>
        <input placeholder="Ola Nordmann" id="tekstinput" class="form-control" type="text" list="gjester"
               onkeydown="if (event.keyCode == 13) { test()}"><br/><br/>
    </div>


<div id="gjestavkryss">
</div>

<div id="gjesteliste">
</div>

<?php
require_once('bunn.php');
?>