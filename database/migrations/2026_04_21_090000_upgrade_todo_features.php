<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todo', function (Blueprint $table) {
            $table->string('priority')->default('medium')->after('task');
            $table->dateTime('due_date')->nullable()->after('priority');
            $table->softDeletes()->after('updated_at');
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color', 20)->default('#0d6efd');
            $table->timestamps();
        });

        Schema::create('tag_todo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_id')->constrained('todo')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['todo_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_todo');
        Schema::dropIfExists('tags');

        Schema::table('todo', function (Blueprint $table) {
            $table->dropColumn(['priority', 'due_date', 'deleted_at']);
        });
    }
};
