<!--
    beevrr
    github.com/01mu
-->

@guest

@else
    <div class="flex">
        <div class="wrapper50">
            <b>Change Password</b>
        </div>
        <div class="wrapper50">
            <b>Stats</b>
        </div>
    </div>
    <hr>
    <div class="flex">
        <div class="wrapper50">
            <form method="POST" action="{{ route('change-pw') }}">
                {{ csrf_field() }}
                <input  placeholder="old password"
                        type="password"
                        id="oldpw"
                        name="oldpw">
                <br>
                <input  placeholder="confirm old password"
                        type="password"
                        id="conoldpw"
                        name="conoldpw">
                <br>
                <input  placeholder="new password"
                        type="password"
                        id="newpw"
                        name="newpw">
                <br>
                <input  placeholder="confirm new password"
                        type="password"
                        id="connewpw"
                        name="connewpw">
                <br>
                <button type="submit">Change</button>
            </form>
        </div>
        <div class="wrapper50">
            • Total Responses: {{ $content['user'][0]->total_responses }}
            <br>
            • Active Responses: {{ $content['user'][0]->active_responses }}
            <br>
            <br>
            • Total Votes: {{ $content['user'][0]->total_votes }}<br>
            • Active Votes: {{ $content['user'][0]->active_votes }}
            <br>
            <br>
            • Total Discussions: {{ $content['user'][0]->total_discussions }}
            <br>
            • Active Discussions: {{ $content['user'][0]->active_discussions }}
        </div>
    </div>
    <hr>
    <div class="flex">
        <div class="wrapper50">
            <b>Bio</b>
        </div>
        <div class="wrapper50">
        </div>
    </div>
    <hr>
    <div class="flex">
        <div class="wrapper50">
            <form method="POST" action="{{ route('change-bio') }}">
                {{ csrf_field() }}
                <textarea   rows="10"
                            cols="40"
                            placeholder="bio (max chars: 500)"
                            id="bio"
                            name="bio"
                            >{{ $content['user'][0]->bio }}</textarea>
                <br>
                <button type="submit">Update</button>
            </form>
        </div>
        <div class="wrapper50">
        </div>
    </div>
@endguest
