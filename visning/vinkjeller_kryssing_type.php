<?php
require_once('topp_vinkjeller.php');
require_once('topp.php');
?>

<div class="container">

    <table class="table table-responsive">
        <?php

        foreach ($typeListe as $typen) {
            /* @var $typen \intern3\Vintype */
            ?>
            <tr>
                <td>
                    <a href="?a=vinkjeller/kryssing/type/<?php echo $typen->getId();?>"><?php echo $typen->getNavn(); ?></a>
                </td>
            </tr>
            <?php

        }

        ?>
    </table>
</div>
<?php
require_once('bunn.php');
?>
