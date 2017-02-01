<?php

namespace intern3;

class KryssCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $krysselisteListe = Krysseliste::medBeboerId($this->cd->getAktivBruker()->getPerson()->getId());
        $sumKryss = array();
        $transaksjoner = array();
        $ukedager = array(
            date('D', strtotime('this monday')) => 0,
            date('D', strtotime('this tuesday')) => 0,
            date('D', strtotime('this wednesday')) => 0,
            date('D', strtotime('this thursday')) => 0,
            date('D', strtotime('this friday')) => 0,
            date('D', strtotime('this saturday')) => 0,
            date('D', strtotime('this sunday')) => 0
        );
        foreach ($krysselisteListe as $krysseliste) {
            $antall = 0;
            foreach ($krysseliste->getKryssListe() as $kryss) {
                $trans = $kryss;
                $trans->drikke = $krysseliste->getDrikke()->getNavn();
                $transaksjoner[] = $trans;
                $antall += $kryss->antall;
                $ukedager[date('D', strtotime($kryss->tid))] += $kryss->antall;
            }
            $sumKryss[$krysseliste->getDrikke()->getNavn()] = $antall;
        }
        uasort($transaksjoner, function ($a, $b) {
            if ($a->tid == $b->tid) {
                return 0;
            }
            return ($a->tid < $b->tid) ? 1 : -1;
        });
        $sum = array_sum($ukedager);
        if ($sum == 0) {
            foreach ($ukedager as $dag => $antall) {
                $ukedager[$dag] = 0;
            }
        } else {
            foreach ($ukedager as $dag => $antall) {
                $ukedager[$dag] = round(($antall / $sum) * 100);
            }
        }

        //$mndkryss = Krysseliste::getKryssByMonth($this->cd->getAktivBruker()->getPerson()->getId());
        $mndkryss = Krysseliste::getAllIkkeFakturertBeboer($this->cd->getAktivBruker()->getPerson()->getId());
        $vinkryss = Vinkryss::getAlleIkkeFakturertByBeboerId($this->cd->getAktivBruker()->getPerson()->getId());
        $ikke_fakturert = Vinkryss::getAlleIkkeFakturertByBeboerId($this->cd->getAktivBruker()->getPerson()->getId());

        $vin_array = array();
        foreach ($ikke_fakturert as $vin_kryss) {
            if (!isset($vin_array[$vin_kryss->getVinId()]) || $vin_array[$vin_kryss->getVinId()] == null) {
                $vin_array[$vin_kryss->getVinId()] = array('kostnad' => round($vin_kryss->getKostnad() * $vin_kryss->getVin()->getAvanse(), 2),
                    'antall' => round($vin_kryss->getAntall(), 2),
                    'aktuell_vin' => $vin_kryss->getVin());
            } else {
                $vin_array[$vin_kryss->getVinId()]['kostnad'] += round($vin_kryss->getKostnad() * $vin_kryss->getVin()->getAvanse(), 2);
                $vin_array[$vin_kryss->getVinId()]['antall'] += round($vin_kryss->getAntall(), 2);
            }
        }

        $fakturert = Vinkryss::getAlleFakturertByBeboerId($this->cd->getAktivBruker()->getPerson()->getId());
        $alle_vin_kryss = $vin_array;
        foreach ($fakturert as $vin_kryss) {
            if (!isset($alle_vin_kryss[$vin_kryss->getVinId()]) || $alle_vin_kryss[$vin_kryss->getVinId()] == null) {
                $alle_vin_kryss[$vin_kryss->getVinId()] = array('kostnad' => round($vin_kryss->getKostnad() * $vin_kryss->getVin()->getAvanse(), 2),
                    'antall' => round($vin_kryss->getAntall(), 2),
                    'aktuell_vin' => $vin_kryss->getVin());
            }
            else {
                $alle_vin_kryss[$vin_kryss->getVinId()]['kostnad'] += round($vin_kryss->getKostnad() * $vin_kryss->getVin()->getAvanse(), 2);
                $alle_vin_kryss[$vin_kryss->getVinId()]['antall'] += round($vin_kryss->getAntall(), 2);
            }
        }


        $dok = new Visning($this->cd);
        $dok->set('mndkryss', $mndkryss);
        $dok->set('vinkryss', $vinkryss);
        $dok->set('vin_array', $vin_array);
        $dok->set('vin_totalt', $alle_vin_kryss);
        //$dok->set('datoen',$first);
        //$dok->set('thisMonth',$thisMonth);
        $dok->set('sumKryss', $sumKryss);
        $dok->set('transaksjoner', $transaksjoner);
        $dok->set('ukedager', $ukedager);
        $dok->vis('kryss_historikk.php');
    }
}

?>
