<?php

namespace QM\Logger\Model;

use Equip\Structure\Dictionary;
use Equip\Structure\UnorderedList;
use function Functional\each;
use function Functional\first;
use function Functional\map;
use QM\Logger\Exception\LogModelInvalidArgumentException;

/**
 * Class LogModel
 * @package Model
 */
class LogModel implements \Iterator
{
    /**
     * @var UnorderedList
     */
    private $costraints;

    /**
     * LogModel constructor.
     * @param UnorderedList $parameters
     */
    private function __construct(UnorderedList $parameters)
    {
        $this->costraints = new UnorderedList($this->requiredFlagToString($parameters));
    }

    /**
     * @param string $key
     * @param string $type
     * @param bool $required
     * @return LogModel
     */
    function add(string $key, string $type, bool $required)
    {
        $array = [
            self::boolToString($required) => [$key => $type]
        ];

        $paramArray = $this->costraints->toArray();
        return new LogModel(new UnorderedList(array_map(null, $paramArray, $array)));
    }

    /**
     * @param UnorderedList $list
     * @return array
     */
    private function requiredFlagToString(UnorderedList $list)
    {
        return map($list, function ($item) {
            $required = self::boolToString(first(array_keys($item)));
            $key = first(array_keys(first($item)));
            $type = first(first($item));
            return [$required => [$key => $type]];
        });
    }

    /**
     * @param bool $required
     * @return string
     */
    static private function boolToString(bool $required)
    {
        return $required
            ? 'true'
            : 'false';
    }

    /**
     * @param UnorderedList $list
     * @return LogModel
     */
    static function create(UnorderedList $list)
    {
        $isAssoc = function (array $array) {
            $keys = array_keys($array);
            return array_keys($keys) !== $keys;
        };

        each($list, function ($item) use ($isAssoc) {
            if (!$isAssoc(reset($item)) || !$item) throw new LogModelInvalidArgumentException("Not valid array for create a model");
        });

        return new LogModel($list);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->costraints->current();
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->costraints->next();
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->costraints->key();
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
        return $this->costraints->valid();
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->costraints->rewind();
    }
}