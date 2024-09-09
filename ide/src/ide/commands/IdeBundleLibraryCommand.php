<?php
namespace ide\commands;

use ide\editors\AbstractEditor;
use ide\misc\AbstractCommand;

class IdeBundleLibraryCommand extends AbstractCommand
{
    public function getName()
    {
        return 'Пакеты расширений';
    }

    public function onExecute($e = null, AbstractEditor $editor = null) {}
}