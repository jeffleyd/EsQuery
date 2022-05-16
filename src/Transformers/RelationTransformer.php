<?php

namespace Jeffleyd\EsLikeEloquent\Transformers;

use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;
use Jeffleyd\EsLikeEloquent\EsQuery;

class RelationTransformer
{
    public function __construct(private EsQuery $esQuery)
    {
    }

    public function transform(Elasticsearch|Promise $result): mixed {
        return ;
    }

}