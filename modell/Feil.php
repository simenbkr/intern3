<?php

namespace intern3;

class Feil
{
	private $id;
	private $navn;
	private $prioritet;
	private $beskrivelse;
	private $feilkategori_id;

	private $db;

	public function __construct()
	{
		$this->db = DB::getDB();
	}

	public static function medId($id)
	{
		$instans = new self();
		$st = $instans->db->prepare('SELECT * FROM feil WHERE id = :id');
		$st->bindParam(':id' , $id);
		$st->execute();

		$instans->init($st);

		return $instans;
	}

	private function init($pdo_statement)
	{
		$st = $pdo_statement;

		$feil = $st->fetch();

		$this->id              = $feil['id'];
		$this->navn            = $feil['navn'];
		$this->prioritet       = Prioritet::medId($feil['prioritet_id']);
		$this->beskrivelse     = $feil['beskrivelse'];
		$this->feilkategori_id = $feil['feilkategori_id'];
	}

	public function getId()
	{
		return $this->id;
	}

	public function getNavn()
	{
		return $this->navn;
	}

	public function getPrioritet()
	{
		return $this->prioritet;
	}

	public function getFeilkategoriId()
	{
		return $this->feilkategori_id;
	}

	public function getBeskrivelse()
	{
		return $this->beskrivelse;
	}

	public function getLikeUlosteRapporterListe($rom)
	{
		return LikeUlosteRapporterListe::medStedOgFeil($rom->getId(),$this->id);
	}

	public function getFeilkategori ()
	{
		return Feilkategori::medId($this->feilkategori_id);
	}
	public function HarAnsvarsomrade ($id) {
	$st = $this->db->prepare('SELECT feil_id FROM ansvarsomrade_feil WHERE ansvarsomrade_id = :ansvarsomrade_id AND feil_id = :feil_id;');
	$st->bindParam(':ansvarsomrade_id' , $id);
	$st->bindParam(':feil_id' , $this->getId());
	$st->execute();
	return count($st->fetchAll()) > 0 ? true : false;
	}
}

?>

