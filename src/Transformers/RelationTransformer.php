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

    public function transform(array $resultArray): array
    {
        $newResult = [];
        if (count($this->esQuery->with)) {

            $this->fetchDataForWith($resultArray);

            foreach ($resultArray as $result) {
                $newResult[] = $this->attachRelation($result);
            }

            $resultArray = $newResult;
        }

        return $resultArray;
    }

    private function fetchDataForWith(array $resultArray): void
    {
        $newWith = [];

        foreach ($this->esQuery->with as $relation) {
            $relation_ids = [];

            foreach ($resultArray as $result) {
                if ($result[$relation['foreign_key']]) {
                    $relation_ids[] = $result[$relation['foreign_key']];
                }
            }

            /** @var Model $class */
            $class = 'App\\Models\\'.$relation['model'];

            if (!class_exists($class)) {
                $class = $relation['model'];
            }

            $queryBuild = $class::whereIn($relation['primary_key'], $relation_ids);

            if ($relation['closure']) {
                $closure = $relation['closure'];
                $relation['data'] = $closure($queryBuild)->get();
            } else {
                $relation['data'] = $queryBuild->get();
            }

            $newWith[] = $relation;
        }

        $this->esQuery->with = $newWith;
    }

    /**
     * Attach the relation to the model.
     * @param array $result
     * @return array
     */
    private function attachRelation(array $result): array
    {
        foreach ($this->esQuery->with as $relation) {
            $arrayPath = explode("\\", $relation['model']);
            $relationName = count($arrayPath) > 1 ? $arrayPath[count($arrayPath) - 1] : $relation['model'];

            foreach ($relation['data'] as $data) {
                if ($data[$relation['primary_key']] == $result[$relation['foreign_key']]) {
                    $result[strtolower($relationName)][] = $data;
                }
            }
        }

        if (!isset($result[strtolower($relationName)])) {
            $result[strtolower($relationName)] = [];
        } else {
            if (count($result[strtolower($relationName)]) == 1) {
                $result[strtolower($relationName)] = $result[strtolower($relationName)][0];
            }
        }

        return $result;
    }

}