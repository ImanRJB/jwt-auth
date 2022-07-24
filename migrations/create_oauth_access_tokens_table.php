<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_access_tokens', function (Blueprint $table) {
            $table->string('id', 200)->primary();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('refresh_token', 200)->index();
            $table->string('user_agent')->nullable();
            $table->string('ip')->nullable();
            $table->text('scopes');
            $table->boolean('revoked')->default(0);
            $table->timestamps();
            $table->dateTime('expires_at')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->dropIfExists('oauth_access_tokens');
    }
};
