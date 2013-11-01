<?php
namespace bigwhoop\NBATitleBelt;

class Stats implements \IteratorAggregate
{
    /** @var TeamStats[] */
    private $stats = [];


    /**
     * @param Game $game
     * @param Team $currentBeltHolder
     * @return bool     True if the game was for the belt, otherwise false.
     */
    public function analyzeGame(Game $game, Team $currentBeltHolder)
    {
        // We want to track all teams, also those that are not actually playing for the belt.
        $this->assertTeamStats($game->getHomeTeam());
        $this->assertTeamStats($game->getAwayTeam());
        
        if (!$currentBeltHolder->isPlayingIn($game)) {
            return false;
        }
        
        if (!$game->wasPlayed()) {
            return false;
        }
        
        $winner = $game->getWinner();
        $this->assertTeamStats($winner)->recordGame(true, $currentBeltHolder->isSame($winner));
        
        $loser  = $game->getLoser();
        $this->assertTeamStats($loser)->recordGame(false, $currentBeltHolder->isSame($loser));
        
        return true;
    }
    

    /**
     * @param Team $team
     * @return TeamStats
     */
    private function assertTeamStats(Team $team)
    {
        if (!array_key_exists($team->getName(), $this->stats)) {
            $this->stats[$team->getName()] = new TeamStats($team);
        }
        return $this->stats[$team->getName()];
    }


    /**
     * @return TeamStats[]
     */
    public function getSortedStats()
    {
        $stats = $this->stats;
        uasort($stats, function(TeamStats $a, TeamStats $b) {
            switch (bccomp($a->calcWinPercentage(), $b->calcWinPercentage()))
            {
                case -1: return 1;
                case  1: return -1;
                default:
                    switch (bccomp($a->countGames(), $b->countGames()))
                    {
                        case -1: return -1;
                        case  1: return 1;
                        default:
                            return strcasecmp($a->getTeam()->getName(), $b->getTeam()->getName());
                    }
            }
        });
        return $stats;
    }


    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getSortedStats());
    }
}
 