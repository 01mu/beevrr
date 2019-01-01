<!--
    beevrr
    github.com/01mu
-->

<b>{{ $content['user'][0]->user_name }}</b>
<hr>
<div class="flex">
    <div class="wrapper50">
        info
    </div>
    <div class="wrapper50">
        bio
    </div>
</div>
<hr>
<div class="box">
    <div class="flex">
        <div class="wrapper50">
            • total responses: {{ $content['user'][0]->total_responses }}
            <span class="small">
                <a href="{{ route('user-info',
                    ['id' => $content['user'][0]->id,
                    'option' => 'tot_res']) }}">
                    [view]</a>
            </span><br>
            • active responses: {{ $content['user'][0]->active_responses }}
            <span class="small">
                <a href="{{ route('user-info',
                    ['id' => $content['user'][0]->id,
                    'option' => 'act_res']) }}">
                    [view]</a>
            </span><br><br>
            • total votes: {{ $content['user'][0]->total_votes }}
            <span class="small">
                <a href="{{ route('user-info',
                    ['id' => $content['user'][0]->id,
                    'option' => 'tot_vot']) }}">
                    [view]</a>
            </span><br>
            • active votes: {{ $content['user'][0]->active_votes }}
            <span class="small">
                <a href="{{ route('user-info',
                    ['id' => $content['user'][0]->id,
                    'option' => 'act_vot']) }}">
                    [view]</a>
            </span><br><br>
            • total discussions: {{ $content['user'][0]->total_discussions }}
            <span class="small">
                <a href="{{ route('user-info',
                    ['id' => $content['user'][0]->id,
                    'option' => 'tot_dis']) }}">
                    [view]</a>
            </span><br>
            • active discussions:
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
                {{ $content['user'][0]->bio }}
            </div>
    </div>
</div>
