<?php

namespace Jeffleyd\EsLikeEloquent;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;

class EsQueryAbstract extends EsConditions
{
    /**
     * @param array $result
     * @return array
     */
    protected function outPut(array $result): array
    {
        return $this->formattedOutPut($result['hits']['hits']);
    }

    /**
     * @param string $index
     * @param array|int|string $value
     */
    protected function updateQuery(string $index, array|int|string $value)
    {
        $this->query[$index] = $value;
    }

    protected function hasCondition()
    {
        if (!$this->hasCondition) {
            unset($this->query['body']['query']);
            $this->query['body']['query']['match_all'] = (Object)[];
        }
    }

    /**
     * @param array $hits
     * @return array
     */
    protected function formattedOutPut(array $hits): array
    {
        if (count($hits)) {
            $output = [];
            foreach ($hits as $hit) {
                $output[] = $hit['_source'];
            }

            return $output;
        }

        return $hits;
    }

    /**
     * @param array $hits
     * @param int $limit
     * @param array $options
     * @return LengthAwarePaginator
     */
    protected function outPutPaginate(array $hits, int $limit, array $options = []): LengthAwarePaginator
    {
        $page = Request::input('page') ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = collect($this->outPut($hits));

        if (count($hits)) {
            $total = $hits['hits']['total']['value'];
            return new LengthAwarePaginator($items, $total, $limit, $page, $options);
        }

        return new LengthAwarePaginator($items, 0, $limit, $page, $options);
    }
}
