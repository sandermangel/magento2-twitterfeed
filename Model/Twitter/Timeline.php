<?php
/**
 * Sama_Twitterfeed extension
 *
 * @package   Sama_Twitterfeed
 * @copyright 2015 Sander Mangel
 * @license   OSL-3.0 - See LICENSE.md for license details.
 * @author    Sander Mangel <sander@sandermangel.nl>
 */
namespace Sama\Twitterfeed\Model\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;

class Timeline
{

    protected $_connection;

    public function __construct(

    ) {
        $this->_connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, $access_token_secret);
    }

    public function get($username, $limit)
    {
        return $this->_connection->get("statuses/user_timeline", [
            "count" => $limit,
            "exclude_replies" => true,
            "screen_name" => $username
        ]);
    }
}
