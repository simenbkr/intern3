<br/>
<hr>
<table class="table table-bordered">
    <tr>
        <th>Dato</th>
        <th>Vaktnr.</th>
        <th>Vakthavende</th>
        <th>Øl</th>
        <th></th>
        <th></th>
        <th></th>
        <th>Carlsberg</th>
        <th></th>
        <th></th>
        <th></th>
        <th>Cider</th>
        <th></th>
        <th></th>
        <th></th>
        <th>Rikdom</th>
        <th></th>
        <th></th>
        <th></th>
    </tr>


    <?php

    foreach ($journal as $vakt) {
        $datoen = substr($vakt['dato'], 0, 10);

        echo "<tr>";
        echo "<td>$datoen</td>";
        echo "<td>$vakt[vaktnr]</td>";
        echo "<td>$vakt[vakthavende]</td>";

        echo "<td>Mottatt<br/>$vakt[ol_mottatt]</td>";
        echo $vakt['ol_pafyll'] > 0 ? "<td><b>Påfyll<br/>$vakt[ol_pafyll]</b></td>" : "<td>Påfyll<br/>$vakt[ol_pafyll]</td>";
        echo "<td>Avlevert<br/>$vakt[ol_avlevert]</td>";
        echo $vakt['ol_svinn'] > 0 ? "<td><b>Svinn<br/>$vakt[ol_svinn]</b></td>" : "<td>Svinn<br/>$vakt[ol_svinn]</td>";

        echo "<td>Mottatt<br/>$vakt[carls_mottatt]</td>";
        echo $vakt['carls_pafyll'] > 0 ? "<td><b>Påfyll<br/>$vakt[carls_pafyll]</b></td>" : "<td>Påfyll<br/>$vakt[carls_pafyll]</td>";
        echo "<td>Avlevert<br/>$vakt[carls_avlevert]</td>";
        echo $vakt['carls_svinn'] > 0 ? "<td><b>Svinn<br/>$vakt[carls_svinn]</b></td>" : "<td>Svinn<br/>$vakt[carls_svinn]</td>";

        echo "<td>Mottatt<br/>$vakt[cid_mottatt]</td>";
        echo $vakt['cid_pafyll'] > 0 ? "<td><b>Påfyll<br/>$vakt[cid_pafyll]</b></td>" : "<td>Påfyll<br/>$vakt[cid_pafyll]</td>";
        echo "<td>Avlevert<br/>$vakt[cid_avlevert]</td>";
        echo $vakt['cid_svinn'] > 0 ? "<td><b>Svinn<br/>$vakt[cid_svinn]</b></td>" : "<td>Svinn<br/>$vakt[cid_svinn]</td>";

        echo "<td>Mottatt<br/>$vakt[rikdom_mottatt]</td>";
        echo $vakt['rikdom_pafyll'] > 0 ? "<td><b>Påfyll<br/>$vakt[rikdom_pafyll]</b></td>" : "<td>Påfyll<br/>$vakt[rikdom_pafyll]</td>";
        echo "<td>Avlevert<br/>$vakt[rikdom_avlevert]</td>";
        echo $vakt['rikdom_svinn'] > 0 ? "<td><b>Svinn<br/>$vakt[rikdom_svinn]</b></td>" : "<td>Svinn<br/>$vakt[rikdom_svinn]</td>";

        echo "</tr>";

    }

    ?>
</table>
<br/>
<hr>
</div>