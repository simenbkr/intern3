<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Intern3.0/Helga-gjest</title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
</head>
<body>
<div class="container fill" align="center">
    <?php if(isset($success) && $success == 1 && $gjesten != null){
        ?>
        <div class="alert alert-success" id="map"><h2>Denne personen er en gjest!</h2>
            <p>Navn: <?php echo $gjesten->getNavn(); ?></p>
            <p>Epost: <?php echo $gjesten->getEpost();?></p>
            <p>Invitert av: <?php echo $gjesten->getVert()->getFulltNavn();?></p>
            <p>Som bor på rom: <?php echo $gjesten->getVert()->getRom()->getNavn();?></p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-danger" id="map"><h2>DENNE PERSONEN ER IKKE EN GJEST!</h2></div>
        <?php
    }
    ?>
</body>
</html>