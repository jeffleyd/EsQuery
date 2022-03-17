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
$response = $build->where('parent_id', 1)->first(); // Example 1 <br>
$response = $build->where('parent_id', '=', 1)->first(); // Example 2 <br>
$response = $build->where('parent_id', 1)->get(); // Example 3 <br>
$response = $build->where('created_at', '>=' '2022-02-26'))->get(); // Example 4 <br>
```

##### Performing an aggregation
```
$build = new ESQuery('MY_INDEX');
$response = $build->where('parent_id', 1)->sum('price', 'total_price')->get(); // Use get() for aggregations <br>
```

##### Delete your document
```
$build = new ESQuery('MY_INDEX');<br><br>
$response = $build->where('parent_id', 1)->delete(); // Example 1 delete with conditions <br>
$response = $build->delete(5); // Example 2 delete by ID <br>
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
[x] Delete by ID <br>
[x] Delete by Query <br>

#### TYPE SEARCH
[x] <strong>FIRST</strong> (with/without conditions) <br>
[x] <strong>GET</strong> (with/without conditions) <br>
[x] <strong>PAGINATION</strong> (with/without conditions) <br>
[x] <strong>AGGREGATION</strong> MAX / MIN / SUM / AVG / COUNT <br>

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
