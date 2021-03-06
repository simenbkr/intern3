<?php
require_once('topp_journal.php');
require_once(__DIR__ . '/../static/topp.php');


if ($beboer == null || !$beboer->harAlkoholdepositum()) {
    header('Location: ?a=journal/krysseliste');
}
?>

<script>
    window.onload = function () {
        changeKnapp();
    }
    var count = 0;
    var drikker = <?php echo json_encode($drikke_navn) . ';';?>
    var drikke_farger = <?php echo json_encode($drikke_farger) . ';';?>

    var drikkeid = <?php echo $forste;?>;

    var teller = 0;
    var forrige = -1;
    var elemcount = 0;
    
    var cart = {}; // {elemcount => [drikkeid, count}
    
    function updateCount(num) {
        if (teller == 0) {
            count = num;
        }
        else {
            temp = count.toString() + num.toString();
            count = parseInt(temp);

            if (count > 48 || count < -48) {
                count = 0;
                teller = 0;
            }
            if (forrige == 0 && num == 0) {
                count = 0;
            }
        }
        teller += 1;
        changeKnapp();
    }

    function setNegativeCount() {
        count = -count;
        changeKnapp();
    }

    function updateDrikkeid(id) {
        drikkeid = id;
        if (drikke_farger[drikkeid] != undefined) {
            color = drikke_farger[drikkeid];
        }
        else {
            color = 'white';
        }
        document.body.style.backgroundColor = color;
        changeKnapp();
    }


    function changeKnapp() {
        var klassen = document.getElementById('krysseknapp');
        var cartknapp = document.getElementById('cartknapp');
        var cartkryssknapp = document.getElementById('cartkryss');
        
        if (Object.keys(cart).length > 0){
            cartkryssknapp.style.display = "block";
            cartkryssknapp.innerHTML = "KRYSS ALT";
        } else {
            cartkryssknapp.style.display = "none";
        }
        
        
        if (count == 0) {
            klassen.style.display = "none";
            cartknapp.style.display = "none";
        }
        else if (count < 0) {
            klassen.innerHTML = "Fjern " + -count + " " + drikker[drikkeid];
            cartknapp.style.display = "none";
        }
        else {
            if (Object.keys(cart).length > 0){
                cartknapp.style.display = "block";
                cartknapp.innerHTML = "Legg til " + count + " " + drikker[drikkeid];
                
                cartkryssknapp.style.display = "block";
                cartkryssknapp.innerHTML = "KRYSS ALT";

                klassen.style.display = "none"
            } else {

                klassen.innerHTML = "Kryss " + count + " " + drikker[drikkeid];
                klassen.style.display = "block"

                cartknapp.style.display = "block";
                cartknapp.innerHTML = "Legg til " + count + " " + drikker[drikkeid];
            }
        }
    }
    
    function addToCart(){
        elemcount++;
        
        cart[elemcount] = [drikkeid, count];
        var cartelement = document.getElementById('cart');
        var nyknapp = document.createElement('button');
        var buttonclasses = ['btn', 'btn-danger', 'btn-block'];
        
        buttonclasses.forEach(function(entry){
            nyknapp.classList.add(entry);
        });
        
        nyknapp.id = elemcount;
        nyknapp.innerHTML = count + " " + drikker[drikkeid];
        nyknapp.onclick = function() { slettFraCart(nyknapp.id) };
        
        cartelement.appendChild(nyknapp);
        count = 0;
        drikkeid = <?php echo $forste; ?>;
        updateDrikkeid(drikkeid);
    }
    
    function slettFraCart(id){
        delete cart[id];
        document.getElementById(id).remove();
        elemcount--;
        changeKnapp()
    }

    
    function cartkryss(beboerId) {
    
        var summaryStr = "Du krysset ";
        var dataStr = [];
        
        for(var key in cart){
            
            var curr_id = cart[key][0];
            var curr_count = cart[key][1];

            summaryStr += curr_count + " " + drikker[curr_id] + ", ";
            
            if(!isNumber(curr_id) || !isNumber(curr_count)){
                alert("Noe gikk veldig galt. Alt ble ikke krysset ordentlig. Påkall vakt elns.");
            }

            dataStr.push([cart[key][0],cart[key][1]])
        }

        console.log(dataStr);

        var jsonData = JSON.stringify(dataStr);
        console.log(jsonData);
        
        $.ajax({
            type: 'POST',
            url: '?a=journal/multikryss/',
            data: 'summary=' + summaryStr + "&beboerId=" + beboerId + "&data=" + jsonData,
            method: 'POST',
            success: function(data){
                //console.log(data);
                window.location.replace("?a=journal/krysseliste");
            },
            error: function(req, stat, err){
                alert("Noe gikk galt!");
            }
        })
    
    }

    function kryss(beboerId) {
        $.ajax({
            type: 'POST',
            url: '?a=journal/kryssing/',
            data: 'beboerId=' + beboerId + "&antall=" + count + "&type=" + drikkeid,
            method: 'POST',
            success: function (data) {
                //history.back();
                window.location.replace("?a=journal/krysseliste");
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }

    function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
    
    document.body.style.backgroundColor = '#5bc0de';
</script>


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

<div class="col-md-12">
    <div class="list-group" id="cart">
    
    </div>
</div>

<h1 style="text-align: center; font-size:2vw;">Kryssing for <?php echo $beboer->getFulltNavn(); ?></h1>
<hr>
<div class="col-lg-12 text-centered" style="text-align:center;">
    <h1>
        <ul class="list-inline">
            <?php
            $knapp_klasser = array('btn-muted', 'btn-info', 'btn-warning', 'btn-success', 'btn-danger');
            $neste_klasse = 0;
            foreach ($drikker as $drikke) {
                if(!$drikke->getAktiv()){
                    continue;
                }
                ?>
                <li>
                    <button class="btn btn-lg <?php echo $knapp_klasser[$neste_klasse]; ?>"
                            onclick="updateDrikkeid(<?php echo $drikke->getId(); ?>)"><?php echo $drikke->getNavn(); ?></button>
                </li>
                <?php
                $neste_klasse++;
                $neste_klasse %= count($knapp_klasser);
            } ?>
        </ul>
    </h1>
    <div id="showgrid">
        <div class="row">
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="updateCount(1)">1</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="updateCount(2)">2</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="updateCount(3)">3</button>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="updateCount(4)">4</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="updateCount(5)">5</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="updateCount(6)">6</button>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="updateCount(7)">7</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="updateCount(8)">8</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="updateCount(9)">9</button>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="setNegativeCount()">-</button>
            </div>
            <div class="column">
                <button class="btn btn-primary btn-huge" onclick="updateCount(0)">0</button>
            </div>
        </div>
    </div>
    <hr>
    <button class="btn btn-lg btn-primary btn-block" id="krysseknapp"
            onclick="kryss(<?php echo $beboer->getId(); ?>)"></button>

    <button class="btn btn-lg btn-primary btn-block" id="cartknapp"
            onclick="addToCart()">Legg til</button>
    
    <button class="btn btn-lg btn-primary btn-block" id="cartkryss"
            onclick="cartkryss(<?php echo $beboer->getId(); ?>)">
    </button>
    
    <br/>
    <h1><a href="javascript:history.back()">TILBAKE</a></h1>
</div>



<?php

require_once(__DIR__ . '/../static/bunn.php');


?>
