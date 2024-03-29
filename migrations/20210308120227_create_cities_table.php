<?php

namespace App\Migration;

use App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

final class CreateCitiesTable extends Migration
{
    public function up(): void
    {
        $this->schema->create('cities', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('name');
            $table->float('latitude');
            $table->float('longitude');
            $table->timestamps();
            $table->primary('id');
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists('cities');
    }
}
