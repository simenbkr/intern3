<?php

namespace intern3;

class Feilkategori
{
	private $id;
	private $navn;
	private $prioritet;
	private $beskrivelse;
	private $feilListe;

	private $db;

	public function __construct()
	{
		$this->db = DB::getDB();
	}

	public static function medId($id)
	{
		$instans = new self();
		$st = $instans->db->prepare('SELECT * FROM feilkategori WHERE id = :id');
		$st->bindParam(':id' , $id);
		$st->execute();

		$instans->init($st);

		return $instans;
	}
	
	private function init($pdo_statement)
	{
		$st = $pdo_statement;
		
		$feilkategori = $st->fetch();

		$this->id          = $feilkategori['id'];
		$this->navn        = $feilkategori['navn'];
		$this->prioritet   = Prioritet::medId($feilkategori['prioritet_id']);
		$this->beskrivelse = $feilkategori['beskrivelse'];
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

	public function getBeskrivelse()
	{
		return $this->beskrivelse;
	}	

	public function getFeilListe()
	{
		return FeilListe::medFeilkategori($this->getId());
	}
}

?>
