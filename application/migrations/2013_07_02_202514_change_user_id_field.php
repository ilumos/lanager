<?php

class Change_User_Id_Field {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// delete all foreign keys referencing users.id
		DB::query('ALTER TABLE `playlist_entries` DROP FOREIGN KEY `playlist_entries_user_id_foreign`;');
		DB::query('ALTER TABLE `shouts` DROP FOREIGN KEY `shouts_user_id_foreign`;');

		// drop users.id as primary key
		DB::query('ALTER TABLE `users` DROP PRIMARY KEY;');

		// rename users.id to users.steam_id_64
		DB::query('ALTER TABLE `users` CHANGE `id` `steam_id_64` VARCHAR(17) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL');


		// create new users.id as int(10)
		DB::query('ALTER TABLE `users` ADD `id` INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT FIRST;');

		// change user id fields in foreign tables to int(10)
		DB::query('ALTER TABLE `events` CHANGE `manager` `manager` INT(10) UNSIGNED NULL DEFAULT NULL');
		DB::query('ALTER TABLE `shouts` CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL');
		DB::query('ALTER TABLE `playlist_entries` CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL');

		// re-add foreign keys for new users.id field
		DB::query('ALTER TABLE `playlist_entries`
				ADD CONSTRAINT `playlist_entries_user_id_foreign`
				FOREIGN KEY ( `user_id` )
				REFERENCES `lanager`.`users` (`id`)
				ON DELETE CASCADE ON UPDATE CASCADE;
		');
		DB::query('ALTER TABLE `shouts`
				ADD CONSTRAINT `shouts_user_id_foreign`
				FOREIGN KEY ( `user_id` )
				REFERENCES `lanager`.`users` (`id`)
				ON DELETE CASCADE ON UPDATE CASCADE;
		');
		DB::query('ALTER TABLE `events`
				ADD CONSTRAINT `events_manager_foreign`
				FOREIGN KEY ( `manager` )
				REFERENCES `lanager`.`users` (`id`)
				ON DELETE SET NULL ON UPDATE CASCADE;
		');

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		// remove foreign keys on users.id
		 DB::query('ALTER TABLE `playlist_entries` DROP FOREIGN KEY `playlist_entries_user_id_foreign`;');
		 DB::query('ALTER TABLE `shouts` DROP FOREIGN KEY `shouts_user_id_foreign`;');
		 DB::query('ALTER TABLE `events` DROP FOREIGN KEY `events_manager_foreign`;');

		// change user id fields in foreign tables to varchar(17)
		DB::query('ALTER TABLE `events` CHANGE `manager` `manager` VARCHAR(17) NULL DEFAULT NULL');
		DB::query('ALTER TABLE `shouts` CHANGE `user_id` `user_id` VARCHAR(17) NOT NULL');
		DB::query('ALTER TABLE `playlist_entries` CHANGE `user_id` `user_id` VARCHAR(17) NOT NULL');

		// drop user.id field
		DB::query('ALTER TABLE `users` DROP `id`;');

		// rename users.steam_id_64 to users.id
		DB::query('ALTER TABLE `users` CHANGE `steam_id_64` `id` VARCHAR(17) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL');

		// make users.id a primary key
		DB::query('ALTER TABLE `users` ADD PRIMARY KEY ( `id` )');

		// create foreign keys on users.id
		DB::query('ALTER TABLE `playlist_entries`
				ADD CONSTRAINT `playlist_entries_user_id_foreign`
				FOREIGN KEY ( `user_id` )
				REFERENCES `lanager`.`users` (`id`)
				ON DELETE CASCADE ON UPDATE CASCADE;
		');

		DB::query('ALTER TABLE `shouts`
				ADD CONSTRAINT `shouts_user_id_foreign`
				FOREIGN KEY ( `user_id` )
				REFERENCES `lanager`.`users` (`id`)
				ON DELETE CASCADE ON UPDATE CASCADE;
		');
	}

}