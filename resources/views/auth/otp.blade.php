@extends('layouts.login')

@section('content')
<div class="content-wrapper d-flex align-items-center auth">
  <div class="row flex-grow">
    <div class="col-lg-4 mx-auto">
      <div class="auth-form-light text-left p-5 text-center">
        <h4 class="mb-3">Verifikasi OTP</h4>
        <h6 class="font-weight-light">Masukkan 6 karakter kode dari email/log Anda.</h6>
        
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form class="pt-3" method="POST" action="{{ route('otp.verify') }}">
          @csrf
          <div class="form-group">
            <input type="text" name="otp" class="form-control form-control-lg text-center" 
                   maxlength="6" placeholder="XXXXXX" 
                   style="letter-spacing: 5px; font-size: 24px; font-weight: bold;" required autofocus>
          </div>

          <div class="mt-3 d-grid gap-2">
            <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">VERIFIKASI</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection