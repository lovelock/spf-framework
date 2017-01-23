<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 1/22/17
 * Time: 4:33 PM
 */

namespace Spf\Framework;


use Interop\Container\ContainerInterface;

class IoC
{
    protected $ci;

    public function __construct(ContainerInterface $container)
    {
        $this->ci = $container;
    }

    public function __get($id)
    {
        return $this->ci->get($id);
    }
}
