<?php


namespace App\Models;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{

    /**
     * Get all rows
     *
     * @return Collection
     */
    public static function getAll()
    {
        return static::all();
    }

    /**
     * Get paginated result
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginated()
    {
        return static::query()->paginate();
    }

    /**
     * Get one row by primary key
     *
     * @param string $id , The primary key
     *
     * @return static
     */
    public static function getByID($id)
    {
        return static::find($id);
    }

    /**
     * Create new resource
     *
     * @param $parameters
     *
     * @return static
     */
    public static function create($parameters)
    {
        return static::query()->create($parameters);
    }

    /**
     * Clear all document within collection. This method does not trigger Model Events but is faster than removing documents one by one.
     * @return mixed
     */
    public static function clear()
    {
        return static::query()->delete();
    }
}
