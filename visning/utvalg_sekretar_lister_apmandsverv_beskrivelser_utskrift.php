<h1>Åpmandsverv - beskrivelser. Skrevet ut <?php echo date('Y-m-d');?>.</h1>


            <?php foreach($vervene as $verv){ ?>
                <b><u><?php echo $verv->getNavn();?></u></b><br/><br/>
                <?php echo $verv->getBeskrivelse();?>
                <br/><br/><br/><br/>


                <?php
            }
            ?>