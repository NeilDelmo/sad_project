<?php
// app/Models/ForumThread.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumThread extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'user_id', 'title', 'body', 'upvotes', 'downvotes'];

    // Add this for automatic eager loading
    protected $with = ['user', 'replies.user'];

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

    // Add this method to get replies count efficiently
    public function getRepliesCountAttribute()
    {
        return $this->replies->count();
    }
}


