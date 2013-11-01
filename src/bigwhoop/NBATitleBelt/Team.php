<?php
namespace bigwhoop\NBATitleBelt;

class Team
{
    /** @var string  */
    private $name = '';


    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @param Team $team
     * @return bool
     */
    public function isSame(Team $team)
    {
        return $this->getName() == $team->getName();
    }


    /**
     * @param Game $game
     * @return bool
     */
    public function isPlayingIn(Game $game)
    {
        if ($game->getHomeTeam()->isSame($this)) {
            return true;
        }
        if ($game->getAwayTeam()->isSame($this)) {
            return true;
        }
        return false;
    }
}
 