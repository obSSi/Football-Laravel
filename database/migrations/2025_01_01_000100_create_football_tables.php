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
        Schema::create('championnats', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->timestamps();
        });

        Schema::create('equipes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->foreignId('championnat_id')->constrained('championnats')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('matchs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('championnat_id')->constrained('championnats')->cascadeOnDelete();
            $table->foreignId('equipe1_id')->constrained('equipes')->cascadeOnDelete();
            $table->foreignId('equipe2_id')->constrained('equipes')->cascadeOnDelete();
            $table->unsignedTinyInteger('score1')->nullable();
            $table->unsignedTinyInteger('score2')->nullable();
            $table->timestamps();

            $table->unique(['championnat_id', 'equipe1_id', 'equipe2_id']);
        });

        Schema::create('classement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipe_id')->constrained('equipes')->cascadeOnDelete();
            $table->foreignId('championnat_id')->constrained('championnats')->cascadeOnDelete();
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('victoires')->default(0);
            $table->unsignedInteger('defaites')->default(0);
            $table->unsignedInteger('nuls')->default(0);
            $table->unsignedInteger('buts_marques')->default(0);
            $table->unsignedInteger('buts_encaisses')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classement');
        Schema::dropIfExists('matchs');
        Schema::dropIfExists('equipes');
        Schema::dropIfExists('championnats');
    }
};
