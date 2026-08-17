<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('login');
Route::view('/register', 'register')->name('register');

Route::post('/login', function(\Illuminate\Http\Request $request) {
    $request->validate([
        'username' => 'required',
        'password' => 'required'
    ]);

    $users = collect(\Illuminate\Support\Facades\Storage::json('users.json'));

    $user = $users->where('username', $request->username)->first();

    if(!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user['password'])) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'username' => 'username or password invalid',
            'password' => 'username or password invalid'
        ]);
    }

    session()->push('user', $user);
    return to_route('main')->with('msg', 'login success');
})->name('login.action');

Route::post('/register', function(\Illuminate\Http\Request $request) {
    $request->validate([
        'username' => 'required',
        'password' => 'required'
    ]);

    $users = collect(\Illuminate\Support\Facades\Storage::json('users.json'));

    $users->push(['username' => $request->username, 'password' => \Illuminate\Support\Facades\Hash::make($request->password)]);

    \Illuminate\Support\Facades\Storage::put('users.json', $users);

    return to_route('login')->with('msg', 'success register user redirect to login');
})->name('register.action');

Route::middleware(\App\Http\Middleware\AuthCheck::class)->group(function() {
    Route::view('/main', 'main')->name('main');

    Route::get('/logout', function() {
        session()->forget('user');
        return to_route('login');
    })->name('logout');
});
