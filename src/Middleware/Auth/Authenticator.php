<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/12
 * Time: 22:09
 */

namespace Spf\Framework\Middleware\Auth;


use Spf\Framework\Config\BasicAuthConfigInterface;

class Authenticator
{
    private $config;

    public function __construct(BasicAuthConfigInterface $basicAuthConfig)
    {
        $this->config = $basicAuthConfig->getPairs();
    }

    /**
     * @param $user
     * @param $pass
     *
     * @return boolean
     */
    public function checkBasicAuth($user, $pass)
    {
        return trim($this->config[$user]) === trim($pass);
    }
}