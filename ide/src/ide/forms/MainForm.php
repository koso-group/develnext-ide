<?php
namespace ide\forms;

use clover\platform\facades\PluginManager;
use clover\platform\plugins\traits\IDEMenuBarReConstructor;
use clover\platform\plugins\traits\Actions;

use ide\commands\ChangeThemeCommand;
use ide\commands\theme\CSSStyle;
use ide\commands\theme\IDETheme;
use ide\Ide;
use ide\IdeConfigurable;
use ide\IdeException;
use ide\Logger;
use ide\ui\elements\DNAnchorPane;
use ide\ui\elements\DNMenuBar;
use ide\ui\elements\DNSplitPane;
use ide\ui\elements\DNTabPane;
use ide\ui\elements\DNTreeView;
use ide\utils\UiUtils;
use php\gui\UXDndTabPane;
use php\gui\UXLabel;
use php\gui\UXMenu;
use php\gui\UXMenuBar;
use php\gui\UXNode;
use php\gui\UXScreen;
use php\gui\UXSplitPane;
use php\gui\UXTabPane;
use php\gui\UXTreeView;
use php\gui\designer\UXDirectoryTreeView;
use php\gui\dock\UXDockPane;
use php\gui\event\UXEvent;
use php\gui\layout\UXAnchorPane;
use php\gui\layout\UXHBox;
use php\gui\layout\UXVBox;
use php\gui\UXMenuItem;
use php\lib\fs;
use php\lib\str;

/**
 * @property UXTabPane $fileTabPane
 * @property UXTabPane $projectTabs
 * @property UXVBox $properties
 * @property UXSplitPane $contentSplit
 * @property UXAnchorPane $directoryTree
 * @property UXTreeView $projectTree
 * @property UXHBox $headPane
 * @property UXHBox $headRightPane
 * @property UXVBox $contentVBox
 * @property UXAnchorPane $bottomSpoiler
 * @property UXTabPane $bottomSpoilerTabPane
 * @property UXSplitPane $splitTree
 *
 * @property UXDockPane $dockPane
 * @property UXHBox $statusPane
 */
class MainForm extends AbstractIdeForm
{
    use IdeConfigurable;

    /**
     * @var UXMenuBar
     */
    public $mainMenu;

    /**
     * @var UXAnchorPane
     */
    private $bottom;

    /**
     * MainForm constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->bottom = $this->bottomSpoiler;

        DNAnchorPane::applyIDETheme($this->contentVBox);

        foreach ($this->contentVBox->children as $one) {
            if ($one instanceof UXMenuBar) {
                $this->mainMenu = $one;
                break;
            }
        }

        if (!$this->mainMenu) {
            throw new IdeException("Cannot find main menu on main form");
        }

        $mainMenu = $this->mainMenu;
            
        PluginManager::forTrait(IDEMenuBarReConstructor::class, function($plugin) use ($mainMenu)
        {
            $plugin->reConstructIDEMenuBar($mainMenu);
        });

        PluginManager::forTrait(Actions::class, function($plugin) use ($mainMenu) {
            foreach($plugin->getActions() as $action)
            {
                $menu = $this->menuBar_getByCategory($action->getCategory());
                if($menu)
                {
                    if ($action->withBeforeSeparator()) {
                        $last = $menu->items->last();

                        if ($last && $last->isSeparator()) { }
                        else
                        {
                            $menu->items->add(UXMenuItem::createSeparator());
                        }    
                    }

                    $menu->items->add($action->resolveMenuItem());

                    if ($action->withAfterSeparator())
                        $menu->items->add(UXMenuItem::createSeparator());
                }
                    
            }
        });            
    }

    public function menuBar_getByCategory(string $category)
    {
        return $this->findSubMenu('menu' . str::upperFirst($category));
    }

    /**
     * @param $string
     * @return null|UXMenu
     */
    public function findSubMenu($string)
    {
        /** @var UXMenu $one */
        foreach ($this->mainMenu->menus as $one) {
            if ($one->id == $string) {
                return $one;
            }
        }

        return null;
    }

