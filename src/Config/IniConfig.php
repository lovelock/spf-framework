<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/19
 * Time: 22:23
 */

namespace Spf\Framework\Config;


class IniConfig extends AbstractConfig
{
    protected $nestSeparator = '.';
    protected $sectionSeparator = ':';

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function toArray($section = null)
    {
        return $this->loadIniFile($section);
    }

    private function parseIniFile()
    {
        set_error_handler([$this, 'loadFileErrorHandler']);
        $iniArray = parse_ini_file($this->filename, true);
        restore_error_handler();

        if ($this->loadFileErrorStr !== null) {
            throw new Exception($this->loadFileErrorStr);
        }

        return $iniArray;
    }

    /**
     * @param $section
     * @return array
     * @throws Exception
     */
    private function loadIniFile($section)
    {
        $loaded = $this->parseIniFile();
        $iniArray = [];

        foreach ($loaded as $key => $data) {
            $pieces = explode($this->sectionSeparator, $key);
            $currentSection = $pieces[0];
            switch (count($pieces)) {
                case 1:
                    $iniArray[$currentSection] = $data;
                    break;

                case 2:
                    $extendedSection = $pieces[1];
                    $iniArray[$currentSection] = array_merge($iniArray[$extendedSection], $data);
                    break;

                default:
                    throw new Exception('Section ' . $currentSection . ' may not exists');
            }
        }

        if ($section !== null) {
            return $iniArray[$section];
        }
        return $iniArray;
    }
}