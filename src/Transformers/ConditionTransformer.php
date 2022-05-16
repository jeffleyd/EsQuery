<?php

namespace Jeffleyd\EsLikeEloquent\Transformers;

use Jeffleyd\EsLikeEloquent\EsQuery;

class ConditionTransformer
{
    /**
     * @param EsQuery $esQuery
     */
    public function __construct(private EsQuery $esQuery)
    {
    }

    /**
     * Used for list all result if you don't have condition
     * @return EsQuery
     */
    public function transform(): EsQuery
    {
        if (!$this->esQuery->hasCondition) {
            unset($this->esQuery->query['body']['query']);
            $this->esQuery->query['body']['query']['match_all'] = (Object)[];
        }

        return $this->esQuery;
    }

}