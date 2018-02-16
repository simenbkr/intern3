<datalist id="gjester">
    <?php
    foreach ($gjesteliste_dag as $gjest) {
        if ($gjest->getInne() != 0) {
            continue;
        }
        ?>
        <option data-value="<?php echo $gjest->getId(); ?>"
                value="<?php echo $gjest->getNavn(); ?>"></option>
        <?php
    }
    ?></datalist>