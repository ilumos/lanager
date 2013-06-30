<?php

class Create_Playlist_Duration_Field {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add entry duration column
		Schema::table('playlist_entries', function($table)
		{
			$table->integer('duration');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('playlist_entries', function($table)
		{
			$table->drop_column('duration');
		});
	}

}