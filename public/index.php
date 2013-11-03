<?php
namespace App;

const CURRENT_SEASON = 2013;

date_default_timezone_set('UTC');

use Slim\Slim;
use Slim\Log;
use bigwhoop\NBATitleBelt\Stats;
use bigwhoop\NBATitleBelt\Team;
use bigwhoop\NBATitleBelt\Game;
use bigwhoop\NBATitleBelt\Schedule;
use bigwhoop\NBATitleBelt\GameLog;
use bigwhoop\NBATitleBelt\Parser\BBReferenceParser;

require __DIR__ . '/../vendor/autoload.php';

$app = new Slim([
    'mode'                    => 'development',
    'app.page.cache.duration' => 3600, // 1 hour
    'templates.path'          => __DIR__ . '/../templates',
]);

$app->configureMode('development', function() use ($app) {
    $app->config([
        'debug'       => true,
        'log.enabled' => true,
        'log.level'   => Log::DEBUG,
        'app.page.cache.duration' => 0,
    ]);
});

$app->configureMode('production', function() use ($app) {
    $app->config([
        'debug'       => false,
        'log.enabled' => true,
        'log.level'   => Log::ERROR,
    ]);
});

$app->get('/', function() use ($app) {
    $season = $app->request()->get('season');
    if ($season !== 'all') {
        $season = (int)$season;
    }
    
    if ($season === 0) {
        $season = CURRENT_SEASON;
    }
    
    $availableSeasons = [
        // Seasons 1950/51 - 2013/14. Minneapolis Lakers won 1949/50 and are the defending champs.
        'all' => ['defendingChamp' => new Team('LAL', 'Minneapolis Lakers')],
        
        2000 => ['defendingChamp' => new Team('LAL', 'Los Angeles Lakers')],
        2001 => ['defendingChamp' => new Team('LAL', 'Los Angeles Lakers')],
        2002 => ['defendingChamp' => new Team('LAL', 'Los Angeles Lakers')],
        2003 => ['defendingChamp' => new Team('SAS', 'San Antonio Spurs')],
        2004 => ['defendingChamp' => new Team('DET', 'Detroit Pistons')],
        2005 => ['defendingChamp' => new Team('SAS', 'San Antonio Spurs')],
        2006 => ['defendingChamp' => new Team('MIA', 'Miami Heat')],
        2007 => ['defendingChamp' => new Team('SAS', 'San Antonio Spurs')],
        2008 => ['defendingChamp' => new Team('BOS', 'Boston Celtics')],
        2009 => ['defendingChamp' => new Team('LAL', 'Los Angeles Lakers')],
        2010 => ['defendingChamp' => new Team('LAL', 'Los Angeles Lakers')],
        2011 => ['defendingChamp' => new Team('DAL', 'Dallas Mavericks')],
        2012 => ['defendingChamp' => new Team('MIA', 'Miami Heat')],
        2013 => ['defendingChamp' => new Team('MIA', 'Miami Heat')],
    ];
    
    // Check season availability
    if (!array_key_exists($season, $availableSeasons)) {
        $app->render('no_data_for_season.phtml', ['season' => $season], 404);
        return;
    }
    
    // Check cache
    $cachePath = __DIR__ . '/../tmp/cache/' . $season . '.html';
    if (file_exists($cachePath) && filemtime($cachePath) + $app->config('app.page.cache.duration') > time()) {
        $app->response()->setBody(file_get_contents($cachePath));
        return;
    }
    
    try {
        $parser = new BBReferenceParser(__DIR__ . '/../data/' . $season . '.csv');
        if ($season === 'all') {
            $parser->setMode($parser::MODE_FRANCHISES);
        }
        $games = $parser->getGames();
    } catch (\RuntimeException $e) {
        $games = [];
    }
    
    $schedule = new Schedule($games);
    $gameLog  = new GameLog();
    $stats    = new Stats();
    
    $beltHolder = $availableSeasons[$season]['defendingChamp'];
    foreach ($schedule as $game) {
        /** @var Game $game */
        if (($beltGame = $stats->analyzeGame($game, $beltHolder))) {
            $gameLog->addGame($beltGame);
            $beltHolder = $beltGame->getBeltHolderAfterGame();
        }
    }
    
    $upcomingChampGame = $schedule->getUpcomingChampionshipGame($beltHolder);
    $upcomingChampGameIfHomeTeamWins = $upcomingChampGameIfAwayTeamWins = null;
    if ($upcomingChampGame) {
        $upcomingChampGameIfHomeTeamWins = $schedule->getUpcomingChampionshipGame($upcomingChampGame->getHomeTeam(), $upcomingChampGame->getAwayTeam());
        $upcomingChampGameIfAwayTeamWins = $schedule->getUpcomingChampionshipGame($upcomingChampGame->getAwayTeam(), $upcomingChampGame->getHomeTeam());
    }
    
    // This is a bit hacky, but we don't have a DB or anything to store the current leader ...
    if ($season == CURRENT_SEASON) {
        $data = [
            'id'   => $beltHolder->getID(),
            'name' => $beltHolder->getName(),
        ];
        file_put_contents(__DIR__ . '/../public/leader.json', json_encode($data));
    }
    
    $view = $app->view();
    $view->setTemplatesDirectory($app->config('templates.path'));
    $view->appendData([
        'season'           => $season,
        'availableSeasons' => $availableSeasons,
        'beltHolder'       => $beltHolder,
        'isOngoingSeason'  => $season == CURRENT_SEASON || $season === 'all',
        'stats'            => $stats,
        'gameLog'          => $gameLog,
        'upcomingGame'     => $upcomingChampGame,
        'upcomingChampGameIfHomeTeamWins' => $upcomingChampGameIfHomeTeamWins,
        'upcomingChampGameIfAwayTeamWins' => $upcomingChampGameIfAwayTeamWins,
    ]);
    
    $content = $view->fetch('homepage.phtml');
    file_put_contents($cachePath, $content);
    $app->response()->setBody($content);
})->name('homepage');

$app->run();