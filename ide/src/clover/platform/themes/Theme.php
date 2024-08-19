<?php
namespace clover\platform\themes;

use php\gui\UXForm;

abstract class Theme
{
    abstract public function getName(): string;
    abstract public function getDescription(): string;
    abstract public function getVersion(): float;
    abstract public function getAuthor(): string;

    abstract public function isDarked(): bool;

    public function reqiuredReboot(): bool { return false; }

    public function __toString()
    {
        return $this->getName();
    }

    abstract public function getStylesheetFilePath();

    public function apply(UXForm $form)
    {
        $form->clearStylesheets();
        $form->addStylesheet($this->getStylesheetFilePath());
    }

    public function getPreferenceItems(): array
    {
        return [];
    }
}