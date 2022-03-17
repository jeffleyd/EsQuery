<?php

namespace Jeffleyd\ESLikeEloquent;

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
     * @param int $size
     * @param int $page
     * @return array
     */
    protected function outPutPaginate(array $hits, int $size, int $page): array
    {
        if (count($hits)) {
            $total = $hits['hits']['total']['value'];

            $result = [
                'total' => $total,
                'per_page' => $size,
                'current_page' => $page,
                'last_page' => intval(ceil($total/$size)),
                'data' => $this->outPut($hits)
            ];

            return $result;
        }

        return $hits;
    }
}
