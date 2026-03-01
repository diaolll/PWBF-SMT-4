@extends('layouts.login')

@section('content')
<div class="content-wrapper d-flex align-items-center auth">
  <div class="row flex-grow">
    <div class="col-lg-4 mx-auto">
      <div class="auth-form-light text-left p-5 text-center">
        <h3 class="text-primary font-weight-bold">WorkshopWEB</h3>
        
        <h4 class="mb-3">Verifikasi OTP</h4>
        <h6 class="font-weight-light">
          Kami telah mengirimkan kode 6 karakter ke email <br>
          <span class="text-primary font-weight-bold">{{ auth()->user()->email ?? 'Anda' }}</span>
        </h6>
        
        @if(session('error'))
            <div class="alert alert-danger mt-3">{{ session('error') }}</div>
        @endif

        <form class="pt-3" method="POST" action="{{ route('otp.verify') }}">
          @csrf
          <div class="form-group">
            <input type="text" name="otp" 
                   class="form-control form-control-lg text-center" 
                   maxlength="6" 
                   placeholder="XXXXXX" 
                   style="letter-spacing: 10px; font-size: 28px; font-weight: bold; text-transform: uppercase;" 
                   oninput="this.value = this.value.toUpperCase()"
                   required autofocus>
            <small class="text-muted mt-2 d-block">Periksa folder Inbox atau Spam</small>
          </div>

          <div class="mt-3 d-grid gap-2">
            <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
              VERIFIKASI
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection