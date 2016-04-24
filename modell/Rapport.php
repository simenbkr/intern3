<?php

namespace intern3;

class Rapport {
	private $id;
	private $kvittering_id;
	private $prioritet_id;
	private $feil_id;
	private $oppgave_id;
	private $merknad;
	private $godkjent;
	private $tid_endret;
	private $tid_godkjent;
	private $godkjent_bruker_id;

	private $db;

	//latskapsinstansiering
	private $feil;
	private $oppgave;
	private $prioritet;
	private $kvittering;

	public function __construct () {
		$this->db = DB::getDB();
	}
	private function init($pdo_statement)
	{
		$st = $pdo_statement;
		$st->execute();
		if($st->rowCount() > 0)
		{
			$rad = $st->fetch();

			$this->id                 = $rad['id'];
			$this->kvittering_id      = $rad['kvittering_id'];
			$this->prioritet_id       = $rad['prioritet_id'];
			$this->feil_id            = $rad['feil_id'];
			$this->oppgave_id         = $rad['oppgave_id'];
			$this->merknad            = $rad['merknad'];
			$this->godkjent           = $rad['godkjent'];
			$this->tid_endret         = $rad['tid_endret'];
			$this->tid_godkjent       = $rad['tid_godkjent'];
			$this->godkjent_bruker_id = $rad['godkjent_bruker_id'];

			$this->feil      = null;
			$this->oppgave   = null;
			$this->prioritet = null;
			$this->kvittering= null;
		}
	}
	public static function medId($id)
	{
		$instans = new self();
		$st = $instans->db->prepare('SELECT * FROM rapport WHERE id = :id');
		$st->bindParam(':id' , $id);

		$instans->init($st);
		return $instans;
	}

	public function getId () {
		return $this->id;
	}

	public function getFeilId () {
		return $this->feil_id;
	}

	public function getOppgaveId () {
		return $this->oppgave_id;
	}

	public function getPrioritetId () {
		return $this->prioritet_id;
	}

	public function getGodkjent () {
		return $this->godkjent;
	}

	public function getTidEndret () {
		return $this->tid_endret;
	}

	public function getTidGodkjent () {
		return $this->tid_godkjent;
	}

	public function getGodkjentBrukerId () {
		return $this->godkjent_bruker_id;
	}

	public function getGodkjentBruker()
	{
		return Bruker::medId($this->godkjent_bruker_id);
	}

	public static function setGodkjent($rapporter_id, $godkjent) {
		$instans = new self(); //BUG: Får ikke tak i $this->db, så hva skal vi med $this->db ?
		foreach ($rapporter_id as $rapport_id) {
			$st = $instans->db->prepare('UPDATE rapport SET godkjent=:godkjent,tid_godkjent=NOW(),godkjent_bruker_id=:bruker_id WHERE godkjent=' . (1^$godkjent) . ' AND id = :rapport_id;');
			//Faen ta PDO: Kan ikke skrive "WHERE godkjent=:motsatt".

			$st->bindParam(':godkjent'   , $godkjent);
			$st->bindParam(':rapport_id' , $rapport_id);
			$st->bindParam(':bruker_id'  , $_SESSION['bruker_id']);
			$st->execute();
		}
	}

	/*
	 * Funksjonene nedenfor instansierer variabler ved behov.
	 *
	 * TODO: Kommenter inn igjen når oppgave-modellen er klar.
	 */
	private function lazyinit_oppgave()
	{
		if(!$this->oppgave){
			$this->oppgave = Oppgave::medId($this->oppgave_id);
		}
		return $this->oppgave; //Ole syns dette er fjongt.
	}
	private function lazyinit_feil()
	{
		if(!$this->feil){
			$this->feil = Feil::medId($this->feil_id);
		}
		return $this->feil; //Ole syns dette er fjongt.
	}
	private function lazyinit_prioritet()
	{
		if(!$this->prioritet){
			$this->prioritet = Prioritet::medId($this->prioritet_id);
		}
		return $this->prioritet; //Ole syns dette er fjongt.
	}
	private function lazyinit_kvittering()
	{
		if(!$this->kvittering){
			$this->kvittering = Kvittering::medId($this->kvittering_id);
		}
		return $this->kvittering; //Ole syns dette er fjongt.
	}

	public function getFeil () {
		return $this->lazyinit_feil();
	}
	public function getFeilNavn()
	{
		return $this->lazyinit_feil()->getNavn();
	}

	//et par dustete alias for at polymorfi skal bli lettere
	public function getNavn()
	{
		return $this->getFeilNavn();
	}
	public function getBeskrivelse()
	{
		return $this->getMerknad();
	}

	public function getFeilkategoriNavn()
	{
// 		if($this->oppgave_id){
// 			return $this->lazyinit_oppgave()->getKategoriNavn();
// 		}
		return $this->lazyinit_feil()->getFeilkategori()->getNavn();
	}

	public function getMerknad()
	{
// Er dette nødvendig?
// 		if($this->oppgave_id){
// 			return $this->lazyinit_oppgave()->getMerknad();
// 		}
		return $this->merknad;
	}

	public function getPrioritet () {
		return $this->lazyinit_prioritet();
	}

	public function getKvittering () {
		return $this->lazyinit_kvittering();
	}

	public function getStedId () {
		return $this->lazyinit_kvittering()->getStedId();
	}
	public function getSted () {
		return $this->lazyinit_kvittering()->getSted();
	}

	public function getBrukerId () {
		return $this->lazyinit_kvittering()->getBrukerId();
	}
	public function getBruker() {
		return $this->lazyinit_kvittering()->getBruker();
	}

	public function getTidOppretta() {
		return $this->lazyinit_kvittering()->getTidOppretta();
	}
}

?>
