@extends('admin.layouts.app')

@section('title', 'Edit Blog')
@section('page_title', 'Edit Blog')

@section('content')

<form method="POST" action="{{ route('admin.blogs.update', $blog) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('admin.blogs._form', [
        'blog' => $blog,
        'buttonText' => 'Update Blog',
        'isEdit' => true,
    ])
</form>

@endsection