<?php

class Allow_Playlist_Duplicates {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::drop('playlist_entries');

		Schema::create('playlist_entries', function($table)
		{
			$table->increments('id');
			$table->string('video_id',11);
			$table->string('user_id',17);
			$table->string('title');
			$table->integer('playback_state')->default(0);
			// 0 - unplayed
			// 1 - playing
			// 2 - paused
			// 3 - skipped
			// 4 - played
			$table->timestamps();
			$table->foreign('user_id')->references('id')->on('users')->on_update('cascade')->on_delete('cascade');
		});

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('playlist_entries');

		Schema::create('playlist_entries', function($table)
		{
			$table->string('id',11);
			$table->primary('id');
			$table->string('user_id',17);
			$table->string('title');
			$table->integer('playback_state')->default(0);
			$table->timestamps();
			$table->foreign('user_id')->references('id')->on('users')->on_update('cascade')->on_delete('cascade');

		});
	}

}