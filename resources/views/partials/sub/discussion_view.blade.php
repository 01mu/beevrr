<div class="{!! $box !!}">
    {!! nl2br($post->response) !!}
    <div style="margin-bottom:5px;"></div>
    <div class="small">
        by <a href="{{ route('user-view',
            array('id' => $post->user_id)) }}">
            {{ $post->user_name }}</a>
            {{ $post->date }}
        <span style="float: right;">
            <a href="{{ route('search-view') }}">[like]</a>
        </span>
    </div>
</div>
