<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proveedors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('direccion');
            $table->string('celular');
            $table->string('descripcion');
            $table->string('imagen');
            $table->integer('id_categoria');
            $table->double('calificacion', 10, 2)->default(0);
            $table->string('estado')->default('activo');
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
        Schema::dropIfExists('proveedors');
    }
}
