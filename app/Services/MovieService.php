<?php

namespace App\Services;

use App\Http\Resources\MovieResource;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Builder;

class MovieService
{

    /**
     * @param string|null $filterActor
     * @param string|null $filterGenre
     * @param string|null $orderByName
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function get(
        string $filterActor = null,
        string $filterGenre = null,
        string $orderByName = null
    )
    {
        $movies = Movie::query();

        if ($filterActor) {
            $movies->whereHas('actors', function (Builder $query) use ($filterActor) {
                $query->where('name', 'like', "%{$filterActor}%");
            });
        }

        if ($filterGenre) {
            $movies->whereHas('genre', function (Builder $query) use ($filterGenre) {
                $query->where('name', 'like', "%{$filterGenre}%");
            });
        }

        if ($orderByName) {
            $movies->orderBy('name', $orderByName);
        }

        return MovieResource::collection($movies->get());
    }
}
