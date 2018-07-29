# Laravel Cacheable trait

## Description

This trait allows you to easily cache your model's data through a variety of handful methods.

**NOTE:** 

- `Cache is automatically cleared when a given model is saved/deleted`

## Installation

```
composer require bluesik/laravel-cacheable
```

## Usage

Add at the top of your model file:

```php
<?php

use Bluesik\LaravelCacheable\Cacheable;
```

Then within a class:

```php
use Cacheable;
```

## Available properties

```php
protected $cacheExpiry;
```

- **`Integer`** `$cacheExpiry`
	- `A number of minutes till the cache expires.`
	- `Defaults to 24 hours`

```php
protected static $fullModelCaching
```
- **`Boolean`** `$fullModelCaching`
	- `Indicates whether models should be cached directly or converted to an array`
	- `Defaults to true`

```php
protected static $bustCacheOnSaved
```

- **`Boolean`** `$bustCacheOnSaved`
    - `Should cache be busted upon saving a model`
    - `Defaults to true`

```php
protected static $bustCacheOnDeleted
```

- **`Boolean`** `$bustCacheOnDeleted`
    - `Should cache be busted upon deleting a model`
    - `Defaults to true`



## Available methods

#### Get latest records

```php
Model::getLatest($limit, $with, $orderBy);
```

- **`Integer`** `$limit`
  - `How many records to getAll`
  - `Defaults to: 3`

- **`String`** `$orderBy`
  - `$orderBy - What column to use when sorting the data`
  - `Defaults to: created_at`

- **`Array`** `$with`
  - `$with - List of relationships to eager load`
  - `Defaults to: An empty array`


> Returns a Collection or an array

---

#### Get a single record by its id

```php
Model::getById($id);
```


- **`Integer`** `$id`
  - `Id of a record you want to get`

> Returns a Model, an array or null

---

#### Get records where

```php
Model::getWhere($column, $value, $with);
```

- **`String`** `$column`
  - `Column name`
  - `Defaults to: id`
- **`Mixed`** `$value`
  - `Value to look for`
  - `Defaults to: an empty string`
- **`Array`** `$with`
  - `An array of relationships to eager load`
  - `Defaults to: an empty array` 
  
> Returns a Collection or an array

---

### Get all records

```php
Model::getAll($with);
```

- **`Array`** `$with`
  - `An array of relationships to eager load`
  - `Defaults to: an empty array` 

> Returns a Collection or an array

---

### Clear cache for a given model

```php
Model::clearCache(); 
```

---

### Enable full model caching

```php
Model::enableFullModelCaching(); 
```

---

### Disable full model caching (use arrays instead)

```php
Model::disableFullModelCaching(); 
```

## License

MIT
