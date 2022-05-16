<?php

namespace Jeffleyd\EsLikeEloquent\Contracts;

use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;

interface ResultTransformerContract
{
    /**
     * Transform the given result.
     *
     * @param  mixed  $result
     * @return mixed
     */
    public function transform(Elasticsearch|Promise $result): mixed;
}