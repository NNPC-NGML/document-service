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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('entity_id')->comment('The id of an entity eg customer id');
            $table->string("entity_name")->comment('The name of an entity eg customer');
            $table->string("tag")->comment('The tag name for differentiating tasks');
            $table->json("file_object")->comment('Object of a stored file in json e.g. [name, extension, size, mimetype, location, ?url]');
            $table->longText("file_content")->comment('The content of a file in encoded string eg base64');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
