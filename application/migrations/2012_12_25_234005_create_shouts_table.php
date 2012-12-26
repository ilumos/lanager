<?php

class Create_Shouts_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function($table)
		{
			$table->unique('id');
		});
		// Manual table creation required for SQLite foreign keys
		$sql = 'CREATE TABLE "shouts" (
			"id" INTEGER NULL,
			"user_id" INTEGER NULL,
			"content" VARCHAR NULL,
			"pinned" INTEGER NULL,
			"created_at" DATE NULL,
			"updated_at" DATETIME NULL,
			PRIMARY KEY ("id"),
			CONSTRAINT shouts_user_id_foreign
			FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON UPDATE cascade)';
		DB::query($sql);
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function($table)
		{
			$table->drop_unique('users_id_unique');
		});
		Schema::drop('shouts');
	}

}