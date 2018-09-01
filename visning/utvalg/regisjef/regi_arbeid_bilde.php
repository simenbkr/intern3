<?php

/* @var \intern3\Arbeid $arbeid */

foreach($arbeid->getArbeidBilder() as $arbeidbilde){
    /* @var \intern3\ArbeidBilde $arbeidbilde */ ?>

    <img class="img-responsive" src="regibilder/<?php echo $arbeidbilde->getFilnavn(); ?>">
    <hr>

<?php

}