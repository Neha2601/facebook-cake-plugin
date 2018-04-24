<?php

namespace App\Controller;

use App\Controller\AppController;
use Facebook\Facebook;

/**
 * SocialDetails Controller
 *
 * @property \App\Model\Table\SocialDetailsTable $SocialDetails
 *
 * @method \App\Model\Entity\SocialDetail[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SocialDetailsController extends AppController
{

    /**
     * initialize
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Facebook');
    }

    /**
     * facebool login method.
     */
    public function index()
    {
        $permissions = ['email', 'manage_pages', 'pages_show_list', 'publish_pages', 'pages_manage_cta']; // Optional permissions
        $actionUrl = 'http://facebook.dev.local/facebook-callback';
        $loginUrl = $this->Facebook->facebookLogin($permissions, $actionUrl);
        $this->set('loginUrl', $loginUrl);
    }

    /**
     * facebook callback method.
     * @return type
     */
    public function facebookCallback()
    {
        $this->autoRender = false;
        $facebook = $this->Facebook->getAccessToken();
        return $this->redirect(['_name' => 'facebook-add']);
    }

    /**
     * get facebook review.
     */
    public function add()
    {
        $this->autoRender = false;
        $session = $this->request->session();
        $accessToken = $session->read('accessToken');
        $getPageList = $this->Facebook->getFacebookPagelists();
        $facebook = [];
        foreach ($getPageList as $key => $page) {
            foreach ($page as $data) {
                $facebook[] = $this->Facebook->getFacebookReviews($data['access_token'], $data['id']);
            }
            break;
        }
        echo '<pre>'; print_r($facebook);die;
    }

    /**
     * get facebook feed.
     */
    public function facebookFeed()
    {
        $this->autoRender = false;
        $session = $this->request->session();
        $accessToken = $session->read('accessToken');
        $getPageList = $this->Facebook->getFacebookPagelists();
        $facebook = [];
        foreach ($getPageList as $key => $page) {
            foreach ($page as $data) {
                $facebook[] = $this->Facebook->getFacebookFeed($data['access_token'], $data['id']);
            }
            break;
        }
    }

    /**
     * create facebook post
     */
    public function facebookPost()
    {
        $this->autoRender = false;
        $session = $this->request->session();
        $accessToken = $session->read('accessToken');
        $getPageList = $this->Facebook->getFacebookPagelists();
        foreach ($getPageList as $key => $page) {
            foreach ($page as $data) {
                $facebook[] = $this->Facebook->createFacebookPost($data['access_token'], $data['id'], 'This is new test for the page posting');
            }
            break;
        } return $this->redirect(['_name' => 'facebook-login']);
    }
}
