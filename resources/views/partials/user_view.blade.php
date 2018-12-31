<!--
    beevrr
    github.com/01mu
-->

<b>{{ $content['user'][0]->user_name }}</b>
<hr>
<div class="box">
    • total responses: {{ $content['user'][0]->total_responses }}
    <br>
    • active responses: {{ $content['user'][0]->active_responses }}
    <br><br>
    • total votes: {{ $content['user'][0]->total_votes }}<br>
    • active votes: {{ $content['user'][0]->active_votes }}
    <br><br>
    • total discussions:{{ $content['user'][0]->total_discussions }}
    <br>
    • active discussions:
        {{ $content['user'][0]->active_discussions }}
</div>
