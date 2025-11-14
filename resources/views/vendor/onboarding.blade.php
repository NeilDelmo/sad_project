@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Vendor Onboarding</h1>
    <p class="text-gray-600 mb-6">Tell us what you want to buy so we can notify you when fishermen add matching catches.</p>

    <form method="POST" action="{{ route('vendor.onboarding.store') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block font-medium mb-2">Preferred Categories</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach($categories as $cat)
                    <label class="inline-flex items-center space-x-2 p-2 border rounded">
                        <input type="checkbox" name="preferred_categories[]" value="{{ $cat->id }}"
                               @checked(in_array($cat->id, $prefs->preferred_categories ?? []))>
                        <span>{{ $cat->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('preferred_categories')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium mb-2" for="min_quantity">Minimum Quantity (kg)</label>
                <input id="min_quantity" name="min_quantity" type="number" min="0" value="{{ old('min_quantity', $prefs->min_quantity ?? '') }}" class="w-full border rounded p-2" />
                @error('min_quantity')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="block font-medium mb-2" for="max_unit_price">Max Unit Price</label>
                <input id="max_unit_price" name="max_unit_price" type="number" min="0" step="0.01" value="{{ old('max_unit_price', $prefs->max_unit_price ?? '') }}" class="w-full border rounded p-2" />
                @error('max_unit_price')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div>
            <label class="block font-medium mb-2">Notify Me</label>
            <div class="space-y-2">
                <label class="inline-flex items-center space-x-2">
                    <input type="radio" name="notify_on" value="matching" @checked(old('notify_on', $prefs->notify_on ?? 'matching') === 'matching')>
                    <span>Only when a catch matches my preferences</span>
                </label>
                <label class="inline-flex items-center space-x-2">
                    <input type="radio" name="notify_on" value="all" @checked(old('notify_on', $prefs->notify_on ?? '') === 'all')>
                    <span>All new catches</span>
                </label>
            </div>
            @error('notify_on')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block font-medium mb-2">Channels</label>
            <div class="space-x-4">
                <label class="inline-flex items-center space-x-2">
                    <input type="checkbox" name="notify_channels[]" value="in_app"
                           @checked(in_array('in_app', old('notify_channels', $prefs->notify_channels ?? ['in_app'])))>
                    <span>In-app</span>
                </label>
                <label class="inline-flex items-center space-x-2">
                    <input type="checkbox" name="notify_channels[]" value="email"
                           @checked(in_array('email', old('notify_channels', $prefs->notify_channels ?? [])))>
                    <span>Email</span>
                </label>
            </div>
            @error('notify_channels')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Preferences</button>
        </div>
    </form>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Vendor Onboarding</h1>
    <p class="text-gray-600 mb-6">Tell us what you want to buy so we can notify you when fishermen list matching catches.</p>

    @if ($errors->any())
        <div class="bg-red-50 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('vendor.onboarding.store') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-2">Preferred Categories</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach($categories as $cat)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="preferred_categories[]" value="{{ $cat->id }}" class="rounded" 
                            @if(!empty($prefs?->preferred_categories) && in_array($cat->id, $prefs->preferred_categories)) checked @endif>
                        <span>{{ $cat->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Minimum Quantity (kg)</label>
                <input type="number" name="min_quantity" value="{{ old('min_quantity', $prefs->min_quantity ?? '') }}" class="w-full border rounded p-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Max Unit Price</label>
                <input type="number" step="0.01" name="max_unit_price" value="{{ old('max_unit_price', $prefs->max_unit_price ?? '') }}" class="w-full border rounded p-2">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Notify Me</label>
            <select name="notify_on" class="w-full border rounded p-2">
                <option value="matching" @selected(($prefs->notify_on ?? 'matching')==='matching')>Only listings matching my preferences</option>
                <option value="all" @selected(($prefs->notify_on ?? '')==='all')>All new listings</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Notification Channels</label>
            <label class="flex items-center space-x-2 mb-2">
                <input type="checkbox" name="notify_channels[]" value="in_app" class="rounded"
                    @if(empty($prefs?->notify_channels) || in_array('in_app', $prefs->notify_channels)) checked @endif>
                <span>In-app notifications</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="notify_channels[]" value="email" class="rounded"
                    @if(!empty($prefs?->notify_channels) && in_array('email', $prefs->notify_channels)) checked @endif>
                <span>Email</span>
            </label>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Preferences</button>
        </div>
    </form>
</div>
@endsection
