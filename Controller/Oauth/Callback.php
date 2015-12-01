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
class Callback extends \Magento\Framework\App\Action\Action
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
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $data = $this->getRequest()->getParams();

        /* Get temporary credentials from session. */
        $request_token = [];
        $request_token['oauth_token'] = $this->scopeConfig->getValue('sama_twitterfeed/oauth/token', $storeScope);
        $request_token['oauth_token_secret'] = $this->scopeConfig->getValue('sama_twitterfeed/oauth/token_secret', $storeScope);
        /* If denied, bail. */
        if (isset($data['denied'])) {
            Throw new Exception("Twitter denied permission");
        }
        /* If the oauth_token is not what we expect, bail. */
        if (isset($data['oauth_token']) && $request_token['oauth_token'] !== $data['oauth_token']) {
            Throw new Exception("Unexpected Oauth token");
        }

        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $connection = new TwitterOAuth($this->_oAuthkey, $this->_oAuthsecret, $request_token['oauth_token'], $request_token['oauth_token_secret']);
        /* Request access tokens from twitter */
        $access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $data['oauth_verifier']));
        /* If HTTP response is 200 continue otherwise send to connect page to retry */
        if (200 == $connection->getLastHttpCode()) {
            $this->_objectManager->get('Magento\Framework\App\MutableScopeConfig')->setValue('sama_twitterfeed/oauth/access_token', $access_token);
            $this->_objectManager->get('Magento\Framework\App\MutableScopeConfig')->setValue('sama_twitterfeed/oauth/token_secret', null);
            $this->_objectManager->get('Magento\Framework\App\MutableScopeConfig')->setValue('sama_twitterfeed/oauth/token', null);
        } else {
            Throw new Exception("Twitter Oauth API status code: {$connection->getLastHttpCode()}");
        }

        return;
    }
}
