Login
<hr>
@guest
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input placeholder="Username" id="user_name" name="user_name"><br>
        <input placeholder="Password" type="password" id="password" name="password"><br>
        <button type="submit">
            {{ __('Login') }}
        </button>
    </form>
    @if ($errors->has('email'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
    @endif

    @if ($errors->has('password'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('password') }}</strong>
        </span>
    @endif
@else
{{ Auth::user()->user_name }}
<a  href="{{ route('logout') }}"
    onclick="event.preventDefault();
    document.getElementById('logout-form').submit();">Logout</a>
<form   id="logout-form"
        action="{{ route('logout') }}"
        method="POST"
        style="display: none;">{{ csrf_field() }}</form>
@endguest
