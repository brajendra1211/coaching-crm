@extends('admin.layouts.app')

@section('title', 'Add Batch Fee Plan')
@section('page_title', 'Add Batch Fee Plan')

@section('content')

<form method="POST" action="{{ route('admin.batch-fee-plans.store') }}">
    @csrf

    @include('admin.fees.batch-plans._form', [
        'plan' => $plan,
        'batches' => $batches,
        'buttonText' => 'Save Fee Plan',
        'isEdit' => false,
    ])
</form>

@endsection