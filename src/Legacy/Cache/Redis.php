<?php
/**
 * TorrentPier – Bull-powered BitTorrent tracker engine
 *
 * @copyright Copyright (c) 2005-2023 TorrentPier (https://torrentpier.com)
 * @link      https://github.com/torrentpier/torrentpier for the canonical source repository
 * @license   https://github.com/torrentpier/torrentpier/blob/master/LICENSE MIT License
 */

namespace TorrentPier\Legacy\Cache;

/**
 * Class Redis
 * @package TorrentPier\Legacy\Cache
 */
class Redis extends Common
{
    public $used = true;
    public $engine = 'Redis';
    public $cfg;
    public $redis;
    public $prefix;
    public $connected = false;

    public function __construct($cfg, $prefix = null)
    {
        if (!$this->is_installed()) {
            die('Error: Redis extension not installed');
        }

        $this->cfg = $cfg;
        $this->prefix = $prefix;
        $this->redis = new \Redis();
        $this->dbg_enabled = sql_dbg_enabled();
    }

    public function connect()
    {
        $this->cur_query = 'connect ' . $this->cfg['host'] . ':' . $this->cfg['port'];
        $this->debug('start');

        if (@$this->redis->connect($this->cfg['host'], $this->cfg['port'])) {
            $this->connected = true;
        }

        if (!$this->connected && $this->cfg['con_required']) {
            die('Could not connect to redis server');
        }

        $this->debug('stop');
        $this->cur_query = null;
    }

    public function get($name, $get_miss_key_callback = '', $ttl = 0)
    {
        if (!$this->connected) {
            $this->connect();
        }

        $this->cur_query = "cache->get('$name')";
        $this->debug('start');
        $this->debug('stop');
        $this->cur_query = null;
        $this->num_queries++;

        return ($this->connected) ? unserialize($this->redis->get($this->prefix . $name)) : false;
    }

    public function set($name, $value, $ttl = 0)
    {
        if (!$this->connected) {
            $this->connect();
        }

        $this->cur_query = "cache->set('$name')";
        $this->debug('start');

        if ($this->redis->set($this->prefix . $name, serialize($value))) {
            if ($ttl > 0) {
                $this->redis->expire($this->prefix . $name, $ttl);
            }

            $this->debug('stop');
            $this->cur_query = null;
            $this->num_queries++;

            return true;
        }

        return false;
    }

    public function rm($name = '')
    {
        if (!$this->connected) {
            $this->connect();
        }

        if ($name) {
            $this->cur_query = "cache->rm('$name')";
            $this->debug('start');
            $this->debug('stop');
            $this->cur_query = null;
            $this->num_queries++;

            return ($this->connected) ? $this->redis->del($this->prefix . $name) : false;
        }

        return ($this->connected) ? $this->redis->flushDB() : false;
    }

    public function is_installed()
    {
        return class_exists('Redis');
    }
}
