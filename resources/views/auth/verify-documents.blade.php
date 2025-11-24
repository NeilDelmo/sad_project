<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Verification - FishPort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .loader {
            border: 4px solid #f3f3f3;
            border-radius: 50%;
            border-top: 4px solid #3498db;
            width: 40px;
            height: 40px;
            -webkit-animation: spin 2s linear infinite; /* Safari */
            animation: spin 2s linear infinite;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .pulse-ring {
            content: '';
            width: 100px;
            height: 100px;
            border: 5px solid #3b82f6;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: pulse 2s infinite;
            opacity: 0;
        }
        
        @keyframes pulse {
            0% {
                width: 60px;
                height: 60px;
                opacity: 0.8;
            }
            100% {
                width: 140px;
                height: 140px;
                opacity: 0;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-blue-600 p-6 text-center">
            <h2 class="text-2xl font-bold text-white">Account Verification</h2>
            <p class="text-blue-100 mt-2">Complete your profile to start trading</p>
        </div>

        <div class="p-8">
            @if($user->verification_status === 'pending')
                <div class="text-center py-8">
                    <div class="relative h-32 w-32 mx-auto mb-6">
                        <div class="pulse-ring"></div>
                        <div class="absolute inset-0 flex items-center justify-center bg-blue-50 rounded-full border-4 border-blue-100 z-10">
                            <i class="fa-solid fa-hourglass-half text-4xl text-blue-500"></i>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Verification in Progress</h3>
                    <p class="text-gray-600 mb-6">
                        We are currently reviewing your documents. This usually takes 24-48 hours. 
                        You will be notified once your account is approved.
                    </p>
                    
                    <div class="flex justify-center gap-2">
                        <div class="h-2 w-2 bg-blue-600 rounded-full animate-bounce"></div>
                        <div class="h-2 w-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="h-2 w-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline">
                                Log out and check back later
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="mb-6">
                    @if($user->verification_status === 'rejected')
                        <div class="text-center mb-6">
                            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                                <i class="fa-solid fa-xmark text-2xl text-red-600"></i>
                            </div>
                            <h3 class="text-lg font-medium text-red-800">Verification Rejected</h3>
                            <p class="text-sm text-red-600 mt-1">Please upload a valid document.</p>
                        </div>
                    @else
                        <div class="flex items-start gap-4 mb-6">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Identity Verification</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    As a {{ ucfirst($user->user_type) }}, we need to verify your identity before you can start.
                                </p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('verification.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Upload ID or Business License
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors cursor-pointer bg-gray-50" onclick="document.getElementById('document').click()">
                                <div class="space-y-1 text-center">
                                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <span class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a file</span>
                                            <input id="document" name="document" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" onchange="showFileName(this)">
                                        </span>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, PDF up to 5MB
                                    </p>
                                    <p id="file-name" class="text-sm text-blue-600 font-medium mt-2 hidden"></p>
                                </div>
                            </div>
                            @error('document')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Submit for Review
                        </button>
                    </form>
                </div>
                
                <div class="text-center mt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline">
                            Log out
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <script>
        function showFileName(input) {
            const fileName = input.files[0]?.name;
            const nameElement = document.getElementById('file-name');
            if (fileName) {
                nameElement.textContent = fileName;
                nameElement.classList.remove('hidden');
            }
        }
    </script>
    <script data-collect-dnt="true" async src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
    <!-- 100% privacy-first analytics -->
    <script data-collect-dnt="true" async src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
</body>
</html>
