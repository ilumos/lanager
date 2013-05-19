<?php

class Info_Controller extends Base_Controller {

	public function action_index()
	{
		// Display top level categories
		$children = LANager\Info::where_null('parent_id')->get();
		return View::make('info.index')
					->with('title','Info')
					->with('children',$children);
	}



	public function action_display($info_id)
	{
		Bundle::start('sparkdown');
		$info = LANager\Info::find($info_id);
		$children = LANager\Info::where('parent_id', '=', $info_id)->get();
		return View::make('info.display')
					->with('title',$info->title)
					->with('info',$info)
					->with('children',$children);
	}


}