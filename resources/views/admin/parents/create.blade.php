@extends('admin.layouts.app')

@section('title', 'Add Parent')
@section('page_title', 'Add Parent / Guardian')

@section('content')

<form method="POST" action="{{ route('admin.parents.store') }}">
    @csrf

    @include('admin.parents._form', [
        'parent' => $parent,
        'students' => $students,
        'buttonText' => 'Save Parent',
        'isEdit' => false,
    ])
</form>

@endsection