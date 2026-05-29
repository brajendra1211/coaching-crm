@extends('admin.layouts.app')

@section('title', 'Edit Student')
@section('page_title', 'Edit Student')

@section('content')

<form method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('admin.students._form', [
        'student' => $student,
        'parent' => $parent,
        'courses' => $courses,
        'buttonText' => 'Update Student',
        'isEdit' => true,
    ])
</form>

@endsection