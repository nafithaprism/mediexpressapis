<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VariationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {   
        $name = $this->faker->text(30);
        return [
            'name' => $name ,
            'route' => str_replace(' ', '-', $name),
            'type' => $this->faker->text(20),
        ];
    }
}
