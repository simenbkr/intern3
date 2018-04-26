<?php

require_once('topp.php');

$beboer = $cd->getAktivBruker()->getPerson();

if(isset($feil) && count($feil) > 0){
    $st = '';
    foreach ($feil as $pkt) {
        $st .= '		<li>' . $pkt . '</li>' . PHP_EOL;
    }
    $st .= '	</ul>' . PHP_EOL;

    $_SESSION['error'] = 1;
    $_SESSION['msg'] = $st;
}

?>
<div class="col-md-12">
	<h1>Profil</h1>
    <?php require_once ('tilbakemelding.php'); ?>
<?php
/*
if (count($feil) > 0) {
	echo '	<ul style="color: #900;">' . PHP_EOL;
	foreach ($feil as $pkt) {
		echo '		<li>' . $pkt . '</li>' . PHP_EOL;
	}
	echo '	</ul>' . PHP_EOL;
}
*/
if(false){}
else {
	echo '	<p>[ <a href="' . $cd->getBase() . 'beboer/' . $beboer->getId() . '">Se profil</a> ]</p>
';
}

?>
</div>
<div class="col-md-4 col-sm-6">
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<h2>Generell info<input type="hidden" name="endre" value="generell"></h2>
		<table class="table table-bordered">
			<tr>
				<th>Fødselsdato</th>
				<td><input type="text" class="form-control" name="fodselsdato" size="10" value="<?php echo isset($_POST['fodselsdato']) ? $_POST['fodselsdato'] : $beboer->getFodselsdato(); ?>"><br>(åååå-mm-dd)</td>
			</tr>
			<tr>
				<th>Epost</th>
				<td><input type="text" class="form-control" name="epost" value="<?php echo isset($_POST['epost']) ? $_POST['epost'] : $beboer->getEpost(); ?>"></td>
			</tr>
			<tr>
				<th>Telefon</th>
				<td><input type="text" class="form-control" name="telefon" size="12" value="<?php echo isset($_POST['telefon']) ? $_POST['telefon'] : $beboer->getTelefon(); ?>"></td>
			</tr>
			<tr>
				<th>Adresse</th>
				<td><input type="text" class="form-control" name="adresse" value="<?php echo isset($_POST['adresse']) ? $_POST['adresse'] : $beboer->getAdresse(); ?>"></td>
			</tr>
			<tr>
				<th>Postnummer</th>
				<td><input type="text" class="form-control" name="postnummer" size="4" value="<?php echo isset($_POST['postnummer']) ? $_POST['postnummer'] : $beboer->getPostnummer(); ?>"></td>
			</tr>
			<tr>
				<th>Skole</th>
				<td><select name="skole_id" class="form-control">
					<option value="0">- velg -</option>
<?php

$skoleId = isset($_POST['skole_id']) && intern3\Skole::medId($_POST['skole_id']) <> null ? $_POST['skole_id'] : $beboer->getSkoleId();
foreach (intern3\SkoleListe::alle() as $skole) {
	echo '					<option value="' . $skole->getId() . '"';
	if ($skole->getId() == $skoleId) {
		echo ' selected="selected"';
	}
	echo '>' . $skole->getNavn() . '</option>' . PHP_EOL;
}

?>
				</select></td>
			</tr>
			<tr>
				<th>Studie</th>
				<td><select name="studie_id" class="form-control">
					<option value="0">- velg -</option>
<?php

$studieId = isset($_POST['studie_id']) && intern3\Studie::medId($_POST['studie_id']) <> null ? $_POST['studie_id'] : $beboer->getStudieId();
foreach (intern3\StudieListe::alle() as $studie) {
	echo '					<option value="' . $studie->getId() . '"';
	if ($studie->getId() == $studieId) {
		echo ' selected="selected"';
	}
	echo '>' . $studie->getNavn() . '</option>' . PHP_EOL;
}

?>
				</select></td>
			</tr>
			<tr>
				<th>Klassetrinn</th>
				<td><input type="text" name="klassetrinn" class="form-control" size="1" value="<?php echo isset($_POST['klassetrinn']) ? $_POST['klassetrinn'] : $beboer->getKlassetrinn(); ?>"></td>
			</tr>
		</table>
		<p><input type="submit" class="btn btn-primary" value="Lagre"></p>
	</form>
</div>
<div class="col-md-4 col-sm-6">
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<h2>Endre passord<input type="hidden" name="endre" value="passord"></h2>
		<p style="color: #990;">Obs: Det anbefales å bruke et sterkt passord, særlig om man er utvalgsmedlem og har admintilgang. Minstekravet er en lengde på 8 tegn.</p>
		<table class="table table-bordered">
			<tr>
				<th>Nytt passord</th>
				<td><input type="password" name="passord1" class="form-control"></td>
			</tr>
			<tr>
				<th>Gjenta passord</th>
				<td><input type="password" name="passord2" class="form-control"></td>
			</tr>
		</table>
		<p><input type="submit" class="btn btn-primary" value="Lagre"></p>
	</form>
