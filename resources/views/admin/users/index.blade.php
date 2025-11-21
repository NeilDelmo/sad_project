<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #F3F4F6;
            min-height: 100vh;
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        }
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
        }
        .btn-suspend {
            background: white;
            color: #1F2937;
            border: 1px solid #D1D5DB;
        }
        .btn-suspend:hover {
            background: #F9FAFB;
            border-color: #9CA3AF;
        }
        .btn-ban {
            background: white;
            color: #1F2937;
            border: 1px solid #D1D5DB;
        }
        .btn-ban:hover {
            background: #F9FAFB;
            border-color: #9CA3AF;
        }
        .btn-reactivate {
            background: #1B5E88;
            color: white;
            border: 1px solid #1B5E88;
        }
        .btn-reactivate:hover {
            background: #0075B5;
            border-color: #0075B5;
        }
        .alert {
            border-radius: 8px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }
        .table-row {
            transition: background 0.15s ease;
        }
        .table-row:hover {
            background: #F9FAFB;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>
    @include('admin.partials.nav')

    <div class="container mx-auto px-4 py-8">
        <div class="card p-6 md:p-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        <i class="fa-solid fa-users text-blue-600"></i> User Management
                    </h1>
                    <p class="text-gray-600 text-sm mt-1">Manage user accounts and permissions</p>
                </div>
                <div class="text-sm">
                    <span class="px-3 py-2 bg-blue-50 text-blue-700 rounded-lg font-semibold border border-blue-200">
                        <i class="fa-solid fa-user-group"></i> {{ $users->total() }} Total Users
                    </span>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="alert bg-blue-50 border border-blue-200 text-blue-800 mb-4">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert bg-gray-100 border border-gray-300 text-gray-800 mb-4">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Username
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Registered
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="table-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                #{{ $user->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                                        {{ strtoupper(substr($user->username, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $user->username }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="badge
                                    @if($user->user_type === 'fisherman') bg-blue-50 text-blue-700 border border-blue-200
                                    @elseif($user->user_type === 'vendor') bg-gray-100 text-gray-700 border border-gray-300
                                    @elseif($user->user_type === 'admin' || $user->user_type === 'regulator') bg-gray-800 text-white border border-gray-800
                                    @else bg-gray-100 text-gray-700 border border-gray-300
                                    @endif">
                                    @if($user->user_type === 'fisherman')
                                        <i class="fa-solid fa-fish"></i>
                                    @elseif($user->user_type === 'vendor')
                                        <i class="fa-solid fa-store"></i>
                                    @elseif($user->user_type === 'admin' || $user->user_type === 'regulator')
                                        <i class="fa-solid fa-user-shield"></i>
                                    @endif
                                    {{ ucfirst($user->user_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="badge
                                    @if($user->account_status === 'active') bg-blue-50 text-blue-700 border border-blue-200
                                    @elseif($user->account_status === 'suspended') bg-gray-100 text-gray-700 border border-gray-300
                                    @elseif($user->account_status === 'banned') bg-gray-800 text-white border border-gray-800
                                    @endif">
                                    @if($user->account_status === 'active')
                                        <i class="fa-solid fa-circle-check"></i>
                                    @elseif($user->account_status === 'suspended')
                                        <i class="fa-solid fa-pause-circle"></i>
                                    @elseif($user->account_status === 'banned')
                                        <i class="fa-solid fa-ban"></i>
                                    @endif
                                    {{ ucfirst($user->account_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($user->user_type !== 'admin' && $user->user_type !== 'regulator' && $user->id !== auth()->id())
                                    @if($user->account_status === 'active')
                                        <div class="flex gap-2">
                                            <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-suspend" onclick="return confirm('Suspend {{ $user->username }}? They will be logged out and cannot access their account.')">
                                                    <i class="fa-solid fa-pause"></i> Suspend
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.ban', $user->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-ban" onclick="return confirm('Ban {{ $user->username }}? This is permanent and more severe than suspension.')">
                                                    <i class="fa-solid fa-ban"></i> Ban
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.reactivate', $user->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="btn btn-reactivate" onclick="return confirm('Reactivate {{ $user->username }}? They will regain full access to their account.')">
                                                <i class="fa-solid fa-circle-check"></i> Reactivate
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-xs">
                                        <i class="fa-solid fa-lock"></i> Protected
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</body>
</html>
