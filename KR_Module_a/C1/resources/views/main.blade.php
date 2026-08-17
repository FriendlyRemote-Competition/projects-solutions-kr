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
    <h1>Main Page</h1>
    <span>Expert Readme!!: users.json check to <strong>/KR_Module_a/C1/public/data/users.json</strong> check please</span>
    <span>username is: <strong>{{session()->get('user')[0]['username']}}</strong></span>
    <a class="btn btn-primary me-auto" href="{{route('logout')}}">Logout</a>
</div>
</body>
</html>
