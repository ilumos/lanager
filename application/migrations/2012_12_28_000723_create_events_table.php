<?php

class Create_Events_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('event_types', function($table)
		{
			$table->string('name')->nullable();
			$table->primary('name');
		});
		Schema::create('events', function($table)
		{
			$table->increments('id')->unsigned();
			$table->string('title');
			$table->string('type')->nullable();
			$table->timestamp('time_start');
			$table->timestamp('time_end');
			$table->text('details');
			$table->string('manager_id',17)->nullable();
			$table->foreign('manager_id')->references('id')->on('users')->on_update('cascade')->on_delete('set null');
			$table->foreign('type')->references('name')->on('event_types')->on_update('cascade')->on_delete('set null');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('events');
		Schema::drop('event_types');
	}

}