<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrearTablaRelArchivosCategorias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rel_archivos_categorias', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('archivo_id')->unsigned();  // pone tipo int(10)
            $table->integer('categoria_id')->unsigned();

            // FK relacion tabla alumnos
            $table->foreign('archivo_id')->references('id')->on('archivos');
            // FK relacion tabla clases
            $table->foreign('categoria_id')->references('id')->on('categorias');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rel_archivos_categorias');
    }
}
