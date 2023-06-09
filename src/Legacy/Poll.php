<?php
/**
 * TorrentPier – Bull-powered BitTorrent tracker engine
 *
 * @copyright Copyright (c) 2005-2023 TorrentPier (https://torrentpier.com)
 * @link      https://github.com/torrentpier/torrentpier for the canonical source repository
 * @license   https://github.com/torrentpier/torrentpier/blob/master/LICENSE MIT License
 */

namespace TorrentPier\Legacy;

/**
 * Class Poll
 * @package TorrentPier\Legacy
 */
class Poll
{
    public $err_msg = '';
    public $poll_votes = [];
    public $max_votes = 0;

    public function __construct()
    {
        global $bb_cfg;
        $this->max_votes = $bb_cfg['max_poll_options'];
    }

    /**
     * Формирование результатов голосования
     *
     * @param $posted_data
     * @return string
     */
    public function build_poll_data($posted_data)
    {
        $poll_caption = (string)@$posted_data['poll_caption'];
        $poll_votes = (string)@$posted_data['poll_votes'];
        $this->poll_votes = [];

        if (!$poll_caption = str_compact($poll_caption)) {
            global $lang;
            return $this->err_msg = $lang['EMPTY_POLL_TITLE'];
        }
        $this->poll_votes[] = $poll_caption; // заголовок имеет vote_id = 0

        foreach (explode("\n", $poll_votes) as $vote) {
            if (!$vote = str_compact($vote)) {
                continue;
            }
            $this->poll_votes[] = $vote;
        }

        // проверять на "< 3" -- 2 варианта ответа + заголовок
        if (\count($this->poll_votes) < 3 || \count($this->poll_votes) > $this->max_votes + 1) {
            global $lang;
            return $this->err_msg = sprintf($lang['NEW_POLL_VOTES'], $this->max_votes);
        }
    }

    /**
     * Добавление голосов в базу данных
     *
     * @param int $topic_id
     */
    public function insert_votes_into_db($topic_id)
    {
        $this->delete_votes_data($topic_id);

        $sql_ary = [];
        foreach ($this->poll_votes as $vote_id => $vote_text) {
            $sql_ary[] = [
                'topic_id' => (int)$topic_id,
                'vote_id' => (int)$vote_id,
                'vote_text' => (string)$vote_text,
                'vote_result' => (int)0,
            ];
        }
        $sql_args = DB()->build_array('MULTI_INSERT', $sql_ary);

        DB()->query("REPLACE INTO " . BB_POLL_VOTES . $sql_args);

        DB()->query("UPDATE " . BB_TOPICS . " SET topic_vote = 1 WHERE topic_id = $topic_id");
    }

    /**
     * Удаление голосования
     *
     * @param int $topic_id
     */
    public function delete_poll($topic_id)
    {
        DB()->query("UPDATE " . BB_TOPICS . " SET topic_vote = 0 WHERE topic_id = $topic_id");
        $this->delete_votes_data($topic_id);
    }

    /**
     * Удаление информации о проголосовавших и голосов
     *
     * @param int $topic_id
     */
    public function delete_votes_data($topic_id)
    {
        DB()->query("DELETE FROM " . BB_POLL_VOTES . " WHERE topic_id = $topic_id");
        DB()->query("DELETE FROM " . BB_POLL_USERS . " WHERE topic_id = $topic_id");
        CACHE('bb_poll_data')->rm("poll_$topic_id");
    }
}
