<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remitente_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('destinatario_id')->nullable()->constrained('usuarios')->cascadeOnDelete();
            $table->string('asunto', 200);
            $table->text('contenido');
            $table->boolean('leido')->default(false);
            $table->boolean('es_masivo')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
