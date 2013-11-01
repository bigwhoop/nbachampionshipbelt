<?php
namespace bigwhoop\NBATitleBelt;

class Schedule implements \IteratorAggregate
{
    /** @var Game[] */
    private $games = [];
    

    /**
     * @param Game[] $games
     */
    public function __construct(array $games)
    {
        $this->setGames($games);
    }


    /**
     * @param Game[] $games
     * @return $this
     */
    private function setGames(array $games)
    {
        usort($games, function(Game $a, Game $b) {
            return $a->getDate() > $b->getDate() ? 1 : -1;
        });
        $this->games = $games;
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
 