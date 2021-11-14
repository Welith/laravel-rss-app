<?php

namespace Database\Factories;

use App\Models\Feed;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Feed::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'             => $this->faker->name,
            'link'          => $this->faker->url,
            'source' => $this->faker->name,
            'source_url' => $this->faker->url,
            'publish_date' => $this->faker->dateTime,
            'description' => $this->faker->text
        ];
    }
}
