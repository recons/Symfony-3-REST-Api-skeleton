<?php

namespace AppBundle\Entity;

use FOS\OAuthServerBundle\Entity\RefreshToken as BaseRefreshToken;

/**
 * RefreshToken
 */
class RefreshToken extends BaseRefreshToken
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
