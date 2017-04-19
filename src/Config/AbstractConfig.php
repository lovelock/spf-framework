<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/19
 * Time: 22:23
 */

namespace Spf\Framework\Config;


abstract class AbstractConfig implements ConfigInterface
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var mixed
     */
    protected $loadFileErrorStr;

    protected function loadFileErrorHandler($errorNum, $errorStr, $errorFile, $errorLine)
    {
        if ($this->loadFileErrorStr === null) {
            $this->loadFileErrorStr = $errorStr;
        } else {
            $this->loadFileErrorStr .= (PHP_EOL . $errorStr);
        }
    }
}