<?php

namespace intern3;

class Oppgave {
	private $id;
	private $tid_oppretta;
	private $anslag_timer;
	private $ansvarsomrade_id; // trengs bare hvis oppgaven ikke tilhører noen rapport
	private $anslag_personer;
	private $prioritet_id;
	private $navn;
	private $beskrivelse;
	private $godkjent;
	private $tid_godkjent;
	private $godkjent_bruker_id;

	private $db;

	//latskapsinstansiering
	private $pameldte;
	private $arbeidListe; //arbeidListe er flertallsformen av arbeid
	private $arbeidListeBrukerId;

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
			$this->tid_oppretta       = $rad['tid_oppretta'];
			$this->anslag_timer       = $rad['anslag_timer'];
			$this->ansvarsomrade_id   = $rad['ansvarsomrade_id'];
			$this->anslag_personer    = $rad['anslag_personer'];
			$this->prioritet_id       = $rad['prioritet_id'];
			$this->navn               = $rad['navn'];
			$this->beskrivelse        = $rad['beskrivelse'];
			$this->godkjent           = $rad['godkjent'];
			$this->tid_godkjent       = $rad['tid_godkjent'];
			$this->godkjent_bruker_id = $rad['godkjent_bruker_id'];

			$this->brukere = null;
		}
	}
	public static function medId($id)
	{
		$instans = new self();
		$st = $instans->db->prepare('SELECT * FROM oppgave WHERE id = :id');
		$st->bindParam(":id" , $id);

		$instans->init($st);
		return $instans;
	}

	public function getId () {
		return $this->id;
	}

	public function getTidOppretta () {
		return $this->tid_oppretta;
	}

	public function getAnslagTimer () {
		return $this->anslag_timer;
	}

	public function getAnsvarsomradeId () {
		return $this->ansvarsomrade_id;
	}

	public function getAnslagPersoner () {
		return $this->anslag_personer;
	}

	public function getNavn () {
		return $this->navn;
	}

	public function getBeskrivelse () {
		return $this->beskrivelse;
	}

	public function getGodkjent () {
		return $this->godkjent;
	}

	public function getTidGodkjent () {
		return $this->tid_godkjent;
	}

	public function getGodkjentBrukerId () {
		return $this->godkjent_bruker_id;
	}

	public function getGodkjentBruker()
	{
		return Person::medId($this->godkjent_bruker_id);
	}

	public function getPrioritet()
	{
		return Prioritet::medId($this->getPrioritetId());
	}

	public static function setGodkjent($oppgaver_id, $godkjent) {
		$instans = new self(); //BUG: Får ikke tak i $this->db, så hva skal vi med $this->db ?
		foreach ($oppgaver_id as $oppgave_id) {
			$st = $instans->db->prepare('UPDATE oppgave SET godkjent=:godkjent,tid_godkjent=NOW(),godkjent_bruker_id=:bruker_id WHERE godkjent=' . (1^$godkjent) . ' AND id = :rapport_id;');
			//Faen ta PDO: Kan ikke skrive "WHERE godkjent=:motsatt".

			$st->bindParam(':godkjent'   , $godkjent);
			$st->bindParam(':rapport_id' , $rapport_id);
			$st->bindParam(":bruker_id"  , $_SESSION['bruker_id']);
			$st->execute();
		}
	}

	public function setBrukerPameldt ($bruker_id, $pameldt) {
		$instans = new self();
		$sql = ($pameldt) ?
			'INSERT INTO oppgave_bruker(bruker_id,oppgave_id) VALUES(:brk,:oppg)'
		:
			'DELETE FROM oppgave_bruker WHERE bruker_id=:brk AND oppgave_id=:oppg'
		;
		$st = $instans->db->prepare($sql);
		$st->bindParam(':brk', $bruker_id);
		$st->bindParam(':oppg', $this->id);
		$st->execute();
	}

	/*
	 * Funksjonene nedenfor instansierer variabler ved behov.
	 */
	public function getPameldte () {
		if(!$this->pameldte){
			// lazyinit
			$this->pameldte = PersonListe::medOppgaveId($this->id);
		}
		return $this->pameldte;
	}

	public function getArbeidListe () {
		if(!$this->arbeidListe){
			// lazyinit
			$this->arbeidListe = ArbeidListe::medOppgaveId($this->id);
		}
		return $this->arbeidListe;
	}

	public function getArbeidListeBrukerId ($bruker_id) {
		if(!$this->arbeidListeBrukerId){
			// lazyinit
			$this->arbeidListeBrukerId = ArbeidListe::medBrukerIdOppgIdVelg($bruker_id, $this->id, ArbeidPolymorfkategori::OPPG);
		}
		return $this->arbeidListeBrukerId;
	}

	public function erBrukerPameldt ($bruker_id) {
		$this->getPameldte();
		foreach($this->pameldte as $bruker){
			if($bruker->getId() == $bruker_id){
				return true;
			}
		}
		return false;
	}
	public function getPrioritetId () {
		if(!$this->prioritet_id){
			// lazyinit
			$pri = array();
			foreach (RapportListe::medOppgaveId($this->id) as $Rapport) {
			$pri[$Rapport->getPrioritetId()] = $Rapport->getPrioritet().getNummer();
			}
			arsort($pri , SORT_NUMERIC);
			$pri = array_keys($pri);
			$this->prioritet_id = $pri[0];
		}
		return $this->prioritet_id;
	}
}

?>