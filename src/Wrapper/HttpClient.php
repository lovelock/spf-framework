<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 1/22/17
 * Time: 5:12 PM
 */

namespace Spf\Framework\Wrapper;

use Unirest\Request;

class HttpClient
{
    public function __call($method, $arguments)
    {
        return forward_static_call_array([Request::class, $method], $arguments);
    }
}