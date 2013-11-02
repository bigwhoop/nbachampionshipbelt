<?php
namespace bigwhoop\NBATitleBelt;

class BeltGame
{
    /** @var Game */
    private $game;

    /** @var Team */
    private $beltHolder;
    
    /** @var int */
    private $winStreak = 0;
    
    /** @var int */
    private $accumulatedWins = 0;
    

    /**
     * @param Game $game
     * @param Team $beltHolder
     */
    public function __construct(Game $game, Team $beltHolder)
    {
        $this->game            = $game;
        $this->beltHolder      = $beltHolder;
    }


    /**
     * @param int $winStreak
     * @return $this
     */
    public function setWinStreak($winStreak)
    {
        $this->winStreak = (int)$winStreak;
        return $this;
    }


    /**
     * @param int $accumulatedWins
     * @return $this
     */
    public function setAccumulatedWins($accumulatedWins)
    {
        $this->accumulatedWins = (int)$accumulatedWins;
        return $this;
    }


    /**
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }


    /**
     * @return Team
     */
    public function getBeltHolderBeforeGame()
    {
        return $this->beltHolder;
    }


    /**
     * @return Team
     */
    public function getBeltHolderAfterGame()
    {
        return $this->game->getWinner();
    }


    /**
     * @return int
     */
    public function getWinStreak()
    {
        return $this->winStreak;
    }


    /**
     * @return int
     */
    public function getAccumulatedWins()
    {
        return $this->accumulatedWins;
    }
}
 