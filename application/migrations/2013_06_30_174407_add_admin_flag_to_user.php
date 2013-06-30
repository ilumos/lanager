<?php

class Add_Admin_Flag_To_User {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add admin flag
		Schema::table('users', function($table)
		{
			$table->boolean('admin');
		});
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
			$table->drop_column('admin');
		});
	}

}