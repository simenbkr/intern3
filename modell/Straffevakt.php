<?php

namespace intern3;

class Straffevakt {

	private $id;
	private $brukerId;
	private $vakttype;

  public static function antallMedBrukerId($brukerId) {
    $st = DB::getDB()->prepare('SELECT count(id) AS antall FROM straffevakt WHERE bruker_id=:brukerId;');
    $st->bindParam(':brukerId', $brukerId);
    $st->execute();
    $res = $st->fetch();
    return $res['antall'];
  }
}
