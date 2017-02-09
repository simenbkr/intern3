<?php
require_once ('topp_journal.php');
require_once('topp.php');


if ($beboer == null || !$beboer->harAlkoholdepositum()) {
    header('Location: ?a=journal/krysseliste');
}
?>

<script>
    window.onload = function(){
        changeKnapp();
    }
    var count = 0;
    var drikkeid = 2;
    var drikker = ['','Pant','Øl','Cider','Carlsberg','Rikdom'];

    var teller = 0;
    var forrige = -1;
    function updateCount(num){
        if(teller == 0){
            count = num;
        }
        else {
            temp = count.toString() + num.toString()
            count = parseInt(temp);

            if (count > 48 || count < -48){
                count = 0;
                teller = 0;
            }
            if(forrige == 0 && num == 0){
                count = 0;
            }
        }
        teller += 1;
        changeKnapp();
    }

    function setNegativeCount(){
        count = -count;
        changeKnapp();
    }

    function updateDrikkeid(id){
        drikkeid = id;
        if (drikkeid==1) {
          color = '#4B515D';
        }
        else if (drikkeid==2) {
          color = '#5bc0de';
        }
        else if (drikkeid==3) {
          color = '#ffbb33';
        }
        else if (drikkeid==4) {
          color = '#00C851';
        }
        else if (drikkeid==5) {
          color = '#ff4444';
        }
        else {
          color = 'white';
        }
        document.body.style.backgroundColor = color;
        changeKnapp();
    }


    function changeKnapp(){
        var klassen = document.getElementById('krysseknapp');
        if(count == 0){
            klassen.style.display="none";
        }
        else if(count < 0 ){
            klassen.innerHTML = "Fjern " + -count + " " + drikker[drikkeid];
        }
        else {
            klassen.innerHTML = "Kryss " + count + " " + drikker[drikkeid];
            klassen.style.display="block"
        }
    }

    function kryss(beboerId) {
        console.log("count"+count + "beboerid:" + beboerId + "drikkeid" + drikkeid);
        $.ajax({
            type: 'POST',
            url: '?a=journal/kryssing/',
            data: 'beboerId=' + beboerId + "&antall=" + count + "&type=" + drikkeid,//'fjern=' + beboerId +'&verv='+ vervId,
            method: 'POST',
            success: function (data) {
                history.back();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
document.body.style.backgroundColor = '#5bc0de';
</script>

<style>
    @media screen and (min-device-width:698px) {
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
        background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #7892c2), color-stop(1, #476e9e));
        background:-moz-linear-gradient(top, #7892c2 5%, #476e9e 100%);
        background:-webkit-linear-gradient(top, #7892c2 5%, #476e9e 100%);
        background:-o-linear-gradient(top, #7892c2 5%, #476e9e 100%);
        background:-ms-linear-gradient(top, #7892c2 5%, #476e9e 100%);
        background:linear-gradient(to bottom, #7892c2 5%, #476e9e 100%);
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#7892c2', endColorstr='#476e9e',GradientType=0);
        background-color:#7892c2;
        -moz-border-radius:18px;
        -webkit-border-radius:18px;
        border-radius:18px;
        display:inline-block;
        cursor:pointer;
        color:#ffffff;
        font-family:Verdana;
        font-size:34px;
        padding:60px 70px;
        text-decoration:none;
        text-shadow:1px 0px 0px #283966;
    }
    .myButton:hover {
        background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #476e9e), color-stop(1, #7892c2));
        background:-moz-linear-gradient(top, #476e9e 5%, #7892c2 100%);
        background:-webkit-linear-gradient(top, #476e9e 5%, #7892c2 100%);
        background:-o-linear-gradient(top, #476e9e 5%, #7892c2 100%);
        background:-ms-linear-gradient(top, #476e9e 5%, #7892c2 100%);
        background:linear-gradient(to bottom, #476e9e 5%, #7892c2 100%);
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#476e9e', endColorstr='#7892c2',GradientType=0);
        background-color:#476e9e;
    }
    .myButton:active {
        position:relative;
        top:1px;
    }


    .btn-huge{
        padding-top:35px;
        padding-bottom:35px;
        padding-left:55px;
        padding-right:55px;
        font-family: Verdana;
        font-size: 32px;
        margin-bottom: 5px;
    }


</style>

<h1 style="text-align: center; font-size:2vw;">Kryssing for <?php echo $beboer->getFulltNavn(); ?></h1><br/>
<div class="container" style="text-align:center;">
<h1>
        <ul class="list-inline">
            <li><button class="btn btn-info btn-lg" onclick="updateDrikkeid(2)">Øl</button></li>
            <li><button class="btn btn-warning btn-lg" onclick="updateDrikkeid(3)">Cider</button></li>
            <li><button class="btn btn-success btn-lg" onclick="updateDrikkeid(4)">Carlsberg</button></li>
            <li><button class="btn btn-danger btn-lg" onclick="updateDrikkeid(5)">Rikdom</button></li>
            <li><button class="btn btn-muted btn-lg" onclick="updateDrikkeid(1)">Pant</button></li>
        </ul>
    </h1>
    <br/>
    <div id="showgrid">
        <div class="row">
            <div class="column"><button class="btn btn-primary btn-huge" onclick="updateCount(1)">1</button></div>
            <div class="column"><button class="btn btn-primary btn-huge" onclick="updateCount(2)">2</button></div>
            <div class="column"><button class="btn btn-primary btn-huge" onclick="updateCount(3)">3</button></div>
        </div>
        <div class="row">
            <div class="column"><button class="btn btn-primary btn-huge" onclick="updateCount(4)">4</button></div>
            <div class="column"><button class="btn btn-primary btn-huge" onclick="updateCount(5)">5</button></div>
            <div class="column"><button class="btn btn-primary btn-huge" onclick="updateCount(6)">6</button></div>
        </div>
        <div class="row">
            <div class="column"><button class="btn btn-primary btn-huge" onclick="updateCount(7)">7</button></div>
            <div class="column"><button class="btn btn-primary btn-huge" onclick="updateCount(8)">8</button></div>
            <div class="column"><button class="btn btn-primary btn-huge" onclick="updateCount(9)">9</button></div>
        </div>
        <div class="row">
            <div class="column"><button class="btn btn-primary btn-huge" onclick="setNegativeCount()">-</button></div>
            <div class="column"><button class="btn btn-primary btn-huge" onclick="updateCount(0)">0</button></div>
        </div>
    </div>
    <br/><hr>
    <button class="btn btn-lg btn-primary btn-block" id="krysseknapp" onclick="kryss(<?php echo $beboer->getId(); ?>)"></button>
    <br/>
    <h1><a href="javascript:history.back()">TILBAKE</a></h1>
</div>
<hr>
<?php

require_once('bunn.php');

?>
