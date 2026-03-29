<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAirportsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('airports');
        $table->addColumn('airport_name', 'string', ['limit' => 50, 'null' => false])
            ->addColumn('city', 'string', ['limit' => 50, 'null' => false])
            ->addColumn('country', 'string', ['limit' => 50, 'null' => false])
            ->addColumn('iata_faa', 'string', ['limit' => 3, 'null' => true])
            ->addColumn('icao', 'string', ['limit' => 4, 'null' => true])
            ->addColumn('latitude', 'decimal', [
                'precision' => 10,
                'scale'     => 6,
                'null'      => false
            ])
            ->addColumn('longitude', 'decimal', [
                'precision' => 10,
                'scale'     => 6,
                'null'      => false
            ])
            ->addColumn('altitude', 'smallinteger', ['null' => true])
            ->addColumn('timezone', 'string', ['limit' => 50, 'null' => true])
            ->addIndex(['id'], ['unique' => true])
            ->create();
    }
}
