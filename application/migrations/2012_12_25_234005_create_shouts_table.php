<?php

class Create_Shouts_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shouts', function($table)
		{
			$table->increments('id');
			$table->string('user_id',17);
			$table->string('content');
			$table->timestamps();
			$table->foreign('user_id')->references('id')->on('users')->on_update('cascade')->on_delete('cascade');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shouts');
	}

}