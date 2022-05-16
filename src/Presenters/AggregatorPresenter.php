<?php

namespace Jeffleyd\EsLikeEloquent\Presenters;

use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Jeffleyd\EsLikeEloquent\EsQuery;
use Jeffleyd\EsLikeEloquent\Transformers\AggregateTransformer;

class AggregatorPresenter
{
    /**
     * @param EsQuery $esQuery
     */
    public function __construct(public EsQuery $esQuery)
    {
    }

    /**
     * @return array
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function getResult(): array
    {
        $aggregationTransform = new AggregateTransformer($this);
        $aggregationTransform->constantScore();

        $result = $this->esQuery->client->search($this->esQuery->query);
        return $aggregationTransform->transform($result);
    }
}