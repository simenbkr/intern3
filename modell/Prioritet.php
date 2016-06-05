<?php

namespace intern3;

class Prioritet
{
	private $id;
	private $navn;
	private $nummer;
	private $farge;

	private $db;

	public function __construct()
	{
		$this->db = DB::getDB();
	}

	public static function medId($id)
	{
		$instans = new self();
		$st = $instans->db->prepare('SELECT * FROM prioritet WHERE id = :id');
		$st->bindParam(':id' , $id);
		$st->execute();

		$instans->init($st);

		return $instans;
	}

	private function init($pdo_statement)
	{
		$prioritet = $pdo_statement->fetch();

		$this->id     = $prioritet['id'];
		$this->navn   = $prioritet['navn'];
		$this->nummer = $prioritet['nummer'];
		$this->farge  = $prioritet['farge'];
	}

	public function getId()
	{
		return $this->id;
	}

	public function getNavn()
	{
		return $this->navn;
	}

	public function getNummer()
	{
		return $this->nummer;
	}
	public function getFarge () {
		return $this->farge;
	}
}

?>
