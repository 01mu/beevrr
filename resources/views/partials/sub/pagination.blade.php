<div class="flex">
    @if($content['pagination']['nl'] == 0)
    <div class="wrapper50">
        <div class="small">
            <a href="{!! $left !!}"><<</a>
        </div>
    </div>
    @else
    <div class="wrapper50">
    </div>
    @endif
    <div class="wrapper50">
        <div class="small">
            <div class="right">
                 <a href="{!! $right !!}">>></a>
            </div>
        </div>
    </div>
</div>
