<!--
    beevrr
    github.com/01mu
-->

<b>{{ $content['user'][0]->user_name }}</b>
<hr>
<div class="flex">
    <div class="wrapper50">
        Info
    </div>
    <div class="wrapper50">
        Bio
    </div>
</div>
<hr>
<div class="box">
    <div class="flex">
        <div class="wrapper50">
            • Total Responses: {{ $content['user'][0]->total_responses }}
            <span class="small">
                <a href="{{ route('user-info',
                    ['id' => $content['user'][0]->id,
                    'option' => 'tot_res']) }}">
                    [view]</a>
            </span><br>
            • Active Responses: {{ $content['user'][0]->active_responses }}
            <span class="small">
                <a href="{{ route('user-info',
                    ['id' => $content['user'][0]->id,
                    'option' => 'act_res']) }}">
                    [view]</a>
            </span><br><br>
            • Total Votes: {{ $content['user'][0]->total_votes }}
            <span class="small">
                <a href="{{ route('user-info',
                    ['id' => $content['user'][0]->id,
                    'option' => 'tot_vot']) }}">
                    [view]</a>
            </span><br>
            • Active Votes: {{ $content['user'][0]->active_votes }}
            <span class="small">
                <a href="{{ route('user-info',
                    ['id' => $content['user'][0]->id,
                    'option' => 'act_vot']) }}">
                    [view]</a>
            </span><br><br>
            • Total Discussions: {{ $content['user'][0]->total_discussions }}
            <span class="small">
                <a href="{{ route('user-info',
                    ['id' => $content['user'][0]->id,
                    'option' => 'tot_dis']) }}">
                    [view]</a>
            </span><br>
            • Active Discussions:
                {{ $content['user'][0]->active_discussions }}
            <span class="small">
                <a href="{{ route('user-info',
                    ['id' => $content['user'][0]->id,
                    'option' => 'act_dis']) }}">
                    [view]</a>
            </span><br><br>
            <span class="small">
                <a href="{{ route('user-info',
                    ['id' => $content['user'][0]->id,
                    'option' => 'act']) }}">
                    [view full activity]</a>
            </span>
        </div>
        <div class="wrapper50">
            <div class="box">
                {!! nl2br($content['user'][0]->bio) !!}
            </div>
    </div>
</div>
