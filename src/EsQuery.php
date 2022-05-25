<?php

namespace Jeffleyd\EsLikeEloquent;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Jeffleyd\EsLikeEloquent\Presenters\AggregatorPresenter;
use Jeffleyd\EsLikeEloquent\Presenters\PaginatePresenter;
use Jeffleyd\EsLikeEloquent\Presenters\QueryPresenter;

class EsQuery extends EsConditions
{

    /**
     * @var Client
     */
    public Client $client;

    /**
     * Structure of the query
     * @var array
     */
    public $query = [];

    /**
     * Flag for verify if the query has aggregations.
     * @var bool
     */
    public bool $hasAggregator = false;

    /**
     * Flag for verify if the query has conditions.
     * @var bool
     */
    public bool $hasCondition = false;

    /**
     * Relations to be eager loaded
     * @var array
     */
    public array $with = [];

    /**
     * @var EsQuery
     */
    protected EsQuery $instance;

    /**
     * Array initial for constructing of the query with based variable constantScore.
     * @var array
     */
    protected array $beginConstruct;

    /**
     * @param bool $constantScore
     * @throws AuthenticationException
     */
    public function __construct(public bool $constantScore = false)
    {
        $this->client = ClientBuilder::create()
            ->setBasicAuthentication(config('esquery.username'), config('esquery.password'))
            ->setHosts(config('esquery.host'))
            ->build();

        $this->query = [
            'body' => []
        ];

        if ($constantScore) {
            $this->beginConstruct = ['query' => ['constant_score' => ['filter' => ['bool' => []]]]];
        } else {
            $this->beginConstruct = ['query' => ['bool' => []]];
        }

        $this->query['body'] = $this->beginConstruct;

        $this->instance = $this;
    }

    /**
     * @param bool $constantScore
     * @return $this
     */
    public function constantScore(bool $constantScore = false): self
    {
        if ($constantScore) {
            $this->beginConstruct = ['query' => ['constant_score' => ['filter' => ['bool' => []]]]];
        } else {
            $this->beginConstruct = ['query' => ['bool' => []]];
        }

        $this->query['body'] = $this->beginConstruct;

        return $this;
    }

    /**
     * @param string $index
     * @return $this
     */
    public function index(string $index): self
    {
        $this->index = strtolower(config('esquery.prefix').$index.config('esquery.suffix'));
        $this->query['index'] = $this->index;
        return $this;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        if ($this->hasAggregator) {
            return (new AggregatorPresenter($this))->getResult();
        }

        return (new QueryPresenter($this))->getResult();
    }

    /**
     * @return array|null
     */
    public function first(): array|null
    {
        $result = (new QueryPresenter($this))->getResult();
        if (isset($result[0])) {
            return $result[0];
        }
        return null;
    }

    /**
     * @param array $field
     */
    public function select(array $field): EsQuery
    {
        $this->query['body']['fields'] = $field;
        return $this;
    }

    /**
     * @param string $field
     * @param string $order
     * @return $this
     */
    public function orderBy(string $field, string $order = 'asc'): self
    {
        $this->query['body']['sort'][] = [$field => $order];
        return $this;
    }

    /**
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        return (new PaginatePresenter($this))->getResult();
    }

    /**
     * @param string $model
     * @param string|int $primaryKey
     * @param string|int $foreignKey
     * @return $this
     */
    public function with(string $model, string|int $primaryKey, string|int $foreignKey, \Closure $closure = null): EsQuery
    {
        $this->with[] = [
            'model' => $model,
            'primary_key' => $primaryKey,
            'foreign_key' => $foreignKey,
            'closure' => $closure
        ];
        return $this;
    }

    /**
     * @param int $limit
     */
    public function limit(int $limit): self
    {
        $this->query['body']['size'] = $limit;
        return $this;
    }

    /**
     * @param int $skip
     * @return $this
     */
    public function skip(int $skip): self
    {
        $this->query['body']['from'] = $skip;
        return $this;
    }

    /**
     * @param array $body
     * @param null $attr
     * @return array
     */
    public function create(array $body, $attr = null): array
    {
        if (!isset($body['id'])) {
            $body['id'] = time();
        }
        $this->query['body'] = $body;
        if ($attr) {
            $this->query[] = $attr;
        }
        $this->query['id'] = $body['id'];
        return $this->client->create($this->query)->asArray();
    }

    /**
     * @param array $items
     * @return array
     */
    public function createMany(array $items): array
    {
        foreach ($items as $index => $item) {

            if (!isset($item['id'])) {
                $item['id'] = time();
            }

            $this->query['body'][] = [
                'create' => [
                    '_index' => $this->index,
                    '_id' => $item['id'],
                ],
            ];

            $this->query['body'][] = $item;
        }
        unset($this->query['body']['query']);
        return $this->client->bulk($this->query)->asArray();
    }

    /**
     * @param int|string $id
     * @return array
     */
    public function delete(int|string $id = 0): array
    {
        if ($this->hasCondition) {
            return $this->client->deleteByQuery([
                'index' => $this->index,
                'body' => $this->query,
            ])->asArray();
        } else {
            if (!$id) {
                throw new \Exception('Id required for deleting from document');
            }
        }

        return $this->client->delete([
            'id' => $id,
            'index' => $this->index
        ])->asArray();
    }

    /**
     * @param int|string $id
     * @param array $body
     * @return array
     */
    public function update(int|string $id, array $body): array
    {
        return $this->client->update([
            'id' => $id,
            'index' => $this->index,
            'body' => $body
        ])->asArray();
    }

    /**
     * 'properties' => [
            'title' => [
                'type' => 'text',
            ],
        ]
     * @param array $body
     * @param string $index
     * @return array
     */
    public function mapping(array $body): array
    {
        return $this->client->indices()->putMapping([
            'index' => $this->index,
            'body' => $body
        ])->asArray();
    }

    /**
     * @param array $body
     * @return array
     */
    public function createIndex(array $body): array
    {
        return $this->client->indices()->create([
            'index' => $this->index,
            'body' => $body
        ])->asArray();
    }

    /**
     * @return bool
     */
    public function existsIndex(): bool {
        return $this->client->indices()->exists([
            'index' => $this->index
        ])->asBool();
    }

    /**
     * @return array
     */
    public function deleteIndex(): array
    {
        return $this->client->indices()->delete([
            'index' => $this->index
        ])->asArray();
    }
}
