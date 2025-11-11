<?php
// app/Http/Controllers/ForumController.php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumReply;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::withCount('threads')->get();
        return view('forums.index', compact('categories'));
    }

    public function showCategory($id)
    {
        $category = ForumCategory::with('threads.user')->findOrFail($id);
        return view('forums.category', compact('category'));
    }

    public function showThread($id)
    {
        $thread = ForumThread::with(['user', 'replies.user'])->findOrFail($id);

        return view('forums.thread', [
            'thread' => $thread,
            'category_id' => $thread->category_id,
        ]);
    }

        public function storeThread(Request $request, $category_id)
        {
            // Validate the request
            $validator = validator($request->all(), [
                'title' => 'required|string|max:255',
                'body'  => 'required|string',
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            try {
                $thread = ForumThread::create([
                    'category_id' => $category_id,
                    'user_id' => auth()->id(),
                    'title' => $request->title,
                    'body' => $request->body,
                ]);

                // Always return JSON for AJAX requests
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Thread posted successfully!',
                        'thread_id' => $thread->id,
                        'category_id' => $category_id
                    ]);
                }

                return redirect()->route('forums.category', $category_id)
                    ->with('success', 'Thread posted successfully!');

            } catch (\Exception $e) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to create thread: ' . $e->getMessage()
                    ], 500);
                }
                
                return back()->with('error', 'Failed to create thread.');
            }
        }

        public function storeReply(Request $request, $thread_id)
        {
            $validator = validator($request->all(), [
                'body' => 'required|string'
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            try {
                ForumReply::create([
                    'thread_id' => $thread_id,
                    'user_id' => auth()->id(),
                    'body' => $request->body,
                ]);

                // Always return JSON for AJAX requests
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Reply posted successfully!',
                        'thread_id' => $thread_id
                    ]);
                }

                return back()->with('success', 'Reply posted successfully!');

            } catch (\Exception $e) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to post reply: ' . $e->getMessage()
                    ], 500);
                }
                
                return back()->with('error', 'Failed to post reply.');
            }
        }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $path = $request->file('file')->store('public/forum_images');
        $url = asset(str_replace('public', 'storage', $path));

        return response()->json(['location' => $url]);
    }
}