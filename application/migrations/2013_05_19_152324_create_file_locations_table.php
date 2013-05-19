<?php

class Create_File_Locations_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('file_locations', function($table)
		{
			$table->increments('id');
			$table->string('title');
			$table->text('location');
			$table->text('description');
			$table->timestamps();
		});	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('file_locations');
	}

}