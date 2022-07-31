<?php

namespace App\Services;

use App\Models\Actor;
use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Builder;

class MovieService
{

    /**
     * @param string|null $filterActor
     * @param string|null $filterGenre
     * @param string|null $orderByName
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function index(
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

        return $movies->get();
    }

    public static function store($name, $actorNames, $genreName)
    {
        $movie = new Movie();

        $movie->name = $name;

        $genre = Genre::firstOrCreate(['name' => $genreName]);
        $movie->genre()->associate($genre);
        $movie->save();

        $actorIds = [];
        foreach ($actorNames as $actorName) {
            $actor = Actor::firstOrCreate(['name' => $actorName]);
            $actorIds[] = $actor->id;
        }
        $movie->actors()->attach($actorIds);

        return $movie;
    }

    public static function update($movie, $name = null, $actorNames = null, $genreName = null)
    {
        if ($name) {
            $movie->name = $name;
        }

        if ($genreName) {
            $genre = Genre::firstOrCreate(['name' => $genreName]);
            $movie->genre()->associate($genre);
            $movie->save();
        }

        if ($actorNames) {
            $actorIds = [];
            foreach ($actorNames as $actorName) {
                $actor = Actor::firstOrCreate(['name' => $actorName]);
                $actorIds[] = $actor->id;
            }

            $movie->actors()->sync($actorIds);
        }

        $movie->save();

        return $movie;
    }

    public static function delete(Movie $movie)
    {
        $movie->delete();
    }
}
