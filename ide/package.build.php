<?php

use php\lib\fs;
use php\lib\str;
use packager\cli\Console;

function task_copySourcesToBuild($e)
{
    foreach ($e->package()->getAny('sources', []) as $src) {
        if (str::startsWith($src, '..')) continue;

        if (str::startsWith($src, 'src')) continue;
        if (str::startsWith($src, 'resources')) continue;

        if (str::startsWith($src, "platforms/")) {
            $to = $src;

            if (str::endsWith($src, '/src')) {
                $to = fs::parent($src);
            }

            Tasks::copy("./$src", "./build/sources/$to");
        } else {
            Tasks::copy("./$src", "./build/sources/$src");
        }
    }

    Tasks::copy("../dn-app-framework/src", "./build/sources/dn-app-framework");
    //Tasks::copy("./src-release", "./build/sources/src");
}

function task_moveAppArtifactToLibs($e)
{
    $outputName = $e->package()->getAny('app.build.file-name', []);//->get('build')->get('file-name');

    Tasks::copy("./build/$outputName.jar", "./build/libs");

    Console::info("Artifact files moved!");

    Tasks::deleteFile("./build/$outputName.jar");
    Tasks::deleteFile("./build/$outputName.bat");
    Tasks::deleteFile("./build/$outputName");
}