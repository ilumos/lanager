<?php

class Change_Events_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$SQL = 'ALTER TABLE  `events` CHANGE  `details`  `description` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL';
		return DB::query($SQL);

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$SQL = 'ALTER TABLE  `events` CHANGE  `description`  `details` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL';
		return DB::query($SQL);
	}

}