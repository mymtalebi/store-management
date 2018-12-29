<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('root_id')->unsigned()->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('lft');
            $table->integer('rgt');
            $table->unique('name');
            $table->index('parent_id');
            $table->index(['root_id', 'lft', 'rgt']);
            $table->foreign('root_id', 'stores_root_id_foreign')
                ->references('id')->on('stores')
                ->onDelete('set null');
            $table->foreign('parent_id', 'stores_parent_id_foreign')
                ->references('id')->on('stores')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
}
