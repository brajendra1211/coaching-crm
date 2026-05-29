@extends('admin.layouts.app')

@section('title', 'Edit Parent')
@section('page_title', 'Edit Parent / Guardian')

@section('content')

<form method="POST" action="{{ route('admin.parents.update', $parent) }}">
    @csrf
    @method('PUT')

    @include('admin.parents._form', [
        'parent' => $parent,
        'students' => $students,
        'buttonText' => 'Update Parent',
        'isEdit' => true,
    ])
</form>

@endsection