<?php
header('Content-type: application/json');

$events_url = 'https://api.guildwars2.com/v1/events.json';
$worlds_url = 'https://api.guildwars2.com/v1/world_names.json';
$event_names_url = 'https://api.guildwars2.com/v1/event_names.json';

$oldData = array();
if( file_exists('events/world_'.$_GET['world_id'].'.json') ) $oldData = json_decode(file_get_contents('events/world_'.$_GET['world_id'].'.json'));

$data = json_decode(file_get_contents($events_url.'?world_id='.$_GET['world_id']));
$newData = array();
$changed = true; 

foreach($data->events as $event){
	$lastspawn=gmdate(DateTime::RSS);
	//Procura pela key referente ao event_id atual na $oldData
	$key = null;
	foreach($oldData as $k=>$value) if($value->event->event_id == $event->event_id) $key = $k;
	//Checa se houve alteração do status do evento e se houve mantem o lastspawn antigo
	if($key){
		if($event->state == $oldData[$key]->event->state) {
			$changed=false;
			$lastspawn = $oldData[$key]->lastspawn;
		}
	}
	//Cria nova entrada no array que será gravado e enviado via json
	$newData[] = array('event'=>$event,'lastspawn'=>$lastspawn);
}
$json = json_encode($newData);

//Alterar arquivo caso houver novas mudanças
if(sizeof($data->events) > 0 && $changed) file_put_contents('events/world_'.$_GET['world_id'].'.json', $json);

echo $json;
//print_r($newData);

?>
