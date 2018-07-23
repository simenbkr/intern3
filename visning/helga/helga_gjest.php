<?php

/* @var $gjest \intern3\HelgaGjest */

echo "<p>Navn: " . $gjest->getNavn() . "</p>";
echo "<p>Vert: " . $gjest->getVert()->getFulltNavn() . "</p>";
echo "<p>Vertens rom: " . $gjest->getVert()->getRom()->getNavn() . "</p>";