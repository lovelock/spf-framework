<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/18
 * Time: 22:38
 */

namespace Spf\Framework\Config;


class AbstractConfig implements \Countable, \Iterator
{

    /**
     * Whether allow in-memory modifications to data are allowed
     *
     * @var boolean
     */
    protected $allowInMemoryModifications = false;

    /**
     * Iteration index
     *
     * @var integer
     */
    protected $index = 0;

    /**
     * Number of elements in configuration data
     *
     * @var integer
     */
    protected $count;

    /**
     * Contains array of configuration data
     *
     * @var array
     */
    protected $data = [];


    /**
     * Used when unsetting values during iteration to ensure we do not skip the next element
     *
     * @var boolean
     */
    protected $skipNextIteration;


    /**
     * Contains which config file sections are loaded. This is null if all sections are loaded, a string name if one section is loaded and an array of string names if multiple sections are loaded.
     *
     * @var mixed
     */
    protected $loadedSection = null;

    /**
     * This is used to track section inheritance. The keys are names of sections extend other sections, and the values are extended sections.
     *
     * @var array
     */
    protected $extends = [];

    /**
     * Load file error string.
     *
     * Is null if there is no error while file loading
     *
     * @var mixed
     */
    protected $loadFileErrorStr;


    /**
     * AbstractConfig provides a property based interface to an array. The data are read-only only if $allowInMemoryModifications is set to TRUE on construction.
     *
     * AbstractConfig also implements Countable and Iterator to facilitate easy access to the data.
     *
     * @param array $array
     * @param bool $allowInMemoryModifications
     */
    public function __construct(array $array, $allowInMemoryModifications = false)
    {
        $this->allowInMemoryModifications = (boolean)$allowInMemoryModifications;
        $this->data = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->data[$key] = new self($value, $this->allowInMemoryModifications);
            } else {
                $this->data[$key] = $value;
            }
        }

        $this->count = count($this->data);
    }

    /**
     * Retrieve a value and return $default if there is no element set
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $result = $default;

        if (array_key_exists($name, $this->data)) {
            $result = $this->data[$name];
        }

        return $result;
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }


    /**
     * Only allow setting of a property only if $allowInMemoryModifications is set to true on construction. Otherwise throws an exception.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     * @throws ConfigException
     */
    public function __set($name, $value)
    {
        if ($this->allowInMemoryModifications) {
            if (is_array($value)) {
                $this->data[$name] = new self($value, true);
            } else {
                $this->data[$name] = $value;
            }
            $this->count = count($this->data);
        } else {
            throw new ConfigException('Config is read only');
        }
    }


    /**
     * Deep clone of this instance to ensure that nested config are also cloned.
     *
     * @return void
     */
    public function __clone()
    {
        $array = [];

        foreach ($this->data as $key => $value) {
            if ($value instanceof AbstractConfig) {
                $array[$key] = clone $value;
            } else {
                $array[$key] = $value;
            }
        }

        $this->data = $array;
    }

    /**
     * Return associative array of the stored value.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        $data = $this->data;

        foreach ($data as $key => $value) {
            if ($value instanceof AbstractConfig) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        if ($this->allowInMemoryModifications) {
            unset($this->data[$name]);
            $this->count = count($this->data);
            $this->skipNextIteration = true;
        } else {
            throw new ConfigException('Config is read only');
        }
    }


    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        $this->skipNextIteration = false;
        return current($this->data);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        if ($this->skipNextIteration) {
            $this->skipNextIteration = false;
            return;
        }

        next($this->data);
        $this->index++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->index < $this->count;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->skipNextIteration = false;
        reset($this->data);
        $this->index = 0;
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * Return section name(s) loaded.
     *
     * @return array|mixed
     */
    public function getSectionName()
    {
        if (is_array($this->loadedSection) && count($this->loadedSection) === 1) {
            $this->loadedSection = $this->loadedSection[0];
        }

        return $this->loadedSection;
    }


    /**
     * Return true if all sections are loaded.
     *
     * @return boolean
     */
    public function areAllSectionsLoaded()
    {
        return $this->loadedSection === null;
    }

    public function merge(AbstractConfig $merge)
    {
        foreach ($merge as $key => $item) {
            if (array_key_exists($key, $this->data)) {
                if ($item instanceof AbstractConfig && $this->key instanceof AbstractConfig) {
                    $this->$key = $this->$key->merge(new self($item->toArray(), !$this->readOnly()));
                } else {
                    $this->$key = $item;
                }
            } else {
                if ($item instanceof AbstractConfig) {
                    $this->$key = new self($item->toArray(), !$this->readOnly());
                } else {
                    $this->$key = $item;
                }
            }
        }

        return $this;
    }


    public function setReadOnly()
    {
        $this->allowInMemoryModifications = false;
        foreach ($this->data as $key => $value) {
            if ($value instanceof AbstractConfig) {
                $value->setReadOnly();
            }
        }
    }

    public function readOnly()
    {
        return !$this->allowInMemoryModifications;
    }

    /**
     * @return array
     */
    public function getExtends()
    {
        return $this->extends;
    }

    /**
     * @param string $extendingSection
     * @param string $extendedSection
     * @return void
     */
    public function setExtend($extendingSection, $extendedSection = null)
    {
        if ($extendedSection === null && isset($this->extends[$extendingSection])) {
            unset($this->extends[$extendingSection]);
        } else if ($extendedSection !== null) {
            $this->extends[$extendingSection] = $extendedSection;
        }
    }


    protected function assertValidExtend($extendingSection, $extendedSection)
    {
        $extendedSectionCurrent = $extendedSection;
        while (array_key_exists($extendedSectionCurrent, $this->extends)) {
            if ($this->extends[$extendedSectionCurrent] === $extendingSection) {
                throw new ConfigException('Illegal circular inheritance detected');
            }

            $extendedSectionCurrent = $this->extends[$extendedSectionCurrent];
        }

        $this->extends[$extendingSection] = $extendedSection;
    }


    public function loadFileErrorHandler($errNum, $errStr, $errFile, $errLine)
    {
        if ($this->loadFileErrorStr === null) {
            $this->loadFileErrorStr = $errStr;
        } else {
            $this->loadFileErrorStr .= (PHP_EOL . $errStr);
        }
    }


    protected function arrayMergeRecursive($firstArray, $secondArray)
    {
        if (is_array($firstArray) && is_array($secondArray)) {
            foreach ($secondArray as $key => $value) {
                if (isset($firstArray[$key])) {
                    $firstArray[$key] = $this->arrayMergeRecursive($firstArray[$key], $value);
                } else {
                    if ($key === 0) {
                        $firstArray = [
                           0 => $this->arrayMergeRecursive($firstArray, $value),
                        ];
                    } else {
                        $firstArray[$key] = $value;
                    }
                }
            }
        } else {
            $firstArray = $secondArray;
        }

        return $firstArray;
    }
}