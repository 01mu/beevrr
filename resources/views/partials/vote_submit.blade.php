<!--
    beevrr
    github.com/01mu
-->


@guest

@else
    <a href="{{ route('home') }}"><b>beevrr</b></a> | submit

    @if($content['phase'] === 'pre-argument')
        pre-argument vote
    @else
        post-argument vote
    @endif

    <hr>
    <div class="box">
        <form method="POST" action="{{ route('vote-post',
            ['phase' => $content['phase'],
            'id' => $content['id']]) }}">
            {{ csrf_field() }}
            @if($content['phase'] === 'pre-argument')
            <select name="v" size="3">
                <option value="for">for</option>
                <option value="against">against</option>
                <option value="undecided">undecided</option>
            </select>
            @else
            <select name="v" size="2">
                <option value="for">for</option>
                <option value="against">against</option>
            </select>
            @endif
            <br>
            {!! captcha_img('flat') !!}<br>
            <input autocomplete="off"
                placeholder="captcha" type="text" name="captcha"><br>
            <button type="submit">submit discussion</button>
        </form>
    </div>

@endguest
