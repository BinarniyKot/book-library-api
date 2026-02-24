<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    private const int TITLE_WORDS_COUNT = 3;

    private const int WORDS_COUNT_MIN = 10000;

    private const int WORDS_COUNT_MAX = 200000;

    private const float PRICE_MIN = 5.0;

    private const float PRICE_MAX = 50.0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(self::TITLE_WORDS_COUNT),
            'publisher' => fake()->company(),
            'author' => fake()->name(),
            'genre' => fake()->randomElement(['Fiction', 'Non-Fiction', 'Science', 'History', 'Biography', 'Romance']),
            'publication_date' => fake()->date(),
            'words_count' => fake()->numberBetween(self::WORDS_COUNT_MIN, self::WORDS_COUNT_MAX),
            'price_usd' => fake()->randomFloat(config('books.price_decimal_precision'), self::PRICE_MIN, self::PRICE_MAX),
        ];
    }
}
