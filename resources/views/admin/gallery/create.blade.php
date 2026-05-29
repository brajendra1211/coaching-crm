@extends('admin.layouts.app')

@section('title', 'Add Gallery Item')
@section('page_title', 'Add Gallery Item')

@section('content')

<form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data">
    @csrf

    @include('admin.gallery._form', [
        'galleryItem' => $galleryItem,
        'buttonText' => 'Save Gallery Item',
        'isEdit' => false,
    ])
</form>

@endsection