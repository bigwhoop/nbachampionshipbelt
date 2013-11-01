<?php
namespace bigwhoop\NBATitleBelt;

class GameLog implements \IteratorAggregate
{
    /** @var Game[] */
    private $games = [];


    /**
     * @param Game $game
     * @return $this
     */
    public function addGame(Game $game)
    {
        $this->games[] = $game;
        return $this;
    }
    

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->games);
    }
}
 