</div>
<div class="col-md-4 col-sm-6">
	<?php if(strlen($beboer->getBilde()) > 0){ ?>
	<img style="width: 200px;" src="profilbilder/<?php echo $beboer->getBilde();?>">
	<?php } ?>
		<form action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="endre" value="bilde">
			<table class="table table-bordered table-responsive">
				<tr>
					<td>Bilde:</td>
					<td><input type="file" class="form-control" name="image"/></td>
				</tr>
				<tr>
					<td></td>
					<td><input class="btn btn-primary" type="submit" value="Legg til"></td>
				</tr>
			</table>
		</form>
</div>
<div class="col-md-4 col-sm-6">
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<h2>Varsler<input type="hidden" name="endre" value="varsler"></h2>
		<table class="table table-bordered">
			<tr>
				<th colspan="2">Send epost ...</th>
			</tr>
			<tr>
				<td>når du har blitt tildelt en vakt</td>
				<td><input type="checkbox"  name="tildeltvakt" value="1" <?php if($epostInst['tildelt'] == 1) { ?> checked="checked"><?php } ?></td>
			</tr>
			<tr>
				<td>når det er 24 timer igjen til å sitte vakt</td>
				<td><input type="checkbox" name="vakt" value="1" <?php if($epostInst['snart_vakt'] == 1) { ?> checked="checked"><?php } ?></td>
			</tr>
			<tr>
				<td>når noen vil bytte eller gi bort en vakt</td>
				<td><input type="checkbox" name="vaktbytte" value="1" <?php if($epostInst['bytte'] == 1) { ?> checked="checked"><?php } ?></td>
			</tr>
			<tr>
				<td>når kosesjef har planlagt et utleie</td>
				<td><input type="checkbox" name="utleie" value="1" <?php if($epostInst['utleie'] == 1) { ?> checked="checked"><?php } ?></td>
			</tr>
			<tr>
				<td>når det er 24 timer igjen til å stå barvakt</td>
				<td><input type="checkbox" name="barvakt" value="1" <?php if($epostInst['barvakt'] == 1) { ?> checked="checked"><?php } ?></td>
			</tr>
		</table>
		<p><input type="submit" class="btn btn-primary" value="Lagre"></p>
	</form>
</div>


<script>
    function sjekk(id) {
        $("#" + id).load("?a=profil/epost/");
    }

    function leggTil(id, group) {
        $("#" + id + " ." + group.split("@")[0]).load("?a=profil/epost/" + group, {"group": group, "id": id});
    }

    function del(id, group) {
        $.ajax({
            url: '?a=profil/epost/' + group,
            type: 'DELETE',
            success: function(result) {
                $("#" + id + " ." + group.split("@"[0])).html(result);
            }
        });
    }

    $(document).ready(function(){
        sjekk('<?php echo $beboer->getId(); ?>')
    });

</script>

<div class="col-md-4 col-sm-6">
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <?php /* @var \intern3\Prefs $prefs */ ?>
        <h2>Preferanser<input type="hidden" name="endre" value="prefs"></h2>
        <table class="table table-bordered">
            <tr>
                <td>Jeg ønsker å stå på krysselista i resepsjonen</td>
                <td><input type="checkbox" name="resepp" value="1" <?php if($prefs->getResepp()) { ?> checked="checked"><?php } ?></td>
            </tr>

            <tr>
                <td>Jeg ønsker å stå på krysselista i vinkjelleren</td>
                <td><input type="checkbox" name="vinkjeller" value="1" <?php if($prefs->getVinkjeller()) { ?> checked="checked"><?php } ?></td>
            </tr>

            <tr>
                <td>Jeg vil ha en pinkode på krysselista</td>
                <td><input type="checkbox" name="pinboo" value="1" <?php if($prefs->harPinkode()) { ?> checked="checked"><?php } ?></td>
            </tr>

            <tr>
                <td>Min pinkode er:</td>
                <td><input type="text" name="pinkode" class="form-control" style="width:75%" value="<?php echo $prefs->getPinkode();?>"</td>
            </tr>

            <tr>
                <td>Min pinkode til vinkjelleren er:</td>
                <td><input type="text" name="vinpin" class="form-control" style="width:75%" value="<?php echo $prefs->getVinPinkode();?>"</td>
            </tr>
            <p>Notat: Pinkode er obligatorisk for vinkjelleren.</p>

        </table>
        <p><input type="submit" class="btn btn-primary" value="Lagre"></p>
    </form>
</div>

<div class="col-md-4 col-sm-6">

    <h3>E-postlister</h3>

    <table class="table table-bordered table-responsive">
        <thead>
        <tr>
            <td>Epost</td>
            <td>SING-ALLE</td>
            <td>SING-SLARV</td>
            <td>Sjekk</td>
        </tr>

        </thead>
        <tbody>
        <tr id="<?php echo $beboer->getId(); ?>">
            <td><?php echo $beboer->getEpost(); ?></td>
            <td class=sing-alle">Laster..</td>
            <td class=sing-slarv">Laster..</td>
            <td>
                <button class="btn btn-danger" onclick="sjekk('<?php echo $beboer->getId(); ?>')">Sjekk</button>
            </td>
        </tr>

        </tbody>
    </table>

</div>

<?php

require_once('bunn.php');

?>
