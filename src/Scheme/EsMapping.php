<?php

namespace Jeffleyd\EsLikeEloquent\Scheme;

use Jeffleyd\EsLikeEloquent\EsQuery;
use Elastic\Elasticsearch\Exception\AuthenticationException;

class EsMapping
{
    protected EsQuery $esQuery;
    protected array $mapping = [];

    /**
     * @throws AuthenticationException
     */
    public function __construct(string $index)
    {
        $this->esQuery = new EsQuery($index);
        $this->setDefaultAnalyzer();
    }

    /**
     * The text family, including text and match_only_text. Analyzed, unstructured text.
     * @param string $column
     */
    public function string(string $column)
    {
        $this->addColumn([$column => ['type' => 'text']]);
    }

    /**
     * A double-precision 64-bit IEEE 754 floating point number, restricted to finite values.
     * @param string $column
     */
    public function double(string $column)
    {
        $this->addColumn([$column => ['type' => 'double']]);
    }

    /**
     * A single-precision 32-bit IEEE 754 floating point number, restricted to finite values.
     * @param string $column
     */
    public function float(string $column)
    {
        $this->addColumn([$column => ['type' => 'float']]);
    }

    /**
     * A signed 64-bit integer with a minimum value of -263 and a maximum value of 263-1.
     * @param string $column
     */
    public function long(string $column)
    {
        $this->addColumn([$column => ['type' => 'long']]);
    }

    /**
     * A signed 32-bit integer with a minimum value of -231 and a maximum value of 231-1.
     * @param string $column
     */
    public function integer(string $column)
    {
        $this->addColumn([$column => ['type' => 'integer']]);
    }

    /**
     * A signed 16-bit integer with a minimum value of -32,768 and a maximum value of 32,767.
     * @param string $column
     */
    public function short(string $column)
    {
        $this->addColumn([$column => ['type' => 'short']]);
    }

    /**
     * A signed 8-bit integer with a minimum value of -128 and a maximum value of 127.
     * @param string $column
     */
    public function byte(string $column)
    {
        $this->addColumn([$column => ['type' => 'byte']]);
    }

    /**
     * True and false values.
     * @param string $column
     */
    public function boolean(string $column)
    {
        $this->addColumn([$column => ['type' => 'boolean']]);
    }

    /**
     * Binary value encoded as a Base64 string.
     * @param string $column
     */
    public function binary(string $column)
    {
        $this->addColumn([$column => ['type' => 'binary']]);
    }

    /**
     * A JSON object.
     * @param string $column
     */
    public function object(string $column)
    {
        $this->addColumn([$column => ['type' => 'object']]);
    }

    /**
     * An entire JSON object as a single field value.
     * @param string $column
     */
    public function flattened(string $column)
    {
        $this->addColumn([$column => ['type' => 'flattened']]);
    }

    /**
     * A JSON object that preserves the relationship between its subfields.
     * @param string $column
     */
    public function nested(string $column)
    {
        $this->addColumn([$column => ['type' => 'nested']]);
    }

    /**
     * Multiple formats can be specified by separating them with || as a separator.
     * Each format will be tried in turn until a matching format is found.
     * The first format will be used to convert the milliseconds-since-the-epoch value back into a string.
     * @param string $column
     */
    public function timestamp(string $column, string $format = 'yyyy-MM-dd HH:mm:ss')
    {
        $this->addColumn([$column => ['type' => 'date', 'format' => $format]]);
    }

    /**
     * Defines a parent/child relationship for documents in the same index.
     * @param string $column
     * @param string $firstKey
     * @param string $secondKey
     */
    public function join(string $column, string $firstKey, string $secondKey)
    {
        $this->addColumn([
            $column => [
                'type' => 'join',
                'relations' => [
                    $firstKey => $secondKey
                ]
            ]
        ]);
    }

    /**
     * Create a new mapping for index.
     */
    public function insert()
    {
        $this->esQuery->createIndex($this->mapping);
    }

    /**
     * Update a mapping for index.
     */
    public function update()
    {
        $this->esQuery->mapping($this->mapping);
    }

    public function drop()
    {
        if ($this->esQuery->existsIndex()) {
            $this->esQuery->deleteIndex();
        }
    }

    public function setDefaultAnalyzer()
    {
        $this->mapping['settings'] = [
            'analysis' => [
                'default_analyze' => [
                    'type' => 'custom',
                    'tokenizer' => 'whitespace',
                    'char_filter' => [
                        'html_strip'
                    ],
                    'filter' => [
                        'lowercase'
                    ]
                ]
            ]
        ];
    }

    private function addColumn(array $type)
    {
        $this->mapping['mappings']['properties'][key($type)] = $type[key($type)];
    }

    private function baseMapping(string $type, array $mapping)
    {
        $this->mapping['mappings'] = ['properties' => []];
    }
}