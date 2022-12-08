<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdShoppingListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopping_lists', function (Blueprint $table) {
            $table->index('user_id');
            //$table->integer('user_id')->unsigned()->change();
            //$table->foreign('user_id')
            //->references('id')->on('users')
            //->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shopping_lists', function (Blueprint $table) {
            //$table->dropForeign('shopping_lists_user_id_foreign');
            $table->dropIndex('user_id');
        });
    }
}
