<!--
    beevrr
    github.com/01mu
-->


<b>{{ $content['discussion']->proposition }}</b>
<hr>
<div class="boxarg">
    {!! nl2br($content['discussion']->argument) !!}
    <div style="margin-bottom:5px;"></div>
    <div class="small">
        by <a href="{{ route('user-view',
            array('id' => $content['discussion']->user_id)) }}">
            {{ $content['discussion']->user_name }}</a>
            {{ $content['discussion']->post_date }} |
            <span
            id="{{ $content['discussion']->id }}score">
            {{ $content['discussion']->score }}
            </span> likes
            @guest

            @else
            <span style="cursor: pointer; float: right;">
                <a id="{{ $content['discussion']->id }}text"
                onclick="like_disc({{ $content['discussion']->id }})">{{ $content['liked'] }}</a>
            </span>
            @endguest
    </div>
</div>
<hr>
<div class="flex">
    <div class="wrapper50">
        <b>arguments for</b>
    </div>
    <div class="wrapper50">
         <b>arguments against</b>
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
                none
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
                none
            </div>
        @endif
    </div>
</div>
<hr>
<b>info</b>
<hr>
<div class="box">
    <div class="small">
        <div class="flex">
            <div class="wrapper50">
                current phase: {{ $content['discussion']->current_phase }}
                <br><br>
                pre-argument <b>for</b>: {{ $content['discussion']->pa_for }}
                    ({{ $content['discussion']->pa_for_per }}%)
                <br>
                pre-argument <b>against</b>:
                    {{ $content['discussion']->pa_against }}
                    ({{ $content['discussion']->pa_against_per }}%)
                <br>
                pre-argument <b>undecided</b>:
                    {{ $content['discussion']->pa_undecided }}
                    ({{ $content['discussion']->pa_undecided_per }}%)
                <br><br>
                post-argument <b>for</b>: {{ $content['discussion']->pv_for }}
                    ({{ $content['discussion']->pv_for_per }}%)
                <br>
                post-argument <b>against</b>:
                    {{ $content['discussion']->pv_against }}
                    ({{ $content['discussion']->pv_against_per }}%)
            </div>
            <div class="wrapper50">
                {{ $content['next_phase'] }}
                <br><br>
                <b>for</b> change: {{ $content['discussion']->for_change }}
                percentage points
                <br>
                <b>against</b>
                    change: {{ $content['discussion']->against_change }}
                    percentage points
                <br>
                <b>winner</b>: {{ $content['discussion']->winner }}
                <br><br>
                responses: {{ $content['discussion']->reply_count }}
                <br>
                votes: {{ $content['discussion']->vote_count }}
            </div>
        </div>
    </div>
</div>

@if($content['can_vote'])
    <hr>
    <a class="button" href="{{ route('vote-view',
            array('phase' => $content['discussion']->current_phase,
            'id' => $content['discussion']->id)) }}">
            submit {{ $content['discussion']->current_phase }} vote</a>
@else

@endif

@if($content['can_reply'])
    <hr>
    <a class="button" href="{{ route('resp-view',
            array('id' => $content['discussion']->id)) }}">submit response</a>
@else

@endif

@if(count($content['action']) > 0)
    <hr>
    {{ $content['action']['did'] }}<b>{{ $content['action']['res'] }}</b>
@else

@endif

<script>
    function like_disc(id)
    {
        var xhttp = new XMLHttpRequest();

        xhttp.open("GET", "/disc_like/" + id, true);
        xhttp.send();

        var txt = document.getElementById(id + 'text').innerHTML;
        var c = parseInt(document.getElementById(id + 'score').innerHTML);

        if(txt === '[like]')
        {
            document.getElementById(id + 'score').innerHTML = c + 1;
            document.getElementById(id + 'text').innerHTML = '[unlike]';
        }
        else
        {
            document.getElementById(id + 'score').innerHTML = c - 1;
            document.getElementById(id + 'text').innerHTML = '[like]';
        }
    }

    function like_resp(id)
    {
        var xhttp = new XMLHttpRequest();

        xhttp.open("GET", "/resp_like/" + id, true);
        xhttp.send();

        var txt = document.getElementById(id + 'text').innerHTML;
        var c = parseInt(document.getElementById(id + 'score').innerHTML);

        if(txt === '[like]')
        {
            document.getElementById(id + 'score').innerHTML = c + 1;
            document.getElementById(id + 'text').innerHTML = '[unlike]';
        }
        else
        {
            document.getElementById(id + 'score').innerHTML = c - 1;
            document.getElementById(id + 'text').innerHTML = '[like]';
        }
    }
</script>
