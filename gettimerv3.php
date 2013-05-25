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
require_once 'config.php';

/**
 * Load libraries
 */
require_once BASEPATH . '/lib/gw2cache.php';
require_once BASEPATH . '/lib/gw2client.php';
require_once BASEPATH . '/lib/gw2utilities.php';

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
$worldid = null;
if (isset($_GET['world'])) {
    $worldid = filter_input(INPUT_GET, 'world', FILTER_SANITIZE_NUMBER_INT);
}

if ($worldid) {
    /**
     * Fetch events, cache for 1 minute
     */
    $events = $gw2api->getResource('events', 60, array('world_id' => $worldid), true);
    $events = $gw2api->registerEvents($events);
    $events = $gw2api->addSpawns($events);

    /**
     * Generate output
     */
    foreach ($events->events as $event) {
            $remaining_timer = '';
            if ($event->remaining_spawn > -1) {
                $remaining_timer = $event->remaining_spawn . 's';
            } else if ($event->remaining_window > -1) {
                $remaining_timer = $event->remaining_window . 's (window)';
            } else {
                $remaining_timer = 'unknown';
            }
            printf("Event ID: %s, Old State: %s, New State: %s, Last Modified: %s, Remaining time: %s <br> ", $event->event_id, $event->old_state, $event->state, $event->last_modified->format('H:i:s'), $remaining_timer
            );
    }

    /**
     * Show output
     */
//printf('<pre>%s</pre>', print_r($events, 1));
} else {
    echo "Error: set the world param";
}