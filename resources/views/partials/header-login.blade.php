<!--
    beevrr
    github.com/01mu
-->

<div class="small">
    <div class="right">
        @guest
            [ <a href="{{ route('login') }}">login</a> |
            <a href="{{ route('register') }}">register</a> ]
        @else
        <a href="{{ route('user-view', ['id' => Auth::user()->id]) }}">
            {{ Auth::user()->user_name }}</a>
        <a href="{{ route('dashboard') }}">[dashboard]</a>
        <a  href="{{ route('logout') }}"
            onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">[logout]</a>
        <form   id="logout-form"
                action="{{ route('logout') }}"
                method="POST"
                style="display: none;">{{ csrf_field() }}</form>
        @endguest
    </div>
</div>
<hr>
<a href="{{ route('home') }}"><h1>beevrr</h1></a>
<h5>oxford style debate platform!</h5>
<hr>
