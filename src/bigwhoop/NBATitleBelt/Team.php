<?php
namespace bigwhoop\NBATitleBelt;

class Team
{
    /** @var string  */
    private $id = '';
    
    /** @var string  */
    private $name = '';
    
    /** @var array */
    private $alternativeNames = [];


    /**
     * @param string $id    3-digit team ID like MIA or LAL
     * @param string|array $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        
        if (is_array($name)) {
            $this->name = $name[0];
            $this->alternativeNames = array_slice($name, 1);
        } else {
            $this->name = $name;
        }
    }


    /**
     * @return string
     */
    public function getID()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return array
     */
    public function getAlternativeNames()
    {
        return $this->alternativeNames;
    }


    /**
     * @param Team $team
     * @return bool
     */
    public function isSame(Team $team)
    {
        return $this->getID() == $team->getID();
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
 