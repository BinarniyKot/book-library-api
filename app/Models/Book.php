<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Builder<Book>|Book query()
 * @method static Builder<Book>|Book search(?string $search)
 */
class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'publisher',
        'author',
        'genre',
        'publication_date',
        'words_count',
        'price_usd',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'words_count' => 'integer',
            'price_usd' => 'decimal:' . config('books.price_decimal_precision'),
        ];
    }

    /**
     * Scope a query to search by title, author or genre.
     *
     * @param Builder $query Eloquent query builder
     * @param string|null $search Search term
     * @return Builder Query builder instance
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('author', 'like', "%{$search}%")
                ->orWhere('genre', 'like', "%{$search}%");
        });
    }
}
