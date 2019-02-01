<div class="{!! $box !!}">
    {!! nl2br($post->response) !!}
    <div style="margin-bottom:5px;"></div>
    <div class="small">
        by
        <a href="{{ route('user-view', array('id' => $post->user_id)) }}">
            {{ $post->user_name }}
        </a>
        {{ $post->date }} |
        <span id="{{ $post->id }}score"> {{ $post->score }} </span> likes
        @guest

        @else
        <span style="cursor: pointer; float: right;">
            <a  id="{{ $post->id }}text"
                onclick="like({{ $post->id }}, 1)"
                >{{ $post->liked }}</a>
        </span>
        @endguest
    </div>
</div>
