<?php

//header('Content-type: application/json');
/**
 * Testfile for api client and cache
 * 
 * A simple testfile to test the api client and the caching mechanism.
 * 
 * @package Test
 * @author  Oliver Schwarz <oliver.schwarz@gmail.com>
 */
/**
 * Root directory
 * @var string
 */
define('BASEPATH', dirname(__FILE__));

/**
 * Load configuration
 */
require 'config.php';

/**
 * Load libraries
 */
require BASEPATH . '/lib/gw2cache.php';
require BASEPATH . '/lib/gw2client.php';
require BASEPATH . '/lib/gw2utilities.php';

/**
 * Instanciate client
 */
$gw2api = new Gw2ApiClient(
                GW2API_ENDPOINT,
                GW2API_VERSION,
                new Gw2ApiCache(BASEPATH . '/cache'));

/**
 * Get all worlds, maps and events (resources are fairly static, so cache it
 * for a day)
 */
$worldlist = $gw2api->getResource('world_names', 86400);
$eventlist = $gw2api->getResource('event_names', 86400);
$maplist = $gw2api->getResource('map_names', 86400);

/**
 * Set world id
 */
$worldid = 0;
if (isset($_GET['world'])) {
    $worldid = filter_input(INPUT_GET, 'world', FILTER_SANITIZE_NUMBER_INT);
}

/**
 * Fetch events, cache for 1 minute
 */
$events = $gw2api->getResource('events', 60, array('world_id' => $worldid), true);
$events = $gw2api->registerEvents($events);

/**
 * Generate output
 */

foreach ($events->events as $event) {
    printf('%s - %s: %s (%s) remaining time: %s<br>',
            Gw2ApiUtil::getResourceById($event->world_id, $worldlist),
            Gw2ApiUtil::getResourceById($event->map_id, $maplist),
            Gw2ApiUtil::getResourceById($event->event_id, $eventlist),
            $event->state, $event->last_changed
    );
}

/**
 * Show output
 */
//printf('<pre>%s</pre>', print_r($events, 1));