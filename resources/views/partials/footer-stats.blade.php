<!--
    beevrr
    github.com/01mu
-->

<div class="small">
    <hr>
    {{ $content['discussion_count'][0]->count }} discussions |
    {{ $content['response_count'][0]->count }} reponses |
    {{ $content['vote_count'][0]->count }} votes |
    {{ $content['user_count'][0]->count }} users
    <span style="float: right;">
        <a href="{{ route('search-view') }}">[search]</a>
    </span>
</div>
