<?php

namespace Jeffleyd\EsLikeEloquent\Transformers;

use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;
use Illuminate\Database\Eloquent\Model;
use Jeffleyd\EsLikeEloquent\EsQuery;

class RelationTransformer
{
    public function __construct(private EsQuery $esQuery)
    {
    }

    public function transform(array $resultArray): mixed
    {
        $newResult = [];
        if (count($this->esQuery->with)) {
            foreach ($resultArray as $result) {
                $newResult[] = $this->attachRelation($result);
            }
            $resultArray = $newResult;
        }

        return $resultArray;
    }

    /**
     * Attach the relation to the model.
     * @param array $result
     * @return array
     */
    private function attachRelation(array $result): array
    {
        foreach ($this->esQuery->with as $relation) {

            /** @var Model $class */
            $class = 'App\\Models\\'.$relation['model'];
            $arrayPath = explode("\\", $relation['model']);
            $relationName = count($arrayPath) > 1 ? $arrayPath[count($arrayPath) - 1] : $relation['model'];
            $foreignKeyValue = $result[$relation['foreign_key']];

            $queryBuild = $class::where($relation['primary_key'], $foreignKeyValue);

            if ($relation['closure']) {
                $closure = $relation['closure'];
                $result[$relationName] = $closure($queryBuild);
            } else {
                $result[$relationName] = $queryBuild->get()->toArray();
            }

        }

        return $result;
    }

}