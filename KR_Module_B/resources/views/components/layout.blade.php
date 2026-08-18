<x-root-layout>
    <header>
        <div class="container hstack justify-content-between h-100">
            <div class="logo fs-5">Board Render</div>

            <div class="hstack gap-4">
                <a href="{{route('board.index')}}">Boards</a>
                <a href="{{route('stats.index')}}">Stats</a>
            </div>
        </div>
    </header>
    <main>
        <div class="container vstack gap-3">
            {{$slot}}
        </div>
    </main>
</x-root-layout>
