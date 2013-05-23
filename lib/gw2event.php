<?php

require BASEPATH . '/lib/gw2database.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gw2event
 *
 * @author walker
 */
class gw2Event {
    protected $id;
    protected $event_id;
    protected $world_id;
    protected $map_id;
    protected $state;
    protected $last_modified;
    protected $remaining_time;
    protected $db;
    
    function __construct($event) {
        $this->event_id = $event->event_id;
        $this->world_id = $event->world_id;
        $this->map_id = $event->map_id;
        $this->state = $event->state;
        $this->db = new gw2Database(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);
    }
    
    public function registerEvent(){
        $this->db->connect();
        //FIXME: If table don't exists create one
        $query = $this->db->query("SELECT * FROM events WHERE event_id=$this->event_id AND world_id=$this->world_id");
        $oldEvent = $this->db->fetch_all_array($query);
        if(!empty($oldEvent)) $oldEvent = (object)$oldEvent[0];
        
        //Se o state do evento for diferente, altera evento na db
        if($this->state != $oldEvent->state){
            //TODO: Novo tempo e state no database
        } else {
            //TODO: Atualizar last_modified e reamining_time
        }
        $this->db->close();
        $newEvent = new stdClass();
        $newEvent->world_id = $this->world_id;
        $newEvent->event_id = $this->event_id;
        $newEvent->map_id = $this->map_id;
        $newEvent->state = $state;
        $newEvent->last_modified = $this->last_modified;
        $newEvent->remaining_time = $this->remaining_time;
        return $newEvent;
    }
    
    
}

?>
