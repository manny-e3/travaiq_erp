<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KbArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category',
        'tags',
        'is_published',
        'views',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'tags' => 'array',
        'views' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = static::generateUniqueSlug($article->title);
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title') && empty($article->slug)) {
                $article->slug = static::generateUniqueSlug($article->title);
            }
        });
    }

    /**
     * Generate unique slug for article.
     */
    protected static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        
        $similarSlugs = static::where('slug', 'like', $slug . '%')
            ->pluck('slug')
            ->toArray();

        if (!in_array($slug, $similarSlugs)) {
            return $slug;
        }

        $count = 1;
        while (in_array("{$slug}-{$count}", $similarSlugs)) {
            $count++;
        }

        return "{$slug}-{$count}";
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('content', 'like', "%{$term}%")
              ->orWhere('category', 'like', "%{$term}%")
              ->orWhere('tags', 'like', "%{$term}%");
        });
    }
}
