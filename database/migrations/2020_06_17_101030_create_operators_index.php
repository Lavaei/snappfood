<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperatorsIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operators', function (Blueprint $table) {
            $table->index('priority'); //To improve performance on sorting
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operators', function (Blueprint $table) {
            $table->dropIndex('operators_priority_index');
        });
    }
}
