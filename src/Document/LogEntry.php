<?php

namespace QM\Logger\Document;

use Equip\Structure\Dictionary;
use Exception\LogEntryValidationException;
use function Functional\map;
use function Functional\each;
use function Functional\reduce_left;
use QM\Logger\Exception\LogEntryInvalidArgumentsException;
use QM\Logger\Model\LogModel;
use QM\Logger\Validator\LogValidator;

/**
 * Class LogEntry
 * @package QM\Logger\Document
 */
class LogEntry implements ImmutableLogInterface
{
    /**
     * @var Dictionary
     */
    protected $entry;

    /**
     * LogEntry constructor.
     * @param Dictionary $entry
     * @param LogModel|null $model
     */
    public function __construct(Dictionary $entry, LogModel $model = null)
    {
        $this->entry = $entry;
        if($model) {
            if(!LogValidator::validate($model, $this)) throw new LogEntryValidationException("Model doesn't match entry data");
        }
    }

    /**
     * @return Dictionary
     * restituisce l'array content dell'entry da loggare.
     * Utile per uso nei test
     */
    function getEntry(): Dictionary
    {
        return $this->entry;
    }

    /**
     * @return string
     * restituisce la stringa di entry da loggare
     */
    function getLog(): string
    {
        // TODO: Implement getLog() method.
        return "";
    }

    /**
     * Create one entry LogEntry from Equpe\Structure\Dictionary or array
     * Array is like ["entryType" => "field name", "content" => "actual content"]
     * @param Dictionary|array $value
     * @param LogModel|null $model
     * @return ImmutableLogInterface
     */
    static function create($value, LogModel $model = null): ImmutableLogInterface
    {
        try {
            return self::createWithDictionary($value, $model);
        } catch (\Error $error) {
            if (!is_array($value))
                throw new LogEntryInvalidArgumentsException("Input value must be an array or a Equip/Structure/Dictionary");

            return self::createWithArray($value, $model);
        }
    }



    /**
     * @param Dictionary $value
     * @param LogModel|null $model
     * @return LogEntry
     */
    static private function createWithDictionary(Dictionary $value, LogModel $model = null)
    {
        $array = [$value->getValue("entryType") => $value->getValue("content")];
        return new LogEntry(new Dictionary($array), $model);
    }

    /**
     * @param array $value
     * @param LogModel|null $model
     * @return LogEntry
     */
    static private function createWithArray(array $value, LogModel $model = null)
    {
        $array = [$value["entryType"] => $value["content"]];
        return new LogEntry(new Dictionary($array), $model);
    }

    /**
     * Compose a new LogEntry merging all the entries inside $arrayOfEntries
     * @param array $arrayOfEntries
     * @param LogModel|null $model
     * @return ImmutableLogInterface
     */
    public static function compose(array $arrayOfEntries, LogModel $model = null) : ImmutableLogInterface
    {
        $validate = function ($item, $acc) use (&$validate) {
            if (is_object($item) && LogEntry::class === get_class($item)) return $validate($item->getEntry()->toArray(), $acc);
            return $acc[] = $item;
        };

        $map = map($arrayOfEntries, function ($item) use ($validate) {
            return $validate($item, []);
        });

        $flat = [];
        each($map, function ($val) use (&$flat) {
            each($val, function ($val, $collectionKey) use (&$flat) {
                $flat[$collectionKey]= $val;
            });
        });

        return new LogEntry(new Dictionary($flat), $model);
    }

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
    static function build(array $buildArray, LogModel $model = null): ImmutableLogInterface
    {
        $logEntry = reduce_left($buildArray, function ($item, $index, $collection, $reduction) {
            $initArray = $reduction
                ? [LogEntry::create(["entryType" => $index, "content" => $item]), $reduction]
                : [LogEntry::create(["entryType" => $index, "content" => $item])];
            return LogEntry::compose($initArray);
        }, null);

        return LogEntry::compose([$logEntry], $model);
    }
}
