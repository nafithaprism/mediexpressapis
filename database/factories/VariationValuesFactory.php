<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VariationValuesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {   
        $name = $this->faker->text(10);
        
        $type = [1,2,3];
        
        return [

            'variation_id' => rand(10 , 30),
            'name' => $name,
            'route' => str_replace(' ' ,'-',$name),
            'type' =>$type[rand(0,2)],
        
        ];
    }
}
