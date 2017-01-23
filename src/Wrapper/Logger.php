<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 1/23/17
 * Time: 11:13 AM
 */

namespace Spf\Framework\Wrapper;


use Interop\Container\ContainerInterface;

class Logger
{
    private $ci;

    public function __construct(ContainerInterface $container)
    {
        $this->ci = $container;
    }

    public function __call($name, $arguments)
    {
        $arguments[0] = 'REQUEST_ID:' . REQUEST_ID . ' ' . $arguments[0];
        call_user_func_array([$this->ci->rawLogger, $name], $arguments);
    }
}