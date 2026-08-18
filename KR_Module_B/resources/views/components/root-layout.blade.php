<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="module-b">
    <title>Document</title>
    <link rel="stylesheet" href="{{asset('public/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/main.css')}}">
    <script src="{{asset('public/bootstrap.bundle.js')}}"></script>
</head>
<body>

{{$slot}}

<div class="toast-container position-fixed start-0 top-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <div
                class="rounded me-2 p-2 {{session()->has('msg') ? 'bg-success' : ''}} {{$errors->any() ? 'bg-danger' : ''}}"></div>
            <strong class="me-auto">Message</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            @if(session()->has('msg'))
                <span class="multiline">{{session()->get('msg')}}</span>
            @endif
            @if($errors->any())
                <div class="vstack gap-2">
                    @foreach($errors->all() as $error)
                        <span>{{$error}}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@if(session()->has('msg') || $errors->any())
    <script>
        const toastLiveExample = document.getElementById('liveToast')

        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
        toastBootstrap.show()
    </script>
@endif

</body>
</html>
