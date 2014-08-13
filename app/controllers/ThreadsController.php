<?php

class ThreadsController extends \BaseController {
		
	public function index($forum_slug, $forum_id, $thread_slug, $thread_id)
	{
		$posts_per_page = Config::get('app.thread_posts_per_page');
		
		$thread = Thread::findOrFail($thread_id);
				
		//if user is authenticated, cache the query
		if (Auth::check())
		{
			$cache_key = 'user_'.Auth::user()->id.'_thread_'.$thread->id.'_page_'.Input::get('page', 1);
			$posts = Cache::remember($cache_key, Config::get('app.cache_posts_for'), function() use ($thread, $posts_per_page)
			{	
				//do not touch. might explode.
				
				$temp_posts = Post::withTrashed()->where('thread_id', '=', $thread->id)->whereNull('parent_id')->where('id', '<>', $thread->post_id)->get();
				$temp_posts = $temp_posts->sortBy('weight')->reverse();
				$count = $temp_posts->count();
				
				$pagination = App::make('paginator');
			    $page = $pagination->getCurrentPage($count);
			    $items = $temp_posts->slice(($page - 1) * $posts_per_page, $posts_per_page)->all();
			    $paginated = $pagination->make($items, $count, $posts_per_page);
			    
				return ['list' => $paginated->getItems(), 'links' => (string) $paginated->links()];
			});
		}
		//else just get the default posts with the default weight
		else
		{
			$paginated = Post::withTrashed()->where('thread_id', '=', $thread_id)->whereNull('parent_id')->where('id', '<>', $thread->post_id)->orderBy('weight', 'DESC')->paginate($posts_per_page);
			$posts['list'] = $paginated->getItems();
			$posts['links'] = $paginated->links();
		}
		
		return View::make('content.thread', array('posts' => $posts['list'], 'links' => $posts['links'], 'thread' => $thread));
	}
	
	public function create($forum_id) {
		$forum = Forum::findOrFail($forum_id);
		return View::make('content.createThread', array('forum' => $forum));
	}
	
	public function submitCreate() {
	
		$forum = Forum::findOrFail(Input::get('forum_id'));
		
		$data = array(
			'forum_id'	=>	Input::get('forum_id'),
			'user_id'	=>  Auth::user()->id,
			'name'		=>  Input::get('name')
		);
		$validator = Thread::validate($data);
		
		if (!$validator->fails())
		{
			$thread = new Thread();
			
			$thread->name = Input::get('name');
			$thread->user_id = Auth::user()->id;
			$thread->forum_id = Input::get('forum_id');
			$thread->post_id = 0;
			$thread->save();
			
			$data = array(
				'thread_id'	=>	$thread->id,
				'body'		=>	Input::get('body')
			);
			
			$validator = Post::validate($data);
			
			if (!$validator->fails())
			{
				$post = new Post();
				
				$post->user_id		= Auth::user()->id;
				$post->thread_id	= $thread->id;
				$post->body			= Input::get('body');
				
				$post->save();		
			}
			else
				return View::make('content.createThread', array('forum' => $forum, 'errors' => $validator->messages()->all()));
			
			$thread->post_id = $post->id;
			
			$thread->save();
			
			return Redirect::to($thread->permalink());
		}
		else 
			return View::make('content.createThread', array('forum' => $forum, 'errors' => $validator->messages()->all()));
	}

}