@extends('admin.layouts.app')

@section('title', 'Add Testimonial')
@section('page_title', 'Add Testimonial')

@section('content')

<form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data">
    @csrf

    @include('admin.testimonials._form', [
        'testimonial' => $testimonial,
        'buttonText' => 'Save Testimonial',
        'isEdit' => false,
    ])
</form>

@endsection