<?php

use App\Models\Call;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('priority')->default(Call::PRIORITY_LOW); // Use one of defined constants in the Call.php for its value
            $table->unsignedBigInteger('operator_id')->nullable(); // Assignee
            $table->boolean('isOpen')->default(true); // Determinate whether the call is open or not
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calls');
    }
}
