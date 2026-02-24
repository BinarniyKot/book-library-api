<?php

namespace App\Http\Controllers\Api;

use App\Contracts\BookRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexBookRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Book;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    /**
     * @param BookRepositoryInterface $bookRepository Book data repository
     */
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository
    )
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexBookRequest $request Validated request with optional search and per_page
     * @return JsonResponse Paginated list of books
     */
    public function index(IndexBookRequest $request): JsonResponse
    {
        $books = $this->bookRepository->getPaginated(
            $request->getValidatedSearch(),
            $request->getValidatedPerPage()
        );

        return ApiResponse::paginated($books);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBookRequest $request Validated book creation data
     * @return JsonResponse Created book with 201 status
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = $this->bookRepository->create($request->validated());

        return ApiResponse::created($book);
    }

    /**
     * Display the specified resource.
     *
     * @param Book $book Book model instance
     * @return JsonResponse Single book data
     */
    public function show(Book $book): JsonResponse
    {
        return ApiResponse::success($book);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBookRequest $request Validated book update data
     * @param Book $book Book model instance
     * @return JsonResponse Updated book data
     */
    public function update(UpdateBookRequest $request, Book $book): JsonResponse
    {
        $this->bookRepository->update($book, $request->validated());

        return ApiResponse::success($book->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Book $book Book model instance
     * @return JsonResponse Empty response with 204 status
     */
    public function destroy(Book $book): JsonResponse
    {
        $this->bookRepository->delete($book);

        return ApiResponse::noContent();
    }
}
