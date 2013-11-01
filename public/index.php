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
    $season = (int)$app->request()->get('season');
    if ($season === 0) {
        $season = CURRENT_SEASON;
    }
    
    $availableSeasons = [
        2000 => ['defendingChamp' => new Team('LAL')],
        2001 => ['defendingChamp' => new Team('LAL')],
        2002 => ['defendingChamp' => new Team('LAL')],
        2003 => ['defendingChamp' => new Team('SAS')],
        2004 => ['defendingChamp' => new Team('DET')],
        2005 => ['defendingChamp' => new Team('SAS')],
        2006 => ['defendingChamp' => new Team('MIA')],
        2007 => ['defendingChamp' => new Team('SAS')],
        2008 => ['defendingChamp' => new Team('BOS')],
        2009 => ['defendingChamp' => new Team('LAL')],
        2010 => ['defendingChamp' => new Team('LAL')],
        2011 => ['defendingChamp' => new Team('DAL')],
        2012 => ['defendingChamp' => new Team('MIA')],
        2013 => ['defendingChamp' => new Team('MIA')],
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
        if ($stats->analyzeGame($game, $beltHolder)) {
            $gameLog->addGame($game);
            $beltHolder = $game->getWinner();
        }
    }
    
    if ($season == CURRENT_SEASON) {
        file_put_contents(__DIR__ . '/../public/leader.json', json_encode(['name' => $beltHolder->getName()]));
    }
    
    $view = $app->view();
    $view->setTemplatesDirectory($app->config('templates.path'));
    $view->appendData([
        'season'           => $season,
        'availableSeasons' => $availableSeasons,
        'beltHolder'       => $beltHolder,
        'isRunningSeason'  => $season == CURRENT_SEASON,
        'stats'            => $stats,
        'gameLog'          => $gameLog,
        'upcomingGame'     => $schedule->getUpcomingChampionshipGame($beltHolder),
    ]);
    
    $content = $view->fetch('homepage.phtml');
    file_put_contents($cachePath, $content);
    $app->response()->setBody($content);
})->name('homepage');

$app->run();