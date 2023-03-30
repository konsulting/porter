<?php

namespace App\Support;

use Exception;
use Illuminate\Filesystem\Filesystem;
use League\Flysystem\StorageAttributes;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;

class FilePublisher
{
    /** @var Filesystem */
    protected $files;

    /** @var bool Should we force the publishing of the files? */
    protected $force = false;

    /**
     * FilePublisher constructor.
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Get the Filesystem.
     *
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }

    /**
     * Set the publisher to force publish the files, or not.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function force($bool = true)
    {
        $this->force = (bool) $bool;

        return $this;
    }

    /**
     * Publish the given item from and to the given location.
     *
     * @param string $from
     * @param string $to
     *
     * @throws Exception
     *
     * @return void
     */
    public function publish($from, $to)
    {
        if ($this->files->isFile($from)) {
            $this->publishFile($from, $to);
        } elseif ($this->files->isDirectory($from)) {
            $this->publishDirectory($from, $to);
        } else {
            throw new Exception("Can't locate path: <{$from}>");
        }
    }

    /**
     * Publish the file to the given path.
     *
     * @param string $from
     * @param string $to
     *
     * @return void
     */
    protected function publishFile($from, $to)
    {
        if (!$this->files->exists($to) || $this->force) {
            $this->createParentDirectory(dirname($to));

            $this->files->copy($from, $to);
        }
    }

    /**
     * Publish the directory to the given directory.
     *
     * @param string $from
     * @param string $to
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return void
     */
    protected function publishDirectory($from, $to)
    {
        $this->moveManagedFiles(new MountManager([
            'from' => new Flysystem(new LocalAdapter($from)),
            'to'   => new Flysystem(new LocalAdapter($to)),
        ]));
    }

    /**
     * Move all the files in the given MountManager.
     *
     * @param \League\Flysystem\MountManager $manager
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return void
     */
    protected function moveManagedFiles($manager)
    {
        foreach ($manager->listContents('from://', true) as $item) {
            /** @var StorageAttributes $item */
            if ($item->isFile() === 'file' && (!$manager->has('to://'.$item->path()) || $this->force)) {
                $manager->write('to://'.$item->path(), (string) $manager->read('from://'.$item->path()));
            }
        }
    }

    /**
     * Create the directory to house the published files if needed.
     *
     * @param string $directory
     *
     * @return void
     */
    protected function createParentDirectory($directory)
    {
        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }
}
