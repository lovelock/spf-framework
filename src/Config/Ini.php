<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/18
 * Time: 22:36
 */

namespace Spf\Framework\Config;


class Ini extends AbstractConfig
{
    protected $nestSeparator = '.';

    protected $sectionSeparator = ':';

    protected $skipExtends = false;

    public function __construct($filename, $section = null, $options = false)
    {
        if (empty($filename)) {
            throw new ConfigException('Filename is not set');
        }

        $allowInMemoryModifications = false;
        if (is_bool($options)) {
            $allowInMemoryModifications = $options;
        } else if (is_array($options)) {
            if (isset($options['allowInMemoryModification'])) {
                $allowInMemoryModifications = (boolean)$options['allowInMemoryModification'];
            }
            if (isset($options['nestSeparator'])) {
                $this->nestSeparator = $options['nestSeparator'];
            }

            if (isset($options['skipExtends'])) {
                $this->skipExtends = (boolean)$options['skipExtends'];
            }
        }

        $iniArray = $this->loadIniFile($filename);

        if (null === $section) {
            $dataArray = [];
        }
    }

    protected function loadIniFile($filename)
    {
        $loaded = $this->parseIniFile($filename);
        $iniArray = [];
        foreach ($loaded as $key => $data) {
            $pieces = explode($this->sectionSeparator, $key);
            $thisSection = trim($pieces[0]);
            switch (count($pieces)) {
                case 1:
                    $iniArray[$thisSection] = $data;
                    break;

                case 2:
                    $extendedSection = trim($pieces[1]);
                    $iniArray[$thisSection] = array_merge([';extends' => $extendedSection], $data);
                    break;

                default:
                    throw new ConfigException("Section '$thisSection' may not exist");
            }
        }

        return $iniArray;
    }
}