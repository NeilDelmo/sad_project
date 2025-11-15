<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Inbox</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <style>
        body { background: #f5f7fa; }
        .navbar { background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%); padding: 16px 0; }
        .nav-brand { color: #fff; font-weight: 700; font-size: 24px; text-decoration: none; }
        .nav-link { color: rgba(255,255,255,.9); margin-left: 12px; }
        .nav-link.active { color: #fff; font-weight: 600; }
        .card { border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .conv-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; border-bottom: 1px solid #eee; text-decoration: none; color: inherit; }
        .conv-item:hover { background: #f8fafc; }
        .badge-unread { background: #dc3545; }
        .empty { text-align: center; padding: 80px 20px; color: #6c757d; }
    </style>
</head>
<body>
@include('vendor.partials.nav')

<div class="container my-4">
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fa-solid fa-inbox"></i> Inbox</h5>
        </div>
        <div class="list-group list-group-flush">
            @forelse($conversations as $c)
                <a class="list-group-item conv-item" href="{{ route('marketplace.message', $c->id) }}">
                    <div>
                        <div class="fw-bold">{{ $c->product->name ?? 'Conversation' }}</div>
                        <div class="text-muted small">with {{ $c->buyer->username ?? ('User #'.$c->buyer_id) }} • {{ $c->latestMessage?->created_at?->diffForHumans() }}</div>
                        @if($c->latestMessage)
                            <div class="text-truncate" style="max-width: 520px;">{{ $c->latestMessage->message }}</div>
                        @endif
                    </div>
                    <div class="text-end">
                        @if(($c->unread_count ?? 0) > 0)
                            <span class="badge rounded-pill badge-unread">{{ $c->unread_count }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="empty">No conversations yet.</div>
            @endforelse
        </div>
    </div>
</div>
</body>
</html>
