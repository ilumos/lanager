<?php

class Create_Users_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function($table)
		{
			$table->create();
			$table->integer('id')->unsigned();
			$table->primary('id');
			$table->string('username');
			$table->string('avatar_small');
			$table->string('avatar_medium');
			$table->string('avatar_large');
			$table->string('real_name');
			$table->string('ip');
			$table->timestamps();
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}