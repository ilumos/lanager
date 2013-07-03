<?php

class Authority_add_authority_tables {

	public function up()
	{

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

	}

	public function down()
	{
		// Schema::drop('users');
		Schema::drop('roles');
		Schema::drop('role_user');
	}

}