<html>
    <head>
        <link href="css/common.css" rel="stylesheet" type="text/css" media="all">
        <title>Beevrr</title>
    </head>
    <body>
        <div class="bd">
        <div class="right">
        @guest
            [ <a href="{{ route('login') }}">login</a> |
            <a href="{{ route('register') }}">register</a> ]
        @else
        {{ Auth::user()->user_name }}
        <a  href="{{ route('logout') }}"
            onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">[logout]</a>
        <form   id="logout-form"
                action="{{ route('logout') }}"
                method="POST"
                style="display: none;">{{ csrf_field() }}</form>
        @endguest
        </div>
        <hr>
        <h1>Beevrr</h1>
        <h5>Oxford style debate platform!</h5>
        <hr>
        <div class="header">
            Posts
        </div>
        <hr>
        @for($i = 0; $i < count($content[0]); $i++)
            <div class="box">
            @if($i % 2 === 0)
                {{ $content[0][$i]->user_name }}
            @else
                {{ $content[0][$i]->user_name }}
            @endif
            </div>
        @endfor
        <hr>
        Discussions: 0 |
        Responses: 0 |
        Votes: 0 |
        Users: {{ $content[1][0]->count }}
        <hr>
        </div>
    </body>
</html>
