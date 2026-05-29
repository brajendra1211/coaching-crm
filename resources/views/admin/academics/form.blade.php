@extends('admin.layouts.app')

@section('title', $title)
@section('page_title', $title)

@section('content')

<style>
    .academic-form {
        max-width: 920px;
    }

    .form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .07);
        overflow: hidden;
    }

    .form-card-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #fff, #f8fafc);
    }

    .form-card-head h3 {
        margin: 0;
        color: #111827;
    }

    .form-card-body {
        padding: 22px;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-weight: 900;
        color: #334155;
        margin-bottom: 8px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 13px 14px;
        font-size: 15px;
        outline: none;
        background: #fff;
        color: #111827;
        font-family: inherit;
    }

    .form-group textarea {
        min-height: 115px;
        resize: vertical;
    }

    .error-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 14px;
        border-radius: 14px;
        margin-bottom: 18px;
    }

    @media(max-width: 800px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>

@if($errors->any())
    <div class="error-box">
        <strong>Please fix errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $isEdit = $item && $item->exists;
    $action = $isEdit
        ? route('admin.' . $routePrefix . '.update', $item)
        : route('admin.' . $routePrefix . '.store');
@endphp

<form method="POST" action="{{ $action }}" class="academic-form" enctype="multipart/form-data">
    @csrf

    @if($isEdit)
        @method('PUT')
    @endif

    <div class="form-card">
        <div class="form-card-head">
            <h3>{{ $title }}</h3>
        </div>

        <div class="form-card-body">
            <div class="grid-2">
                @foreach($fields as $field)
                    <div class="form-group" style="{{ ($field['type'] ?? 'text') === 'textarea' ? 'grid-column:1/-1;' : '' }}">
                        <label>{{ $field['label'] }}</label>

                        @php
                            $name = $field['name'];
                            $type = $field['type'] ?? 'text';
                            $value = old($name, $item->$name ?? '');
                        @endphp

                        @if($type === 'textarea')
                            <textarea name="{{ $name }}" {{ !empty($field['required']) ? 'required' : '' }}>{{ $value }}</textarea>
                      @elseif($type === 'select')
                    <select name="{{ $name }}" {{ !empty($field['required']) ? 'required' : '' }}>
                        <option value="">Select {{ $field['label'] }}</option>

                        @foreach($field['options'] as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}" {{ $value == $optionValue ? 'selected' : '' }}>
                                {{ $optionLabel }}
                            </option>
                        @endforeach
                    </select>
                @elseif($type === 'file')
                    <input
                        type="file"
                        name="{{ $name }}"
                        {{ !empty($field['required']) ? 'required' : '' }}
                    >

                    @if(!empty($value))
                        <div style="margin-top:8px;">
                            <a href="{{ asset('storage/' . $value) }}" target="_blank" class="btn btn-light">
                                View Current File
                            </a>
                        </div>
                    @endif
                @else
                    <input
                        type="{{ $type }}"
                        name="{{ $name }}"
                        value="{{ $type === 'date' && $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d') : $value }}"
                        {{ !empty($field['required']) ? 'required' : '' }}
                    >
                @endif
                    </div>
                @endforeach
            </div>

            <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    {{ $isEdit ? 'Update' : 'Save' }}
                </button>

                <a href="{{ route('admin.' . $routePrefix . '.index') }}" class="btn btn-light">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

@endsection