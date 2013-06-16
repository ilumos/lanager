<?php

class Events_Add_Timestamps {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add playback state column
		Schema::table('events', function($table)
		{
			$table->timestamps();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('events', function($table)
		{
			$table->drop_column('created_at');
			$table->drop_column('updated_at');
		});
	}

}