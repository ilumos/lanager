<?php

class Edit_Events_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$SQL = "ALTER TABLE  `events` CHANGE  `time_start`  `start` TIMESTAMP NOT NULL DEFAULT  '0000-00-00 00:00:00',
CHANGE  `time_end`  `end` TIMESTAMP NOT NULL DEFAULT  '0000-00-00 00:00:00'";
		return DB::query($SQL);
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$SQL = "ALTER TABLE  `events` CHANGE  `start`  `time_start` TIMESTAMP NOT NULL DEFAULT  '0000-00-00 00:00:00',
CHANGE  `end`  `time_end` TIMESTAMP NOT NULL DEFAULT  '0000-00-00 00:00:00'";
		return DB::query($SQL);
	}

}