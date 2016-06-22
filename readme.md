[![Build Status](https://travis-ci.org/MindyPHP/Mindy_QueryBuilder.svg?branch=master)](https://travis-ci.org/MindyPHP/Mindy_QueryBuilder)
[![Latest Stable Version](https://poser.pugx.org/mindy/query_builder/v/stable)](https://packagist.org/packages/mindy/query_builder)
[![Total Downloads](https://poser.pugx.org/mindy/query_builder/downloads)](https://packagist.org/packages/mindy/query_builder)
[![License](https://poser.pugx.org/mindy/query_builder/license)](https://packagist.org/packages/mindy/query_builder)

# API

## Установка

```
composer require mindy/query_builder
```

Пример использования

```php
require('vendor/autoload.php'); // Composer autoloader

use Mindy\QueryBuilder\QueryFactory;
use Mindy\QueryBuilder\Mysql\Adapter;
use Mindy\QueryBuilder\LegacyLookupBuilder;

$pdo = new PDO(...);
// PDO является не обязательным, используется для экранирования
$factory = new QueryFactory(new Adapter($pdo), new LegacyLookupBuilder);

$qb = $factory->getQueryBuilder();
$sql = $qb->setTypeSelect()->setFrom('test')->setSelect('*')->toSQL();
echo $sql;
// Output: SELECT * FROM `test`
```

## Lookup

| Lookup | SQL | PHP |
|-----|----|----|
| raw | ```foo REGEX ?``` | ```$qb->setWhere(['foo__raw' => 'REGEX ?')``` |
| range | ```foo BETWEEN 1 AND 2``` | ```$qb->setWhere(['foo__range' => [1,2])``` |
| in | ```foo IN (1,2)``` | ```$qb->setWhere(['foo__in' => [1,2])``` |
| iendswith | ```lower(foo) LIKE %bar``` | ```$qb->setWhere(['foo__iendswith' => 'BAR'])``` |
| endswith | ```foo LIKE %bar``` | ```$qb->setWhere(['foo__endswith' => 'bar'])``` |
| istartswith | ```lower(foo) LIKE bar%``` | ```$qb->setWhere(['foo__istartswith' => 'BAR'])``` |
| startswith | ```foo LIKE bar%``` | ```$qb->setWhere(['foo__startswith' => 'bar'])``` |
| icontains | ```lower(foo) LIKE %bar%``` | ```$qb->setWhere(['foo__icontains' => 'BAR'])``` |
| contains | ```foo LIKE % bar%``` | ```$qb->setWhere(['foo__contains' => 'bar'])``` |
| exact | ```foo = 10``` | ```$qb->setWhere(['foo' => 10])``` |
| gte | ```foo >= 10``` | ```$qb->setWhere(['foo__gte' => 10])``` |
| gt | ```foo > 10``` | ```$qb->setWhere(['foo__gt' => 10])``` |
| lte | ```foo <= 10``` | ```$qb->setWhere(['foo__lte' => 10])``` |
| lt | ```foo < 10``` | ```$qb->setWhere(['foo__lt' => 10])``` |
| isnull | ```foo IS NOT NULL``` | ```$qb->setWhere(['foo__isnull' => false])``` |

## LegacyLookupBuilder

```php
$qb = new QueryBuilder(new MysqlAdapter($pdo), new LegacyLookupBuilder);
$qb
	->setTypeSelect()
	->setSelect('*')
	->setFrom('comment')
	->setWhere(['id__gte' => 1])
	->setOrder(['created_at']);
$pdo->query($qb->toSQL())->fetchAll();
```

## LookupBuilder

```php
$qb = new QueryBuilder(new MysqlAdapter($pdo), new LookupBuilder);
$qb
	->setTypeSelect()
	->setSelect('*')
	->setFrom('comment')
	->setWhere(['id' => ['gte' => 1]])
	->setOrder(['created_at']);
$pdo->query($qb->toSQL())->fetchAll();
```

## Известные проблемы

При использовании `LegacyLookupBuilder` невозможно использовать вложенные `lookup`ы. 

Пример:

```php
['created_at__second__gte' => 200]
```

`second, day, month, year, week_day, minute, hour` работают только в режиме `exact`.