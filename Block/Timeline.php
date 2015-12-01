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

class Timeline extends \Magento\Framework\View\Element\Template
{

    protected $_twitter;

    /**
     * Timeline constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Timeline $timeline
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Sama\Twitterfeed\Model\Twitter\User $twitter,
        array $data = []
    ) {
        $this->_twitter = $twitter;

        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getTweets()
    {
        return $this->_twitter->getTimeline('magestackday', 5);
    }
}
