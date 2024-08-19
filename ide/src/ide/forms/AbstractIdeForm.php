<?php
namespace ide\forms;

use clover\platform\facades\ThemeManager;
use clover\platform\themes\Theme;
use ide\Ide;
use ide\Logger;
use ide\ui\elements\DNAnchorPane;
use php\gui\framework\AbstractForm;
use php\gui\UXForm;

/**
 * Class AbstractIdeForm
 * @package ide\forms
 */
class AbstractIdeForm extends AbstractForm
{
    public function __construct(UXForm $origin = null)
    {
        parent::__construct($origin);

        if (Ide::isCreated()) {
            $this->owner = Ide::get()->getMainForm();
        }

        Logger::info("Create form " . get_class($this));

        $this->on('show', function () {
            $formName = get_class($this);

            Logger::info("Show form '$formName' ..");

            Ide::get()->trigger('showForm', [$this]);
        }, __CLASS__);

        $this->on('hide', function () {
            $formName = get_class($this);

            Logger::info("Hide form '$formName' ..");

            Ide::get()->trigger('hideForm', [$this]);
        }, __CLASS__);
    }

    public function showPreloader($text = '')
    {
        parent::showPreloader(_($text));
    }

    public function toast($message, $timeout = 0)
    {
        parent::toast(_($message), $timeout);
    }


    protected function init()
    {
        parent::init();

        $this->title = _($this->title);
        _($this->layout);

        ThemeManager::currentTheme()->apply($this);
    }
}
