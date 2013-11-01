<?php
namespace bigwhoop\NBATitleBelt;

class TeamStats
{
    /** @var Team  */
    private $team;
    
    /** @var int */
    private $winsAsChallenger = 0;
    
    /** @var int */
    private $winsAsDefender = 0;
    
    /** @var int */
    private $lossesAsChallenger = 0;
    
    /** @var int */
    private $lossesAsDefender = 0;
    
    
    /**
     * @param Team $team
     */
    public function __construct(Team $team)
    {
        $this->team = $team;
    }


    /**
     * @param bool $hasWon
     * @param bool $wasBeltHolder
     * @return $this
     */
    public function recordGame($hasWon, $wasBeltHolder)
    {
        if ($hasWon) {
            if ($wasBeltHolder) {
                $this->winsAsDefender++;
            } else {
                $this->winsAsChallenger++;
            }
        } else {
            if ($wasBeltHolder) {
                $this->lossesAsDefender++;
            } else {
                $this->lossesAsChallenger++;
            }
        }
        return $this;
    }


    /**
     * @return int
     */
    public function countGames()
    {
        return $this->countWins() + $this->countLosses();
    }


    /**
     * @return int
     */
    public function countWins()
    {
        return $this->winsAsChallenger + $this->winsAsDefender;
    }


    /**
     * @return int
     */
    public function countWinsAsChallenger()
    {
        return $this->winsAsChallenger;
    }


    /**
     * @return int
     */
    public function countWinsAsDefender()
    {
        return $this->winsAsDefender;
    }


    /**
     * @return int
     */
    public function countLosses()
    {
        return $this->lossesAsChallenger + $this->lossesAsDefender;
    }


    /**
     * @return int
     */
    public function countLossesAsChallenger()
    {
        return $this->lossesAsChallenger;
    }


    /**
     * @return int
     */
    public function countLossesAsDefender()
    {
        return $this->lossesAsDefender;
    }


    /**
     * @return float
     */
    public function calcWinPercentage()
    {
        $numGames = $this->countGames();
        if ($numGames == 0) {
            return 0.0;
        }
        return round($this->countWins() / $this->countGames() * 100, 2);
    }
    

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }
}
 