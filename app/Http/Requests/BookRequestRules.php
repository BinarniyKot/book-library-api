<?php

namespace App\Http\Requests;

class BookRequestRules
{
    private const int MIN_NUMERIC = 0;

    /**
     * Base validation rules for book attributes (without required/sometimes).
     *
     * @return array<string, array<int, string>>
     */
    private static function attributeRules(): array
    {
        $maxLength = config('books.max_string_length');

        return [
            'title' => ['string', 'max:' . $maxLength],
            'publisher' => ['string', 'max:' . $maxLength],
            'author' => ['string', 'max:' . $maxLength],
            'genre' => ['string', 'max:' . $maxLength],
            'publication_date' => ['date'],
            'words_count' => ['integer', 'min:' . self::MIN_NUMERIC],
            'price_usd' => ['numeric', 'min:' . self::MIN_NUMERIC],
        ];
    }

    /**
     * Validation rules for creating a book (all fields required).
     *
     * @return array<string, array<int, string>>
     */
    public static function forStore(): array
    {
        return collect(self::attributeRules())
            ->map(fn(array $rules) => array_merge(['required'], $rules))
            ->all();
    }

    /**
     * Validation rules for updating a book (all fields optional).
     *
     * @return array<string, array<int, string>>
     */
    public static function forUpdate(): array
    {
        return collect(self::attributeRules())
            ->map(fn(array $rules) => array_merge(['sometimes'], $rules))
            ->all();
    }
}
