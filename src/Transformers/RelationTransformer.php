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
        if (count($this->esQuery->with)) {
            foreach ($resultArray as $result) {
                array_push($result, $this->attachRelation($result));
            }
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
        $relations = [];
        foreach ($this->esQuery->with as $relation) {

            /** @var Model $class */
            $class = 'App\\Models\\'.$relation['model'];
            $arrayPath = explode("\\", $relation['model']);
            $relationName = count($arrayPath) > 1 ? $arrayPath[count($arrayPath) - 1] : $relation['model'];
            $foreignKeyValue = $result[$relation['foreign_key']];

            $queryBuild = $class::where($relation['primary_key'], $foreignKeyValue);

            if ($relation['closure']) {
                $closure = $relation['closure'];
                $relations[$relationName] = $closure($queryBuild);
            } else {
                $relations[$relationName] = $queryBuild->get()->toArray();
            }
        }

        return $relations;
    }

}