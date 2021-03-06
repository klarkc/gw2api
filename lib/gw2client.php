<?php

require_once BASEPATH . '/lib/gw2event.php';
require_once BASEPATH . '/lib/gw2spawn.php';

/**
 * Guild Wars 2 api client
 * 
 * A client for the Guild Wars 2 api, handling all requests to the API using
 * file_get_contents(). Please note that the allow_url_fopen directive must
 * be active in your PHP environment.
 * 
 * @package Core
 * @author  Oliver Schwarz <oliver.schwarz@gmail.com>
 */

/**
 * Guild Wars 2 api client instance
 * 
 * Provides a simple class to request the RESTful Guild Wars 2 api. Uses a
 * caching to store static data in a local cache. Uses file_get_contents() to
 * fetch data from the api, thus requires <i>allow_url_fopen</i> activated in
 * your PHP environment.
 * 
 * @package Core
 * @author  Oliver Schwarz <oliver.schwarz@gmail.com>
 */
class Gw2ApiClient {

    /**
     * Endpoint address
     * 
     * URL(TLD) to the api.
     * 
     * @var string
     */
    protected $endpoint;

    /**
     * Api version
     * 
     * The Guild Wars 2 api version. Maybe useful in later versioning.
     * 
     * @var string
     */
    protected $version;

    /**
     * Api cache
     * 
     * Filesystem cache to temporarily store api responses.
     * 
     * @var Gw2ApiCache
     */
    protected $cache;

    /**
     * Constructor
     * 
     * Initialises the Api instance and requires default values to set
     * up parts of the request.
     * 
     * @param string      $endpoint Endpoint URL of the api
     * @param string      $version  Version number (prefixed with 'v') of the api
     * @param Gw2ApiCache $cache    Cache for temporarily store API responses
     * 
     * @return void
     */
    public function __construct($endpoint, $version, Gw2ApiCache $cache) {
        $this->endpoint = rtrim($endpoint, '/');
        $this->version = $version;
        $this->cache = $cache;
    }

    /**
     * Get api resource
     * 
     * Fetches the requested API resource from the Guild Wars 2 api endpoint.
     * If required, reads and writes from the cache, but requires the cache
     * lifetime for the request to check the cache validity. Allows additional
     * parameters to be appended to the request URL.
     * 
     * @param string  $resource Resource name to request from the api
     * @param integer $lifetime Allowed cache lifetime in seconds
     * @param array   $params   Optional parameters to append to the request URL [optional]
     * 
     * @return stdClass Object from either API directly or from the cache
     */
    public function getResource($resource, $lifetime, $params = false) {
        $request_url = sprintf('%s/%s/%s.json', $this->endpoint, $this->version, $resource);

        // Append params
        if ($params !== false) {
            $request_url .= '?' . http_build_query($params, null, '&amp;');
        }

        // Try to fetch from cache
        /* if (($json = $this->cache->get($request_url, $lifetime)) !== false) {
          return json_decode($json);
          } */

        // Fetch from remote api
        $res = file_get_contents($request_url);

        /*
          // Cache
          $this->cache->set($request_url, $res);
         */

        //FIXME: I can't use event_id and map_id in api, idk why.
        //return json_decode($res);
        $filter = function($value) use ($params) {
                    $ret = true;
                    foreach ($params as $pkey => $pvalue) {
                        $fvalue = $pvalue;
                        if ($pkey == 'event_id') {
                            foreach ($pvalue as $event_id) {
                                if ($value->event_id == $event_id)
                                    $fvalue = $event_id;
                            }
                        }
                        $ret = ($ret && ($value->$pkey == $fvalue));
                    }
                    return $ret;
                };
        $events = (array) json_decode($res);
        $events = array_filter($events['events'], $filter);
        $ret = new stdClass();
        $ret->events = $events;
        return $ret;
    }

    /**
     * Store events from api
     * 
     * Use event_id for store the last change of all events from param.
     * Return the same array with two more properties, last_changed containing
     * the last change DateTime and old_state containing the last known state.
     * 
     * @param stdClass $events all events from api
     */
    public function registerEvents($events) {
        $evts = $events->events;
        foreach ($evts as &$event) {
            $retEvent = new gw2Event($event);
            $event = $retEvent->registerEvent();
        }
        unset($event);
        $events->events = $evts;
        return $events;
    }

    /**
     * Add properties spawn_timer and spawn_window for next state all events
     * in the param
     * @param stdClass $events events from api containing last_modified
     * property
     */
    public function addSpawns($events) {
        $evts = $events->events;
        foreach ($evts as &$event) {
            $retEvent = new gw2Spawn($event);
            $event = $retEvent->getSpawn();
        }
        unset($event);
        $events->events = $evts;
        return $events;
    }

}