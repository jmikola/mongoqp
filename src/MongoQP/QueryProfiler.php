<?php

namespace MongoQP;

use MongoDB\Client;
use MongoDB\BSON\Javascript;
use MongoDB\BSON\Regex;
use MongoDB\Driver\ReadPreference;
use MongoDB\Model\CollectionInfo;
use MongoDB\Model\DatabaseInfo;

class QueryProfiler
{
    private $client;
    private $code;

    public function __construct(Client $client, array $code)
    {
        $this->client = $client;
        $this->code = $code;
    }

    public function getPrimaryHostAndPort()
    {
        $server = $this->client->getManager()->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $server->getHost() . ':' . $server->getPort();
    }

    public function getDatabases()
    {
        return array_map(
            function(DatabaseInfo $databaseInfo) { return $databaseInfo->getName(); },
            iterator_to_array($this->client->listDatabases())
        );
    }

    public function getCollections($database)
    {
        return array_map(
            function(CollectionInfo $collectionInfo) { return $collectionInfo->getName(); },
            iterator_to_array($this->client->selectDatabase($database)->listCollections())
        );
    }

    public function getProfilingLevel($database)
    {
        return $this->client->selectDatabase($database)->command(['profile' => -1])->toArray()[0]['was'];
    }

    public function setProfilingLevel($database, $level)
    {
        $this->client->selectDatabase($database)->command(['profile' => (int) $level]);
    }

    public function getProfilingData($database, $collection = null)
    {
        $database = $this->client->selectDatabase($database);

        // Ensure the database has a "system.profile" collection
        if ( ! in_array('system.profile', $this->getCollections($database))) {
            return [];
        }

        /* Exclude system collection queries. Commands, which are queries on the
         * special "$cmd" collection, should be allowed, but their collection
         * may need to be matched during the map JavaScript function. For normal
         * operations, the namespace may match an exact string or a regex of the
         * database prefix.
         */
        $query = ['ns' => [
            '$not' => new Regex('^' . preg_quote("$database.system.")),
            '$in' => [
                "$database.\$cmd",
                isset($collection) ? "$database.$collection" : new Regex('^' . preg_quote("$database.")),
            ],
        ]];

        $rs = $database->command([
            'mapreduce' => 'system.profile',
            'map' => new Javascript($this->code['map']),
            'reduce' => new Javascript($this->code['reduce']),
            'finalize' => new Javascript($this->code['finalize']),
            'out' => ['inline' => 1],
            'query' => $query,
            'scope' => [
                'database' => $database,
                'collection' => $collection,
                'skeleton' => new Javascript($this->code['skeleton']),
            ],
            'jsMode' => true,
        ])->toArray()[0];

        if ( ! $rs['ok']) {
            throw new \RuntimeException(
                isset($rs['errmsg']) ? $rs['errmsg'] : 'MapReduce error',
                isset($rs['code']) ? $rs['code'] : 0
            );
        }

        foreach ($rs['results'] as $i => $result) {
            $rs['results'][$i] = $result['_id']->getArrayCopy() + $result['value']->getArrayCopy();
            $rs['results'][$i]['ts']['min'] = $rs['results'][$i]['ts']['min']->toDateTime();
            $rs['results'][$i]['ts']['max'] = $rs['results'][$i]['ts']['max']->toDateTime();
        }

        return $rs['results'];
    }
}
