@extends('admin.layouts.app')

@section('title', 'Edit Admission')
@section('page_title', 'Edit Admission')

@section('content')

<form method="POST" action="{{ route('admin.admissions.update', $admission) }}">
    @csrf
    @method('PUT')

    @include('admin.admissions._form', [
        'admission' => $admission,
        'courses' => $courses,
        'buttonText' => 'Update Admission',
        'isEdit' => true,
    ])
</form>

@endsection