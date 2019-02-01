<!--
    beevrr
    github.com/01mu
-->

<div class="argtitle">
    <b>{{ $content['discussion']->proposition }}</b>
</div>
<hr>
<div class="boxarg">
    {!! nl2br($content['discussion']->argument) !!}
    <div style="margin-bottom:5px;"></div>
    <div class="small">
        by
        <a   href="{{ route('user-view',
                array('id' => $content['discussion']->user_id)) }}">
            {{ $content['discussion']->user_name }}
        </a>
        {{ $content['discussion']->post_date }} |
        <span id="{{ $content['discussion']->id }}score">
            {{ $content['discussion']->score }}
        </span> likes
        @guest

        @else
        <span style="cursor: pointer; float: right;">
            <a  id="{{ $content['discussion']->id }}text"
                onclick="like({{ $content['discussion']->id }}, 0)"
                >{{ $content['liked'] }}</a>
        </span>
        @endguest
    </div>
</div>
<hr>
<div class="flex">
    <div class="wrapper50">
        <b>For</b>
    </div>
    <div class="wrapper50">
         <b>Against</b>
    </div>
</div>
<hr>
<div class="flex">
    <div class="wrapper50rm">
        @if(count($content['f']) > 0)
            @foreach($content['f'] as $post)
                @include('partials/sub/discussion_view', array('box' => 'boxb'))
            @endforeach
        @else
            <div class="boxb">
                None
            </div>
        @endif
    </div>
    <div class="wrapper50lm">
        @if(count($content['a']) > 0)
            @foreach($content['a'] as $post)
                @include('partials/sub/discussion_view', array('box' => 'boxr'))
            @endforeach
        @else
            <div class="boxr">
                None
            </div>
        @endif
    </div>
</div>
<hr>
<b>Info</b>
<hr>
<div class="box">
    <div class="small">
        <div class="flex">
            <div class="wrapper50">
                current phase:
                    {{ $content['discussion']->current_phase }}
                <br>
                <br>
                pre-argument <b>for</b>:
                    {{ $content['discussion']->pa_for }}
                    ({{ $content['discussion']->pa_for_per }}%)
                <br>
                pre-argument <b>against</b>:
                    {{ $content['discussion']->pa_against }}
                    ({{ $content['discussion']->pa_against_per }}%)
                <br>
                pre-argument <b>undecided</b>:
                    {{ $content['discussion']->pa_undecided }}
                    ({{ $content['discussion']->pa_undecided_per }}%)
                <br>
                <br>
                post-argument <b>for</b>: {{ $content['discussion']->pv_for }}
                    ({{ $content['discussion']->pv_for_per }}%)
                <br>
                post-argument <b>against</b>:
                    {{ $content['discussion']->pv_against }}
                    ({{ $content['discussion']->pv_against_per }}%)
            </div>
            <div class="wrapper50">
                {{ $content['next_phase'] }}
                <br>
                <br>
                <b>for</b> change:
                    {{ $content['discussion']->for_change }}
                    percentage points
                <br>
                <b>against</b> change:
                    {{ $content['discussion']->against_change }}
                    percentage points
                <br>
                <b>winner</b>:
                    {{ $content['discussion']->winner }}
                <br>
                <br>
                responses:
                    {{ $content['discussion']->reply_count }}
                <br>
                votes:
                    {{ $content['discussion']->vote_count }}
            </div>
        </div>
    </div>
</div>

@if($content['can_vote'])
    <hr>
    <a  class="button"
        href="{{ route('vote-view',
        array('phase' => $content['discussion']->current_phase,
        'id' => $content['discussion']->id)) }}">
        Submit
        @if($content['discussion']->current_phase === 'pre-argument')
            Pre-Argument Vote
        @else
            Post-Argument Vote
        @endif
    </a>
@endif

@if($content['can_reply'])
    <hr>
    <a  class="button"
        href="{{ route('resp-view',
        array('id' => $content['discussion']->id)) }}">
        Submit Response
    </a>
@endif

@if(count($content['action']) > 0)
    <hr>
    {{ $content['action']['did'] }}<b>{{ $content['action']['res'] }}</b>
@endif

<script>
    function like(id, type) {
        var xhttp = new XMLHttpRequest();

        var c = parseInt(document.getElementById(id + 'score').innerHTML);
        var txt = document.getElementById(id + 'text').innerHTML;

        var newc = c + 1;
        var newtxt = '[unlike]';

        var opt = '/disc_like/';

        if(txt === '[unlike]') {
            newc = c - 1;
            newtxt = '[like]';
        }

        if(type === 1) {
            opt = '/resp_like/';
        }

        document.getElementById(id + 'score').innerHTML = newc;
        document.getElementById(id + 'text').innerHTML = newtxt;

        xhttp.open('GET', opt + id, true);
        xhttp.send();
    }
</script>
