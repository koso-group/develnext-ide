<?php
namespace ide\commands\account;

use ide\editors\AbstractEditor;
use ide\forms\MessageBoxForm;
use ide\Ide;
use ide\misc\AbstractCommand;

/**
 * Class AccountLogoutCommand
 * @package ide\commands\account
 */
class AccountLoginCommand extends AbstractCommand
{
    public function getName()
    {
        return "command.login::Войти";
    }

    public function getCategory()
    {
        return 'account';
    }



    public function onExecute($e = null, AbstractEditor $editor = null)
    {
        Ide::accountManager()->authorize(true);
    }
}