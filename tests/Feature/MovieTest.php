<?php

namespace Tests\Feature;

use App\Models\Actor;
use App\Models\Genre;
use App\Models\Movie;
use App\Services\MovieService;
use Database\Seeders\FakeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovieTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndexMovies()
    {
        $movies = MovieService::index();
        $this->assertCount(FakeSeeder::COUNT, $movies);

        $actor = Actor::first();
        $actorName = $actor->name;
        $moviesFilteredByActor = MovieService::index($actorName);

        foreach ($moviesFilteredByActor as $item) {
            $actorNames = $item->actors->pluck('name');
            $this->assertContains($actorName, $actorNames);
        }

        $genre = Genre::first();
        $genreName = $genre->name;
        $moviesFilteredByGenre = MovieService::index(null, $genreName);

        foreach ($moviesFilteredByGenre as $item) {
            $this->assertEquals($genreName, $item->genre->name);
        }

        $moviesOrderedByNameAsc = MovieService::index(null, null, 'asc');
        // to double check that we have enough initial data (FakeSeeder::COUNT)
        $this->assertGreaterThanOrEqual(2, count($moviesOrderedByNameAsc), 'Not enough rows to perform test');
        $this->assertGreaterThan($moviesOrderedByNameAsc[0]->name, $moviesOrderedByNameAsc[1]->name);

        $moviesOrderedByNameDesc = MovieService::index(null, null, 'desc');
        $this->assertGreaterThanOrEqual(2, count($moviesOrderedByNameDesc), 'Not enough rows to perform test');
        $this->assertGreaterThan($moviesOrderedByNameDesc[1]->name, $moviesOrderedByNameDesc[0]->name);

    }

    public function testStoreAndUpdateMovie()
    {
        $name = "The Shawshank Redemption";
        $actors = [
            "Tim Robbins",
            "Morgan Freeman",
            "Bob Gunton"
        ];
        $genre = "Drama";

        $movie = MovieService::store($name, $actors, $genre);

        $this->assertDatabaseHas('movies', ['name' => $name]);
        $this->assertDatabaseHas('actors', ['name' => $actors[0]]);
        $this->assertDatabaseHas('genres', ['name' => $genre]);

        $newName = "Not The Shawshank Redemption";
        $newActors = [
            "Not Tim Robbins",
            "Not Morgan Freeman",
            "Not Bob Gunton"
        ];
        $newGenre = "Not Drama";

        $updatedMovie = MovieService::update($movie, $newName, $newActors, $newGenre);

        $this->assertDatabaseHas('movies', ['name' => $newName]);
        $this->assertDatabaseHas('actors', ['name' => $newActors[0]]);
        $this->assertDatabaseHas('genres', ['name' => $newGenre]);

        $this->assertCount(3, $updatedMovie->actors);   // check that it replaced old actors with new ones,
        // and not just added new
    }

    public function testDeleteMovie()
    {
        $movie = Movie::first();
        MovieService::delete($movie);
        $this->assertDeleted($movie);
    }

}
