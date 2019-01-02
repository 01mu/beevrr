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
            | {{ $content['discussions'][$i]->reply_count }} responses
            | {{ $content['discussions'][$i]->vote_count }} votes
            | {{ $content['discussions'][$i]->current_phase }}
        </div>
    </div>
@endfor

@include('partials/sub/pagination', array(
    'left' => $content['pagination']['left'],
    'right' =>  $content['pagination']['right'],))

@guest

@else
    <hr>
    <a class="button" href="{{ route('disc-sub-view') }}">submit discussion</a>
@endguest
