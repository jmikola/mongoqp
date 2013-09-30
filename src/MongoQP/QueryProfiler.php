<?php

namespace MongoQP;

class QueryProfiler
{
    private $mongo;
    private $code;
    private $timezone;

    public function __construct(\MongoClient $mongo, array $code)
    {
        $this->mongo = $mongo;
        $this->code = $code;
        $this->timezone = new \DateTimeZone(ini_get('date.timezone'));
    }

    public function getDatabases()
    {
        return array_map(
            function($database) { return $database['name']; },
            $this->mongo->listDBs()['databases']
        );
    }

    public function getCollections($database)
    {
        return $this->mongo->selectDB($database)->getCollectionNames();
    }

    public function getProfilingLevel($database)
    {
        return $this->mongo->selectDB($database)->getProfilingLevel();
    }

    public function setProfilingLevel($database, $level)
    {
        $this->mongo->selectDB($database)->setProfilingLevel((int) $level);
    }

    public function getProfilingData($database, $collection = null)
    {
        $mongodb = $this->mongo->selectDB($database);

        // Ensure the database has a "system.profile" collection
        if ( ! in_array('system.profile', $mongodb->getCollectionNames(true))) {
            return array();
        }

        /* Exclude system collection queries. Commands, which are queries on the
         * special "$cmd" collection, should be allowed, but their collection
         * may need to be matched during the map JavaScript function. For normal
         * operations, the namespace may match an exact string or a regex of the
         * database prefix.
         */
        $query = ['ns' => [
            '$not' => new \MongoRegex('/^' . preg_quote("$database.system.") . '/'),
            '$in' => [
                "$database.\$cmd",
                isset($collection) ? "$database.$collection" : new \MongoRegex('/^' . preg_quote("$database.") . '/'),
            ],
        ]];

        $rs = $mongodb->command([
            'mapreduce' => 'system.profile',
            'map' => $this->code['map'],
            'reduce' => $this->code['reduce'],
            'finalize' => $this->code['finalize'],
            'out' => ['inline' => 1],
            'query' => $query,
            'scope' => [
                'database' => $database,
                'collection' => $collection,
                'skeleton' => $this->code['skeleton'],
            ],
            'jsMode' => true,
        ]);

        if ( ! $rs['ok']) {
            throw new \RuntimeException(
                isset($rs['errmsg']) ? $rs['errmsg'] : 'MapReduce error',
                isset($rs['code']) ? $rs['code'] : 0
            );
        }

        foreach ($rs['results'] as $i => $result) {
            $rs['results'][$i] = $result['_id'] + $result['value'];
            $rs['results'][$i]['ts']['min'] = new \DateTime('@' . $rs['results'][$i]['ts']['min']->sec);
            $rs['results'][$i]['ts']['max'] = new \DateTime('@' . $rs['results'][$i]['ts']['max']->sec);
        }

        return $rs['results'];
    }
}
