@extends('layouts.forum')

@section('content')
<style>
    body {
        background-color: #0b1d3a;
    }
    .forum-card {
        background-color: #132d55;
        border: 1px solid #1f3b6e;
        color: #f1f3f5;
        transition: all 0.25s ease;
    }
    .forum-card:hover {
        background-color: #1a3b70;
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.35);
    }
    .card-title {
        color: #ffffff;
    }
    .text-muted {
        color: #9fb3d2 !important;
    }
</style>

<div id="forum-content">

    <!-- Header Card -->
    <div class="card border-0 mb-5 shadow-lg" style="background: linear-gradient(135deg, #0d6efd 0%, #1a73e8 100%);">
        <div class="card-body text-white d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">Welcome to the Community!</h3>
                <p class="mb-0">Share experiences, ask questions, and connect with fellow fishermen.</p>
            </div>
            <div class="d-none d-md-block">
                <i class="bi bi-water fs-1 text-light"></i>
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="row g-4">
        @foreach($categories as $category)
            <div class="col-md-6 col-lg-4">
                <div class="card forum-card h-100" data-category="{{ $category->id }}" style="cursor: pointer;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="rounded-circle bg-primary bg-opacity-25 p-2">
                                {{-- Category Icon --}}
                                @php
                                    $icons = [
                                        'Equipment & Maintenance' => 'bi-wrench-adjustable',
                                        'Fish & Fishing Spots' => 'bi-geo-alt',
                                        'FAQ & Advice' => 'bi-question-circle',
                                        'General Discussion' => 'bi-chat-dots',
                                    ];
                                    $icon = $icons[$category->name] ?? 'bi-bubble';
                                @endphp
                                <i class="bi {{ $icon }} text-primary fs-5"></i>
                            </div>
                            <span class="badge bg-secondary">{{ $category->threads_count }} discussions</span>
                        </div>
                        <h5 class="card-title fw-semibold">{{ $category->name }}</h5>
                        <p class="card-text text-muted small">{{ $category->description }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-muted small d-flex align-items-center">
                        Click to explore <i class="bi bi-arrow-right-short ms-1"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@include('forums.forum-script')
@endsection
