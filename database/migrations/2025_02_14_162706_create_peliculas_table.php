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
        // Tabla de géneros
        Schema::create('generos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->timestamps();
        });

        // Tabla de películas
        Schema::create('peliculas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('precio', 10, 2);
            $table->unsignedBigInteger('codigogenero'); // FK
            $table->timestamps();

            // Restricción de clave foránea
            $table->foreign('codigogenero')->references('id')->on('generos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peliculas');
        Schema::dropIfExists('generos');
    }
};
