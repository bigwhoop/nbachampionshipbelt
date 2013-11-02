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
     * @param Team $beltHolder
     * @param Team|null $butNotAgainstTeam
     * @return Game|null
     */
    public function getUpcomingChampionshipGame(Team $beltHolder, Team $butNotAgainstTeam = null)
    {
        foreach ($this->games as $game) {
            if ($game->wasPlayed()) {
                continue;
            }
            if ($butNotAgainstTeam && $butNotAgainstTeam->isPlayingIn($game)) {
                continue;
            }
            if ($beltHolder->isPlayingIn($game)) {
                return $game;
            }
        }
        return null;
    }


    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->games);
    }
}
 