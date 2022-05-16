<?php

namespace Jeffleyd\EsLikeEloquent\Presenters;

use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Jeffleyd\EsLikeEloquent\EsQuery;
use Jeffleyd\EsLikeEloquent\Transformers\ConditionTransformer;
use Jeffleyd\EsLikeEloquent\Transformers\QueryTransformer;

class QueryPresenter
{
    /**
     * @param EsQuery $esQuery
     */
    public function __construct(private EsQuery $esQuery)
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
        $conditionTransform = new ConditionTransformer($this->esQuery);
        $this->esQuery = $conditionTransform->transform();

        $result = $this->esQuery->client->search($this->esQuery->query);
        return (new QueryTransformer)->transform($result);
    }
}