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
        Schema::create('root_translations', static function (Blueprint $table): void {
            $table->id();
            $table->morphs('translatable');
            $table->string('language', 8);
            $table->json('values')->nullable();
            $table->timestamps();

            $table->unique(['translatable_id', 'translatable_type', 'language'], 'root_translatable_language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('root_translations');
    }
};