    protected function init()
    {
        parent::init();

        $this->opacity = 0.01;

        Ide::get()->on('start', function () {
            $this->opacity = 1;
        });

        $mainMenu = $this->mainMenu; // FIX!!!!! see FixSkinMenu

        $this->headRightPane->spacing = 5;

        /** @var UXTabPane $pane */
        $pane = new UXDndTabPane();

        $parent = $this->fileTabPane->parent;
        $this->fileTabPane->free();

        $tabPane = $pane;
        $tabPane->id = 'fileTabPane';
        $tabPane->tabClosingPolicy = 'ALL_TABS';
        $tabPane->classes->add('dn-file-tab-pane');

        DNTabPane::applyIDETheme($tabPane);
        DNSplitPane::applyIDETheme($this->splitTree);
        DNSplitPane::applyIDETheme($this->contentSplit);

        // todo fix bug
        /*$tabPane->on('keyDown', $keyDown = function (UXKeyEvent $e) {
            if ($e->controlDown && $e->codeName == 'Tab') {
                $e->consume();
                FileSystem::openNext();
            }
        });*/

        if ($pane) {
            UXAnchorPane::setAnchor($pane, 0);
            $parent->add($pane);

            // fix bug
            // $pane->on('keyDown', $keyDown);
        } else {
            $parent->add($tabPane);
        }

        $tree = new UXDirectoryTreeView();
        $tree->position = [0, 0];
        DNTreeView::applyIDETheme($tree);
        $this->directoryTree->add($tree);

        UXAnchorPane::setAnchor($tree, 0);

        Ide::get()->bind('shutdown', function () {
            $this->ideConfig()->set("splitTree.dividerPositions", $this->splitTree->dividerPositions);
        });

        Ide::get()->bind('openProject', function () use ($tree) {
            $project = Ide::project();
            $project->getTree()->setView($tree);

            $tree->treeSource = $project->getTree()->createSource();

            $tree->root->expanded = true;
            $project->getConfig()->loadTreeState($project->getTree());

            $label = new UXLabel(fs::normalize($project->getMainProjectFile()));
            $label->paddingLeft = 10;

            $this->statusPane->children->setAll([
                $label
            ]);
        });

        Ide::get()->bind('afterCloseProject', function () use ($tree) {
            $tree->treeSource->shutdown();
            $tree->treeSource = null;

            $this->statusPane->children->clear();
        });
    }

    /**
     * @param string $id
     * @param string $text
     * @param bool $prepend
     * @return UXMenu
     * @throws IdeException
     */
    public function defineMenuGroup($id, $text, $prepend = false)
    {
        $id = str::upperFirst($id);

        $menu = null;

        foreach ($this->mainMenu->menus as $one) {
            if ($one->id == "menu$id") {
                $menu = $one;
                break;
            }
        }

        if ($menu == null) {
            $menu = _(new UXMenu($text));
            $menu->id = "menu$id";

            if ($prepend) {
                $this->mainMenu->menus->insert(0, $menu);
            } else {
                $this->mainMenu->menus->add($menu);
            }
        } else {
            $menu->text = _($text);
        }

        return $menu;
    }

    /**
     * @event showing
     */
    public function doShowing()
    {
        DNMenuBar::applyIDETheme($this->mainMenu);
        _($this->mainMenu);
    }

