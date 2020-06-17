<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Redis;

/**
 * @property int id
 */
class Operator extends BaseModel
{
    const PRIORITY_LOW    = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH   = 3;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
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
     * Get first free operator depend on their prioriry
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

    /**
     * Get a random free operator depend on their priority
     * @return Operator
     */
    public static function getRandomFreeOperator()
    {
        /**
         * Get list of busy operators IDs
         */
        $busyOperators = Call::getBusyOperators();

        /**
         * Get all free operators
         */
        $operators = static::query()
                           ->whereNotIn('id', $busyOperators)
                           ->orderBy('priority', 'DESC')
                           ->get();

        /**
         * If there is no free operator, return null
         */
        if($operators->count() === 0)
            return null;

        /**
         * Choose one operator randomly
         */
        return $operators->where('priority', $operators->first()->priority)->random();
    }

    public static function getFreeOperators()
    {
        $busyOperators = Call::getBusyOperators();

        return static::query()
                     ->whereNotIn('id', $busyOperators)
                     ->orderBy('priority', 'DESC')
                     ->get();
    }

    public function isBusy()
    {
        return static::query()->whereHas(
            'call',
            function($callQuery) {
                $callQuery->where('operator_id', $this->id)
                          ->where('isOpen', true);
            }
        )->exists();
    }
}
