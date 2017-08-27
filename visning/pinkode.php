<?php
$skjulMeny = 1;
require_once ('topp_journal.php');
require_once('topp.php');

?>

<?php //$_SESSION[md5($beboer->getFulltNavn())] = 1; ?>

<script>
    var pinkoden = [];

    function oppdater(){

        var tekst = '';
        for(var i=0; i < pinkoden.length; i++){
            tekst += ' *';
        }

        document.getElementById("pinkoden").innerHTML = "<h3>" + tekst +"</h3>";
    }

    function klikk(tall){
        pinkoden.push(tall);
        oppdater();
    }

    function fjernSiste(){
        pinkoden.pop();
        oppdater();
    }

    function submit(){
        var beboerId = <?php echo $beboer->getId(); ?>;
        $.ajax({
            type: 'POST',
            url: '?a=journal/pinkode/' + beboerId,
            data: 'pinkode=' + pinkoden.join(''),
            method: 'POST',
            success: function (data) {
                window.location.href = "?a=journal/kryssing/" + beboerId;
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }


</script>


<div class="col-lg-12 text-centered" style="text-align:center;">
    <h1>Pinkode » <?php echo $beboer->getFulltNavn(); ?></h1>
    <hr>

    <h2 id="pinkoden"></h2>

    <div id="showgrid">
        <div class="row">
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="klikk(1)">1</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="klikk(2)">2</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="klikk(3)">3</button>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="klikk(4)">4</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="klikk(5)">5</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="klikk(6)">6</button>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="klikk(7)">7</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="klikk(8)">8</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="klikk(9)">9</button>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="fjernSiste()">-</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="klikk(0)">0</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="fjernSiste()">-</button>
            </div>
        </div>
    </div>
<hr><br/>
    <button class="btn btn-lg btn-primary btn-block" onclick="submit()">KJØR</button>

</div>

<style>
    @media screen and (min-device-width: 698px) {
        div#showgrid {
            width: auto;
            margin: auto;
        }
    }

    div.column {
        border-style: solid;
        border-width: 0px;
    }

    .row {
        width: 100%;
        margin: auto;
    }

    @media (min-device-width: 698px) {
        .column {
            width: 180px;
            height: 180px;
            display: inline;
            float: block;
        }
    }

    .column {
        width: 180px;
        height: 180px;
        margin: auto;
    }

    div .respsquare:hover:before {
        background: rgba(200, 200, 200, 0);
    }

    .myButton {
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #7892c2), color-stop(1, #476e9e));
        background: -moz-linear-gradient(top, #7892c2 5%, #476e9e 100%);
        background: -webkit-linear-gradient(top, #7892c2 5%, #476e9e 100%);
        background: -o-linear-gradient(top, #7892c2 5%, #476e9e 100%);
        background: -ms-linear-gradient(top, #7892c2 5%, #476e9e 100%);
        background: linear-gradient(to bottom, #7892c2 5%, #476e9e 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#7892c2', endColorstr='#476e9e', GradientType=0);
        background-color: #7892c2;
        -moz-border-radius: 18px;
        -webkit-border-radius: 18px;
        border-radius: 18px;
        display: inline-block;
        cursor: pointer;
        color: #ffffff;
        font-family: Verdana;
        font-size: 34px;
        padding: 60px 70px;
        text-decoration: none;
        text-shadow: 1px 0px 0px #283966;
    }

    .myButton:hover {
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #476e9e), color-stop(1, #7892c2));
        background: -moz-linear-gradient(top, #476e9e 5%, #7892c2 100%);
        background: -webkit-linear-gradient(top, #476e9e 5%, #7892c2 100%);
        background: -o-linear-gradient(top, #476e9e 5%, #7892c2 100%);
        background: -ms-linear-gradient(top, #476e9e 5%, #7892c2 100%);
        background: linear-gradient(to bottom, #476e9e 5%, #7892c2 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#476e9e', endColorstr='#7892c2', GradientType=0);
        background-color: #476e9e;
    }

    .myButton:active {
        position: relative;
        top: 1px;
    }

    .btn-huge {
        padding-top: 35px;
        padding-bottom: 35px;
        padding-left: 55px;
        padding-right: 55px;
        font-family: Verdana;
        font-size: 32px;
        margin-bottom: 5px;
    }


</style>

<?php
require_once('bunn.php');
?>
