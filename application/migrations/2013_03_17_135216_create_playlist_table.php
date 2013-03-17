<?php

class Create_Playlist_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		
		Schema::create('playlist_entries', function($table)
		{
			$table->string('id',11);
			$table->primary('id');
			$table->string('user_id',17);
			$table->string('title');
			$table->boolean('played');
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
	}

}