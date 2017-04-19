<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/19
 * Time: 22:59
 */

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Spf\Framework\Config\IniConfig;

require __DIR__ . '/../vendor/autoload.php';

define('ENV', 'dev');

$iniConfig = new IniConfig(__DIR__ . '/app.ini');

//var_export($iniConfig->toArray(ENV));
$logger = new \Monolog\Logger('app');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('./logs/application.log'));
$middleware = \GuzzleHttp\Middleware::log($logger, new \GuzzleHttp\MessageFormatter(\GuzzleHttp\MessageFormatter::CLF));
$stack = HandlerStack::create();
$stack->push($middleware);
$client = new Client(['base_uri' => 'http://gank.io','handler' => $stack]);

$response = $client->get('/api/data/Android/10/1');
echo $response->getBody();
