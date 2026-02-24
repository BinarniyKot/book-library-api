<?php

namespace App\Contracts;

use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BookRepositoryInterface
{
    /**
     * Get paginated list of books with optional search.
     *
     * @param string|null $search Search term for title, author or genre
     * @param int|null $perPage Number of items per page (default from config)
     * @return LengthAwarePaginator Paginated book collection
     */
    public function getPaginated(?string $search = null, ?int $perPage = null): LengthAwarePaginator;

    /**
     * Create a new book.
     *
     * @param array<string, mixed> $data Book attributes
     * @return Book Created book model
     */
    public function create(array $data): Book;

    /**
     * Update a book.
     *
     * @param Book $book Book model to update
     * @param array<string, mixed> $data Attributes to update
     * @return bool Success status
     */
    public function update(Book $book, array $data): bool;

    /**
     * Delete a book.
     *
     * @param Book $book Book model to delete
     * @return bool Success status
     */
    public function delete(Book $book): bool;
}
