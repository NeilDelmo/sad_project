<?php
//  app/Models/ForumReply.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ForumReply extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = ['thread_id', 'user_id', 'body', 'upvotes', 'downvotes'];

    protected $appends = ['all_images', 'body_without_marker', 'net_votes', 'user_vote'];

    // belongs to thread via thread_id
    public function thread()
    {
        return $this->belongsTo(ForumThread::class, 'thread_id');
    }

    // belongs to user via user_id
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function votes()
    {
        return $this->hasMany(ForumReplyVote::class, 'reply_id');
    }

    public function getNetVotesAttribute()
    {
        return $this->upvotes - $this->downvotes;
    }

    public function getUserVoteAttribute()
    {
        if (!auth()->check()) {
            return null;
        }
        
        $vote = $this->votes()->where('user_id', auth()->id())->first();
        return $vote ? $vote->vote_type : null;
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

    public function getBodyWithoutMarkerAttribute()
    {
        // Remove the marker div from body for display
        return preg_replace('/<div class="attached-images-marker"[^>]*>.*?<\/div>/', '', $this->body);
    }
}

