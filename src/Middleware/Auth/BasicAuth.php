<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 4/12/17
 * Time: 21:28 PM
 */

namespace Spf\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spf\Framework\Middleware\Auth\Authenticator;

/**
 * @property Authenticator authenticator
 */
class BasicAuth extends Base
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $failedResponse = $response->withStatus(401)
            ->withAddedHeader('WWW-Authorization', 'realm=Protected');

        $authHeaderLine = $request->getHeaderLine('Authorization');
        if (!$authHeaderLine) {
            return $failedResponse;
        }

        if (!preg_match("/Basic\s+(.*)$", $authHeaderLine, $matches)) {
            return $failedResponse;
        }

        list($user, $pass) = explode(':', base64_decode($matches[1]), 2);

        if ($this->authenticator->checkBasicAuth($user, $pass)) {
            return $failedResponse;
        }

        return $next($request, $response);

    }
}
