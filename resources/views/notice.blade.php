<!--
    beevrr
    github.com/01mu
-->

<html>
    <head>
        @include('partials/header')
    </head>
    <body>
        <div class="bd">
            <a href="{{ route('home') }}"><b>beevrr</b></a> | notice
            <hr>
            {{ $notice }}
        </div>
    </body>
</html>
