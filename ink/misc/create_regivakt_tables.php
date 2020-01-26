<?php


$sql = "CREATE TABLE `regivakt`(
 `id` INT(10) NOT NULL AUTO_INCREMENT ,
`dato` DATE NOT NULL , 
`start_tid` VARCHAR(255) NULL , 
`slutt_tid` VARCHAR(255) NULL , 
`beskrivelse` TEXT NULL , 
`nokkelord` TEXT NULL , 
`antall` INT(10) NOT NULL , 
`status` INT(10) NOT NULL , 
PRIMARY KEY (`id`));";