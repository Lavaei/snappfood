<?php


namespace App\Models;


use Illuminate\Support\Facades\Redis;

/**
 * @property int     id
 * @property boolean isOpen
 * @property int     priority
 * @property int     operator_id
 */
class Call extends BaseModel
{
    const PRIORITY_LOW  = 1;
    const PRIORITY_HIGH = 2;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * Get a low priority call (without any assignee) if any exist
     * @return Call
     */
    public static function getFirstLowPriorityCall()
    {
        return static::query()
                     ->whereNull('operator_id')
                     ->where('priority', static::PRIORITY_LOW)
                     ->first();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(
            function(Call $call) {

                if($call->priority === static::PRIORITY_HIGH)
                {
                    Redis::publish("calls.new", $call->id);
                }
            }
        );

        static::updated(
            function(Call $call) {
                if($call->isDirty('isOpen') && !$call->isOpen)
                {
                    Redis::publish("calls.end", $call->operator_id);
                }
            }
        );
    }

    public static function getBusyOperators()
    {
        return static::query()
                     ->select(['operator_id'])
                     ->where('isOpen', true)
                     ->whereNotNull('operator_id')
                     ->pluck('operator_id');
    }

    public static function getHighPriorityUnassignedCalls()
    {
        return static::query()
                     ->where('priority', static::PRIORITY_HIGH)
                     ->whereNull('operator_id')
                     ->get();
    }

    public function assign($operatorID)
    {
        $this->operator_id = $operatorID;
        $this->save();
    }
}
