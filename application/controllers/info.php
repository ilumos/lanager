<?php

class Info_Controller extends Base_Controller {

	public function action_index($info_id)
	{
		$info = LANager\Info::find($info_id);
		return View::make('info.display')
					->with('title',$info->title)
					->with('info',$info);
	}


}