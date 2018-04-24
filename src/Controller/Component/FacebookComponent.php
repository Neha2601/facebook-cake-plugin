<?php

namespace FacebookApi\Controller\Component;

use Cake\Core\Configure;
use Cake\Controller\Component;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;

/**
 * Facebook Component
 */
class FacebookComponent extends Component
{

    /**
     * @var Facebook\Facebook
     */
    private $facebookObject;

    /**
     * @var Facebook\Helpers\FacebookRedirectLoginHelper
     */
    private $helper;

    /**
     * Constructor
     *
     * To use Facebook config, you should have a facebook.php in your config folder like
     * <?php
     * return [
     * 'Facebook.app_id' => 'YOUR_APP_ID',
     * 'Facebook.app_secret' => 'YOUR_APP_SECRET'
     * 'Facebook.DefaultGraphVersion' => 'YOUR_DEFAULT_GRAPH_VERSION'
     * ];
     * and use Configure::load('facebook', 'default'); in your bootstrap.php.
     *
     * @param \Cake\Controller\ComponentRegistry $registry The Component registry used on this request.
     * @param array $config Array of config to use.
     */
    public function __construct($registry, array $config = [])
    {
        parent::__construct($registry, $config);
        $this->facebookObject = new Facebook([
            'app_id' => Configure::read('Facebook.AppId'),
            'app_secret' => Configure::read('Facebook.AppSecret'),
            'default_graph_version' => Configure::read('Facebook.DefaultGraphVersion'),
        ]);
        $this->helper = $this->facebookObject->getRedirectLoginHelper();
    }

    /**
     * Facebook login
     * @param string $permissions permissions
     * @param string $callbaclURl callback url
     * @return string
     */
    public function facebookLogin($permissions, $callbaclURl)
    {
        $loginUrl = $this->helper->getLoginUrl($callbaclURl, $permissions);
        return $loginUrl;
    }

    /**
     * Get access user accessToken
     * @return string
     */
    public function getAccessToken()
    {
        try {
            $this->helper = $this->facebookObject->getRedirectLoginHelper();
            if (isset($this->request->query['state'])) {
                $this->helper->getPersistentDataHandler()->set('state', $this->request->query['state']);
            }
            $accessToken = $this->helper->getAccessToken();
            if (!$accessToken->isLongLived()) {
                $oAuth2Client = $this->facebookObject->getOAuth2Client();
                try {
                    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken); // Exchanges a short-lived access token for a long-lived one
                } catch (FacebookSDKException $e) {
                    return $e->getMessage();
                }
            }
            $session = $this->request->session();
            $session->write('accessToken', (string) $accessToken); //Write user access token in session
        } catch (FacebookResponseException $e) {
            return $e->getMessage();
        } catch (FacebookSDKException $e) {
            return $e->getMessage();
        }
        return $accessToken;
    }

    /**
     * Get facebook pageList
     * @return type string
     */
    public function getFacebookPagelists()
    {
        try {
            $session = $this->request->session();
            $accessToken = $session->read('accessToken');
            $pageListing = $this->facebookObject->get('/me/accounts', $accessToken);
            $getPage = $pageListing->getDecodedBody();
        } catch (FacebookResponseException $e) {
            return $e->getMessage();
        } catch (FacebookSDKException $e) {
            return $e->getMessage();
        }
        return $getPage;
    }

    /**
     * Get facebook reviews  
     * @param string $pageToken fetch page accessToken
     * @param int $id fetch pageId
     * @return string
     */
    public function getFacebookReviews($pageToken, $id)
    {
        try {
            $response = $this->facebookObject->get('/' . $id . '/ratings', $pageToken);
        } catch (FacebookResponseException $e) {
            return $e->getMessage();
        } catch (FacebookSDKException $e) {
            return $e->getMessage();
        }
        return $response;
    }

    /**
     * Get facebook feeds
     * @param string $pageToken fetch page accessToken
     * @param int $id fetch pageId
     * @return string
     */
    public function getFacebookFeed($pageToken, $id)
    {
        try {
            $response = $this->facebookObject->get('/' . $id . '/feed', $pageToken);
        } catch (FacebookResponseException $e) {
            return $e->getMessage();
        } catch (FacebookSDKException $e) {
            return $e->getMessage();
        }
        return $response;
    }

    /**
     * Create post in facebook page
     * @param string $pageToken fetch page accessToken
     * @param int $id  fetch pageId
     * @param string $message create message
     * @return string
     */
    public function createFacebookPost($pageToken, $id, $message)
    {
        try {
            $createPost = $this->facebookObject->post('/' . $id . '/feed', ['message' => $message], $pageToken);
        } catch (FacebookResponseException $e) {
            return $e->getMessage();
        } catch (FacebookSDKException $e) {
            return $e->getMessage();
        }
        return $createPost;
    }

}
