<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<script type="text/javascript" src="js/dataTables.js"></script>

<script>

    $(document).ready(function () {
        var table = $('#tabellen').DataTable({
            "paging": true,
            "searching": true,
            "order": [[0, "desc"]]
        });
    });

</script>

<table class="table table-bordered table-condensed" id="tabellen">

    <thead>
    <tr>
        <th>Tid</th>
        <th>Navn</th>
        <th>Epost</th>
        <th>Fødselsår</th>
        <th>Fagbrev</th>
        <th>Kjenner</th>
    </tr>
    </thead>

    <tbody>

    <?php foreach ($soknader as $soknad) {
        /* @var \intern3\Soknad $soknad */
        ?>
        <tr>
            <td><?php echo $soknad->getInnsendt(); ?></td>
            <td>
                <a href="?a=utvalg/romsjef/soknad/id/<?php echo $soknad->getId(); ?>"><?php echo $soknad->getNavn(); ?></a>
            </td>
            <td><?php echo $soknad->getEpost(); ?></td>
            <td><?php echo $soknad->getFodselsar(); ?></td>
            <td><?php echo $soknad->getFagbrev() ? 'Ja' : 'Nei'; ?></td>
            <td><?php echo $soknad->getKjenner(); ?></td>

        </tr>
    <?php }
      ?>


    </tbody>
</table>