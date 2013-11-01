<?php
namespace bigwhoop\NBATitleBelt;

class Game
{
    /** @var \DateTime */
    private $date;
    
    /** @var Team */
    private $homeTeam;
    
    /** @var Team */
    private $awayTeam;
    
    /** @var int */
    private $homeTeamScore = 0;
    
    /** @var int */
    private $awayTeamScore = 0;


    /**
     * @param \DateTime $date
     * @param Team $homeTeam
     * @param int $homeTeamScore
     * @param Team $awayTeam
     * @param int $awayTeamScore
     */
    public function __construct(\DateTime $date, Team $homeTeam, $homeTeamScore, Team $awayTeam, $awayTeamScore)
    {
        $this->date          = $date;
        $this->homeTeam      = $homeTeam;
        $this->homeTeamScore = (int)$homeTeamScore;
        $this->awayTeam      = $awayTeam;
        $this->awayTeamScore = (int)$awayTeamScore;
    }


    /**
     * @return bool
     */
    public function wasPlayed()
    {
        return $this->homeTeamScore > 0 && $this->awayTeamScore > 0;
    }


    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }


    /**
     * @return Team
     */
    public function getHomeTeam()
    {
        return $this->homeTeam;
    }


    /**
     * @return Team
     */
    public function getAwayTeam()
    {
        return $this->awayTeam;
    }


    /**
     * @return int
     */
    public function getHomeTeamScore()
    {
        return $this->homeTeamScore;
    }


    /**
     * @return int
     */
    public function getAwayTeamScore()
    {
        return $this->awayTeamScore;
    }


    /**
     * @return string
     */
    public function getScore()
    {
        return "{$this->getHomeTeamScore()}:{$this->getAwayTeamScore()}";
    }


    /**
     * @return Team
     * @throws \LogicException
     */
    public function getWinner()
    {
        switch (bccomp($this->homeTeamScore, $this->awayTeamScore))
        {
            case -1: return $this->awayTeam;
            case  1: return $this->homeTeam;     
            default: throw new \LogicException('What?');
        }
    }


    /**
     * @return Team
     * @throws \LogicException
     */
    public function getLoser()
    {
        switch (bccomp($this->homeTeamScore, $this->awayTeamScore))
        {
            case -1: return $this->homeTeam;
            case  1: return $this->awayTeam;     
            default: throw new \LogicException('What?');
        }
    }
}
 