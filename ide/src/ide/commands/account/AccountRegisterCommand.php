<?php
namespace ide\commands\account;

use ide\editors\AbstractEditor;
use ide\forms\MessageBoxForm;
use ide\forms\RegisterForm;
use ide\Ide;
use ide\misc\AbstractCommand;

class AccountRegisterCommand extends AbstractCommand
{
    public function getName()
    {
        return "command.register::Зарегистрироваться";
    }

    public function getCategory()
    {
        return 'account';
    }

    public function onExecute($e = null, AbstractEditor $editor = null)
    {
        $dialog = new RegisterForm();
        $dialog->showAndWait();
    }
}