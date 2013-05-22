<?php

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

/**
 * Instanciate client
 */
$gw2api = new Gw2ApiClient(
    GW2API_ENDPOINT,
    GW2API_VERSION,
    new Gw2ApiCache(BASEPATH . '/cache'));

/**
 * Get all worlds (this is static, so cache it for a day)
 */
$worlds = $gw2api->getResource('world_names', 86400);

/**
 * Show output
 */
printf('<pre>%s</pre>', print_r($worlds, 1));