<?php
// app/Models/ForumThread.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ForumThread extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = ['category_id', 'user_id', 'title', 'body'];

    // Add this for automatic eager loading
    protected $with = ['user', 'replies.user'];

    protected $appends = ['thumbnail', 'image_count', 'all_images', 'body_without_marker', 'net_votes', 'user_vote'];

    // belongs to category via category_id
    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }

    // belongs to user via user_id
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // has many replies
    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'thread_id');
    }

    public function votes()
    {
        return $this->hasMany(ForumThreadVote::class, 'thread_id');
    }

    // Add this method to get replies count efficiently
    public function getRepliesCountAttribute()
    {
        return $this->replies->count();
    }

    public function getNetVotesAttribute()
    {
        $upvotes = $this->votes()->where('vote_type', 'upvote')->count();
        $downvotes = $this->votes()->where('vote_type', 'downvote')->count();
        return $upvotes - $downvotes;
    }

    public function getUpvotesAttribute()
    {
        return $this->votes()->where('vote_type', 'upvote')->count();
    }

    public function getDownvotesAttribute()
    {
        return $this->votes()->where('vote_type', 'downvote')->count();
    }

    public function getUserVoteAttribute()
    {
        if (!auth()->check()) {
            return null;
        }
        
        $vote = $this->votes()->where('user_id', auth()->id())->first();
        return $vote ? $vote->vote_type : null;
    }

    public function getThumbnailAttribute()
    {
        // Extract first image from body HTML
        preg_match('/<img[^>]+src="([^">]+)"/', $this->body, $matches);
        return $matches[1] ?? null;
    }

    public function getAllImagesAttribute()
    {
        $images = [];
        
        // Extract inline images from HTML
        preg_match_all('/<img[^>]+src="([^">]+)"/', $this->body, $matches);
        if (!empty($matches[1])) {
            $images = array_merge($images, $matches[1]);
        }
        
        // Extract attached images from marker
        preg_match('/<div class="attached-images-marker"[^>]*>(.*?)<\/div>/', $this->body, $marker);
        if (!empty($marker[1])) {
            $attachedImages = json_decode($marker[1], true);
            if (is_array($attachedImages)) {
                $images = array_merge($images, $attachedImages);
            }
        }
        
        return array_unique($images);
    }

    public function getImageCountAttribute()
    {
        return count($this->all_images);
    }

    public function getBodyWithoutMarkerAttribute()
    {
        // Remove the marker div from body for display
        return preg_replace('/<div class="attached-images-marker"[^>]*>.*?<\/div>/', '', $this->body);
    }
}

