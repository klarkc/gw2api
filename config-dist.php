<?php

/**
 * Configuration file template
 * 
 * A simple configuration file to contain the basic settings for using the
 * Guild Wars 2 api client. How to use? Copy the file to ./config.php and
 * fill in the required values.
 * 
 * @package Core
 * @author  Oliver Schwarz <oliver.schwarz@gmail.com>
 */

/**
 * Guild Wars 2 api endpoint
 * 
 * Protocol and URL of the Guild Wars 2 api without trailing slash. Example:
 * https://api.guildwars2.com
 * 
 * @var string
 */
define('GW2API_ENDPOINT', '');

/**
 * Guild Wars 2 api version
 * 
 * Required for connecting to the correct api version. Currently there is
 * only v1, may be useful later.
 * 
 * @var string
 */
define('GW2API_VERSION', '');

/**
 * Mysql Hostname Configuration
 * 
 * Required for log changes from remote Guild Wars 2 api 
 * @var string
 */
define('MYSQL_HOST', '');

/**
 * Mysql User Configuration
 * 
 * Required for log changes from remote Guild Wars 2 api 
 * @var string
 */
define('MYSQL_USER', '');

/**
 * Mysql Password Configuration
 * 
 * Required for log changes from remote Guild Wars 2 api 
 * @var string
 */
define('MYSQL_PASSWORD', '');

/**
 * Mysql Database Configuration
 * 
 * Required for log changes from remote Guild Wars 2 api 
 * @var string
 */
define('MYSQL_DATABASE', '');

/**
 * Mysql Table Prefix Configuration
 * 
 * Required for log changes from remote Guild Wars 2 api 
 * @var string
 */
define('MYSQL_PREFIX', '');