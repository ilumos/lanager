<?php

class Rename_Events_Field {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		$SQL = 'ALTER TABLE  `events` DROP FOREIGN KEY `events_manager_id_foreign`;';
		$query1 = DB::query($SQL);
		$SQL = 'ALTER TABLE  `events` CHANGE  `manager_id`  `manager` VARCHAR(17) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL';
		$query2 = DB::query($SQL);
		

		return ($query1 & $query2);

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		$SQL = 'ALTER TABLE  `events` ADD FOREIGN KEY (  `events_manager_id_foreign` ) REFERENCES  `lanager`.`users` (`id`
				) ON DELETE SET NULL ON UPDATE CASCADE ;
				ALTER TABLE  `events` CHANGE  `manager`  `manager_id` VARCHAR(17) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL';
		return DB::query($SQL);
	}

}