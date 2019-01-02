<!--
    beevrr
    github.com/01mu
-->

<a href="{{ route('home') }}"><b>beevrr</b></a> | submit reply
<hr>
@guest

@else
    <div class="box">
        <form method="POST"
                action="{{ route('resp-post', ['id' => $id]) }}">
            {{ csrf_field() }}
            <textarea rows="10" cols="50"
                placeholder="response (min chars: 10)"
                id="resp" name="resp"></textarea><br>
            <select name="type" size="2">
                <option value="for">for proposition</option>
                <option value="against">against proposition</option>
            </select><br>
            {!! captcha_img('flat') !!}<br>
            <input autocomplete="off" placeholder="captcha" type="text"
                name="captcha"><br>
            <button type="submit">submit response</button>
        </form>
    </div>
@endguest
