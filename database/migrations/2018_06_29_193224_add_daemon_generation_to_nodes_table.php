<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDaemonGenerationToNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nodes', function (Blueprint $table) {
            $table->integer('daemon_generation', false, true)->default(1)->after('daemonBase');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nodes', function (Blueprint $table) {
            $table->dropColumn('daemon_generation');
        });
    }
}
