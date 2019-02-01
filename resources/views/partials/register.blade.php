<!--
    beevrr
    github.com/01mu
-->

<a href="{{ route('home') }}"><b>Beevrr</b></a> | Register
<hr>
@guest
    <form method="POST" action="{{ route('register') }}">
        {{ csrf_field() }}
        <input  autocomplete="off"
                placeholder="username"
                id="user_name"
                name="user_name">
        <br>
        <input  autocomplete="off"
                placeholder="password"
                type="password"
                id="password"
                name="password">
        <br>
        <input  autocomplete="off"
                placeholder="confirm password"
                type="password"
                id="password-confirm"
                name="password_confirmation">
        <br>
        {!! captcha_img('flat') !!}
        <br>
        <input  autocomplete="off"
                placeholder="captcha"
                type="text"
                name="captcha">
        <br>
        <button type="submit">
            {{ __('Register') }}
        </button>
    </form>
@else

@endguest
