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
							<li><a href="<?php echo $cd->getBase(); ?>beboer">Beboer</a></li>
							<li><a href="<?php echo $cd->getBase(); ?>vakt">Vakt</a></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Regi <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $cd->getBase(); ?>regi/oppgave">Oppgaver</a></li>
									<li><a href="<?php echo $cd->getBase(); ?>regi/rapport">Rapporter</a></li>
									<li><a href="<?php echo $cd->getBase(); ?>regi/minregi">Min regi</a></li>
									<li><a href="<?php echo $cd->getBase(); ?>regi/registatus">Registatus</a></li>
								</ul>
							</li>
							<li><a href="<?php echo $cd->getBase(); ?>verv">Verv</a></li>
							<li><a href="<?php echo $cd->getBase(); ?>kryss">Kryss</a></li>
							<li><a href="<?php echo $cd->getBase(); ?>wiki">Wiki</a></li>
							<li><a href="<?php echo $cd->getBase(); ?>utleie">Utleie</a></li>
							<li><a href="<?php echo $cd->getBase(); ?>helga">(Helga)</a></li>
							<li><a href="<?php echo $cd->getBase(); ?>kjeller">(Vinkjeller)</a></li>
<?php

if ($cd->getAktivBruker()->getPerson()->harUtvalgVerv()) {
?>
							<li><a href="<?php echo $cd->getBase(); ?>utvalg">Utvalget</a></li>
<?php
}

?>
						</ul>
						<ul class="nav navbar-nav navbar-right">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $cd->getAktivBruker()->getPerson()->getFulltNavn(); ?> <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $cd->getBase(); ?>profil">Profil</a></li>
<?php

if ($cd->getAktivBruker()->getPerson()->erBeboer()) {
	?>
									<li><a href="<?php echo $cd->getBase(); ?>romskjema">Romskjema (<?php echo $cd->getAktivBruker()->getPerson()->getRomhistorikk()->getAktivtRom()->getNavn(); ?>)</a></li>
									<li><a href="<?php echo $cd->getBase(); ?>rombytte">Rombytte</a></li>
									<li><a href="<?php echo $cd->getBase(); ?>utflytting">Utflytting</a></li>
<?php
}

?>
									<li role="separator" class="divider"></li>
									<li><a href="<?php

if ($cd->getAdminBruker() == null) {
	echo $cd->getBase() . 'logginn/loggut&amp;ref=' . htmlspecialchars($_SERVER['REQUEST_URI']);
}
else {
	echo substr($cd->getBase(), 0, strrpos(rtrim($cd->getBase(), '/'), '/'));
}

?>">Logg ut</a></li>
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
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/romsjef/beboerliste">Beboerliste</a></li>
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/romsjef/nybeboer">Legg til ny beboer</a></li>
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/romsjef/endrebeboer">Endre beboer</a></li>
                </ul>
              </li>
            </ul>

            <ul class="nav navbar-nav">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Regisjef <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/regisjef/arbeid">Arbeid</a></li>
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/regisjef/oppgave">Oppgave</a></li>
                </ul>
              </li>
            </ul>

            <ul class="nav navbar-nav">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Sekretær <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/sekretar/apmandsverv">Åpmandsverv</a></li>
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/sekretar/utvalgsverv">Utvalgsverv</a></li>
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/sekretar/lister">Skrive ut lister</a></li>
                </ul>
              </li>
            </ul>

            <ul class="nav navbar-nav">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Vaktsjef <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/vaktstyring">Vaktstyring</a></li>
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/vaktoversikt">Vaktoversikt</a></li>
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/generer">Generer vaktliste</a></li>
					<li><a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/ukerapport">Ukerapport</a></li>
                </ul>
              </li>
            </ul>

            <ul class="nav navbar-nav">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Kosesjef <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/kosesjef/utleie">Utleie</a></li>
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/kosesjef/krysseliste">Krysseliste for Bodega</a></li>
                </ul>
              </li>
            </ul>

            <ul class="nav navbar-nav">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Husfar <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo $cd->getBase(); ?>utvalg/husfar/viktigedatoer">Viktige datoer</a></li>
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
