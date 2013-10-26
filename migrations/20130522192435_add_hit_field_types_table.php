<?php

use Phinx\Migration\AbstractMigration;

class AddHitFieldTypesTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
    	$type = $this->table('type');
    	$type->addColumn('hits', 'integer')
    	->save();
    	
    	// execute()
		$result = $this->execute('ALTER TABLE type DROP COLUMN color');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}