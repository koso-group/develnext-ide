<?php
namespace ide\project;

use ide\Ide;
use ide\Logger;
use php\gui\framework\Application;
use php\gui\UXImage;
use php\io\File;
use php\io\IOException;
use php\io\MemoryStream;
use php\lib\fs;

abstract class AbstractProjectTemplate
{
    abstract public function getName();
    abstract public function getDescription();

    public ?string $pathProject = null;


    /**
     * desktop, web, etc.
     * @return string
     */
    public function getSupportContext(): string
    {
        return 'desktop';
    }

    abstract public function getIcon();

     public function getIcon32() : \php\gui\UXImage
     {

         $image = null;

         $base64 = $this->getIconBase64();
         // var_dump($base64 .  '  base64');

         if ($base64 != null){
             try {
                 $imageData = base64_decode($base64);
                 $mem = new MemoryStream();
                 $mem->write($imageData);
                 $mem->seek(0);
                 $image = new UXImage($mem);
             }catch (IOException $e) {
                 Logger::error("Cannot load project icon: {0}", $e->getMessage());
             }
         }else{
             $image = new UXImage($this->getIcon());
         }

         return $image;
     }

    /**
     * @param Project $project
     * @return mixed
     */
    public function openProject(Project $project)
    {
    }

    /**
     * @param Project $project
     *
     * @return Project
     */
    abstract public function makeProject(Project $project);

    /**
     * @param Project $project
     * @return mixed
     */
    abstract public function recoveryProject(Project $project);

    /**
     * @param Project $project
     * @return bool
     */
    public function isProjectWillMigrate(Project $project)
    {
        return false;
    }

    public function getIconBase64()
    {
        if ($this->pathProject != null){

            if (fs::exists($this->pathProject)) {
                $exts = fs::scan($this->pathProject, ['extensions' => ['yml', 'dnproject'], 'excludeDirs' => false]);

                foreach ($exts as $file) {
                    if (fs::ext($file) == 'dnproject') {
                        $projectConfig = new ProjectConfig($this->pathProject, fs::nameNoExt($file));
                        return $projectConfig->getIconProjectBase64();
                    }
                }
            }

        }


    }

    public function setPathProject(string $path)
    {
        $this->pathProject = $path;

    }
}