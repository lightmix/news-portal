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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('source', 50);
            $table->string('url', 700)->unique();
            $table->string('image_url', 700)->nullable();

            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText(['title', 'content']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
