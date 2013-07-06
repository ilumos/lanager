<?php

class Create_Database_Structure {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table)
		{
			$table->increments('id');
			$table->string('steam_id_64', 17)->unique();
			$table->string('username',32);
			$table->string('ip',15);
			$table->string('avatar',255);
			$table->timestamps();
		});

		Schema::create('roles', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->timestamps();
		});

		Schema::create('role_user', function($table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('role_id')->unsigned();
			$table->timestamps();
			$table->foreign('user_id')->references('id')->on('users')->on_update('cascade')->on_delete('cascade');
			$table->foreign('role_id')->references('id')->on('roles')->on_update('cascade')->on_delete('cascade');
		});

		Schema::create('shouts', function($table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->string('content');
			$table->timestamps();
			$table->foreign('user_id')->references('id')->on('users')->on_update('cascade')->on_delete('cascade');
		});

		Schema::create('playlist_entries', function($table)
		{
			$table->increments('id');
			$table->string('video_id',11);
			$table->integer('user_id')->unsigned();
			$table->string('title',100);
			$table->integer('playback_state')->unsigned();
			$table->integer('duration')->unsigned();
			$table->timestamps();
			$table->foreign('user_id')->references('id')->on('users')->on_update('cascade')->on_delete('cascade');
		});
		Schema::create('info', function($table)
		{
			$table->increments('id');
			$table->integer('parent_id')->nullable()->unsigned();
			$table->string('title');
			$table->text('content')->nullable();
			$table->timestamps();
		});

		Schema::create('file_locations', function($table)
		{
			$table->increments('id');
			$table->text('location');
			$table->text('description')->nullable();
			$table->timestamps();
		});

		Schema::create('event_types', function($table)
		{
			$table->string('name');
			$table->primary('name');
			$table->timestamps();
		});

		Schema::create('events', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('type')->nullable();
			$table->timestamp('start');
			$table->timestamp('end');
			$table->text('description');
			$table->integer('manager')->unsigned()->nullable();
			$table->foreign('manager')->references('id')->on('users')->on_update('cascade')->on_delete('set null');
			$table->foreign('type')->references('name')->on('event_types')->on_update('cascade')->on_delete('set null');
			$table->timestamps();
		});

		Schema::table(Config::get('session.table'), function($table)
		{
			$table->create();
			$table->string('id')->length(40)->primary('session_primary');
			$table->integer('last_activity');
			$table->text('data');
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
		Schema::drop('file_locations');
		Schema::drop('info');
		Schema::drop('playlist_entries');
		Schema::drop('shouts');
		Schema::drop('role_user');
		Schema::drop('roles');
		Schema::drop('users');
		Schema::drop(Config::get('session.table'));
	}

}