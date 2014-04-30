<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\System\Event\FileAccessEvent;

class FinderController extends Controller
{
    /**
     * @Request({"path"})
     */
    public function listAction($path)
    {
        if (!$dir = $this->getPath() or !is_dir($dir)) {
            return $this->error(__('Invalid path.'));
        }

        if ('-' === $mode = $this->getMode($dir)) {
            return $this->error(__('Permission denied.'));
        }

        $data = array_fill_keys(array('folders', 'files'), array());
        $data['mode'] = $mode;

        foreach ($this('file')->find()->depth(0)->in($dir) as $file) {

            if ('-' === $mode = $this->getMode($file->getPathname())) {
                continue;
            }

            $info = array(
                'name'     => $file->getFilename(),
                'path'     => $this->normalizePath($path.'/'.$file->getFilename()),
                'url'      => htmlspecialchars($this('url')->to($file->getPathname()), ENT_QUOTES),
                'writable' => $mode == 'w'
            );

            if (!$isDir = $file->isDir()) {
                $info = array_merge($info, array(
                    'size'         => $this->formatFileSize($file->getSize()),
                    'lastmodified' => $this('dates')->format($file->getMTime(), 'd.m.y H:m')
                ));
            }

            $data[$isDir ? 'folders' : 'files'][] = $info;
        }



        return $this('response')->json($data);
    }

    /**
     * @Request({"name"})
     */
    public function createFolderAction($name)
    {
        if (!$path = $this->getPath($name)) {
            return $this->error(__('Invalid path.'));
        }

        if ('w' !== $this->getMode(dirname($path))) {
            return $this->error(__('Permission denied.'));
        }

        try {

            $this('file')->makeDir($path);

            return $this->success(__('Directory created.'));

        } catch(\Exception $e) {
            return $this->error(__('Unable to create directory.'));
        }
    }

    /**
     * @Request({"oldname", "newname"})
     */
    public function renameAction($oldname, $newname)
    {
        if (!$source = $this->getPath($oldname) or !file_exists($source) or !$target = $this->getPath($newname) or file_exists($target)) {
            return $this->error(__('Invalid path.'));
        }

        if ('w' !== $this->getMode($source) or 'w' !== $this->getMode(dirname($target))) {
            return $this->error(__('Permission denied.'));
        }

        try {

            $this('file')->rename($source, $target);

            return $this->success(__('Renamed.'));

        } catch(\Exception $e) {
            return $this->error(__('Unable to rename.'));
        }
    }

    /**
     * @Request({"names": "array"})
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

                $this('file')->delete($path);

            } catch(\Exception $e) {
                return $this->error(__('Unable to remove.'));
            }
        }

        return $this->success(__('Removed selected.'));
    }

    public function uploadAction()
    {
        try {
            if (!$path = $this->getPath() or !is_dir($path)) {
                return $this->error(__('Invalid path.'));
            }

            $files = $this('request')->files->get('files');

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
        $mode = $this('events')->trigger('system.finder', new FileAccessEvent)->mode($path);

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

      $sizes = array(__('%d Bytes'), __('%d  KB'), __('%d  MB'), __('%d  GB'), __('%d TB'), __('%d PB'), __('%d EB'), __('%d ZB'), __('%d YB'));
      $size  = round($size/pow(1024, ($i = floor(log($size, 1024)))), 2);
      return sprintf($sizes[$i], $size);
    }

    protected function getPath($path = '')
    {
        $root = $this('path');
        $path = $this->normalizePath($root.$this('request')->get('root').'/'.$this('request')->get('path').'/'.$path);

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
        $path   = str_replace(array('\\', '//'), '/', $path);
        $prefix = preg_match('|^(?P<prefix>([a-zA-Z]+:)?//?)|', $path, $matches) ? $matches['prefix'] : '';
        $path   = substr($path, strlen($prefix));
        $parts  = array_filter(explode('/', $path), 'strlen');
        $tokens = array();

        foreach ($parts as $part) {
            if ('..' === $part) {
                array_pop($tokens);
            } elseif ('.' !== $part) {
                array_push($tokens, $part);
            }
        }

        return $prefix . implode('/', $tokens);
    }

    protected function success($message) {
        return $this('response')->json(compact('message'));
    }

    protected function error($message) {
        return $this('response')->json(array('error' => true, 'message' => $message));
    }
}
