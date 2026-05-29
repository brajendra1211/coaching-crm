@extends('admin.layouts.app')

@section('title', 'Add Student')
@section('page_title', 'Add Student')

@section('content')

<form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data">
    @csrf

    @include('admin.students._form', [
        'student' => $student,
        'parent' => $parent,
        'courses' => $courses,
        'buttonText' => 'Save Student',
        'isEdit' => false,
    ])
</form>

@endsection