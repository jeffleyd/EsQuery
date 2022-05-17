<?php

namespace Jeffleyd\EsLikeEloquent\Scheme;

use \Elastic\Elasticsearch\Exception\AuthenticationException;

class EsScheme
{
    /**
     * @param string $table
     * @param \Closure $callback
     * @throws AuthenticationException
     */
    static public function create(string $table, \Closure $callback)
    {
        $EsMapping = new EsMapping($table);
        $callback($EsMapping);
        $EsMapping->insert();
    }

    /**
     * @param string $table
     * @param \Closure $callback
     * @throws AuthenticationException
     */
    static public function table(string $table, \Closure $callback)
    {
        $EsMapping = new EsMapping($table);
        $callback($EsMapping);
        $EsMapping->update();

    }

    /**
     * @param string $table
     * @throws AuthenticationException
     */
    static public function dropIfExists(string $table)
    {
        $EsMapping = new EsMapping($table);
        $EsMapping->drop();
    }

}