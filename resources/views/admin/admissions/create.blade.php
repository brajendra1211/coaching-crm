@extends('admin.layouts.app')

@section('title', 'New Admission')
@section('page_title', 'New Admission')

@section('content')

<form method="POST" action="{{ route('admin.admissions.store') }}">
    @csrf

    @include('admin.admissions._form', [
        'admission' => $admission,
        'courses' => $courses,
        'buttonText' => 'Save Admission',
        'isEdit' => false,
    ])
</form>

@endsection