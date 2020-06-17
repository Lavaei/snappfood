<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Redis;

/**
 * @property int   id
 */
class Operator extends BaseModel
{
    const PRIORITY_LOW    = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH   = 3;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    /**
     * @return HasOne
     */
    public function call()
    {
        return $this->hasOne(Call::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(
            function(Operator $operator) {

                Redis::publish("operators.new", $operator->id);
            }
        );

    }

    /**
     * @return Operator
     */
    public static function getFreeOperator()
    {
        $busyOperators = Call::getBusyOperators();

        return static::query()
                     ->whereNotIn('id', $busyOperators)
                     ->orderBy('priority', 'DESC')
                     ->first();
    }

    public static function getFreeOperators()
    {
        $busyOperators = Call::getBusyOperators();

        return static::query()
                     ->whereNotIn('id', $busyOperators)
                     ->orderBy('priority', 'DESC')
                     ->get();
    }
}
