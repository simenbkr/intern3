<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <style>
        .box {
            margin: 5px;
            padding: 20px;
            width: 60%;
        }
        section {
            border: 5px dotted grey;
            overflow: auto;
            /*  display: inline-block;*/
        }

        .greenBox {
            background-color: lightgreen;
            margin: auto;
        }

        .whiteBox {
            border: 1px solid grey;
        }

        .redBox {
            background-color: pink;
        }

        .blueBox {
            color: white;
            background-color: blue;
        }

        /* The media query should describe the wide version */
        @media (min-width:600px){
            .box {
                float: left;
                width: 20%;
            }
            section {
                border: 5px dotted red;
            }
        }
    </style>
    <meta charset="utf-8">
    <title>Intern3.0</title>
</head>
<body>
<div class="container">
    <?php if(isset($success) && $success == 1 && $gjesten != null){
        ?>
    <div class="box greenBox"><h2>Denne personen er en gjest!</h2>
        <p>Navn: <?php echo $gjesten->getNavn(); ?></p>
        <p>Invitert av: <?php echo $gjesten->getVert()->getFulltNavn();?></p>
        <p>Epost: <?php echo $gjesten->getEpost();?></p>

    </div>
    <?php
} else {
    ?>
    <div class="box redBox"><h2>DENNE PERSONEN ER IKKE EN GJEST!</h2></div>
    <?php
    }
    ?>
</div>
</body>
</html>