<!--
    beevrr
    github.com/01mu
-->

<div class="small">
    <hr>
    discussions: {{ $content['discussion_count'][0]->count }} |
    responses: {{ $content['response_count'][0]->count }} |
    votes: {{ $content['vote_count'][0]->count }} |
    users: {{ $content['user_count'][0]->count }}
    <span style="float: right;">
        <a href="{{ route('search-view') }}">[search]</a>
    </span>
</div>
