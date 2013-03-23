<?php

class Change_Playback_Fields {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add playback state column
		Schema::table('playlist_entries', function($table)
		{
			// 0 - unplayed
			// 1 - playing
			// 2 - played
			$table->drop_column('played');
			$table->integer('playback_state')->default(0);
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
			$table->drop_column('playback_state');
			$table->boolean('played');
		});
	}

}