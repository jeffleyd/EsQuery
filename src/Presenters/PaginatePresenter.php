<?php

namespace Jeffleyd\EsLikeEloquent\Presenters;

use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use Jeffleyd\EsLikeEloquent\EsQuery;
use Jeffleyd\EsLikeEloquent\Transformers\ConditionTransformer;
use Jeffleyd\EsLikeEloquent\Transformers\PaginateTransformer;

class PaginatePresenter
{
    /**
     * Max size of the pagination.
     * @var int
     */
    private int $max_per_page;

    /**
     * Total number of items per page.
     * @var int
     */
    public int $per_page = 15;

    /**
     * Page number.
     * @var int
     */
    public int $page = 1;

    /**
     * @param EsQuery $esQuery
     */
    public function __construct(public EsQuery $esQuery)
    {
        $this->max_per_page = config('esquery.max_per_page');
        $this->page = Request::input('page') ?? $this->page;
        $this->per_page = Request::input('per_page') ?? $this->per_page;
        if ($this->per_page > $this->max_per_page) {
            $this->per_page = $this->max_per_page;
        }
    }

    /**
     * @return LengthAwarePaginator
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function getResult(): LengthAwarePaginator
    {
        $conditionTransform = new ConditionTransformer($this->esQuery);
        $this->esQuery = $conditionTransform->transform();

        $paginateTransform = new PaginateTransformer($this);
        $paginateTransform->setPerPage($this->per_page);
        $paginateTransform->setPage($this->page, $this->per_page);

        $response = $this->esQuery->client->search($this->esQuery->query);
        return $paginateTransform->transform($response);
    }
}