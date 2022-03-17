## EsQuery
This project is to be similar to the eloquent query, like this
facilitating searches in ElasticSearch's <strong>Lucene</strong>.
<br><br>

```
composer jeffleyd/esquery
```

#### USAGE EXAMPLES
##### First create a mapping for your index
###### For more information about mapping types: https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-types.html

```
$build = new ESQuery('MY_INDEX');
$response = $build->createIndex([
        'mappings' => [
            'properties' => [
                'parent_id' => [
                    'type' => 'long',
                ],
                'created_at' => [
                    'type' => 'date',
                    'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd'
                ]
            ]
        ]
    ]);
```

##### Now you can create your first document

```
$build = new ESQuery('MY_INDEX');
$response = $build->create([
        'parent_id' => 1,
        'created_at' => '2022-02-26 23:44:00',
    ]);
```

##### Find your document
```
$build = new ESQuery('MY_INDEX');
$response = $build->where('parent_id', 1)->first(); // Example 1
$response = $build->where('parent_id', '=', 1)->first(); // Example 2
$response = $build->where('parent_id', 1)->get(); // Example 3
$response = $build->where('created_at', '>=' '2022-02-26'))->get(); // Example 4
```

##### Performing an aggregation
```
$build = new ESQuery('MY_INDEX');
$response = $build->where('parent_id', 1)->sum('price', 'total_price')->get(); // Use get() for aggregations
```

##### Delete your document
```
$build = new ESQuery('MY_INDEX');
$response = $build->where('parent_id', 1)->delete(); // Example 1 delete with conditions
$response = $build->delete(5); // Example 2 delete by ID
```

##### Delete your index
```
$build = new ESQuery('MY_INDEX');
$response = $build->deleteIndex(); 
```

<br><br>
#### INDEX
[x] Create <br>
[x] Delete <br>
[x] Update mapping <br>

#### DOCUMENT
[x] Create <br>
[x] Update <br>
[x] Delete by ID
[x] Delete by Query

#### TYPE SEARCH
[x] <strong>FIRST</strong> (with/without conditions)
[x] <strong>GET</strong> (with/without conditions)
[x] <strong>PAGINATION</strong> (with/without conditions)
[x] <strong>AGGREGATION</strong> MAX / MIN / SUM / AVG / COUNT

#### CONDITIONS
[x] where <br>
[x] whereIn <br>
[x] whereExists <br>
[x] whereNotExists <br>
[x] whereMissing <br>
[x] between <br>

#### ELASTIC SEARCH
Site: https://www.elastic.co/guide/en/elasticsearch/reference/current/index.html
Version: 8.1