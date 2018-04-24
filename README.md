# Facebook Api plugin for CakePHP 3

[![Build Status](https://img.shields.io/travis/cakephp/app/master.svg?style=flat-square)](https://travis-ci.org/cakephp/app)
[![License](https://img.shields.io/packagist/l/cakephp/app.svg?style=flat-square)](https://packagist.org/packages/cakephp/app)

This plugin provides basic support for use FACEBOOK API services in your CakePHP 3 application. 

## Requirements
This plugin has the following requirements:

* CakePHP 3.0.0 or greater.
* Facebook PHP_SDK 5.


## Installation
You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:
```
composer require facebook/graph-sdk
```

## Configuration

To use a Facebook config file, you should have a global.php file in your config folder.
The default configurations are as below and defined in config/api.php.
```php
<?php

return [
    'Facebook' => [
        'AppId' => '1336289226489835',
        'AppSecret' => 'b2c01b4544a3e388ce84c41456dccaf3',
        'DefaultGraphVersion' => 'v2.2',
    ],
];
```
  and load below  file in your bootstrap.php.
```php
Configure::load('global', 'default');
```
In your  controller,load facebook component  into your overridden initialize method like this.
```php
   public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Facebook');
    }
```
## Usage
@link https://developers.facebook.com/docs/php/howto/example_facebook_login.
You just need to load facebook component in controller. For example,
```php
namespace App\Controller;

use Facebook\Facebook;

/**
 * Facebook login.
 */
class SocialDetailsController extends ApiController
{

    /**
     * Facebook login.
     */
    public function login()
    {
        $permissions = ['email']; // Optional permissions
        $callbackUrl = 'http://' . $_SERVER['SERVER_NAME'] . 'facebook-callback'; // Redirect URL
        $loginUrl = $this->Facebook->facebookLogin($permissions, $callbackUrl);
        $this->set('loginUrl', $loginUrl);
    }
    
     /**
     * facebook callback method.
     */
    public function facebookCallback()
    {
        $this->autoRender = false;
        $facebook = $this->Facebook->getAccessToken();
        return true; //Return in your page
    }
}
```
You can define your logic in your action function as per your need. For above example, You can redirect them to a your page.

The URL for above example will be `http://yourdomain.com/SocialDetails/login`. You can customize it by setting the routes in `APP/config/routes.php`.

Simple :)
