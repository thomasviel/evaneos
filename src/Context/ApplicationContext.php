<?php

class ApplicationContext
{
    /**
     * @var User
     */
    private User $currentUser;

    public function __construct()
    {
        $faker = \Faker\Factory::create();
        $this->currentUser = new User($faker->randomNumber(), $faker->firstName, $faker->lastName, $faker->email);
    }


    /**
     * @return User
     */
    public function getCurrentUser(): User
    {
        return $this->currentUser;
    }


}
