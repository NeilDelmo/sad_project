<?php
// app/Http/Controllers/ForumController.php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\ForumThreadVote;
use App\Models\ForumReplyVote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $categories = ForumCategory::withCount('threads')->get();
        
        // Get latest 10 threads across all categories
        $latestThreads = ForumThread::with(['user', 'category'])
            ->withCount('replies')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Check if a specific category is requested
        $categoryId = $request->get('category');
        
        if ($categoryId) {
            $sort = $request->get('sort', 'newest');
            $search = $request->get('search', '');
            
            $category = ForumCategory::findOrFail($categoryId);
            
            $threadsQuery = ForumThread::where('category_id', $categoryId)
                ->with(['user', 'replies']);
            
            if (!empty($search)) {
                $threadsQuery->where(function($query) use ($search) {
                    $query->where('title', 'LIKE', "%{$search}%")
                          ->orWhere('body', 'LIKE', "%{$search}%");
                });
            }
            
            if ($sort === 'oldest') {
                $threadsQuery->orderBy('created_at', 'asc');
            } elseif ($sort === 'best') {
                $threadsQuery->withCount([
                    'votes as upvotes_count' => function ($query) {
                        $query->where('vote_type', 'upvote');
                    },
                    'votes as downvotes_count' => function ($query) {
                        $query->where('vote_type', 'downvote');
                    }
                ])->orderByRaw('(upvotes_count - downvotes_count) DESC');
            } else {
                $threadsQuery->orderBy('created_at', 'desc');
            }
            
            $threads = $threadsQuery->paginate(15)->appends(['category' => $categoryId, 'sort' => $sort, 'search' => $search]);
            
            return view('forums.index', [
                'categories' => $categories,
                'latestThreads' => $latestThreads,
                'showCategory' => true,
                'category' => $category,
                'threads' => $threads,
                'sort' => $sort,
                'search' => $search
            ]);
        }
        
        return view('forums.index', compact('categories', 'latestThreads'));
    }

    public function showCategory(Request $request, $id)
    {
        $sort = $request->get('sort', 'newest');
        $search = $request->get('search', '');
        
        $category = ForumCategory::findOrFail($id);
        
        $threadsQuery = ForumThread::where('category_id', $id)
            ->with(['user', 'replies']);
        
        // Apply search filter
        if (!empty($search)) {
            $threadsQuery->where(function($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('body', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply sorting
        if ($sort === 'oldest') {
            $threadsQuery->orderBy('created_at', 'asc');
        } elseif ($sort === 'best') {
            $threadsQuery->withCount([
                'votes as upvotes_count' => function ($query) {
                    $query->where('vote_type', 'upvote');
                },
                'votes as downvotes_count' => function ($query) {
                    $query->where('vote_type', 'downvote');
                }
            ])->orderByRaw('(upvotes_count - downvotes_count) DESC');
        } else {
            $threadsQuery->orderBy('created_at', 'desc');
        }
        
        $threads = $threadsQuery->paginate(15)->appends(['sort' => $sort, 'search' => $search]);
        
        return view('forums.category', compact('category', 'threads', 'sort', 'search'));
    }

    public function showThread($id)
    {
        $thread = ForumThread::with(['user','replies.user'])->findOrFail($id);
        return view('forums.thread', [
            'thread' => $thread,
            'category_id' => $thread->category_id,
        ]);
    }

    public function storeThread(Request $request, $category_id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image_urls' => 'nullable|string',
        ]);

        $cleanBody = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->body);
        
        $additionalImages = [];
        if ($request->image_urls) {
            $additionalImages = json_decode($request->image_urls, true) ?? [];
        }

        if (!empty($additionalImages)) {
            $cleanBody .= '<div class="attached-images-marker" style="display:none;">' . json_encode($additionalImages) . '</div>';
        }

        $thread = ForumThread::create([
            'category_id' => $category_id,
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'body' => $cleanBody,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thread posted successfully!',
            'category_id' => $category_id,
            'thread_id' => $thread->id,
        ]);
    }

    public function storeReply(Request $request, $thread_id)
    {
        $validated = $request->validate([
            'body' => 'required|string',
            'image_urls' => 'nullable|string',
        ]);

        $cleanBody = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->body);
        
        $additionalImages = [];
        if ($request->image_urls) {
            $additionalImages = json_decode($request->image_urls, true) ?? [];
        }

        if (!empty($additionalImages)) {
            $cleanBody .= '<div class="attached-images-marker" style="display:none;">' . json_encode($additionalImages) . '</div>';
        }

        $reply = ForumReply::create([
            'thread_id' => $thread_id,
            'user_id' => auth()->id(),
            'body' => $cleanBody,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply posted successfully!',
            'thread_id' => $thread_id,
        ]);
    }

    public function voteThread(Request $request, $thread_id)
    {
        $request->validate([
            'vote_type' => 'required|in:upvote,downvote',
        ]);

        $thread = ForumThread::findOrFail($thread_id);
        $userId = auth()->id();
        $voteType = $request->vote_type;

        DB::transaction(function () use ($thread, $userId, $voteType) {
            $existingVote = ForumThreadVote::where('user_id', $userId)
                ->where('thread_id', $thread->id)
                ->first();

            if ($existingVote) {
                if ($existingVote->vote_type === $voteType) {
                    // Remove vote
                    $existingVote->delete();
                } else {
                    // Change vote
                    $existingVote->update(['vote_type' => $voteType]);
                }
            } else {
                // New vote
                ForumThreadVote::create([
                    'user_id' => $userId,
                    'thread_id' => $thread->id,
                    'vote_type' => $voteType,
                ]);
            }
        });

        $thread->refresh();

        return response()->json([
            'success' => true,
            'upvotes' => $thread->upvotes,
            'downvotes' => $thread->downvotes,
            'net_votes' => $thread->net_votes,
            'user_vote' => $thread->user_vote,
        ]);
    }

    public function voteReply(Request $request, $reply_id)
    {
        $request->validate([
            'vote_type' => 'required|in:upvote,downvote',
        ]);

        $reply = ForumReply::findOrFail($reply_id);
        $userId = auth()->id();
        $voteType = $request->vote_type;

        DB::transaction(function () use ($reply, $userId, $voteType) {
            $existingVote = ForumReplyVote::where('user_id', $userId)
                ->where('reply_id', $reply->id)
                ->first();

            if ($existingVote) {
                if ($existingVote->vote_type === $voteType) {
                    // Remove vote
                    $existingVote->delete();
                } else {
                    // Change vote
                    $existingVote->update(['vote_type' => $voteType]);
                }
            } else {
                // New vote
                ForumReplyVote::create([
                    'user_id' => $userId,
                    'reply_id' => $reply->id,
                    'vote_type' => $voteType,
                ]);
            }
        });

        $reply->refresh();

        return response()->json([
            'success' => true,
            'upvotes' => $reply->upvotes,
            'downvotes' => $reply->downvotes,
            'net_votes' => $reply->net_votes,
            'user_vote' => $reply->user_vote,
        ]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('forum-images', $filename, 'public');

        return response()->json([
            'location' => Storage::url($path),
        ]);
    }
}