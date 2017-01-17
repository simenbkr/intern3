<?php

namespace intern3;

class Journal {

    private $ukenr;
    private $fra;
    private $til;
    private $år;

    private $alle_kryss;
    private $ukeskryss;

    public function __construct($ukenr=Null, $år = Null){
        //Default-uke er denne uken, men det er åpent for å lage journal-objekt med andre uker også.
        if(!isset($ukenr)) {
            $this->ukenr = date('w');
            $this->fra = date('Y-m-d', strtotime('-' . (self::getUkenr()-1) . ' days'));
            $this->til = date('Y-m-d', strtotime('+' . (6 - self::getUkenr()) . ' days'));
            $this->alle_kryss = self::getAlleKryss();
        }
        else {
            $this->år = (isset($år) ? $år : date('Y') );
            $this->ukenr = $ukenr;

            $fraogtil = self::getStartSluttDato($this->ukenr,$this->år);
            $this->fra = $fraogtil[0];
            $this->til = $fraogtil[1];

        }
    }

    public static function getStartSluttDato($uke,$år){
        $return = array();
        $now = strtotime("1 January $år",time());
        $dag = date('w',$now);
        $now += ((7*$uke)+1-$dag)*24*3600;
        $return[0] = date('Y-m-d',$now);
        $now += 6*24*3600;
        $return[1] = date('Y-m-d',$now);

        return $return;
    }

    public function getUkenr(){
        return $this->ukenr;
    }

    public function getPeriode(){
        return array(
            'fra' => $this->fra,
            'til' => $this->til
        );
    }

    public function getAlleKryss(){
        $st = DB::getDB()->prepare('SELECT * from krysseliste');
        $st->execute();
        $krysse_array = array();

        while($rad = $st->fetch()){
            $krysse_array[] = $rad['krysseliste'];
        }
        return $krysse_array;
    }

    public function getUkeKryss(){
        $resultatsArray = [];
        $periode = self::getPeriode();
        //TODO fjern test-array!
        /*$periode = array(
            'fra' => date('2015-08-17'),
            'til' => date('2015-08-24')
        );*/
        if(count($this->alle_kryss) > 0){
            foreach($this->alle_kryss as $krysseliste){
                $krysseliste = json_decode($krysseliste,true);

                foreach($krysseliste as $enkelt_kryssing){
                    $tid = date($enkelt_kryssing['tid']);
                    if($tid <= $periode['til'] && $tid >= $periode['fra']){
                        $resultatsArray[] = $enkelt_kryssing;
                    }
                }
            }
        }
        return $resultatsArray;
    }

    public function getVaktHavende($vaktnr,$dato){
        //Returnerer et beboer-objekt med vakthavende.
        $st = DB::getDB()->prepare('SELECT * from vakt WHERE vakttype=:vakttype AND dato=:dato');
        $st->bindParam(':vakttype',$vaktnr);
        $st->bindParam(':dato',$dato);
        $st->execute();
        $vaktrad = $st->fetchColumn();

        return Beboer::medBrukerId($vaktrad['bruker_id']);
    }

    public function getKrysseInfo(){
        $info_arr = array();
        $perioden = self::getPeriode();
        $start = date('Y-m-d',strtotime('-1 week',strtotime($perioden['fra'])));
        $slutt = date('Y-m-d',strtotime('-1 week',strtotime($perioden['til'])));
        //Henter ut siste kryssing/vaktbytte/overføring fra forrige periode.
        $st = DB::getDB()->prepare('SELECT * FROM journal WHERE dato>=:start AND dato <=:slutt ORDER BY dato DESC');
        $st->bindParam(':start', $start);
        $st->bindParam(':slutt', $slutt);
        $st->execute();
        //$forrige = $st->fetchAll()[0];

        //Henter ut hele journalen for nåværende uke.
        $st = DB::getDB()->prepare('SELECT * FROM journal WHERE dato>=:start AND dato<=:slutt ORDER BY dato ASC');
        $st->bindParam(':start',$perioden['fra']);
        $st->bindParam(':slutt',$perioden['til']);
        $st->execute();

        $journalen = $st->fetchAll();

        $endelig_array = array();

        $indeks = 0;
        foreach($journalen as $vakt){
            $denne_vakta = array();
            $vakten = ($vakt['beboer_id'] > 1 ? Beboer::medId($vakt['beboer_id']) : Ansatt::medId(1));
            $denne_vakta['vakthavende'] = $vakten->getFulltNavn();
            $denne_vakta['vaktnr'] = $vakt['vakt'];
            $denne_vakta['dato'] = $vakt['dato'];

            $denne_vakta['ol_mottatt'] = $vakt['ol_mottatt'];
            $denne_vakta['ol_pafyll'] = $vakt['ol_pafyll'];
            $denne_vakta['ol_avlevert'] = $vakt['ol_avlevert'];
            $denne_vakta['ol_utavskap'] = $vakt['ol_utavskap'];
            $denne_vakta['ol_svinn'] = $vakt['ol_mottatt']+$vakt['ol_pafyll']-$vakt['ol_utavskap']-$vakt['ol_avlevert'];

            $denne_vakta['cid_mottatt'] = $vakt['cid_mottatt'];
            $denne_vakta['cid_pafyll'] = $vakt['cid_pafyll'];
            $denne_vakta['cid_avlevert'] = $vakt['cid_avlevert'];
            $denne_vakta['cid_utavskap'] = $vakt['cid_utavskap'];
            $denne_vakta['cid_svinn'] =  $vakt['cid_mottatt']+$vakt['cid_pafyll']-$vakt['cid_utavskap']-$vakt['cid_avlevert'];

            $denne_vakta['carls_mottatt'] = $vakt['carls_mottatt'];
            $denne_vakta['carls_pafyll'] = $vakt['carls_pafyll'];
            $denne_vakta['carls_avlevert'] = $vakt['carls_avlevert'];
            $denne_vakta['carls_utavskap'] = $vakt['carls_utavskap'];
            $denne_vakta['carls_svinn'] =  $vakt['carls_mottatt']+$vakt['carls_pafyll']-$vakt['carls_utavskap']-$vakt['carls_avlevert'];

            $denne_vakta['rikdom_mottatt'] = $vakt['rikdom_mottatt'];
            $denne_vakta['rikdom_pafyll'] = $vakt['rikdom_pafyll'];
            $denne_vakta['rikdom_avlevert'] = $vakt['rikdom_avlevert'];
            $denne_vakta['rikdom_utavskap'] = $vakt['rikdom_utavskap'];
            $denne_vakta['rikdom_svinn'] = $vakt['rikdom_mottatt']+$vakt['rikdom_pafyll']-$vakt['rikdom_utavskap']-$vakt['rikdom_avlevert'];

            $endelig_array[] = $denne_vakta;
        }
        return $endelig_array;
    }
}
?>