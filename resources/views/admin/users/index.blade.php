<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin</title>
    @php
        $adminFavicon = asset('images/logo.png').'?v=admin-users';
    @endphp
    <link rel="icon" type="image/png" href="{{ $adminFavicon }}">
    <link rel="shortcut icon" type="image/png" href="{{ $adminFavicon }}">
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
                <div class="flex gap-2 text-sm">
                    <span class="px-3 py-2 bg-blue-50 text-blue-700 rounded-lg font-semibold border border-blue-200">
                        <i class="fa-solid fa-user-group"></i> {{ $totalUsers }} Total
                    </span>
                    <span class="px-3 py-2 bg-green-50 text-green-700 rounded-lg font-semibold border border-green-200">
                        <i class="fa-solid fa-circle-check"></i> {{ $activeUsers }} Active
                    </span>
                    <span class="px-3 py-2 bg-yellow-50 text-yellow-700 rounded-lg font-semibold border border-yellow-200">
                        <i class="fa-solid fa-pause-circle"></i> {{ $suspendedUsers }} Suspended
                    </span>
                    <span class="px-3 py-2 bg-red-50 text-red-700 rounded-lg font-semibold border border-red-200">
                        <i class="fa-solid fa-ban"></i> {{ $bannedUsers }} Banned
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

            <!-- Filters -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1 w-full">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 border" 
                                placeholder="Username or Email">
                        </div>
                    </div>
                    
                    <div class="w-full md:w-48">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" id="role" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 border px-3">
                            <option value="">All Roles</option>
                            <option value="fisherman" {{ request('role') == 'fisherman' ? 'selected' : '' }}>Fisherman</option>
                            <option value="vendor" {{ request('role') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="regulator" {{ request('role') == 'regulator' ? 'selected' : '' }}>Regulator</option>
                        </select>
                    </div>

                    <div class="w-full md:w-48">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2 border px-3">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-sm font-medium">
                            <i class="fa-solid fa-filter mr-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'role', 'status']))
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-sm font-medium">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

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
                                Verification
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->user_type === 'buyer' || $user->user_type === 'admin' || $user->user_type === 'regulator')
                                    <span class="text-gray-400 text-xs">N/A</span>
                                @else
                                    <span class="badge
                                        @if($user->verification_status === 'approved') bg-green-50 text-green-700 border border-green-200
                                        @elseif($user->verification_status === 'pending') bg-yellow-50 text-yellow-700 border border-yellow-200
                                        @elseif($user->verification_status === 'rejected') bg-red-50 text-red-700 border border-red-200
                                        @else bg-gray-100 text-gray-600 border border-gray-200
                                        @endif">
                                        {{ ucfirst($user->verification_status) }}
                                    </span>
                                    @if($user->verification_document)
                                        <a href="{{ Storage::url($user->verification_document) }}" target="_blank" class="text-blue-600 hover:text-blue-800 ml-2" title="View Document">
                                            <i class="fa-solid fa-file-lines"></i>
                                        </a>
                                    @endif
                                @endif
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
                                            <button type="button" class="btn btn-ban" style="border-color: #f59e0b; color: #b45309;" onclick="openPenaltyModal('{{ $user->id }}', '{{ $user->username }}')">
                                                <i class="fa-solid fa-gavel"></i> Penalty
                                            </button>
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.reactivate', $user->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="btn btn-reactivate" onclick="return confirm('Reactivate {{ $user->username }}? They will regain full access to their account.')">
                                                <i class="fa-solid fa-circle-check"></i> Reactivate
                                            </button>
                                        </form>
                                    @endif

                                    @if($user->verification_status === 'pending')
                                        <div class="mt-2 flex gap-2">
                                            <form method="POST" action="{{ route('admin.users.approve-verification', $user->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold hover:bg-green-200" onclick="return confirm('Approve verification for {{ $user->username }}?')">
                                                    <i class="fa-solid fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.reject-verification', $user->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold hover:bg-red-200" onclick="return confirm('Reject verification for {{ $user->username }}?')">
                                                    <i class="fa-solid fa-xmark"></i> Reject
                                                </button>
                                            </form>
                                        </div>
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

    <!-- Penalty Modal -->
    <div id="penaltyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" style="z-index: 50;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <i class="fa-solid fa-gavel text-yellow-600 text-xl"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2" id="modalTitle">Apply Penalty</h3>
                <div class="mt-2 px-7 py-3">
                    <form id="penaltyForm" method="POST" action="">
                        @csrf
                        <div class="mb-4 text-left">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="reason">
                                Reason
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="reason" name="reason" type="text" placeholder="e.g. Rude behavior" required>
                        </div>
                        <div class="mb-4 text-left">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="severity">
                                Severity
                            </label>
                            <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="severity" name="severity">
                                <option value="low">Low (-5 pts)</option>
                                <option value="medium" selected>Medium (-15 pts)</option>
                                <option value="high">High (-30 pts)</option>
                                <option value="critical">Critical (-50 pts)</option>
                            </select>
                        </div>
                        <div class="mb-4 text-left">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="details">
                                Details (Optional)
                            </label>
                            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="details" name="details" rows="3"></textarea>
                        </div>
                        <div class="items-center px-4 py-3">
                            <button id="ok-btn" type="submit" class="px-4 py-2 bg-yellow-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                                Apply Penalty
                            </button>
                        </div>
                    </form>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closePenaltyModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPenaltyModal(userId, username) {
            const modal = document.getElementById('penaltyModal');
            const form = document.getElementById('penaltyForm');
            const title = document.getElementById('modalTitle');
            
            // Set form action
            form.action = `/admin/users/${userId}/penalty`;
            
            // Set title
            title.textContent = `Apply Penalty to ${username}`;
            
            // Show modal
            modal.classList.remove('hidden');
        }

        function closePenaltyModal() {
            const modal = document.getElementById('penaltyModal');
            modal.classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('penaltyModal');
            if (event.target == modal) {
                closePenaltyModal();
            }
        }
    </script>
</body>
</html>
