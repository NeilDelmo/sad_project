<?php
// app/Models/ForumCategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Add this for automatic eager loading
    protected $with = ['threads.user'];

    // 👇 specify 'category_id' as the foreign key to avoid 'forum_category_id' assumption
    public function threads()
    {
        return $this->hasMany(ForumThread::class, 'category_id');
    }

    // Add this method to get threads count efficiently
    public function getThreadsCountAttribute()
    {
        return $this->threads->count();
    }
}

