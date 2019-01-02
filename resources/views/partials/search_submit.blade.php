<!--
    beevrr
    github.com/01mu
-->

<a href="{{ route('home') }}"><b>beevrr</b></a> | search
<hr>
<form method="GET" action="{{ route('search-post', ['p' => 0]) }}">
    <input autocomplete="off" placeholder="query" type="text" name="q"><br>
    <button type="submit">search</button>
</form>
