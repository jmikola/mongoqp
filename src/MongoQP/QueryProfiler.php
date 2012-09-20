<?php

namespace MongoQP;

class QueryProfiler
{
    // TODO: Remove this after MongoDB::getConnectionNames() is implemented
    const NS_ERROR = 'ns doesn\'t exist';

    private $mongo;
    private $code;
    private $timezone;

    public function __construct(\Mongo $mongo, array $code)
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
        return array_map(
            function($collection) { return $collection->getName(); },
            $this->mongo->selectDB($database)->listCollections()
        );
    }

    public function getProfilingLevel($database)
    {
        $mongodb = $this->mongo->selectDB($database);

        return $mongodb->getProfilingLevel();
    }

    public function setProfilingLevel($database, $level)
    {
        $mongodb = $this->mongo->selectDB($database);

        $mongodb->setProfilingLevel((int) $level);
    }

    public function getDatabaseProfiles($database)
    {
        $mongodb = $this->mongo->selectDB($database);

        return $this->getProfiles($mongodb);
    }

    public function getCollectionProfiles($database, $collection)
    {
        $mongodb = $this->mongo->selectDB($database);
        $options = ['query' => ['ns' => $database.'.'.$collection]];

        return $this->getProfiles($mongodb, $options);
    }

    private function getProfiles(\MongoDB $mongodb, array $options = array())
    {
        $rs = $mongodb->command([
            'mapreduce' => 'system.profile',
            'map' => $this->code['map'],
            'reduce' => $this->code['reduce'],
            'finalize' => $this->code['finalize'],
            'out' => ['inline' => 1],
            'scope' => ['clean' => $this->code['clean']],
            'jsMode' => true,
        ] + $options);

        if (!$rs['ok']) {
            if (isset($rs['errmsg']) && self::NS_ERROR === $rs['errmsg']) {
                return array();
            }

            throw new QueryProfilerException(
                isset($rs['errmsg']) ? $rs['errmsg'] : 'MapReduce error',
                isset($rs['code']) ? $rs['code'] : 0
            );
        }

        foreach ($rs['results'] as $i => $result) {
            $rs['results'][$i] = $result['_id'] + $result['value'];

            $rs['results'][$i]['ts']['min'] = new \DateTime('@' . $rs['results'][$i]['ts']['min']->sec);
            $rs['results'][$i]['ts']['max'] = new \DateTime('@' . $rs['results'][$i]['ts']['max']->sec);

            //$rs['results'][$i]['ts']['min']->setTimezone($this->timezone);
            //$rs['results'][$i]['ts']['max']->setTimezone($this->timezone);
        }

        return $rs['results'];
    }
}
