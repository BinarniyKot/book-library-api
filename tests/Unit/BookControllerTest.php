<?php

namespace Tests\Unit;

use App\Contracts\BookRepositoryInterface;
use App\Http\Controllers\Api\BookController;
use App\Http\Requests\IndexBookRequest;
use App\Models\Book;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    private const string API_BOOKS_URI = '/api/books';

    private const string TEST_SEARCH_TERM = 'Fiction';

    private const int TEST_PER_PAGE = 10;

    private const int FIRST_PAGE = 1;

    private const int SINGLE_ITEM_TOTAL = 1;

    private const int EMPTY_COLLECTION_TOTAL = 0;

    /**
     * Clean up after each test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @return void
     */
    public function test_index_returns_paginated_books_from_repository(): void
    {
        $defaultPerPage = config('books.default_per_page');
        $singleItem = Book::factory()->make();
        $paginator = new LengthAwarePaginator(
            collect([$singleItem]),
            self::SINGLE_ITEM_TOTAL,
            $defaultPerPage,
            self::FIRST_PAGE
        );

        $repository = Mockery::mock(BookRepositoryInterface::class);
        $repository->shouldReceive('getPaginated')
            ->once()
            ->with(null, $defaultPerPage)
            ->andReturn($paginator);

        $request = Mockery::mock(IndexBookRequest::class);
        $request->shouldReceive('getValidatedSearch')->andReturn(null);
        $request->shouldReceive('getValidatedPerPage')->andReturn($defaultPerPage);

        $controller = new BookController($repository);
        $response = $controller->index($request);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function test_index_passes_search_and_per_page_to_repository(): void
    {
        $paginator = new LengthAwarePaginator(
            collect([]),
            self::EMPTY_COLLECTION_TOTAL,
            self::TEST_PER_PAGE,
            self::FIRST_PAGE
        );

        $repository = Mockery::mock(BookRepositoryInterface::class);
        $repository->shouldReceive('getPaginated')
            ->once()
            ->with(self::TEST_SEARCH_TERM, self::TEST_PER_PAGE)
            ->andReturn($paginator);

        $request = Mockery::mock(IndexBookRequest::class);
        $request->shouldReceive('getValidatedSearch')->andReturn(self::TEST_SEARCH_TERM);
        $request->shouldReceive('getValidatedPerPage')->andReturn(self::TEST_PER_PAGE);

        $controller = new BookController($repository);
        $response = $controller->index($request);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function test_store_creates_book_via_repository_and_returns_201(): void
    {
        $validData = [
            'title' => 'New Book',
            'publisher' => 'Publisher',
            'author' => 'Author',
            'genre' => 'Fiction',
            'publication_date' => '2024-01-15',
            'words_count' => 50000,
            'price_usd' => 19.99,
        ];
        $createdBook = Book::factory()->make($validData);

        $request = Mockery::mock(\App\Http\Requests\StoreBookRequest::class);
        $request->shouldReceive('validated')->andReturn($validData);

        $repository = Mockery::mock(BookRepositoryInterface::class);
        $repository->shouldReceive('create')
            ->once()
            ->with($validData)
            ->andReturn($createdBook);

        $controller = new BookController($repository);
        $response = $controller->store($request);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertSame($createdBook->title, $response->getData(true)['data']['title']);
    }

    public function test_show_returns_book(): void
    {
        $book = Book::factory()->make(['title' => 'Test Book', 'author' => 'Test Author']);

        $repository = Mockery::mock(BookRepositoryInterface::class);

        $controller = new BookController($repository);
        $response = $controller->show($book);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('Test Book', $response->getData(true)['data']['title']);
    }

    public function test_update_updates_book_via_repository_and_returns_200(): void
    {
        $updateData = ['title' => 'Updated Title'];
        $book = Mockery::mock(Book::class)->makePartial();
        $book->id = 1;
        $updatedBook = Book::factory()->make(array_merge(['id' => 1], $updateData));

        $request = Mockery::mock(\App\Http\Requests\UpdateBookRequest::class);
        $request->shouldReceive('validated')->andReturn($updateData);

        $repository = Mockery::mock(BookRepositoryInterface::class);
        $repository->shouldReceive('update')
            ->once()
            ->with($book, $updateData)
            ->andReturn(true);

        $book->shouldReceive('fresh')->andReturn($updatedBook);

        $controller = new BookController($repository);
        $response = $controller->update($request, $book);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('Updated Title', $response->getData(true)['data']['title']);
    }

    public function test_destroy_deletes_book_via_repository_and_returns_204(): void
    {
        $book = Book::factory()->make();

        $repository = Mockery::mock(BookRepositoryInterface::class);
        $repository->shouldReceive('delete')
            ->once()
            ->with($book)
            ->andReturn(true);

        $controller = new BookController($repository);
        $response = $controller->destroy($book);

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
