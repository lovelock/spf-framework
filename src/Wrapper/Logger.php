<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 1/23/17
 * Time: 11:13 AM
 */

namespace Spf\Framework\Wrapper;


use Interop\Container\ContainerInterface;

/**
 * Class Logger
 * @package Spf\Framework\Wrapper
 *
 * @method emergency($message, array $context = [])
 * @method alert($message, array $context = [])
 * @method critical($message, array $context = [])
 * @method error($message, array $context = [])
 * @method warning($message, array $context = [])
 * @method notice($message, array $context = [])
 * @method info($message, array $context = [])
 * @method debug($message, array $context = [])
 */
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