    public function show()
    {
        parent::show();
        Logger::info("Show main form ...");

        $screen = UXScreen::getPrimary();

        $this->showBottom(null);

        /*$this->contentSplitPane->dividerPositions = Ide::get()->getUserConfigArrayValue(get_class($this) . '.dividerPositions', $this->contentSplitPane->dividerPositions);
        $this->contentSplitPaneDividerPositions = $this->contentSplitPane->dividerPositions;
        */

        uiLater(function () use ($screen) {
            $this->width  = $width = Ide::get()->getUserConfigValue(get_class($this) . '.width', $screen->bounds['width'] * 0.75);
            $this->height = $height = Ide::get()->getUserConfigValue(get_class($this) . '.height', $screen->bounds['height'] * 0.75);

            if ($this->width < 300 || $this->height < 200) {
                $this->width = $screen->bounds['width'] * 0.75;
                $this->height = $screen->bounds['height'] * 0.75;
            }

            $this->centerOnScreen();

            $this->x = Ide::get()->getUserConfigValue(get_class($this) . '.x', 0);
            $this->y = Ide::get()->getUserConfigValue(get_class($this) . '.y', 0);

            if ($this->x > $screen->visualBounds['width'] - 10 || $this->y > $screen->visualBounds['height'] - 10 ||
                $this->x < -999 || $this->y < -999) {
                $this->x = $this->y = 50;
            }

            $this->maximized = Ide::get()->getUserConfigValue(get_class($this) . '.maximized', true);
        });

        $this->observer('maximized')->addListener(function ($old, $new) {
            Ide::get()->setUserConfigValue(get_class($this) . '.maximized', $new);
        });

        /*$this->contentSplitPane->items[0]->observer('width')->addListener(function ($old, $new) {
            Ide::get()->setUserConfigValue(get_class($this) . '.dividerPositions', $new);
        });*/

        foreach (['width', 'height', 'x', 'y'] as $prop) {
            $this->observer($prop)->addListener(function ($old, $new) use ($prop) {
                if ($this->iconified) {
                    return;
                }

                if (!$this->maximized) {
                    Ide::get()->setUserConfigValue(get_class($this) . '.' . $prop, $new);
                }

                //Ide::get()->setUserConfigValue(get_class($this) . '.dividerPositions', $this->contentSplitPane->dividerPositions);
            });
        }


        uiLater(function () {
            if ($this->ideConfig()->has('splitTree.dividerPositions')) {
                $this->splitTree->dividerPositions = $this->ideConfig()->getArray('splitTree.dividerPositions', [0.3]);
            }
        });
    }

    /**
     * @event close
     *
     * @param UXEvent $e
     *
     * @throws \Exception
     * @throws \php\io\IOException
     */
    public function doClose(UXEvent $e = null)
    {
        Logger::info("Close main form ...");

        $project = Ide::get()->getOpenedProject();

        if ($project) {
            $dialog = new MessageBoxForm(_('exit.project.message', $project->getName()), [
                'yes' => _('exit.project.yes'),
                'no'  => _('exit.project.no'),
                'abort' => _('exit.project.abort')
            ]);
            $dialog->title = _('exit.project.title');

            if ($dialog->showDialog()) {
                $result = $dialog->getResult();

                if ($result == 'yes') {
                    Logger::info("Remember the last project = yes!");

                    Ide::get()->setUserConfigValue('lastProject', $project->getProjectFile());
                } elseif ($result == 'abort') {
                    if ($e) {
                        $e->consume();
                    }
                    return;
                } else {
                    Logger::info("Cancel closing main form.");
                    Ide::get()->setUserConfigValue('lastProject', null);
                }

                Ide::get()->shutdown();
            } else {
                if ($e) {
                    $e->consume();
                }
            }
        } else {
            Ide::get()->setUserConfigValue('lastProject', null);

            $dialog = new MessageBoxForm(_('exit.message'), [_('exit.yes'), _('exit.no')]);
            if ($dialog->showDialog() && $dialog->getResultIndex() == 0) {
                $this->hide();

                Ide::get()->shutdown();
            } else {
                if ($e) {
                    $e->consume();
                }
            }
        }
    }

    /**
     * @return UXHBox
     */
    public function getHeadPane()
    {
        return $this->headPane;
    }

    /**
     * @return UXHBox
     */
    public function getHeadRightPane()
    {
        return $this->headRightPane;
    }

    /**
     * @return UXTreeView
     */
    public function getProjectTree()
    {
        return $this->projectTree;
    }

    public function hideBottom()
    {
        $this->showBottom(null);
    }

    public function showBottom(UXNode $content = null)
    {
        uiLater(function () use ($content) {
            if ($content) {
                if (!$this->contentSplit->items->has($this->bottom)) {
                    $this->contentSplit->items->add($this->bottom);
                }

                $this->bottom->children->clear();

                $height = $this->layout->height;

                $content->height = (int) Ide::get()->getUserConfigValue('mainForm.consoleHeight', 350);

                $content->observer('height')->addListener(function ($old, $new) use ($content) {
                    if (!$content->isFree()) {
                        Ide::get()->setUserConfigValue('mainForm.consoleHeight', $new);
                    }
                });

                UXAnchorPane::setAnchor($content, 0);

                $this->bottom->add($content);

                $percent = (($content->height + 3) * 100 / $height) / 100;

                $this->contentSplit->dividerPositions = [1 - $percent, $percent];
            } else {
                $this->contentSplit->items->remove($this->bottom);
            }
        });
    }
}
