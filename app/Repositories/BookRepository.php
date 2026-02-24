<?php

namespace App\Repositories;

use App\Contracts\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookRepository implements BookRepositoryInterface
{
    /**
     * Get paginated list of books with optional search.
     *
     * @param string|null $search Search term for title, author or genre
     * @param int|null $perPage Number of items per page
     * @return LengthAwarePaginator Paginated book collection
     */
    public function getPaginated(?string $search = null, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= config('books.default_per_page');

        return Book::query()
            ->search($search)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create a new book.
     *
     * @param array<string, mixed> $data Book attributes
     * @return Book Created book model
     */
    public function create(array $data): Book
    {
        return Book::create($data);
    }

    /**
     * Update a book.
     *
     * @param Book $book Book model to update
     * @param array<string, mixed> $data Attributes to update
     * @return bool Success status
     */
    public function update(Book $book, array $data): bool
    {
        return $book->update($data);
    }

    /**
     * Delete a book.
     *
     * @param Book $book Book model to delete
     * @return bool Success status
     */
    public function delete(Book $book): bool
    {
        return $book->delete();
    }
}
