<?php

class SiteRepository implements Repository
{
    /**
     * @param int $id
     *
     * @return Site
     */
    public function getById(int $id) : Site
    {
        // DO NOT MODIFY THIS METHOD
        $faker = Faker\Factory::create();
        $faker->seed($id);
        return new Site($id, $faker->url);
    }
}
