<?php

namespace QM\Logger\Document;

use Equip\Structure\Dictionary;
use QM\Logger\Model\LogModel;

/**
 * Interface ImmutableLogInterface
 */
interface ImmutableLogInterface
{
    /**
     * @return Dictionary
     * restituisce l'array content dell'entry da loggare.
     * Utile per uso nei test
     */
    function getEntry(): Dictionary;

    /**
     * @return string
     * restituisce la stringa di entry da loggare
     */
    function getLog(): string;

    /**
     * Create one entry LogEntry from Equpe\Structure\Dictionary or array
     * Array is like ["entryType" => "field name", "content" => "actual content"]
     * @param Dictionary|array $value
     * @param LogModel|null $model
     * @return ImmutableLogInterface
     */
    static function create($value, LogModel $model = null): ImmutableLogInterface;

    /**
     * Compose a new LogEntry merging all the entries inside $arrayOfEntries
     * @param array $arrayOfEntries
     * @param LogModel|null $model
     * @return ImmutableLogInterface
     */
    static function compose(array $arrayOfEntries, LogModel $model = null): ImmutableLogInterface;

    /**
     * Build LogEntry from $buildArray like:
     * [ 'date' => new \DateTime(),
     *   'state' => 'update',
     *   'trigger' => 'order' ]
     * where keys are the "entryType" and values are "content"
     * @param array $buildArray
     * @param LogModel|null $model
     * @return ImmutableLogInterface
     */
    static function build(array $buildArray, LogModel $model = null): ImmutableLogInterface;
}