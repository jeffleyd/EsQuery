<?php

namespace Jeffleyd\EsLikeEloquent\Transformers;

use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Jeffleyd\EsLikeEloquent\Contracts\ResultTransformerContract;
use Jeffleyd\EsLikeEloquent\Presenters\PaginatePresenter;

class PaginateTransformer implements ResultTransformerContract
{
    /**
     * @param PaginatePresenter $presenter
     */
    public function __construct(public PaginatePresenter $presenter)
    {
    }

    /**
     * @param Elasticsearch|Promise $result
     * @return LengthAwarePaginator
     */
    public function transform(Elasticsearch|Promise $result): LengthAwarePaginator
    {
        $resultArray = $result->asArray();

        $page = $this->presenter->page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = collect($this->extractHits($result));

        return new LengthAwarePaginator($items, $this->extractTotal($resultArray), $this->presenter->per_page, $page, []);
    }

    /**
     * Set total per page.
     * @param int $perPage
     */
    public function setPerPage(int $perPage): void
    {
        $this->presenter->esQuery->query['body']['size'] = $perPage;
    }

    /**
     * Set page number.
     * @param int $page
     * @param int $perPage
     */
    public function setPage(int $page, int $perPage): void
    {
        $this->presenter->esQuery->query['body']['from'] = $page > 1 ? $perPage * ($page-1) : 0;
    }

    /**
     * Extract hits from result.
     * @param Elasticsearch|Promise $result
     * @return array
     */
    private function extractHits(Elasticsearch|Promise $result): array
    {
        return (new QueryTransformer($this->presenter->esQuery))->transform($result);
    }

    /**
     * Extract total from result.
     * @param array $resultArray
     * @return int
     */
    private function extractTotal(array $resultArray): int
    {
        return $resultArray['hits']['total']['value'] ?? 0;
    }
}