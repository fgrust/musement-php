<?php

namespace App\Migration;

use App\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

final class CreateWeathersTable extends Migration
{
    public function up()
    {
        $this->schema->create('weathers', function (Blueprint $table) {
            $table->unsignedInteger('city_id');
            $table->date('date');
            $table->string('weather');
            $table->primary(['city_id', 'date']);
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        });
    }

    public function down()
    {
        $this->schema->table('weathers', function (Blueprint $table) {
            $table->dropForeign('weathers_city_id_foreign');
        });
        $this->schema->dropIfExists('weathers');
    }
}
