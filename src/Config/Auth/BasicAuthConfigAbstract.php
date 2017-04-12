<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/12
 * Time: 21:59
 */

namespace Spf\Framework\Config\Auth;


abstract class BasicAuthConfigAbstract implements BasicAuthConfigInterface
{
    public $config;

    abstract public function __construct();

    public function getPairs()
    {
        return $this->config;
    }
}