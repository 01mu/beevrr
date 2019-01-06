<!--
    beevrr
    github.com/01mu
-->

<span class="hidden"
    id="disc_count">{{ $content['disc_count'] }}</span>
<b>discussions</b>
<hr>
@for($i = 0; $i < $content['disc_count']; $i++)
    <span class="hidden"
        id="arg-{{ $i }}">{{ $content['discussions'][$i]->argument }}</span>
    <div class="box">
        <span id="app-{{ $i }}">
            <span v-bind:title="message">
            <b>
                <a href="{{ route('disc-view',
                    ['id' => $content['discussions'][$i]->id]) }}">
                    {{ $content['discussions'][$i]->proposition }}</a>
            </b>
            </span>
        </span>
        <br>
        <div class="small">
            by
            <a href="{{ route('user-view',
                ['id' => $content['discussions'][$i]->user_id]) }}">
                {{ $content['discussions'][$i]->user_name }}</a>
            {{ $content['discussions'][$i]->post_date }}
            | {{ $content['discussions'][$i]->score }} likes
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

<script>
    var count = document.getElementById('disc_count').innerHTML

    for(var i = 0; i < count; i++)
    {
        new Vue({
            el: '#app-' + i,
            data: {
                message: document.getElementById('arg-' + i).innerHTML
            }
        })
    }
</script>
