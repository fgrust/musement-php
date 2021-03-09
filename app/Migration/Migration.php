<?php

namespace App\Migration;

use Illuminate\Database\Capsule\Manager as Capsule;
use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration
{
    public $capsule;

    public $schema;

    public function init()
    {
        $this->capsule = new Capsule;
        $this->capsule->addConnection([
            'driver'    => DB_CONNECTION,
            'host'      => DB_HOST,
            'port'      => DB_PORT,
            'database'  => DB_NAME,
            'username'  => DB_USER,
            'password'  => DB_PASSWORD,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci'
        ]);

        $this->capsule->bootEloquent();
        $this->capsule->setAsGlobal();
        $this->schema = $this->capsule->schema();
    }
}


