<!--
    beevrr
    github.com/01mu
-->

<a href="{{ route('home') }}"><b>beevrr</b></a> | submit discussion
<hr>
@guest

@else
    <div class="box">
        <form method="POST" action="{{ route('disc-sub-post') }}">
            {{ csrf_field() }}
            <input placeholder="proposition (min chars: 10)"
                id="prop" name="prop"><br>
            <textarea rows="10" cols="50" placeholder="argument (min chars: 10)"
                id="arg" name="arg"></textarea><br>
            <select name="pa" size="5">
                <option value="title">pre-argument phase</option>
                <option value="1hour">1 hour</option>
                <option value="6hours">6 hours</option>
                <option value="1day">24 hours</option>
                <option value="3days">72 hours</option>
            </select>
            <select name="a" size="5">
                <option value="title">argument phase</option>
                <option value="1hour">1 hour</option>
                <option value="6hours">6 hours</option>
                <option value="1day">24 hours</option>
                <option value="3days">72 hours</option>
            </select>
            <select name="v" size="5">
                <option value="title">voting phase</option>
                <option value="1hour">1 hour</option>
                <option value="6hours">6 hours</option>
                <option value="1day">24 hours</option>
                <option value="3days">72 hours</option>
            </select><br>
            {!! captcha_img('flat') !!}<br>
            <input autocomplete="off"
                placeholder="captcha" type="text" name="captcha"><br>
            <button type="submit">submit discussion</button>
        </form>
    </div>
@endguest
