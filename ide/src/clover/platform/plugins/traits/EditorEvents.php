<?php
namespace clover\platform\plugins\traits;

use ide\editors\AbstractEditor;

trait EditorEvents
{
    public function handleRequestFocus(AbstractEditor $editor) {}

    public function handleUpdateMultipleEditor(AbstractEditor $editor) {}
}