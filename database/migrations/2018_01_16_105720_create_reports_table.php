<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('imo');
            $table->timestamp('created_on')->useCurrent();
            $table->enum('conditionType', ['steaming', 'anchor']);
            $table->decimal('meHours', 6, 2);
            $table->decimal('meCons', 6, 2);
            $table->decimal('auxHours', 6, 2);
            $table->decimal('auxCons', 6, 2);
            $table->decimal('observedDistance', 6, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
