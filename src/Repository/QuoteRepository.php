<?php

class QuoteRepository implements Repository
{
    /**
     * @param int $id
     *
     * @return Quote
     */
    public function getById(int $id) :Quote
    {
        $generator = Faker\Factory::create();
        $generator->seed($id);
        return new Quote(
            $id,
            $generator->numberBetween(1, 10),
            $generator->numberBetween(1, 200),
            $generator->date()
        );
    }
}
