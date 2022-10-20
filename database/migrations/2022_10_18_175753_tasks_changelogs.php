<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks_changelogs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->json('changes');
            $table->bigInteger('task_id', false, true);
            $table->unique('uid');
            $table->timestamps();
            $table->foreign('task_id')
                ->references('id')
                ->on('tasks')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks_changelogs');
    }
};
