<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    private const string API_BOOKS_URI = '/api/books';

    private const int LIST_BOOKS_COUNT = 3;

    private const int NON_EXISTENT_BOOK_ID = 99999;

    private const float PRICE_ASSERT_DELTA = 0.01;

    private const int CREATE_BOOK_WORDS_COUNT = 50000;

    private const float CREATE_BOOK_PRICE_USD = 19.99;

    private const float UPDATE_BOOK_INITIAL_PRICE = 10.00;

    private const float UPDATE_BOOK_NEW_PRICE = 25.50;

    /**
     * Test GET /api/books - list all books
     *
     * @return void
     */
    public function test_can_list_books(): void
    {
        Book::factory()->count(self::LIST_BOOKS_COUNT)->create();

        $response = $this->getJson(self::API_BOOKS_URI);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'publisher',
                        'author',
                        'genre',
                        'publication_date',
                        'words_count',
                        'price_usd',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
        $this->assertCount(self::LIST_BOOKS_COUNT, $response->json('data'));
    }

    /**
     * Test GET /api/books/{id} - show single book
     *
     * @return void
     */
    public function test_can_show_single_book(): void
    {
        $book = Book::factory()->create([
            'title' => 'Test Book Title',
            'author' => 'Test Author',
        ]);

        $response = $this->getJson(self::API_BOOKS_URI . "/{$book->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => [
                    'id' => $book->id,
                    'title' => 'Test Book Title',
                    'author' => 'Test Author',
                ],
            ]);
    }

    /**
     * Test POST /api/books - create new book
     *
     * @return void
     */
    public function test_can_create_book(): void
    {
        $bookData = [
            'title' => 'New Book',
            'publisher' => 'Test Publisher',
            'author' => 'John Doe',
            'genre' => 'Fiction',
            'publication_date' => '2024-01-15',
            'words_count' => self::CREATE_BOOK_WORDS_COUNT,
            'price_usd' => self::CREATE_BOOK_PRICE_USD,
        ];

        $response = $this->postJson(self::API_BOOKS_URI, $bookData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('data.title', $bookData['title'])
            ->assertJsonPath('data.author', $bookData['author'])
            ->assertJsonPath('data.genre', $bookData['genre'])
            ->assertJsonPath('data.words_count', $bookData['words_count']);

        $this->assertDatabaseHas('books', [
            'title' => $bookData['title'],
            'author' => $bookData['author'],
            'words_count' => $bookData['words_count'],
        ]);
    }

    /**
     * Test GET /api/books - validation fails when per_page exceeds max
     *
     * @return void
     */
    public function test_list_books_validates_per_page_max(): void
    {
        $response = $this->getJson(self::API_BOOKS_URI . '?per_page=1000');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['per_page']);
    }

    /**
     * Test POST /api/books - validation fails with invalid data
     *
     * @return void
     */
    public function test_create_book_validates_required_fields(): void
    {
        $response = $this->postJson(self::API_BOOKS_URI, []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['title', 'publisher', 'author', 'genre', 'publication_date', 'words_count', 'price_usd']);
    }

    /**
     * Test PATCH /api/books/{id} - update book
     *
     * @return void
     */
    public function test_can_update_book(): void
    {
        $book = Book::factory()->create([
            'title' => 'Original Title',
            'price_usd' => self::UPDATE_BOOK_INITIAL_PRICE,
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'price_usd' => self::UPDATE_BOOK_NEW_PRICE,
        ];

        $response = $this->patchJson(self::API_BOOKS_URI . "/{$book->id}", $updateData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.title', $updateData['title']);
        $this->assertEqualsWithDelta(self::UPDATE_BOOK_NEW_PRICE, (float)$response->json('data.price_usd'), self::PRICE_ASSERT_DELTA);

        $book->refresh();
        $this->assertEquals('Updated Title', $book->title);
        $this->assertEquals(self::UPDATE_BOOK_NEW_PRICE, (float)$book->price_usd);
    }

    /**
     * Test DELETE /api/books/{id} - delete book
     *
     * @return void
     */
    public function test_can_delete_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson(self::API_BOOKS_URI . "/{$book->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    /**
     * Test GET /api/books/{id} - returns 404 for non-existent book
     *
     * @return void
     */
    public function test_show_returns_404_for_non_existent_book(): void
    {
        $response = $this->getJson(self::API_BOOKS_URI . '/' . self::NON_EXISTENT_BOOK_ID);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
