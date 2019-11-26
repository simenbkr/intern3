<?php


namespace intern3;


class UtvalgFordelingsCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $dok = new Visning($this->cd);
            $dok->vis('utvalg/fordeling.php');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $start = $post['start'];
            $slutt = $post['slutt'];
            $ansattfri = $post['fri'];
            $minimum_regi = $post['min_regi'];
            $minimum_full = $post['min_full'];
            $minimum_gauder = $post['min_gauder'];
            $max_regi = $post['max_regi'];
            $max_full = $post['max_full'];
            $max_gauder = $post['max_gauder'];
            $fridager = $post['fridager'];

            if (empty($ansattfri)) {
                $ansattfri = 0;
            }

            if (empty($minimum_regi)) {
                $minimum_regi = 10;
            }

            if (empty($minimum_full)) {
                $minimum_full = 10;
            }

            if (empty($minimum_gauder)) {
                $minimum_gauder = 20;
            }

            if (empty($max_regi)) {
                $max_regi = 40;
            }

            if (empty($max_full)) {
                $max_full = 40;
            }

            if (empty($max_gauder)) {
                $max_gauder = 30;
            }

            $days = floor(
                (strtotime($slutt) - strtotime($start)) / (60 * 60 * 24)
            );

            $vakt_estimate = intval(floor($days * 4 - ($days * 5 / 7))) + intval($ansattfri) - intval($fridager);

            $valid_combos = array(); // array( array(halv/halv, fullv, fullr) .. )
            $semester = Funk::generateSemesterString(date('Y-m-d', strtotime($slutt)));

            if (explode('-', $semester)[0] == 'host') {
                $fullvakt = Rolle::medNavn('Full vakt')->getVakterH();
                $vanlig = Rolle::medNavn('Halv vakt/regi')->getVakterH();
            } else {
                $fullvakt = Rolle::medNavn('Full vakt')->getVakterV();
                $vanlig = Rolle::medNavn('Halv vakt/regi')->getVakterH();
            }

            $beboer_count = 110;
            for ($full_n = $minimum_full; $full_n <= $max_full; $full_n++) {

                for ($halv_n = $minimum_gauder; $halv_n + $full_n < $beboer_count - $minimum_regi; $halv_n++) {

                    $cand_vakter = $full_n * $fullvakt + $vanlig * $halv_n;

                    if ($cand_vakter >= $vakt_estimate &&
                        $cand_vakter - intval(($full_n + $halv_n) * 0.5) < $vakt_estimate) {

                        $valid_combos[] =
                            array(
                                $halv_n,
                                $full_n,
                                $beboer_count - $halv_n - $full_n,
                                $cand_vakter,
                                18 * $halv_n + 48 * ($beboer_count - $full_n - $halv_n)
                            );
                    }
                }
            }

            uasort($valid_combos, function ($a, $b) {
                if ($a[4] < $b[4]) {
                    return 1;
                }
                if ($a[4] > $b[4]) {
                    return -1;
                }
                return 0;
            });

            $num = count($valid_combos);
            $valid_combos = array_merge(array(
                "Denne perioden har ca {$vakt_estimate} vakter. Viser gyldige kombinasjoner 
            som gir minst {$vakt_estimate} vakter, sortert i rekkefølge som maksimerer regitimer etter krav gitt. 
            Det følger {$num} gyldige kombinasjoner. Tips: Strengere krav gir færre kombinasjoner."
            ), $valid_combos);

            echo json_encode($valid_combos, true);

        }

    }

}