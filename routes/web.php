<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class UserMatchingCallback
{
    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function __invoke($it)
    {
        return $it->getBasename('.' . $it->getExtension()) === $this->userId;
    }
};

function findMatchingUserPics($userId) {
    return array_filter(
            File::files("../binary/photo/"),
            new UserMatchingCallback($userId));
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
        return response()->download("../binary/photo/" . $files[0]->getFilename());
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
});

