## EsQuery
This project is to be similar to the eloquent query, like this
facilitating searches in ElasticSearch's <strong>Lucene</strong>.
<br><br>

```
composer require jeffleyd/esquery
```

#### PUBLISH THE FILE CONFIG

```
php artisan vendor:publish --tag="esquery-provider"
```

Access the config folder and change the settings of the esquery.php file.

#### USAGE EXAMPLES
##### First create a mapping for your index
###### For more information about mapping types: https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-types.html

```
$build = EsQuery::index('my_index');
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
$build = EsQuery::index('my_index');
$response = $build->create([
        'parent_id' => 1,
        'created_at' => '2022-02-26 23:44:00',
    ]);
```

##### Find your document
```
$build = EsQuery::index('my_index');
$response = $build->where('parent_id', 1)->first(); // Example 1
$response = $build->where('parent_id', '=', 1)->first(); // Example 2
$response = $build->where('parent_id', 1)->get(); // Example 3
$response = $build->where('created_at', '>=' '2022-02-26'))->get(); // Example 4
```

##### Performing an aggregation
```
$build = EsQuery::index('my_index');
$response = $build->where('parent_id', 1)->sum('price', 'total_price')->get(); // Use get() for aggregations
```

##### Delete your document
```
$build = EsQuery::index('my_index');
$response = $build->where('parent_id', 1)->delete(); // Example 1 delete with conditions
$response = $build->delete(5); // Example 2 delete by ID
```

##### Delete your index
```
$build = EsQuery::index('my_index');
$response = $build->deleteIndex(); 
```

##### How you can attach relation
```
$build = EsQuery::index('my_index');
$response = $build->with('category', 'id', 'group_id')->get();

OR

$build = EsQuery::index('my_index');
$response = $build->with('category', 'id', 'group_id', function (QueryBuilder $query) {
    $query->where('is_active', 1)->withTrashed->get();
})->with('boss', 'id', 'boss_id', function (QueryBuilder $query) {
    $query->where('is_active', 1)->withTrashed->get();
})->get();
```

<br><br>
#### INDEX
[x] Create <br>
[x] Delete <br>
[x] Update mapping <br>
[x] Exists <br>
[x] Skip <br>

#### DOCUMENT
[x] Create <br>
[x] Create Many <br>
[x] Update <br>
[x] Delete by ID <br>
[x] Delete by Query <br>

#### TYPE SEARCH
[x] <strong>FIRST</strong> (with/without conditions) <br>
[x] <strong>GET</strong> (with/without conditions) <br>
[x] <strong>PAGINATION</strong> (with/without conditions) <br>
[x] <strong>AGGREGATION</strong> MAX / MIN / SUM / AVG / COUNT <br>
[x] <strong>LIMIT</strong><br>
[x] <strong>GROUP BY</strong><br>
[x] <strong>GROUP BY DATE</strong><br>

#### CONDITIONS
[x] where <br>
[x] whereIn <br>
[x] whereExists <br>
[x] whereNotExists <br>
[x] whereMissing <br>
[x] between <br>
[x] orderBy <br>

#### ADDITIONAL
[x] with <br>
[x] reset query after get/first/paginate <br>

#### ELASTIC SEARCH
Site: https://www.elastic.co/guide/en/elasticsearch/reference/current/index.html
<br>Version: 8.1
