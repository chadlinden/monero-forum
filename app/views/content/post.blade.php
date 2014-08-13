<div id="post-{{ $post->id }}" class="post col-lg-12 level-{{ $level }}">
			<h4>
			@if ($post->children()->count() > 0)
			<span class="drawer-button drawer-buttons-{{ $post->id }}" style="display: none;"><span onClick="drawer_close({{ $post->id }})" class="glyphicon glyphicon-collapse-up"></span></span>
			@endif
			@if ($post->children()->count() > 0)
			 <small>Replies: {{ $post->children()->count() }}</small>
			@endif
			Weight: {{ $post->weight }}
			<a href="/" class="disabled-link" onclick="vote({{ $post->id }}, 'insightful')">
			@if (Vote::voted_insightful($post->id))
			<button type="button" class="disabled btn btn-default btn-xs pull-right insightful-{{ $post->id }}"><span class="glyphicon glyphicon-thumbs-up"></span> Insightful</button>
			@else
			<button type="button" class="btn btn-default btn-xs pull-right insightful-{{ $post->id }}"><span class="glyphicon glyphicon-thumbs-up"></span> Insightful</button>
			@endif
			</a> 
			<a href="/" class="disabled-link" onclick="vote({{ $post->id }}, 'irrelevant')">
			@if (Vote::voted_irrelevant($post->id))
			<button type="button" class="disabled btn btn-default btn-xs pull-right irrelevant-{{ $post->id }}"><span class="glyphicon glyphicon-thumbs-down"></span> Irrelevant</button>
			@else
			<button type="button" class="btn btn-default btn-xs pull-right irrelevant-{{ $post->id }}"><span class="glyphicon glyphicon-thumbs-down"></span> Irrelevant</button>
			@endif
			</a>
			</h4>
			<p class="post-meta"><a href="/user/{{ $post->user->id }}" target="_blank">{{ $post->user->username }}</a> posted this on {{ $post->created_at }}</p>
			<div class="post-content-{{ $post->id }}">
				@if ($post->trashed())
				<p><em>[deleted]</em></p>
				@else
				{{ Markdown::string(e($post->body)) }}
				@endif
			</div>
			<div class="btn-group btn-group-sm post-buttons">
			  @if (Auth::check())
			  <a href="/posts/reply/{{ $post->id }}" class="post-action-btn"><button type="button" onclick="post_reply({{ $post->id }}, {{ $thread_id }}, '{{ $post->title }}')" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-arrow-left"></span>
 Reply</button></a>
 			  @if ($post->user->id != Auth::user()->id)
			  <a href="/posts/report/{{ $post->id }}" class="post-action-btn"><button type="button" onclick="post_flag({{ $post->id }})" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-flag"></span>
 Flag</button></a>
 			  @endif
			  @if ($post->user->id == Auth::user()->id)
			  <a href="/posts/update/{{ $post->id }}" class="post-action-btn"><button type="button" onclick="post_edit({{ $post->id }}, {{ $thread_id }}, '{{ $post->title }}')" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span>
 Edit</button></a>
			  <a class="post-action-btn" href="/posts/delete/page/{{ $post->id }}"><button type="button" onclick="post_delete({{ $post->id }})" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-trash"></span>
 Delete</button></a>
			  @endif
			  @endif
			</div>
</div>

{{ display_posts($post->id, $thread_id, $level + 1) }}