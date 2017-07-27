<?php

namespace QM\Logger\Logger;

use QM\Logger\Document\ImmutableLogInterface;
use MongoDB;

/**
 * Class Logger
 */
class Logger
{
    /**
     * @var \MongoClient
     */
    private $client;

    /**
     * @var string
     */
    private $dbName;

    /**
     * @var string
     */
    private $collectionName;

    /**
     * Logger constructor.
     * @param MongoDB\Client $client
     * @param string $dbName
     * @param string $collectionName
     */
    public function __construct(MongoDB\Client $client, string $dbName, string $collectionName)
    {
        $this->client = $client;
        $this->dbName = $dbName;
        $this->collectionName = $collectionName;
    }

    /**
     * @param ImmutableLogInterface $logEntry
     * @return ImmutableLogInterface
     */
    public function log(ImmutableLogInterface $logEntry) : ImmutableLogInterface
    {
        if($this->client->connected) {
            $this->client
                ->selectDatabase($this->dbName)
                ->selectCollection($this->collectionName)
                ->insertOne($logEntry->getEntry()->toArray());
        }

        echo $logEntry->getLog();
        return $logEntry;
    }
}