@extends('layouts.app')

@section('style')

@endsection
@section('content')

@if($resources->count() > 0)
  @foreach($resources as $resource)
  <div class="card mb-2">
    <div class="card-header">
      {{ $resource->topic }}
    </div>
    <div class="card-body">
      {!! Str::limit($resource->content,$limit = 500, $end = '...')  !!}
    </div>
    <div class="card-footer ">
      <a href="{{ route('teacher.resource.single',['resource'=>$resource]) }}" class="btn btn-info btn-sm float-right mx-2">Read More</a>
    </div>

  </div>
  @endforeach
@else
  <div class="card">
    <div class="card-body text-center">
      No resource available for you.
    </div>

  </div>
@endif

@endsection

@section('script')

@endsection
