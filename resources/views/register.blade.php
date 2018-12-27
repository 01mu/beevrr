Register
<hr>
@guest
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input placeholder="Username" id="user_name" name="user_name"><br>
        <input placeholder="Password" type="password" id="password" name="password"><br>
        <input placeholder="Confirm Password" type="password" id="password-confirm" name="password_confirmation"><br>
        <button type="submit">
            {{ __('Register') }}
        </button>
    </form>
    @if ($errors->has('name'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
    @endif
    @if ($errors->has('password'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('password') }}</strong>
        </span>
    @endif
@else

@endguest
