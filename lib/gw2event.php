<?php

require_once BASEPATH . '/lib/gw2database.php';
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
    protected $old_state;
    protected $last_modified;
    protected $db;

    function __construct($event) {
        $this->event_id = $event->event_id;
        $this->world_id = $event->world_id;
        $this->map_id = $event->map_id;
        $this->state = $event->state;
        $this->last_modified = new DateTime('now');
        $this->old_state = $this->state;
        $this->db = new gw2Database(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
    }

    /**
     * Look or create a new event in database containing ids and last_modified
     * datetime for use with spawn timers.
     * @return \stdClass Returns an object containing the event and data
     * updated from database
     */
    public function registerEvent() {
        $this->db->connect();
        //FIXME: If table don't exists create one
        $dbEvent = $this->db->fetch_all_array("SELECT * FROM events WHERE event_id='$this->event_id' AND world_id=$this->world_id");
        $results_size = sizeof($dbEvent);
        $dbEvent = (object)$dbEvent[0];
        $this->last_modified = new DateTime($dbEvent->last_modified);
        //Se evento ainda não foi registrado cria um na tabela
        if ($results_size==0) {
            //Cria um registro na tabela com horário atual
            $sql = "INSERT INTO events (id, world_id, event_id, state, last_modified) VALUES (
                NULL,
                " . $this->world_id . ",
                '" . $this->event_id . "',
                '" . $this->state . "',
                NOW()
                );";
            $this->db->query($sql);
            $this->id = $this->db->last_id();
            $date = $this->db->fetch_all_array("SELECT last_modified FROM events WHERE id=$this->id");
            $date = $date[0]['last_modified'];
            $this->last_modified = new DateTime($date);
        } else {
            if($dbEvent->state != $this->state){
                $this->id = $dbEvent->id;
                $this->old_state = $dbEvent->state;
                //Se state for diferente atualiza evento com novo state e data
                //echo "OPS! update no evento $this->id do estado $dbEvent->state para o estado $this->state<br>";
                $this->db->query("UPDATE events SET state='$this->state', last_modified=NOW() WHERE id=".$this->id);
                $date = $this->db->fetch_all_array("SELECT last_modified FROM events WHERE id=$this->id");
                $date = $date[0]['last_modified'];
                $this->last_modified = new DateTime($date);
            }
        }
        $this->db->close();
        $newEvent = new stdClass();
        $newEvent->world_id = $this->world_id;
        $newEvent->event_id = $this->event_id;
        $newEvent->map_id = $this->map_id;
        $newEvent->state = $this->state;
        $newEvent->old_state = $this->old_state;
        $newEvent->last_modified = $this->last_modified;
        return $newEvent;
    }

}

?>
