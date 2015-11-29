<?php
/**
 * Sama_Twitterfeed extension
 *
 * @package   Sama_Twitterfeed
 * @copyright 2015 Sander Mangel
 * @license   OSL-3.0 - See LICENSE.md for license details.
 * @author    Sander Mangel <sander@sandermangel.nl>
 */

namespace Sama\Twitterfeed\Block;

use Sama\Twitterfeed\Model\Twitter\Timeline;

class Timeline  extends \Magento\Framework\View\Element\Template
{

    protected $_timeline;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Timeline $timeline,
        array $data = []
    ) {
        $this->_timeline = $timeline;

        parent::__construct($context, $data);
    }

    public function getTweets()
    {
        return $this->_timeline->get('magestackday', 5);
    }
}
