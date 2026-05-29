@extends('admin.layouts.app')

@section('title', 'Edit Testimonial')
@section('page_title', 'Edit Testimonial')

@section('content')

<form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('admin.testimonials._form', [
        'testimonial' => $testimonial,
        'buttonText' => 'Update Testimonial',
        'isEdit' => true,
    ])
</form>

@endsection