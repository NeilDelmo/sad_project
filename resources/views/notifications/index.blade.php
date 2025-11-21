@extends('layouts.plain')

@section('content')
<style>
    .notif-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; }
    .notif-row { padding:14px 18px; display:flex; justify-content:space-between; gap:18px; border-bottom:1px solid #f1f5f9; }
    .notif-row.unread { background:#eff6ff; }
    .notif-row:last-child { border-bottom:none; }
    .notif-title { font-weight:600; font-size:15px; margin-bottom:4px; color:#0f172a; }
    .notif-msg { font-size:13px; color:#475569; margin-bottom:6px; }
    .meta { font-size:11px; color:#64748b; }
    .otp-box { display:inline-flex; align-items:center; gap:6px; background:#dbeafe; color:#1e3a8a; font-size:12px; padding:4px 10px; border-radius:8px; font-weight:600; letter-spacing:1px; margin-top:6px; }
    .actions { display:flex; gap:8px; flex-shrink:0; }
    .btn-xs { display:inline-flex; align-items:center; font-size:11px; font-weight:500; border-radius:6px; padding:4px 8px; border:1px solid #d1d5db; background:#fff; color:#1f2937; text-decoration:none; }
    .btn-xs:hover { background:#f1f5f9; }
    .btn-open { background:#475569; color:#fff; border-color:#475569; }
    .btn-open:hover { background:#334155; }
    .btn-mark { background:#2563eb; color:#fff; border-color:#2563eb; }
    .btn-mark:hover { background:#1d4ed8; }
    .new-badge { background:#2563eb; color:#fff; font-size:10px; padding:2px 6px; border-radius:10px; font-weight:600; }
    .pager { margin-top:24px; }
    .back-inline { display:inline-flex; align-items:center; gap:6px; font-size:14px; font-weight:500; padding:6px 12px; border-radius:8px; background:#ffffff; border:1px solid #d1d5db; color:#1f2937; text-decoration:none; margin-bottom:18px; cursor:pointer; }
    .back-inline:hover { background:#f1f5f9; }
</style>

<button type="button" class="back-inline" onclick="window.history.back()">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 2 5 8l6 6"/></svg>
    Back
</button>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
    <h1 style="font-size:22px; font-weight:700; margin:0; color:#0f172a;">Notifications</h1>
    <form method="POST" action="{{ route('notifications.read.all') }}" style="margin:0;">
        @csrf
        <button class="btn-xs" type="submit">Mark all read</button>
    </form>
</div>

<div class="notif-card">
    @forelse($notifications as $n)
        @php($data = $n->data ?? [])
        <div class="notif-row @if(!$n->read_at) unread @endif">
            <div style="flex:1; min-width:0;">
                <div class="notif-title">
                    {{ $data['title'] ?? 'Notification' }}
                    @if(!$n->read_at)
                        <span class="new-badge" style="margin-left:6px;">NEW</span>
                    @endif
                </div>
                @if(!empty($data['message']))
                    <div class="notif-msg">{{ $data['message'] }}</div>
                @endif
                @if(($data['type'] ?? null) === 'rental_approved')
                    @if(!empty($data['pickup_otp']))
                        <div class="otp-box">Pickup OTP: {{ $data['pickup_otp'] }}</div>
                    @endif
                    @if(!empty($data['expires_at']))
                        <div class="meta" style="margin-top:4px;">Expires: {{ $data['expires_at'] }}</div>
                    @endif
                @endif
                <div class="meta" style="margin-top:8px;">{{ $n->created_at->diffForHumans() }}</div>
            </div>
            <div class="actions">
                @if(!empty($data['action_url']))
                    <a href="{{ $data['action_url'] }}" class="btn-xs btn-open">Open</a>
                @endif
                @if(!$n->read_at)
                    <form method="POST" action="{{ route('notifications.read', $n->id) }}" style="margin:0;">
                        @csrf
                        <button class="btn-xs btn-mark" type="submit">Mark read</button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="notif-row" style="justify-content:center; color:#64748b;">No notifications</div>
    @endforelse
</div>

<div class="pager">{{ $notifications->links() }}</div>
@include('partials.toast-notifications')
@endsection
