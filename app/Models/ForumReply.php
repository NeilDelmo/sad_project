<?php
//  app/Models/ForumReply.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    use HasFactory;

    protected $fillable = ['thread_id', 'user_id', 'body', 'upvotes', 'downvotes'];

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
}


