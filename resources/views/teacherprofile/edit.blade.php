@extends('layouts.app')

@section('style')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
@endsection

@section('content')

<div class="card">
  <div class="card-header">Edit Profile</div>

  <div class="card-body">
    <form method="POST" action="{{ route('teacherProfile.update',['teacherProfile'=>Auth::user()->teacherProfile]) }}" enctype="multipart/form-data">

        @csrf
        @method('put')
        <div class="form-group">
          <label for="">Avatar</label>
          <input type="file" name="avatar" value="{{ old('class') }}" class="form-control form-control @error('avatar') is-invalid @enderror">
          @error('avatar')
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
          @enderror
        </div>

        <div class="form-group">
          <label for="phone" class="">{{ __('Phone') }}</label>
          <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ $user->profile->phone }}" required autocomplete="phone">

          @error('phone')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>

        <div class="form-group">
          <label for="profession" class="">{{ __('profession') }}</label>
          <input id="profession" type="text" class="form-control @error('profession') is-invalid @enderror" name="profession" value="{{ $user->profile->profession }}" required autocomplete="profession">

          @error('profession')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>

        <div class="form-group">
          <label for="address" class="">{{ __('address') }}</label>
          <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ $user->profile->address }}" required autocomplete="address">

          @error('address')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>

        <div class="form-group">
          <label for="birthdate" class="">{{ __('birthdate') }}</label>

          <input id="birthdate" type="date" class="form-control @error('birthdate') is-invalid @enderror" name="birthdate" value="{{ $user->profile->birthdate }}" required autocomplete="birthdate">

          @error('birthdate')
          <span class="invalid-feedback" role="alert">
           <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>

        <div class="form-group mx-2">
            <label for="year_of_experience" class="">Years of experience</label>
            <input id="year_of_experience" type="number" class="form-control @error('year_of_experience') is-invalid @enderror" name="year_of_experience" value="{{ $user->teacherProfile->year_of_experience }}" required autocomplete="year_of_experience" min="1">
            @error('year_of_experience')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group mx-2">
          <label for="specilization">Specilization</label>
          <textarea  id="specilization" name="specilization" class="form-control @error('specilization') is-invalid @enderror" rows="8" cols="80">
            {!! $user->teacherProfile->specilization !!}
          </textarea>
          @error('specilization')
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
          @enderror

        </div>

        <div class="form-group row justify-content-center mb-1">
          <button type="submit" class="btn btn-primary">
              Update
          </button>
        </div>
    </form>
  </div>
</div>

@endsection

@section('script')
<script>
  $('#specilization').summernote({
    placeholder: 'Teacher Specilization',
    tabsize: 2,
    height: 100
  });
</script>
@endsection
