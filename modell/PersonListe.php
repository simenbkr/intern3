<?php

namespace intern3;

class PersonListe extends Liste {

  public static function alle ($Sideinndeling = null) {
  return self::listeFraSql('Person::medId' , 'SELECT id FROM person ORDER BY fornavn ASC,etternavn DESC,id DESC' . $Grense . ';' , array() , $Sideinndeling);
  }
  // public static function medOppgaveId($oppgave_id , $Sideinndeling = null) {
  //   $sql = 'SELECT person.id FROM person, oppgave_bruker WHERE oppgave.id=oppgave_bruker.oppgave_id AND oppgave_bruker.bruker_id=:brk_id ORDER BY godkjent ASC,tid_oppretta DESC,id DESC;';
  //   return self::listeFraSql('Oppgave::medId' , $sql , array(':brk_id' => $bruker_id) , $Sideinndeling);
  // }
}
