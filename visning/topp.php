<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Intern3.0</title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/stilark.css">
		<script src="js/jquery-2.1.4.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jquery-ui.js"></script>
		<link rel="stylesheet" href="css/jquery-ui.css">
		<script>
var formaterDatovelger = function() {
  $('.datepicker').datepicker({dateFormat: "yy-mm-dd"});
};
$(formaterDatovelger);
		</script>
	</head>
	<body>
		<div id="ramme">

<?php
if (!isset($skjulMeny)) {
	/* Meny start */
	?>

			<nav class="navbar navbar-default" id="topp">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="?a=diverse">intern.singsaker.no</a>
					</div>
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
<?php

$menyvalg = array(
		'beboer' => 'Beboer',
		'vakt' => 'Vakt',
		'regi' => 'Regi',
		'verv' => 'Verv',
		'kryss' => 'Kryss',
		'wiki' => 'Wiki',
		'utleie' => 'Utleie',
		'helga' => '(Helga)',
		'kjeller' => '(Vinkjeller)'
);

$forsteArg = $cd->getAktueltArg();

foreach ($menyvalg as $adr => $navn) {
	if ($adr == $forsteArg) {
		?>
							<li class="active"><a href="?a=<?php echo $adr; ?>"><?php echo $navn; ?> <span class="sr-only">(du er her)</span></a></li>
<?php
	}
	else {
		?>
							<li><a href="?a=<?php echo $adr; ?>"><?php echo $navn; ?></a></li>
<?php
	}
}

?>
<?php

if ($cd->getAktivBruker()->getPerson()->harUtvalgVerv()) {
?>
							<li><a href="?a=utvalg">Utvalget</a></li>
<?php
}

?>
						</ul>
						<ul class="nav navbar-nav navbar-right">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $cd->getAktivBruker()->getPerson()->getFulltNavn(); ?> <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="?a=profil">Profil</a></li>
<?php

if ($cd->getAktivBruker()->getPerson()->erBeboer()) {
	?>
									<li><a href="?a=romskjema">Romskjema (<?php echo $cd->getAktivBruker()->getPerson()->getRomhistorikk()->getAktivtRom()->getNavn(); ?>)</a></li>
									<li><a href="?a=rombytte">Rombytte</a></li>
									<li><a href="?a=utflytting">Utflytting</a></li>
<?php
}

?>
									<li role="separator" class="divider"></li>
									<li><a href="?a=logginn/loggut&amp;ref=<?php echo $_SERVER['REQUEST_URI']; ?>">Logg ut</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
<?php
	/* Meny slutt */
}
?>

<?php
if (isset($visUtvalgMeny)) {
/* Utvalgmeny start */
?>

        <div class="container-fluid">
          <div class="navbar-header">
            <a class="navbar-brand" href="?a=utvalg">Utvalget</a>
            <ul class="nav navbar-nav">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Romsjef <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="?a=utvalg/romsjef/beboerliste">Beboerliste</a></li>
                  <li><a href="?a=utvalg/romsjef/nybeboer">Legg til ny beboer</a></li>
                  <li><a href="?a=utvalg/romsjef/lister">Skrive ut lister</a></li>
                </ul>
              </li>
            </ul>

            <ul class="nav navbar-nav">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Regisjef <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="?a=utvalg/regisjef">Regiportal</a></li>
                  <li><a href="?a=utvalg/regisjef">#</a></li>
                  <li><a href="?a=utvalg/regisjef">#</a></li>
                </ul>
              </li>
            </ul>

            <ul class="nav navbar-nav">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Sekretær <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="?a=utvalg/sekretar/apmandsverv">Åpmandsverv</a></li>
                  <li><a href="?a=utvalg/sekretar/utvalgsverv">Utvalgsverv</a></li>
                  <li><a href="?a=utvalg/sekretar/lister">Skrive ut lister</a></li>
                </ul>
              </li>
            </ul>

            <ul class="nav navbar-nav">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Vaktsjef <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="?a=utvalg/vaktsjef/vaktstyring">Vaktstyring</a></li>
                  <li><a href="?a=utvalg/vaktsjef/vaktoversikt">Vaktoversikt</a></li>
                  <li><a href="?a=utvalg/vaktsjef/generer">Generer vaktliste</a></li>
                </ul>
              </li>
            </ul>

            <ul class="nav navbar-nav">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Kosesjef <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="?a=utvalg/kosesjef/utleie">Utleie</a></li>
                  <li><a href="?a=utvalg/kosesjef/krysseliste">Krysseliste for Bodega</a></li>
                </ul>
              </li>
            </ul>

            <ul class="nav navbar-nav">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Husfar <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="?a=utvalg/husfar/viktigedatoer">Viktige datoer</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>

<?php
/* Utvalgmeny slutt */
}
?>
			</nav>

		<div id="innhold">

      <!-- innhold -->
