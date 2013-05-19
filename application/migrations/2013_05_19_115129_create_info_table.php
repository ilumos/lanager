<?php

class Create_Info_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('info', function($table)
		{
			$table->increments('id');
			$table->integer('parent_id')->nullable()->unsigned();
			$table->string('title');
			$table->text('content')->nullable();
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
		Schema::drop('info');
	}

}