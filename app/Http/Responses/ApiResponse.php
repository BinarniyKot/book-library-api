<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    /**
     * Return success response with data.
     *
     * @param mixed $data Response payload
     * @param int $status HTTP status code
     * @return JsonResponse
     */
    public static function success(mixed $data, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['data' => $data], $status);
    }

    /**
     * Return created response (201).
     *
     * @param mixed $data Created resource
     * @return JsonResponse
     */
    public static function created(mixed $data): JsonResponse
    {
        return self::success($data, Response::HTTP_CREATED);
    }

    /**
     * Return no content response (204).
     *
     * @return JsonResponse
     */
    public static function noContent(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Return paginated response. Keeps Laravel paginator structure.
     *
     * @param LengthAwarePaginator $paginator Paginated collection
     * @return JsonResponse
     */
    public static function paginated(LengthAwarePaginator $paginator): JsonResponse
    {
        return response()->json($paginator);
    }
}
