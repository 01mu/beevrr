<!--
    beevrr
    github.com/01mu
-->

<b>discussions</b>
<hr>
@for($i = 0; $i < count($content['discussions']); $i++)
    <div class="box">
        <b>
            <a href="{{ route('disc-view',
                ['id' => $content['discussions'][$i]->id]) }}">
                {{ $content['discussions'][$i]->proposition }}</a>
        </b><br>
        <div class="small">
            by
            <a href="{{ route('user-view',
                ['id' => $content['discussions'][$i]->user_id]) }}">
                {{ $content['discussions'][$i]->user_name }}</a>
            {{ $content['discussions'][$i]->post_date }}
            | replies: {{ $content['discussions'][$i]->reply_count }}
            | votes: {{ $content['discussions'][$i]->vote_count }}
            | {{ $content['discussions'][$i]->current_phase }}
        </div>
    </div>
@endfor
<div class="flex">
    @if($content['page'] != 1)
    <div class="wrapper50">
        <a href="{{ route('page', ['p' => $content['left']]) }}"><<</a>
    </div>
    @else
    <div class="wrapper50">
    </div>
    @endif
    <div class="wrapper50">
        <div class="right">
            <a href="{{ route('page', ['p' => $content['page']]) }}">>></a>
        </div>
    </div>
</div>
@guest

@else
    <hr>
    <a class="button" href="{{ route('disc-sub-view') }}">submit discussion</a>
@endguest
