<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\System\Event\FileAccessEvent;

class FinderController extends Controller
{
    /**
     * @Request({"path"})
     * @Response("json")
     */
    public function listAction($path)
    {
        if (!$dir = $this->getPath()) {
            return $this->error(__('Invalid path.'));
        }

        if (!is_dir($dir) || '-' === $mode = $this->getMode($dir)) {
            return $this->error(__('Permission denied.'));
        }

        $data = array_fill_keys(['folders', 'files'], []);
        $data['mode'] = $mode;

        foreach ($this['file']->find()->depth(0)->in($dir) as $file) {

            if ('-' === $mode = $this->getMode($file->getPathname())) {
                continue;
            }

            $info = [
                'name'     => $file->getFilename(),
                'path'     => $this->normalizePath($path.'/'.$file->getFilename()),
                'url'      => htmlspecialchars($this['url']->to($file->getPathname()), ENT_QUOTES),
                'writable' => $mode == 'w'
            ];

            if (!$isDir = $file->isDir()) {
                $info = array_merge($info, [
                    'size'         => $this->formatFileSize($file->getSize()),
                    'lastmodified' => $this['dates']->format($file->getMTime(), 'd.m.y H:m')
                ]);
            }

            $data[$isDir ? 'folders' : 'files'][] = $info;
        }

        return $data;
    }

    /**
     * @Request({"name"})
     * @Response("json")
     */
    public function createFolderAction($name)
    {
        if (!$this->isValidFilename($name)) {
            return $this->error(__('Invalid file name.'));
        }

        if (!$path = $this->getPath($name)) {
            return $this->error(__('Invalid path.'));
        }

        if (file_exists($this->getPath($name))) {
            return $this->error(__('Folder already exists.'));
        }

        if ('w' !== $this->getMode(dirname($path))) {
            return $this->error(__('Permission denied.'));
        }

        try {

            $this['file']->makeDir($path);

            return $this->success(__('Directory created.'));

        } catch(\Exception $e) {

            return $this->error(__('Unable to create directory.'));
        }
    }

    /**
     * @Request({"oldname", "newname"})
     * @Response("json")
     */
    public function renameAction($oldname, $newname)
    {
        if (!$this->isValidFilename($newname)) {
            return $this->error(__('Invalid file name.'));
        }

        if (!$source = $this->getPath($oldname) or !$target = $this->getPath($newname)) {
            return $this->error(__('Invalid path.'));
        }

        if ('w' !== $this->getMode($source) || file_exists($target) || 'w' !== $this->getMode(dirname($target))) {
            return $this->error(__('Permission denied.'));
        }

        try {

            $this['file']->rename($source, $target);

            return $this->success(__('Renamed.'));

        } catch(\Exception $e) {

            return $this->error(__('Unable to rename.'));
        }
    }

    /**
     * @Request({"names": "array"})
     * @Response("json")
     */
    public function removeFilesAction($names)
    {
        foreach ($names as $name) {

            if (!$path = $this->getPath($name)) {
                return $this->error(__('Invalid path.'));
            }

            if ('w' !== $this->getMode($path)) {
                return $this->error(__('Permission denied.'));
            }

            try {

                $this['file']->delete($path);

            } catch (\Exception $e) {

                return $this->error(__('Unable to remove.'));
            }
        }

        return $this->success(__('Removed selected.'));
    }

    /**
     * @Response("json")
     */
    public function uploadAction()
    {
        try {

            if (!$path = $this->getPath()) {
                return $this->error(__('Invalid path.'));
            }

            if (!is_dir($path) || 'w' !== $mode = $this->getMode($path)) {
                return $this->error(__('Permission denied.'));
            }

            $files = $this['request']->files->get('files');

            if (!$files) {
                throw new Exception(__('No files uploaded.'));
            }

            foreach ($files as $file) {

                if (!$file->isValid()) {
                    throw new Exception(__('Uploaded file invalid.'));
                }

                $file->move($path, $file->getClientOriginalName());
            }

            return $this->success(__('Upload complete.'));

        } catch(\Exception $e) {

            return $this->error(__('Unable to upload.'));
        }
    }

    protected function getMode($path)
    {
        $mode = $this['events']->dispatch('system.finder', new FileAccessEvent)->mode($path);

        if ('w' == $mode && !is_writable($path)) {
            $mode = 'r';
        }

        if ('r' == $mode && !is_readable($path)) {
            $mode = '-';
        }

        return $mode;
    }

    protected function formatFileSize($size)
    {
      if ($size == 0) {
          return __('n/a');
      }

      $sizes = [__('%d Bytes'), __('%d  KB'), __('%d  MB'), __('%d  GB'), __('%d TB'), __('%d PB'), __('%d EB'), __('%d ZB'), __('%d YB')];
      $size  = round($size/pow(1024, ($i = floor(log($size, 1024)))), 2);
      return sprintf($sizes[$i], $size);
    }

    protected function getPath($path = '')
    {
        $root = strtr($this['path'], '\\', '/');
        $path = $this->normalizePath($root.'/'.$this['request']->get('root').'/'.$this['request']->get('path').'/'.$path);

        return 0 === strpos($path, $root) ? $path : false;
    }

    /**
     * Normalizes the given path
     *
     * @param  string $path
     * @return string
     */
    protected function normalizePath($path)
    {
        $path   = str_replace(['\\', '//'], '/', $path);
        $prefix = preg_match('|^(?P<prefix>([a-zA-Z]+:)?//?)|', $path, $matches) ? $matches['prefix'] : '';
        $path   = substr($path, strlen($prefix));
        $parts  = array_filter(explode('/', $path), 'strlen');
        $tokens = [];

        foreach ($parts as $part) {
            if ('..' === $part) {
                array_pop($tokens);
            } elseif ('.' !== $part) {
                array_push($tokens, $part);
            }
        }

        return $prefix . implode('/', $tokens);
    }

    protected function isValidFilename($name)
    {
        if (empty($name)) {
            return false;
        }

        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            return !preg_match('#[\\/:"*?<>|]#', $name);
        }

        return -1 !== strpos($name, '/');
    }

    protected function success($message) {
        return compact('message');
    }

    protected function error($message) {
        return ['error' => true, 'message' => $message];
    }
}
