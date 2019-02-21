<?php

/*
 * POST-skript for å håndtere søknader fra studenterhjem.singsaker.no
 */
define('SHARED_SECRET', 'test');

if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {

    header('Location: https://studenterhjem.singsaker.no/soknad.html');
    exit();
}

if (isset($_FILES['uploads']) && $_FILES['image']['size'] > 0) {
    $gyldige_extensions = array("jpeg", "jpg", "png", "gif");
    $file_ext = strtolower(end(explode('.', $_FILES['uploads']['name'])));

    if(in_array($file_ext, $gyldige_extensions)) {

        $new_name = md5(random_bytes(20)) . $file_ext;
        $path = '/var/www/studenterhjem.singsaker.no/www/uploads/' . $new_name;
        if (!move_uploaded_file($_FILES['uploads']['tmp_name'], $path)) {
            Throw new \RuntimeException("dafuq");
        }
        chmod($path, 0644);
        $bilde = array('bilde' => 'https://studenterhjem.singsaker.no/uploads/' . $new_name);
    }
}

$url = 'https://intern.singsaker.no/?a=extern/soknad';
$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$auth_arr = array('secret' => SHARED_SECRET);

$postStr = http_build_query(array_merge($post, $auth_arr, $bilde));
$options = array(
    'http' => array(
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postStr
    )
);

$streamContext = stream_context_create($options);
$result = file_get_contents($url, false, $streamContext);

?>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Singsaker Studenterhjem</title>

    <!-- Bootstrap -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!-- menylinje -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" id="menybar">
    <div class="container">
        <!-- brand(link til forside) og toogle-button ved kollapset meny (mobilvisning) -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.html">Singsaker Studenterhjem</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li>
                    <a href="fasiliteter.html">Hybler og fasiliteter</a>
                </li>
                <li>
                    <a href="utleie.html">Utleie av lokaler</a>
                </li>
                <li>
                    <a href="drift.html">Husets drift</a>
                </li>
                <li>
                    <a href="#" class="active" id="applynow">SØK NÅ</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>

<!-- Sideinnhold -->
<div class="container">
    <div class="col-lg-12 text-center" id="apply-paragraph">
        <h2>Kvittering</h2>

        <hr>

        <p>
            Navn: <?php echo $post['name']; ?><br/>
            Epost: <?php echo $post['email']; ?><br/>
            Telefon: <?php echo $post['phone']; ?><br/>
            Fødselsår: <?php echo $post['birthyear']; ?><br/>
            Studie: <?php echo $post['studyyear']; ?>. på <?php echo $post['studyfield']; ?><br/>
            Fagbrev: <?php echo $post['fagbrev']; ?><br/>
            Kompetanse: <?php echo $post['kompetanse']; ?><br/>
            Kjenner fra før: <?php echo $post['kjennskap']; ?><br/>

            Søknadstekst: <br/>
            <?php echo $post['personalletter']; ?>
        </p>

    </div>
    <hr>


</div> <!-- /container -->

<!-- Footer -->
<footer>
    <div class="row" id="footer-row">
        <p class="lead p-footer">
            Singsaker Studenterhjem - Rogerts gate 1, 7052 Trondheim - 73 89 31 30 - utvalget@singsaker.no
        </P>
        <p class="lead p-footer">
            Besøk også <a href="http://sommerhotell.singsaker.no/?welcome">Singsaker Sommerhotell</a> sine nettsider.
        <p>
        <div class="icon-group">
                <span>
                    <a href="https://www.facebook.com/pages/Singsaker-Studenterhjem/136202819779146">
                        <img class="icon-facebook" src="../files/facebook.png">
                    </a>
                </span>
            <span>
                    <a href="http://websta.me/tag/singsakerstudenterhjem">
                        <img class="icon-image" src="../files/instagram.png">
                    </a>
                </span>
        </div>
    </div>

</footer>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../js/bootstrap.min.js"></script>
<!-- Contact Form JavaScript -->
<!--    <script src="js/jqBootstrapValidation.js"></script>
    <script src="js/contact_me.js"></script>-->
</body>
</html>
