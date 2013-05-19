<?php

class Files_Controller extends Base_Controller {

	public function action_index()
	{
		// Display file locations
		$locations = LANager\File_location::get();
		return View::make('files.index')
					->with('title','Files')
					->with('locations',$locations);
	}

}