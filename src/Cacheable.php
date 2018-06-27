<?php 

namespace Bluesik\LaravelCacheable;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait Cacheable{
	
	/**
	 * Set the cache expiry time in minutes.
	 *
	 * @var int
	 */
	protected $cacheExpiry = 60*24;

	/**
	 * Cache full models? Otherwise toArray() is used.
	 *
	 * @var bool
	 */
	protected static $fullModelCaching = true;

	/**
	 * Bust cache when model is created/saved
	 *
	 * @var bool
	 */
	protected static $bustCacheOnSaved = true;

	/**
	 * Bust cache when the model is deleted
	 *
	 * @var bool
	 */
	protected static $bustCacheOnDeleted = true;
	
	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	public static function boot()
	{
		parent::boot();

		if(static::$bustCacheOnSaved){
			static::saved(function ($model) {
				$model->clearCache();
			});
		}

		if(static::$bustCacheOnDeleted){
			static::deleted(function ($model) {
				$model->clearCache();
			});
		}
	}

	/**
	 * Get latest records
	 * @param  integer $limit Limit a number of records
	 * @param  string $orderBy Column name by which the records will be sorted
	 * @param  array $with Relationships to eager load
	 * @return Collection|array
	 */
	protected function getLatest ($limit = 3, $orderBy = 'created_at', $with = []){
		$cacheKey = $this->getTable() . ".latest";

		$results  = Cache::remember($cacheKey, $this->cacheExpiry, function () use ($limit, $with, $orderBy) {
			$data = $this->latest($orderBy)->take($limit)->get();

			if(!empty($with)){
				$data->load($with);
			}
			
			return $this->getResults($data); 
		});

		return $results;
	}

	/**
	 * Get a single record by its id
	 * @param  integer $id Item id
	 * @param  array $with Relationships to eager load
	 * @return Model|array|null
	 */
	protected function getById ($id, $with = []){
		$cacheKey = $this->getTable() . ".id.{$id}";

		$results  = Cache::remember($cacheKey, $this->cacheExpiry, function () use ($id, $with) {
			if($model = $this->find($id)){
				if(!empty($with)){
					$model->load($with);	
				}

				return $this->getResults($model); 
			}

			return null;
		});

		$this->rememberWhereQuery("id.{$id}");

		return $results;
	}

	/**
	 * Get by column
	 * @param  string $column Column name
	 * @param  mixed $value Value to look for
	 * @param  array $with Relationships to eager load
	 * @return Collection|array
	 */
	protected function getWhere ($column = 'id', $value = '', $with = []){	
		$cacheKey = $this->getTable() . ".{$column}.{$value}";

		$results  = Cache::remember($cacheKey, $this->cacheExpiry, function () use ($column, $value, $with) {
			$data = $this->where($column, $value)->get();

			if(!empty($with)){
				$data->load($with);	
			}

			return $this->getResults($data); 
		});

		$this->rememberWhereQuery("{$column}.{$value}");

		return $results;
	}

	/**
	 * Get all records
	 * @param  array $with Relationships to eager load
	 * @return Collection|array
	 */
	protected function getAll ($with = []){
		$cacheKey = $this->getTable() . ".all";

		$results  = Cache::remember($cacheKey, $this->cacheExpiry, function () use ($with) {
			$data = $this->latest()->get();
			
			if(!empty($with)){
				$data->load($with);	
			}

			return $this->getResults($data); 
		});

		return $results;
	}

	/**
	 * Clear model's cache
	 * @return void
	 */
	protected function clearCache (){
		$this->forgetWhereQueries();
		Cache::forget($this->getTable() . ".all");
		Cache::forget($this->getTable() . ".latest");
	}

	/**
	 * Get cached data
	 *
	 * @param Collection $data
	 * @return Collection|array
	 */
	protected function getResults ($data){
		if(static::$fullModelCaching)
			return $data;
		
		return $data->toArray();
	}

	/**
	 * Forget all where queries
	 *
	 * @return void
	 */
	protected function forgetWhereQueries (){
		$keys = $this->getRememberedWhereQueries();

		foreach ($keys as $key) {
			Cache::forget($this->getTable() . ".{$key}");
		}

		Cache::forget($this->getTable() . ".where");
	}


	/**
	 * Remember a kay for where query
	 *
	 * @param string $key
	 * @return void
	 */
	protected function rememberWhereQuery ($key){
		$keys = $this->getRememberedWhereQueries();
		
		if(!in_array($key, $keys)){
			$keys[] = $key;
			Cache::put($this->getTable() . ".where", $keys, $this->cacheExpiry);	
		}

	}

	/**
	 * Get remembered where queries keys
	 *
	 * @return array
	 */
	protected function getRememberedWhereQueries (){
		return Cache::get($this->getTable() . ".where", []);
	}

	/**
	 * Enable full model caching
	 *
	 * @return void
	 */
	protected function enableFullModelCaching (){
		static::$fullModelCaching = true;
	}

	/**
	 * Disable full model caching
	 *
	 * @return void
	 */
	protected function disableFullModelCaching (){
		static::$fullModelCaching = false;
	}
}