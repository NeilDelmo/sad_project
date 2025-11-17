<?php
// app/Models/ForumCategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ForumCategory extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = ['name', 'description'];

    // Remove automatic eager loading for better performance with pagination
    // protected $with = ['threads.user'];

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
