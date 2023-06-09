<?php
/**
 * TorrentPier – Bull-powered BitTorrent tracker engine
 *
 * @copyright Copyright (c) 2005-2023 TorrentPier (https://torrentpier.com)
 * @link      https://github.com/torrentpier/torrentpier for the canonical source repository
 * @license   https://github.com/torrentpier/torrentpier/blob/master/LICENSE MIT License
 */

if (!defined('BB_ROOT')) {
    die(basename(__FILE__));
}

// Root path
$rootPath = __DIR__;
if (DIRECTORY_SEPARATOR != '/') $rootPath = str_replace(DIRECTORY_SEPARATOR, '/', $rootPath);
define('BB_PATH', dirname($rootPath));

// Path (trailing slash '/' at the end: XX_PATH - without, XX_DIR - with)
define('ADMIN_DIR', BB_PATH . '/admin');
define('DATA_DIR', BB_PATH . '/data');
define('INT_DATA_DIR', BB_PATH . '/internal_data');
define('AJAX_HTML_DIR', BB_PATH . '/internal_data/ajax_html');
define('CACHE_DIR', BB_PATH . '/internal_data/cache');
define('LOG_DIR', BB_PATH . '/internal_data/log');
define('TRIGGERS_DIR', BB_PATH . '/internal_data/triggers');
define('AJAX_DIR', BB_PATH . '/library/ajax');
define('ATTACH_DIR', BB_PATH . '/library/attach_mod');
define('CFG_DIR', BB_PATH . '/library/config');
define('INC_DIR', BB_PATH . '/library/includes');
define('UCP_DIR', BB_PATH . '/library/includes/ucp');
define('LANG_ROOT_DIR', BB_PATH . '/library/language');
define('SITEMAP_DIR', BB_PATH . '/sitemap');
define('IMAGES_DIR', BB_PATH . '/styles/images');
define('TEMPLATES_DIR', BB_PATH . '/styles/templates');

// Templates
define('ADMIN_TPL_DIR', TEMPLATES_DIR . '/admin/');
define('XS_USE_ISSET', '1');
define('XS_TPL_PREFIX', 'tpl_');
define('XS_TAG_NONE', 0);
define('XS_TAG_BEGIN', 2);
define('XS_TAG_END', 3);
define('XS_TAG_INCLUDE', 4);
define('XS_TAG_IF', 5);
define('XS_TAG_ELSE', 6);
define('XS_TAG_ELSEIF', 7);
define('XS_TAG_ENDIF', 8);
define('XS_TAG_BEGINELSE', 11);

// Debug
define('COOKIE_DBG', 'bb_dbg'); // debug cookie name
define('SQL_DEBUG', true);     // enable forum sql & cache debug
define('SQL_LOG_ERRORS', true);     // all SQL_xxx options enabled only if SQL_DEBUG == TRUE
define('SQL_CALC_QUERY_TIME', true);     // for stats
define('SQL_LOG_SLOW_QUERIES', true);     // log sql slow queries
define('SQL_SLOW_QUERY_TIME', 10);       // slow query in seconds
define('SQL_PREPEND_SRC_COMM', false);    // prepend source file comment to sql query

// Log options
define('LOG_EXT', 'log');
define('LOG_SEPR', ' | ');
define('LOG_LF', "\n");
define('LOG_MAX_SIZE', 1048576); // bytes

// Error reporting
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', LOG_DIR . '/php_err.log');

// Triggers
define('BB_ENABLED', TRIGGERS_DIR . '/$on');
define('BB_DISABLED', TRIGGERS_DIR . '/$off');
define('CRON_ALLOWED', TRIGGERS_DIR . '/cron_allowed');
define('CRON_RUNNING', TRIGGERS_DIR . '/cron_running');

// Gzip
define('GZIP_OUTPUT_ALLOWED', extension_loaded('zlib') && !ini_get('zlib.output_compression'));
define('UA_GZIP_SUPPORTED', isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false);

// Torrents (reserved: -1)
define('TOR_NOT_APPROVED', 0); // не проверено
define('TOR_CLOSED', 1); // закрыто
define('TOR_APPROVED', 2); // проверено
define('TOR_NEED_EDIT', 3); // недооформлено
define('TOR_NO_DESC', 4); // неоформлено
define('TOR_DUP', 5); // повтор
define('TOR_CLOSED_CPHOLD', 6); // закрыто правообладателем
define('TOR_CONSUMED', 7); // поглощено
define('TOR_DOUBTFUL', 8); // сомнительно
define('TOR_CHECKING', 9); // проверяется
define('TOR_TMP', 10); // временная
define('TOR_PREMOD', 11); // премодерация
define('TOR_REPLENISH', 12); // пополняемая

// Cron
define('CRON_LOG_ENABLED', true); // global ON/OFF
define('CRON_FORCE_LOG', false); // always log regardless of job settings
define('CRON_DIR', INC_DIR . '/cron/');
define('CRON_JOB_DIR', CRON_DIR . 'jobs/');
define('CRON_LOG_DIR', 'cron'); // inside LOG_DIR
define('CRON_LOG_FILE', 'cron'); // without ext

// Session variables
define('ONLY_NEW_POSTS', 1);
define('ONLY_NEW_TOPICS', 2);

// Ratio limits
define('TR_RATING_LIMITS', true);        // ON/OFF
define('MIN_DL_FOR_RATIO', 10737418240); // 10 GB in bytes, 0 - disable
