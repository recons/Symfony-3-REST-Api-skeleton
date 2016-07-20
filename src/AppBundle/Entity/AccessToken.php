<?php

namespace AppBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;

/**
 * AccessToken
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \AppBundle\Entity\Client
     */
    protected $client;

    /**
     * @var \AppBundle\Entity\User
     */
    protected $user;
}
