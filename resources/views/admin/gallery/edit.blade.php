@extends('admin.layouts.app')

@section('title', 'Edit Gallery Item')
@section('page_title', 'Edit Gallery Item')

@section('content')

<form method="POST" action="{{ route('admin.gallery.update', $galleryItem) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('admin.gallery._form', [
        'galleryItem' => $galleryItem,
        'buttonText' => 'Update Gallery Item',
        'isEdit' => true,
    ])
</form>

@endsection