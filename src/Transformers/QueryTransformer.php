<?php

namespace Jeffleyd\EsLikeEloquent\Transformers;

use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;
use Jeffleyd\EsLikeEloquent\Contracts\ResultTransformerContract;

class QueryTransformer implements ResultTransformerContract
{
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

        return $output;
    }

}