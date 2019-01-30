<!--
    beevrr
    github.com/01mu
-->

<b>{{ $content['user']->user_name }}'s {{ $content['title'] }}</b>
<hr>
@if(count($content['activities']) > 0)
    @foreach($content['activities'] as $activity)
        <div class="box">
            • {{ $activity['date'] }}:
            {{ $activity['thing'] }}
            <b><a href="{{ route('disc-view', array(
                'id' => $activity['prop'],)) }}">{{ $activity['prop'] }}</a></b>
            {{ $activity['type'] }}
        </div>
    @endforeach

@else
<div class="box">
    None
</div>
@endif

@include('partials/sub/pagination', array(
    'left' => $content['pagination']['left'],
    'right' =>  $content['pagination']['right'],))
