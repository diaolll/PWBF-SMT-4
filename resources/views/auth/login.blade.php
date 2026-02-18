@extends('layouts.login') {{-- Kita akan buat layout khusus login sebentar lagi --}}

@section('content')
<div class="content-wrapper d-flex align-items-center auth">
  <div class="row flex-grow">
    <div class="col-lg-4 mx-auto">
      <div class="auth-form-light text-left p-5">
        <div class="brand-logo">
          <img src="{{ asset('assets/images/logo.svg') }}">
        </div>
        <h4>Halo! Mari kita mulai</h4>
        <h6 class="font-weight-light">Sign in untuk melanjutkan.</h6>
        
        <form class="pt-3" method="POST" action="{{ route('login') }}">
          @csrf
          <div class="form-group">
            <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Email Address" value="{{ old('email') }}" required autofocus>
            @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
          
          <div class="form-group">
            <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Password" required>
            @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <div class="mt-3 d-grid gap-2">
            <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
          </div>

          <div class="my-2 d-flex justify-content-between align-items-center">
            <div class="form-check">
              <label class="form-check-label text-muted">
                <input type="checkbox" name="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}> Ingat saya </label>
            </div>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="auth-link text-black">Lupa password?</a>
            @endif
          </div>
          
          <div class="text-center mt-4 font-weight-light"> Belum punya akun? <a href="{{ route('register') }}" class="text-primary">Buat</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection