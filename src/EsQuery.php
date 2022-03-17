<?php

namespace Jeffleyd\ESLikeEloquent;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class EsQuery extends EsQueryAbstract
{
    protected EsQuery $instance;
    public Client $client;
    public $query;
    protected $beginConstruct;

    /**
     * @param string $index
     * @param bool $constantScore
     */
    public function __construct(string $index, protected bool $constantScore = true)
    {
        $this->index = strtolower(config('esquery.prefix').$index.config('esquery.suffix'));
        $this->client = ClientBuilder::create()
            ->setBasicAuthentication(config('esquery.username'), config('esquery.password'))
            ->setHosts(config('esquery.host'))
            ->build();

        $this->query = [
            'index' => $this->index,
            'body' => []
        ];

        if ($constantScore) {
            $this->beginConstruct = ['query' => ['constant_score' => ['filter' => ['bool' => []]]]];
        } else {
            $this->beginConstruct = ['query' => ['bool' => []]];
        }

        $this->updateQuery('body', $this->beginConstruct);

        $this->instance = $this;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        if ($this->hasAggregator) {
            if ($this->constantScore) {
                if (!count($this->query['body']['query']['constant_score']['filter']['bool'])) {
                    unset($this->query['body']['query']);
                }
            } else {
                if (!count($this->query['body']['query']['bool'])) {
                    unset($this->query['body']['query']);
                }
            }
            return $this->client->search($this->query)['aggregations'];
        }
        $this->hasCondition();
        return $this->outPut($this->client->search($this->query));
    }

    /**
     * @return array|null
     */
    public function first(): array|null
    {
        $this->hasCondition();
        $result = $this->outPut($this->client->search($this->query));
        if (isset($result[0])) {
            return $result[0];
        }
        return null;
    }

    /**
     * @param int $size
     * @param int $page
     * @return array
     */
    public function paginate(int $size, int $page = 1): array
    {
        $this->hasCondition();
        $this->query['body']['size'] = $size;
        $this->query['body']['from'] = $page > 1 ? $size * ($page-1) : 0;
        return $this->outPutPaginate($this->client->search($this->query), $size, $page);
    }

    /**
     * @param array $body
     * @param null $attr
     * @return array
     */
    public function create(array $body, $attr = null): array
    {
        if (!isset($body['id'])) {
            $_id = date('ymdHis');
            $body['id'] = intval($_id);
        }
        $this->query['body'] = $body;
        if ($attr) {
            $this->query[] = $attr;
        }
        $this->updateQuery('id', $body['id']);
        return $this->client->create($this->query);
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
            ]);
        } else {
            if (!$id) {
                throw new \Exception('Id required for deleting from document');
            }
        }

        return $this->client->delete([
            'id' => $id,
            'index' => $this->index
        ]);
    }

    public function update(int|string $id, array $body): array
    {
        return $this->client->update([
            'id' => $id,
            'index' => $this->index,
            'body' => $body
        ]);
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
        ]);
    }

    /**
     * caso seja a primeira vez, crie o index por aqui e depois insira os documentos nele.
     * @param array $body
     * @return array
     */
    public function createIndex(array $body): array
    {
        return $this->client->indices()->create([
            'index' => $this->index,
            'body' => $body
        ]);
    }

    /**
     * @return array
     */
    public function deleteIndex(): array
    {
        return $this->client->indices()->delete([
            'index' => $this->index
        ]);
    }
}
