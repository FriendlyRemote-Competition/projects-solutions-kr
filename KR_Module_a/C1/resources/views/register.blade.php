<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>C1: Login System Using JSON File (Level 03)</title>
    <link rel="stylesheet" href="{{asset('public/bootstrap.min.css')}}">
    <script>
        @if(session()->has('msg'))
        alert('{{session()->get('msg')}}');
        @endif
    </script>
</head>
<body>
<div class="vstack gap-3 p-4">
    <h1>Register User</h1>
    <form action="{{route('register.action')}}" class="col-4 vstack gap-3" method="post">
        @csrf
        <div>
            <label for="username" class="form-label">username</label>
            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror">
            @error('username')
            <span class="invalid-feedback">{{$message}}</span>
            @enderror
        </div>
        <div>
            <label for="password" class="form-label">password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
            @error('password')
            <span class="invalid-feedback">{{$message}}</span>
            @enderror
        </div>
        <button class="btn btn-primary">Register</button>
    </form>
    <a href="{{route('login')}}">Login</a>
</div>
</body>
</html>
