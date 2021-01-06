<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FileFilterByIdCallback
{
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

function findMatchingGameReplays($gameId)
{
    return array_filter(
        File::files("../binary/replay/"),
        new FileFilterByIdCallback($gameId));
}

function findMatchingGameMap($gameId, $map)
{
    return array_filter(
        File::files("../binary/map/" . $gameId),
        new FileFilterByIdCallback($map));
}

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->get('user/{userId}/photo', function ($userId) {
        $files = findMatchingUserPics($userId);
        if (empty($files)) {
            return abort(404);
        }
        return response()->download("../binary/photo/" . array_values($files)[0]->getFilename());
    });

    $router->post('user/{userId}/photo', function (Request $request, $userId) {
        $currFiles = array_map(function ($it) {
            return $it->getPathname();
        }, findMatchingUserPics($userId));
        File::delete($currFiles);
        $uploadedFile = $request->file('photo');
        $uploadedFile->move(
            '../binary/photo',
            "$userId." . $uploadedFile->extension());
    });

    $router->delete('user/{userId}/photo', function (Request $request, $userId) {
        $currFiles = array_map(function ($it) {
            return $it->getPathname();
        }, findMatchingUserPics($userId));
        File::delete($currFiles);
    });

    $router->get('game/{gameId}/replay', function ($gameId) {
        $files = findMatchingGameReplays($gameId);
        if (empty($files)) abort(404, '404');
        return response()
            ->download("../binary/replay/" . array_values($files)[0]->getFilename())
            ->setCache(['public' => true, 'max_age' => 604800, "immutable" => true]);
    });

    $router->post('game/{gameId}/replay', function (Request $request, $gameId) {
	if ($request->header('third-party-token') !== env('CWT_THIRD_PARTY_TOKEN')) abort(403, 'forbidden');
        $currFiles = array_map(function ($it) {
            return $it->getPathname();
        }, findMatchingGameReplays($gameId));
        File::delete($currFiles);
        $uploadedFile = $request->file('replay');
        $uploadedFile->move(
            '../binary/replay',
            "$gameId." . $uploadedFile->extension());
    });

    $router->get('game/{gameId}/map/{map}', function ($gameId, $map) {
        $files = findMatchingGameMap($gameId, $map);
        $filename = array_values($files)[0]->getFilename();
        return response()
            ->download(
                "../binary/map/${gameId}/" . $filename,
                $filename,
                ["Content-Type" => "image/png"])
            ->setCache(['public' => true, 'max_age' => 604800, "immutable" => true]);
    });

    $router->post('game/{gameId}/map/{map}', function (Request $request, $gameId, $map) {
        $uploadedFile = $request->file('map');
        $uploadedFile->move(
            '../binary/map/' . $gameId,
            "$map.png");
    });
});

