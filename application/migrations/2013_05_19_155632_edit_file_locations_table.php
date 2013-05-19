<?php

class Edit_File_Locations_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('file_locations', function($table)
		{
			$table->drop_column('title');
		});

		$SQL = "ALTER TABLE  `file_locations` MODIFY `description` TEXT";
		return DB::query($SQL);

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('file_locations', function($table)
		{
			$table->string('title');
		});
		$SQL = "ALTER TABLE  `file_locations` MODIFY `description` TEXT NOT NULL";
		return DB::query($SQL);
	}

}