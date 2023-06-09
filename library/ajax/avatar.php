<?php
/**
 * TorrentPier – Bull-powered BitTorrent tracker engine
 *
 * @copyright Copyright (c) 2005-2023 TorrentPier (https://torrentpier.com)
 * @link      https://github.com/torrentpier/torrentpier for the canonical source repository
 * @license   https://github.com/torrentpier/torrentpier/blob/master/LICENSE MIT License
 */

if (!defined('IN_AJAX')) {
    die(basename(__FILE__));
}

global $bb_cfg, $lang, $user;

$mode = (string)$this->request['mode'];
$user_id = (int)$this->request['user_id'];

if (!$user_id or !$u_data = get_userdata($user_id)) {
    $this->ajax_die('Invalid user_id');
}

if (!IS_ADMIN && $user_id != $user->id) {
    $this->ajax_die($lang['NOT_ADMIN']);
}

switch ($mode) {
    case 'delete':
        delete_avatar($user_id, $u_data['avatar_ext_id']);
        $new_ext_id = 0;
        $response = '<img src="' . $bb_cfg['avatars']['upload_path'] . $bb_cfg['avatars']['no_avatar'] . '" alt="' . $user_id . '" />';
        break;
    default:
        $this->ajax_die('Invalid mode');
}

DB()->query("UPDATE " . BB_USERS . " SET avatar_ext_id = $new_ext_id WHERE user_id = $user_id");

\TorrentPier\Legacy\Sessions::cache_rm_user_sessions($user_id);

$this->response['avatar_html'] = $response;
