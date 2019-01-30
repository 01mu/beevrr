<!--
    beevrr
    github.com/01mu
-->

<a href="{{ route('home') }}"><b>Beevrr</b></a> | Login
<hr>
@guest
    <div class="box">
        <form method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}
            <input autocomplete="off" placeholder="username"
                id="user_name" name="user_name"><br>
            <input autocomplete="off" placeholder="password"
                type="password" id="password" name="password"><br>
            {!! captcha_img('flat') !!}<br>
            <input autocomplete="off" placeholder="captcha"
                type="text" name="captcha"><br>
            <button type="submit">
                {{ __('Login') }}
            </button>
        </form>
    </div>
@else
<a  href="{{ route('logout') }}"
    onclick="event.preventDefault();
    document.getElementById('logout-form').submit();">Logout</a>
<form   id="logout-form"
        action="{{ route('logout') }}"
        method="POST"
        style="display: none;">{{ csrf_field() }}</form>
@endguest
