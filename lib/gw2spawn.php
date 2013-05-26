<?php
require_once BASEPATH . '/lib/gw2database.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gw2spawn
 *
 * @author walker
 */
class gw2Spawn {
    protected $id;
    protected $event;
    protected $spawn_timer;
    protected $spawn_window;
    protected $remaining_spawn;
    protected $remaining_window;
    protected $db;
    
    function __construct($event) {
        $this->event = $event;
        $this->remaining_spawn = -1; //-1 para indicar falta de registro
        $this->remaining_window = -1; //-1 para indicar falta de registro
        $this->db = new gw2Database(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);;
    }
    
    public function getSpawn(){
        $this->db->connect();
        $dbSpawn = $this->db->fetch_all_array("SELECT * FROM spawns WHERE event_id='".$this->event->event_id."' AND state='".$this->event->state."'");
        if(empty($dbSpawn)){
            //TODO: Utilizar sistema inteligente de spawn baseado em médias de
            //tempo já registradas
               /*
            //Adiciona o tempo de spawn na db caso não exista ainda na db
            $spawn_timer = date_diff(new DateTime('now'), $event->last_modified, true);
            $this->spawn_timer = $spawn_timer;
            $seconds = $spawn_timer->s;
            $seconds += $spawn_timer->m*60;
            $seconds += $spawn_timer->h*60*60;
            $seconds += $spawn_timer->d*24*60*60;
            $seconds;
            $sql = "INSERT INTO spawns (id, event_id, state, spawn_timer, spawn_window) VALUES (
                NULL,
                '" . $this->event->event_id . "',
                '" . $this->event->state . "',
                '" . $seconds. "',
                '0',
            );";
            $this->db->query($sql);
            $this->id = $this->db->last_id();
                * 
                */
        } else {
            $spawn = (object)$dbSpawn[0];
            $this->spawn_timer = new DateInterval('PT'.$spawn->spawn_timer.'S');
            $this->spawn_window = new DateInterval('PT'.$spawn->spawn_window.'S');
            //Se próximo spawn for posterior ao momento atual
            $nextSpawnMoment = clone $this->event->last_modified;
            $nextSpawnMoment = $nextSpawnMoment->add($this->spawn_timer); 
            $now = new DateTime('now');
            if($nextSpawnMoment > $now){
                //Se o tempo de spawn é maior que o momento atual
                //setar como tempo de spawn
                $this->remaining_spawn = $nextSpawnMoment->getTimestamp() - $now->getTimestamp();
                //print_r($this->event); echo "<br>"; print_r($this->remaining_spawn); echo "<br>";
            } else {
                // Se tempo de spawn é menor que momento atual
                if ($spawn->spawn_window > 0) {
                    //Se tempo de spawn_window existir
                    $nextSpawnMoment->add($this->spawn_window);
                    $this->remaining_window = $nextSpawnMoment->getTimestamp() - $now->getTimestamp();
                    if($this->remaining_window < $spawn->spawn_window){
                        // Se tempo atual é maior que o tempo somado para
                        // o tempo de spawn_window, corrigir colocando -1 como
                        // situação desconhecida
                        $this->remaining_window = -1;
                         //TODO: Avisar que spawn window está errado
                    }
                } else {
                    //Caso contrário setar -1
                    $this->remaining_spawn = -1;
                    $this->remaining_window = -1;
                }
           }
        }
                
        $this->db->close();
        $newEvent = new stdClass();
        $newEvent->world_id = $this->event->world_id;
        $newEvent->event_id = $this->event->event_id;
        $newEvent->map_id = $this->event->map_id;
        $newEvent->state = $this->event->state;
        $newEvent->old_state = $this->event->old_state;
        $newEvent->last_modified = $this->event->last_modified;
        $newEvent->remaining_spawn = $this->remaining_spawn;
        $newEvent->remaining_window = $this->remaining_window;
        return $newEvent;
    }
}

?>
