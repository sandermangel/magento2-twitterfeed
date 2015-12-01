<?php
/**
 * Sama_Twitterfeed extension
 *
 * @package   Sama_Twitterfeed
 * @copyright 2015 Sander Mangel
 * @license   OSL-3.0 - See LICENSE.md for license details.
 * @author    Sander Mangel <sander@sandermangel.nl>
 */
namespace Sama\Twitterfeed\Controller\Oauth;

use Magento\Framework\Webapi\Exception;
use \Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Contact index controller
 */
class Redirect extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopePool
     */
    protected $_scopePool;

    protected $_oAuthkey;

    protected $_oAuthsecret;

    /**
     * Callback constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $this->_oAuthkey = $this->scopeConfig->getValue('sama_twitterfeed/oauth/key', $storeScope);
        $this->_oAuthsecret = $this->scopeConfig->getValue('sama_twitterfeed/oauth/secret', $storeScope);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        /* Build TwitterOAuth object with client credentials. */
        $connection = new TwitterOAuth($this->_oAuthkey, $this->_oAuthsecret);
        /* Get temporary credentials. */
        $request_token = $connection->oauth('oauth/request_token', [
            'oauth_callback' => $this->_objectManager->get('Magento\Framework\Url')->getUrl('twitterfeed/oauth/callback', [])
        ]);

        /* If last connection failed don't display authorization link. */
        if (200 ==$connection->getLastHttpCode()) {
            $this->_objectManager->get('Magento\Framework\App\MutableScopeConfig')->setValue('sama_twitterfeed/oauth/token', $request_token['oauth_token']);
            $this->_objectManager->get('Magento\Framework\App\MutableScopeConfig')->setValue('sama_twitterfeed/oauth/token_secret', $request_token['oauth_token_secret']);

            /* Build authorize URL and redirect user to Twitter. */
            $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
            header('location: '.$url);
            exit;
        } else {
            Throw new Exception("Twitter Oauth API status code: {$connection->getLastHttpCode()}");
        }
    }
}
