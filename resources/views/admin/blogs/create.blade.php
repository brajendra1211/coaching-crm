@extends('admin.layouts.app')

@section('title', 'Add Blog')
@section('page_title', 'Add Blog')

@section('content')

<form method="POST" action="{{ route('admin.blogs.store') }}" enctype="multipart/form-data">
    @csrf

    @include('admin.blogs._form', [
        'blog' => $blog,
        'buttonText' => 'Save Blog',
        'isEdit' => false,
    ])
</form>

@endsection