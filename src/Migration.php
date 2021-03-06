<?php

namespace ProAI\SuperMigrations;

use Illuminate\Database\Migrations\Migration as BaseMigration;

abstract class Migration extends BaseMigration
{
    /**
     * Direction of the migration (up or down).
     *
     * @var string
     */
    protected $direction;

    /**
     * Get table names and related methods for up and down schemas.
     *
     * @return array
     */
    abstract protected function schemas();

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->direction = 'up';

        $this->processSchemas();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->direction = 'down';

        $this->processSchemas();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    protected function processSchemas()
    {
        $schemas = $this->schemas();
        
        if ($this->direction === 'down') {
            $schemas = array_reverse($schemas);
        }

        foreach ($schemas as $name => $method) {
            $schema = $this->getSchemaInstance($name);

            $schema->$method();
        }
    }


    /**
     * Get an instance of a table schema.
     *
     * @param  string  $name
     * @return \ProAI\SuperMigrations\Migration
     */
    protected function getSchemaInstance($name)
    {
        require_once database_path('migrations/tables/'.$name.'.php');

        $separator = '_';

        $classname = str_replace($separator, '', ucwords($name, $separator)).'Table';

        return new $classname($name, $this->direction);
    }
}
