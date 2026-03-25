<div>
    @if (!$getState() || (is_object($getState()) && $getState()->isEmpty()))
          Not Assigned
    @else
    <ul>
        @foreach ($getState() ?? [] as $floor)
            <li>
                <span style="margin-right: 6px;">&#8226;</span>
                {{ $floor->numberWithFormat() }}
            </li>
        @endforeach
    </ul>
    @endif
</div>
