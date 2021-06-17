<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedorXCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proveedor_x_categorias', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_proveedor');
            $table->integer('id_categoria');
            $table->string('alias');
            $table->string('descripcion');
            $table->string('celular');
            $table->string('hora_min');
            $table->string('hora_max');
            $table->string('precio');
            $table->string('banco');
            $table->string('cuenta');
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
        Schema::dropIfExists('proveedor_x_categorias');
    }
}
