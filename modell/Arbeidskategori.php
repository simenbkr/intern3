<?php

namespace intern3;

class Arbeidskategori {
	private $id;
	private $navn;
	private $beskrivelse;
	private $tid_oppretta;

	private $db;

	public function __construct () {
		$this->db = DB::getDB();
	}
	private function init($st)
	{
		$st->execute();
		if($st->rowCount() > 0)
		{
			$rad = $st->fetch();

			$this->id           = $rad['id'];
			$this->navn         = $rad['navn'];
			$this->beskrivelse  = $rad['beskrivelse'];
			$this->tid_oppretta = $rad['tid_oppretta'];
		}
	}
	public static function medId($id)
	{
		$instans = new self();
		$st = $instans->db->prepare('SELECT * FROM arbeidskategori WHERE id=:id');
		$st->bindParam(':id' , $id);

		$instans->init($st);
		return $instans;
	}

	public function getId () {
		return $this->id;
	}

	public function getNavn () {
		return $this->navn;
	}

	public function getBeskrivelse () {
		return $this->beskrivelse;
	}

	public function getTidOppretta () {
		return $this->tid_oppretta;
	}

	public static function ny($navn, $besk = NULL)
	{
		if($besk == '') $besk = NULL;

		$sql = 'INSERT INTO arbeidskategori(navn,beskrivelse) VALUES(:nvn,:besk)';
		$instans = new self();
		$st = $instans->db->prepare($sql);
		$st->bindParam(':nvn', $navn);
		$st->bindParam(':besk', $besk);

		$st->execute();
	}

	public static function fins($navn)
	{
		$instans = new self();
		$st = $instans->db->prepare('SELECT COUNT(*) FROM arbeidskategori WHERE navn = :navn');
		$st->bindParam(":navn",$navn);
		$st->execute();

		return $st->fetchColumn() > 0;
	}

	public function slett($id)
	{
		$instans = new self();
		$st = $instans->db->prepare('DELETE FROM arbeidskategori WHERE id = :id');
		$st->bindParam(":id", $id);
		$st->execute();
	}

	public function endre($id, $navn, $besk)
	{
		$instans = new self();
		$st = $instans->db->prepare('UPDATE arbeidskategori SET navn = :navn, beskrivelse = :beskrivelse WHERE id = :id');
		$st->bindParam(":id", $id);
		$st->bindParam(":navn", $navn);
		$st->bindParam(":beskrivelse", $besk);
		$st->execute();
	}
}

?>
