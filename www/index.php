<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Intern3.0</title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="js/jquery-2.1.4.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
	</head>
	<body>
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">Intern3.0</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="active"><a href="?s=diverse">Diverse <span class="sr-only">(current)</span></a></li>
						<li><a href="?s=beboer">Beboere</a></li>
						<li><a href="?s=vakt">Vakt</a></li>
						<li><a href="?s=regi">Regi</a></li>
						<li><a href="?s=verv">Verv</a></li>
						<li><a href="?s=kryss">Kryss</a></li>
						<li><a href="?s=wiki">Wiki</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Navn Navnesen <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="?s=profil">Profil</a></li>
								<li><a href="?s=utflytting">Utflytting</a></li>
								<li role="separator" class="divider"></li>
								<li><a href="?s=logginn/loggut">Logg ut</a></li>
							</ul>
						</li>
					</ul>
					<!--
					<form class="navbar-form navbar-left" role="search">
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Search">
						</div>
						<button type="submit" class="btn btn-default">Submit</button>
					</form>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#">Link</a></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="#">Action</a></li>
								<li><a href="#">Another action</a></li>
								<li><a href="#">Something else here</a></li>
								<li role="separator" class="divider"></li>
								<li><a href="#">Separated link</a></li>
							</ul>
						</li>
					</ul>
					-->
				</div>
			</div>
		</nav>
<?php
$denneUka = @date('W');
$detteAret = @date('Y');
	foreach (range($denneUka, $denneUka > 26 ? 52 : 26) as $uke){
?>
		<table class="table-bordered table">
				<tr>
					<th>Uke <?php echo $uke; ?></th>
					<th>Mandag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week"));?></th>
					<th>Tirsdag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week +1 day"));?></th>
					<th>Onsdag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week +2 day"));?></th>
					<th>Torsdag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week +3 day"));?></th>
					<th>Fredag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week +4 day"));?></th>
					<th>Lørdag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week +5 day"));?></th>
					<th>Søndag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week +6 day"));?></th>
				</tr>
<?php
	foreach (range(1,4) as $vakttype){
?>
				<tr>
					<td><?php echo $vakttype; ?>. vakt</td>
					<td>Gauder</td>
					<td>Gauder</td>
					<td>Gauder</td>
					<td>Gauder</td>
					<td>Gauder</td>
					<td>Gauder</td>
					<td>Gauder</td>
				</tr>
<?php
	}
	?>
	</table>
	<?php
	}
?>
	</body>
</html>
