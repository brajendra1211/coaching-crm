@extends('admin.layouts.app')

@section('title', 'Add SEO Meta')
@section('page_title', 'Add SEO Meta')

@section('content')

<form method="POST" action="{{ route('admin.seo.store') }}" enctype="multipart/form-data">
    @csrf

    @include('admin.seo._form', [
        'seoMeta' => $seoMeta,
        'buttonText' => 'Save SEO Meta',
        'isEdit' => false,
    ])
</form>

@endsection