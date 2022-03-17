<?php

namespace Jeffleyd\EsLikeEloquent;

class EsConditions
{
    const OPERATORS = ['>=', '>', '<=', '<'];

    public $hasAggregator;
    public $hasCondition;

    /**
     * @param string $column
     * @param string|int $value
     * @param string $type
     * @param string $operator
     * @param string $typeSearch
     * @return EsQuery
     */
    public function where(string $column, string $operatorOrValue, string|int $value = '', string $type = 'must', string $typeSearch = 'term'): EsQuery
    {
        if ($operatorOrValue === '=' or !in_array($operatorOrValue, self::OPERATORS)) {
            $value = $operatorOrValue === '=' ? $value : $operatorOrValue;
            return $this->whereStructure($typeSearch, $column, $value, $type);
        } else {
            return $this->whereOperatorStructure($operatorOrValue, $column, $value, $type);
        }
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param string $type
     * @return EsQuery
     */
    public function whereExists(string $column, string|int $value, string $type = 'must'): EsQuery
    {
        return $this->whereStructure('term', $column, $value, $type);
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param string $type
     * @return EsQuery
     */
    public function whereNotExists(string $column, string|int $value, string $type = 'must_not'): EsQuery
    {
        return $this->whereStructure('term', $column, $value, $type);
    }

    /**
     * @param string $column
     * @param array $value
     * @param string $type
     * @return EsQuery
     */
    public function whereIn(string $column, array $value, string $type = 'must'): EsQuery
    {
        return $this->whereStructure('terms', $column, $value, $type);
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param string $type
     * @return EsQuery
     */
    public function whereMissing(string $column, string|int $value, string $type = 'must'): EsQuery
    {
        return $this->whereStructure('missing', $column, $value, $type);
    }

    /**
     * @param string $typeSearch
     * @param string $column
     * @param string|array $value
     * @param string $type
     * @return EsQuery
     */
    private function whereStructure(string $typeSearch, string $column, string|array $value, string $type = 'must'): EsQuery
    {
        $this->hasCondition = true;
        if ($this->constantScore) {
            $this->query['body']['query']['constant_score']['filter']['bool'][$type][][$typeSearch][$column] = $value;
        } else {
            $this->query['body']['query']['bool'][$type][][$typeSearch][$column] = $value;
        }

        return $this->instance;
    }

    /**
     * @param $operator
     * @param $column
     * @param $value
     * @param $type
     * @return EsQuery
     */
    private function whereOperatorStructure($operator, $column, $value, $type): EsQuery
    {
        $this->hasCondition = true;
        $value = match ($operator) {
            '>=' => ['gte' => $value],
            '>' => ['gt' => $value],
            '<=' => ['lte' => $value],
            '<' => ['lt' => $value],
            default => '',
        };

        if ($value) {
            if ($this->constantScore) {
                $this->query['body']['query']['constant_score']['filter']['bool'][$type][]['range'][$column] = $value;
            } else {
                $this->query['body']['query']['bool'][$type][]['range'][$column] = $value;
            }
        }

        return $this->instance;
    }

    /**
     * @param string $column
     * @param string $from
     * @param string $to
     * @param string $type
     * @return EsQuery
     */
    public function between(string $column, string $from, string $to, string $type = 'must'): EsQuery
    {
        $this->hasCondition = true;
        if ($this->constantScore) {
            $this->query['body']['query']['constant_score']['filter']['bool'][$type][]['range'][$column] = ['gte' => $from, 'lte' => $to];
        } else {
            $this->query['body']['query']['bool'][$type][]['range'][$column] = ['gte' => $from, 'lte' => $to];
        }

        return $this->instance;
    }

    /**
     * @param string $column
     * @param string $as
     * @return EsQuery
     */
    public function count(string $column, string $as): EsQuery
    {
        $this->hasAggregator = true;
        $this->query['body']['aggs'][$as]['value_count']['field'] = $column;
        return $this->instance;
    }

    /**
     * @param string $column
     * @param string $as
     * @return EsQuery
     */
    public function sum(string $column, string $as): EsQuery
    {
        $this->hasAggregator = true;
        $this->query['body']['aggs'][$as]['sum']['field'] = $column;
        return $this->instance;
    }

    /**
     * @param string $column
     * @param string $as
     * @return EsQuery
     */
    public function avg(string $column, string $as): EsQuery
    {
        $this->hasAggregator = true;
        $this->query['body']['aggs'][$as]['avg']['field'] = $column;
        return $this->instance;
    }

    /**
     * @param string $column
     * @param string $as
     * @return EsQuery
     */
    public function max(string $column, string $as): EsQuery
    {
        $this->hasAggregator = true;
        $this->query['body']['aggs'][$as]['max']['field'] = $column;
        return $this->instance;
    }

    /**
     * @param string $column
     * @param string $as
     * @return EsQuery
     */
    public function min(string $column, string $as): EsQuery
    {
        $this->hasAggregator = true;
        $this->query['body']['aggs'][$as]['min']['field'] = $column;
        return $this->instance;
    }

}
