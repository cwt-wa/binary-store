<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FileFilterByIdCallback
{
    private $fileId;

    public function __construct($fileId)
    {
        $this->fileId = $fileId;
    }

    public function __invoke($it)
    {
        return $it->getBasename('.' . $it->getExtension()) === $this->fileId;
    }
};

function findMatchingUserPics($userId) {
    return array_filter(
            File::files("../binary/photo/"),
            new FileFilterByIdCallback($userId));
}

function findMatchingGameReplays($gameId) {
    return array_filter(
            File::files("../binary/replay/"),
            new FileFilterByIdCallback($gameId));
}

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->get('user/{userId}/photo', function ($userId) {
        $files = findMatchingUserPics($userId);
        if (empty($files)) {
            $rndFiles = File::files("./albino");
            $rnd = rand(0, count($rndFiles) - 1);
            Log::debug($rndFiles[$rnd]->getPathname());
            return response()->download($rndFiles[$rnd]->getPathname());
        }
        return response()->download("../binary/photo/" . array_values($files)[0]->getFilename());
    });

    $router->post('user/{id}/photo', function (Request $request, $userId) {
        $currFiles = array_map(function ($it) {
            return $it->getPathname();
        }, findMatchingUserPics($userId));
        File::delete($currFiles);
        $uploadedFile = $request->file('photo');
        $uploadedFile->move(
            '../binary/photo',
            "$userId." . $uploadedFile->extension());
    });

    $router->delete('user/{id}/photo', function (Request $request, $userId) {
        $currFiles = array_map(function ($it) {
            return $it->getPathname();
        }, findMatchingUserPics($userId));
        File::delete($currFiles);
    });

    $router->get('replay', function () {
        return response()->download("../binary/replays_2007_till_2011.zip");
    });

    $router->get('game/{gameId}/replay', function ($gameId) {
        $files = findMatchingGameReplays($gameId);
        if (empty($files)) {
            return response()->download("../binary/replays_2007_till_2011.zip");
        }
        return response()->download("../binary/replay/" . array_values($files)[0]->getFilename());
    });

    $router->post('game/{id}/replay', function (Request $request, $gameId) {
        $currFiles = array_map(function ($it) {
            return $it->getPathname();
        }, findMatchingGameReplays($gameId));
        File::delete($currFiles);
        $uploadedFile = $request->file('replay');
        $uploadedFile->move(
            '../binary/replay',
            "$gameId." . $uploadedFile->extension());
    });
});

