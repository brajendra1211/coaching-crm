@extends('admin.layouts.app')

@section('title', 'Edit SEO Meta')
@section('page_title', 'Edit SEO Meta')

@section('content')

<form method="POST" action="{{ route('admin.seo.update', $seoMeta) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('admin.seo._form', [
        'seoMeta' => $seoMeta,
        'buttonText' => 'Update SEO Meta',
        'isEdit' => true,
    ])
</form>

@endsection