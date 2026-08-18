<x-layout>
    <a href="{{route('board.index')}}" class="btn outline text-dark me-auto">Back</a>
    <h1>{{$station->name}}</h1>

    <div class="list-item table-responsive">
        <table class="table table-borderless table-hover">
            <thead>
            <tr>
                <th>departure time</th>
                <th>line</th>
                <th>destination</th>
                <th>departure in</th>
                <th>seats available</th>
                <th>status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($departures as $departure)
                <tr>
                    <td>{{$departure['departure_time']}}</td>
                    <td>{{$departure['line']->name}}</td>
                    <td>{{$departure['destination']['name']}}</td>
                    <td>{{$departure['departs_in']}}</td>
                    <td>{{$departure['status'] === 'cancelled' ? '-' : $departure['seats_available']}}</td>
                    <td>{{$departure['status']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        setTimeout(() => window.location.reload(), 10000);
    </script>
</x-layout>
