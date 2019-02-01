<!--
    beevrr
    github.com/01mu
-->

<b>Search Results</b>
<hr>
@if(count($content['search']) > 0)
    @foreach($content['search'] as $search)
        <div class="box">
            <b>
                <a  href="{{ route('disc-view',
                    ['id' => $search->id]) }}">
                    {{ $search->proposition }}
                </a>
            </b>
            <br>
            <div class="small">
                by
                <a  href="{{ route('user-view',
                    ['id' => $search->user_id]) }}">
                    {{ $search->user_name }}
                </a>
                {{ $search->post_date }}
                | replies: {{ $search->reply_count }}
                | votes: {{ $search->vote_count }}
                | {{ $search->current_phase }}
            </div>
        </div>
    @endforeach
@else
<div class="box">
    None
</div>
@endif
@include('partials/sub/pagination', array(
    'left' => $content['pagination']['left'],
    'right' =>  $content['pagination']['right'],))
