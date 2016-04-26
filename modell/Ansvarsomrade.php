<?php

namespace intern3;

class Ansvarsomrade
{
	private $id;
	private $navn;
	private $beskrivelse;

	private $db;

	public function __construct ()
	{
		$this->db = new DB();
	}

	public static function medId($id)
	{
		$instans = new self();
		$st = $instans->db->prepare('SELECT * FROM ansvarsomrade WHERE id = :id');
		$st->bindParam(':id', $id);
		$st->execute();

		$instans->init($st);

		return $instans;
	}
	
	private function init($pdo_statement)
	{
		$st = $pdo_statement;
		
		$ansvarsomrade = $st->fetch();

		$this->id          = $ansvarsomrade['id'];
		$this->navn 	   = $ansvarsomrade['navn'];
		$this->beskrivelse = $ansvarsomrade['beskrivelse'];
	}

	public function getId()
	{
		return $this->id;
	}

	public function getNavn()
	{
		return $this->navn;
	}

	public function getBeskrivelse()
	{
		return $this->beskrivelse;
	}	
}

?>
