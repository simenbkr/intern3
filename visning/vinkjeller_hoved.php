<?php
require_once ('topp_vinkjeller.php');
require_once('topp.php');
?>
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
    <div class="container">
        <h1>Vinkjeller » Hovedside</h1>

        <hr>

        <div class="tilbakemelding">
            <?php if (isset($_SESSION['success']) && isset($_SESSION['msg'])) { ?>

                <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $_SESSION['msg']; ?>
                </div>
                <p></p>
                <?php
            } elseif (isset($_SESSION['error']) && isset($_SESSION['msg'])) { ?>
                <div class="alert alert-danger fade in" id="danger" style="display:table; margin: auto; margin-top: 5%">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $_SESSION['msg']; ?>
                </div>
                <p></p>
                <?php
            }
            unset($_SESSION['success']);
            unset($_SESSION['error']);
            unset($_SESSION['msg']);
            ?></div>

        <div class="col-lg-12" style="display:table; margin: auto; margin-top:20%">
            <div id="showgrid">
                <div class="row">
                    <div class="column">
                        <a href="?a=vinkjeller/kryssing/navn"><button class="btn btn-primary btn-huge">Alle (alfabetisk)</button></a>
                    </div>

                    <div class="column">
                        <a href="?a=vinkjeller/kryssing/type"><button class="btn btn-primary btn-huge">Sorter på vintype</button></a>
                    </div>

                    <div class="column">
                        <a href="?a=vinkjeller/kryssing/land"><button class="btn btn-primary btn-huge">Sorter på land</button></a>
                    </div>
                </div>
                <div class="row">
                    <div class="column">
                        <a href="?a=vinkjeller/regler"><button class="btn btn-primary btn-huge">Regler</button></a>
                    </div>


                </div>

            </div>
        </div>
    </div>
<?php
require_once('bunn.php');
?>