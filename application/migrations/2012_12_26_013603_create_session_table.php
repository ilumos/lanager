<?php

class Create_Session_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::query('CREATE TABLE "sessions" (
     "id" VARCHAR PRIMARY KEY NOT NULL UNIQUE,
     "last_activity" INTEGER NOT NULL,
     "data" TEXT NOT NULL);');
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sessions');
	}

}