<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 3/9/17
 * Time: 10:49 AM
 */

namespace Spf\Framework\Utils\Parser;


class IniParser
{
    private $config;

    public function __construct($iniConfigFile)
    {
        if (file_exists($iniConfigFile)) {
            $array = parse_ini_file($iniConfigFile, true);
            $this->config = $this->parseIniAdvanced($array);
        } else {
            $this->config = [];
        }
    }

    public function get($configCategory)
    {
        if (empty($this->config)) {
            return [];
        }

        return $this->config[$configCategory];
    }

    public function parseIniAdvanced($array) {
        $returnArray = array();
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $e = explode(':', $key);
                if (!empty($e[1])) {
                    $x = array();
                    foreach ($e as $tk => $tv) {
                        $x[$tk] = trim($tv);
                    }
                    $x = array_reverse($x, true);
                    foreach ($x as $k => $v) {
                        $c = $x[0];
                        if (empty($returnArray[$c])) {
                            $returnArray[$c] = array();
                        }
                        if (isset($returnArray[$x[1]])) {
                            $returnArray[$c] = array_merge($returnArray[$c], $returnArray[$x[1]]);
                        }
                        if ($k === 0) {
                            $returnArray[$c] = array_merge($returnArray[$c], $array[$key]);
                        }
                    }
                } else {
                    $returnArray[$key] = $array[$key];
                }
            }
        }
        return $returnArray;
    }
    public function recursiveParse($array)
    {
        $returnArray = array();
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = $this->recursiveParse($value);
                }
                $x = explode('.', $key);
                if (!empty($x[1])) {
                    $x = array_reverse($x, true);
                    if (isset($returnArray[$key])) {
                        unset($returnArray[$key]);
                    }
                    if (!isset($returnArray[$x[0]])) {
                        $returnArray[$x[0]] = array();
                    }
                    $first = true;
                    foreach ($x as $k => $v) {
                        if ($first === true) {
                            $b = $array[$key];
                            $first = false;
                        }
                        $b = array($v => $b);
                    }
                    $returnArray[$x[0]] = array_merge_recursive($returnArray[$x[0]], $b[$x[0]]);
                } else {
                    $returnArray[$key] = $array[$key];
                }
            }
        }
        return $returnArray;
    }

}