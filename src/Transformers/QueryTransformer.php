<?php

namespace Jeffleyd\EsLikeEloquent\Transformers;

use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;
use Jeffleyd\EsLikeEloquent\Contracts\ResultTransformerContract;
use Jeffleyd\EsLikeEloquent\EsQuery;

class QueryTransformer implements ResultTransformerContract
{
    public function __construct(private EsQuery $esQuery)
    {
    }

    /**
     * @param Elasticsearch|Promise $result
     * @return array
     */
    public function transform(Elasticsearch|Promise $result): array
    {
        $resultArray = $result->asArray();

        $output = [];
        if (isset($resultArray['hits']['hits'])) {
            $hits = $resultArray['hits']['hits'];
            foreach ($hits as $hit) {
                $output[] = $hit['_source'];
            }
        }

        /** Attach Relations */
        $output = (new RelationTransformer($this->esQuery))->transform($output);

        return $output;
    }

}