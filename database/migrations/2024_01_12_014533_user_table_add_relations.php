<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table){
            $table->unsignedBigInteger('departamento_id')->nullable();
            $table->unsignedBigInteger('cargo_id')->nullable();

            $table->foreign('departamento_id')
                ->references('id')
                ->on('departamentos')
                ->onDelete('NO ACTION');

            $table->foreign('cargo_id')
                ->references('id')
                ->on('cargos')
                ->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table){
            $table->dropForeign(['departamento_id']);
            $table->dropForeign(['cargo_id']);
            $table->dropColumn('departamento_id');
            $table->dropColumn('cargo_id');
        });
    }
};
