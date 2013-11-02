<?php
namespace bigwhoop\NBATitleBelt;

class GameLog implements \IteratorAggregate
{
    /** @var BeltGame[] */
    private $games = [];
    
    /** @var int */
    private $winStreak = 0;
    
    /** @var array */
    private $accumulatedWinsByTeam = [];
    

    /**
     * @param BeltGame $beltGame
     * @return $this
     */
    public function addGame(BeltGame $beltGame)
    {
        $oldHolder = $beltGame->getBeltHolderBeforeGame();
        $newHolder = $beltGame->getBeltHolderAfterGame();
        
        if (!$oldHolder->isSame($newHolder)) {
            $this->winStreak = 0;
        }
        $beltGame->setWinStreak(++$this->winStreak);
        
        $winnerName = $newHolder->getName();
        if (!array_key_exists($winnerName, $this->accumulatedWinsByTeam)) {
            $this->accumulatedWinsByTeam[$winnerName] = 0;
        }
        $this->accumulatedWinsByTeam[$winnerName]++;
        $beltGame->setAccumulatedWins($this->accumulatedWinsByTeam[$winnerName]);
        
        $this->games[] = $beltGame;
        
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
 