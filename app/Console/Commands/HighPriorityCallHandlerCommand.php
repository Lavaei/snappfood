<?php

namespace App\Console\Commands;

use App\Models\Call;
use App\Models\Operator;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use SplQueue;

class HighPriorityCallHandlerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapp:call';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle high priority calls';

    /**
     * A very simple queue to keep calls
     * @var SplQueue
     */
    protected $queue;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        /**
         * Initialize the calls queue
         */
        $this->queue = new SplQueue();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);

        /**
         * Get list of unassigned calls and enqueue all of them
         */
        Call::getHighPriorityUnassignedCalls()->each(function(Call $call){
            $this->queue->enqueue($call->id);

            /**
             * Log the activity
             */
            $this->info("Enqueue call " . $call->id);
        });

        /**
         * Assign calls to free operators
         */
        foreach(Operator::getFreeOperators() as $operator)
        {
            /**
             * If there is nothing in the queue, break the loop
             */
            if($this->queue->isEmpty())
                break;

            /**
             * Get callID from queue
             */
            $callID = $this->queue->dequeue();

            /**
             * Get call by its ID
             */
            $call = Call::getByID($callID);

            /**
             * If call found by its ID, assign operator to it
             */
            if($call)
            {
                /**
                 * Assign call to given operator
                 */
                $call->assign($operator->id);

                $this->info($call->id . " assigned to " . $operator->id);
            }
        }

        /**
         * Listen to redis
         */
        Redis::psubscribe(['calls.*', 'operators.*'], function ($data, $channel) {

            try
            {
                /**
                 * Act depend on channel name
                 */
                if(Str::endsWith($channel, 'calls.new'))
                {
                    /**
                     * New call has been established, we should assign it to an operator or queue it if all operators are busy
                     */


                    /**
                     * Get call by its ID
                     */
                    $call = Call::getByID($data);

                    /**
                     * Get a free operator
                     */
                    $operator = Operator::getFreeOperator();

                    /**
                     * If call and operator has set properly, assign call to operator.
                     * If just call is set (and there is no free operator) enqueue the call
                     */
                    if($call && $operator)
                    {
                        /**
                         * Assign call to given operator
                         */
                        $call->assign($operator->id);

                        $this->info($call->id . " assigned to " . $operator->id);
                    }
                    elseif($call)
                    {
                        $this->queue->enqueue($call->id);

                        /**
                         * Log the activity
                         */
                        $this->info("Enqueue call " . $call->id);
                    }
                }
                else if((Str::endsWith($channel, 'calls.end') || Str::endsWith($channel, 'operators.new'))  && !$this->queue->isEmpty())
                {
                    /**
                     * Get call's ID from queue
                     */
                    $callID = $this->queue->dequeue();

                    /**
                     * Get call by its ID
                     */
                    $call = Call::getByID($callID);

                    /**
                     * Get operator by its ID
                     */
                    $operator = Operator::getByID($data);

                    /**
                     * If call and operator has set properly, assign call to operator
                     */
                    if($call && $operator)
                    {
                        /**
                         * Assign call to given operator
                         */
                        $call->assign($operator->id);

                        /**
                         * Log the activity
                         */
                        $this->info($call->id . " assigned to " . $operator->id);
                    }
                }
            }
            catch(Exception $ex)
            {
                /**
                 * Just log error and do not throw it again!
                 */
                $this->error($ex->getMessage() . "\n\n" . $ex->getTraceAsString());
            }
        });
    }
}
