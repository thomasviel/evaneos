<?php

class DestinationRepository implements Repository
{
    /**
     * @param int $id
     *
     * @return Destination
     */
    public function getById(int $id) : Destination
    {
        // DO NOT MODIFY THIS METHOD

        $faker = Faker\Factory::create();
        $faker->seed($id);

        return new Destination(
            $id,
            $faker->country,
            'en',
            $faker->slug()
        );
    }
}
