<?php

namespace QM\Logger\Validator;

use function Functional\filter;
use function Functional\first;
use function Functional\reduce_left;
use QM\Logger\Document\ImmutableLogInterface;
use QM\Logger\Model\LogModel;

/**
 * Class LogValidator
 * @package Validator
 */
class LogValidator
{
    /**
     * @param LogModel $model
     * @param ImmutableLogInterface $logEntry
     * @return mixed
     */
    static public function validate(LogModel $model, ImmutableLogInterface $logEntry)
    {
        $res = reduce_left($model, function ($item, $index, $collection, $reduction) use ($logEntry) {
            $entry = first(filter($logEntry->getEntry(), function ($i, $key) use ($item) {
                return first(array_flip(first($item))) === $key;
            }));
            $type = first(first($item));
            $typeOfEntry = gettype($entry);
            if ($type === $typeOfEntry || $type === $typeOfEntry) return true && $reduction;
            if ('object' === $typeOfEntry && get_class($entry) === $type) return true && $reduction;
            return false && $reduction;
        }, true);

        return $res;
    }
}