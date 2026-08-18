<x-layout>
    <h1>Board</h1>
    <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-lg-3">
        @foreach($stations as $station)
            <div class="col">
                <div class="list-item vstack gap-3">
                    <h2 class="fs-5">{{$station->name}}</h2>
                    <a href="{{route('board.show', $station)}}" class="btn outline text-primary">→ {{$station->code}} Detail Page</a>
                </div>
            </div>
        @endforeach
    </div>
</x-layout>
