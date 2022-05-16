<?php

namespace Jeffleyd\EsLikeEloquent\Transformers;

use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;
use Jeffleyd\EsLikeEloquent\Contracts\ResultTransformerContract;
use Jeffleyd\EsLikeEloquent\EsQuery;
use Jeffleyd\EsLikeEloquent\Presenters\AggregatorPresenter;

class AggregateTransformer implements ResultTransformerContract
{
    /**
     * @param AggregatorPresenter $presenter
     */
    public function __construct(public AggregatorPresenter $presenter)
    {
    }

    /**
     * @param Elasticsearch|Promise $result
     * @return array
     */
    public function transform(Elasticsearch|Promise $result): array
    {
        $resultArray = $result->asArray()['aggregations'];
        $output = [];

        foreach ($resultArray as $key => $value) {
            $output[$key] = $value['value'];
        }

        return $output;
    }

    /**
     * Used for changer structure of query with based variable constantScore
     * @return void
     */
    public function constantScore(): void
    {
        if ($this->presenter->esQuery->constantScore) {
            if (!count($this->presenter->esQuery->query['body']['query']['constant_score']['filter']['bool'])) {
                unset($this->presenter->esQuery->query['body']['query']);
            }
        } else {
            if (!count($this->presenter->esQuery->query['body']['query']['bool'])) {
                unset($this->presenter->esQuery->query['body']['query']);
            }
        }
    }

}