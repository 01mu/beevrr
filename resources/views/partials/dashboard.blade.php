<!--
    beevrr
    github.com/01mu
-->

@guest

@else
    <b>dashboard</b>
    <hr>
    <div class="flex">
        <div class="wrapper50">
            change password
        </div>
        <div class="wrapper50">
            stats
        </div>
    </div>
    <hr>
    <div class="flex">
        <div class="wrapper50">
            <div class="box">
                <form method="POST" action="{{ route('change-pw') }}">
                    {{ csrf_field() }}
                    <input placeholder="old password"
                        type="password" id="oldpw" name="oldpw"><br>
                    <input placeholder="confirm old password"
                        type="password" id="conoldpw" name="conoldpw"><br>
                    <input placeholder="new password"
                        type="password" id="newpw" name="newpw"><br>
                    <input placeholder="confirm new password"
                        type="password" id="connewpw" name="connewpw"><br>
                    <button type="submit">Change</button>
                </form>
            </div>
        </div>
        <div class="wrapper50">
            <div class="box">
                • total responses: {{ $content['user'][0]->total_responses }}
                <br>
                • active responses: {{ $content['user'][0]->active_responses }}
                <br><br>
                • total votes: {{ $content['user'][0]->total_votes }}<br>
                • active votes: {{ $content['user'][0]->active_votes }}
                <br><br>
                • total discussions:{{ $content['user'][0]->total_discussions }}
                <br>
                • active discussions:
                    {{ $content['user'][0]->active_discussions }}
            </div>
        </div>
    </div>
@endguest
