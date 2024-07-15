@extends('layouts.guest')

@section('content')
<main class="form-signin">
    <form class="text-center" method="POST" action="{{ route('login') }}">
        @csrf
      <img class="mb-4 " src="/icon.jpeg" alt="" width="120">
  
      <div class="form-floating">
        <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email" value="{{ old('email') }}" required autofocus>
        <label for="floatingInput">Email address</label>
      </div>
      <div class="form-floating">
        <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password" required>
        <label for="floatingPassword">Password</label>
      </div>
  
      <button class="w-100 btn btn-lg btn-dark mb-3" type="submit">Sign in</button>
        <a href="{{ route('register') }}" class="mt-3 text-dark">Register</a>
      <p class="mt-5 mb-3 text-muted">&copy; 2008–{{ Date('Y') }}</p>
    </form>
  </main>
@endsection
