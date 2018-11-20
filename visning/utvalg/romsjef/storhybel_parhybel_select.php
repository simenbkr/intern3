<link rel="stylesheet" href="css/chosen.min.css">
<script src="js/chosen.jquery.min.js"></script>

<script>
    $(document).ready(function () {
        $(".chosen").chosen({
            max_selected_options: 2
        });


    });
</script>



<select class="chosen" multiple="true" name="select[]">

    <?php foreach ($beboerliste as $beboer) {
        /* @var \intern3\Beboer $beboer */ ?>

        <option value="<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></option>

        <?php
    }
    ?>

</select>