<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;

class FinderController extends Controller
{
    /**
     * @Request({"path"})
     */
    public function listAction($path)
    {
        if (!$this->verify()) {
            return $this('response')->create(__('Permission denied.'), 401);
        }

        if (!$root = $this->getPath() or !is_dir($root)) {
            return $this('response')->create(__('Invalid path.'), 403);
        }

        $data = array_fill_keys(array('folders', 'files'), array());

        foreach ($this('file')->find()->depth(0)->in($root) as $file) {

            $info = array(
                'name' => $file->getFilename(),
                'path' => $this->normalizePath($path.'/'.$file->getFilename()),
                'url'  => htmlspecialchars($this('url')->to($file->getPathname()), ENT_QUOTES),
            );

            if (!$isDir = $file->isDir()) {
                $info = array_merge($info, array(
                    'size'         => $this->formatFileSize($file->getSize()),
                    'lastmodified' => $this('dates')->format($file->getMTime(), 'd.m.y H:m')
                ));
            }

            $data[$isDir ? 'folders' : 'files'][] = $info;
        }

        return $this('response')->json(compact('data'));
    }

    /**
     * @Request({"name"})
     */
    public function createFolderAction($name)
    {
        if (!$this->verify(true)) {
            return $this('response')->create(__('Permission denied.'), 401);
        }

        if (!$path = $this->getPath($name)) {
            return $this('response')->create(__('Invalid path.'), 403);
        }

        try {

            $this('file')->makeDir($path);

            $result = array('message' => __('Directory created.'));

        } catch(\Exception $e) {
            return $this('response')->json(array('error' => true, 'message' => __('Unable to create directory.')));
        }

        return $this('response')->json($result);
    }

    /**
     * @Request({"oldname", "newname"})
     */
    public function renameAction($oldname, $newname)
    {
        if (!$this->verify(true)) {
            return $this('response')->create(__('Permission denied.'), 401);
        }

        if (!$source = $this->getPath($oldname) or !file_exists($source) or !$target = $this->getPath($newname) or file_exists($target)) {
            return $this('response')->create(__('Invalid path.'), 403);
        }

        try {

            $this('file')->rename($source, $target);

            $result = array('message' => __('Renamed.'));

        } catch(\Exception $e) {
            return $this('response')->json(array('error' => true, 'message' => __('Unable to rename.')));
        }

        return $this('response')->json($result);
    }

    /**
     * @Request({"names": "array"})
     */
    public function removeFilesAction($names)
    {
        if (!$this->verify(true)) {
            return $this('response')->create(__('Permission denied.'), 401);
        }

        $result = array('message' => __('Removed selected.'));

        foreach ($names as $name) {

            if (!$path = $this->getPath($name) or $path == $this->getPath() or !file_exists($path)) {
                return $this('response')->create(__('Invalid path.'), 403);
            }

            try {

                $this('file')->delete($path);

            } catch(\Exception $e) {
                return $this('response')->json(array('error' => true, 'message' => __('Unable to remove.')));
            }
        }

        return $this('response')->json($result);
    }

    public function uploadAction()
    {
        try {
            if (!$this->verify(true)) {
                throw new Exception(__('Permission denied.'));
            }

            if (!$path = $this->getPath() or !is_dir($path)) {
                throw new Exception(__('Invalid path.'));
            }

            $files = $this('request')->files->get('files');

            if (!$files) {
                throw new Exception(__('No files uploaded.'));
            }

            $result = array('message' => __('Upload complete.'));
            foreach ($files as $file) {

                if (!$file->isValid()) {
                    throw new Exception(__('Uploaded file invalid.'));
                }

                $file->move($path, $file->getClientOriginalName());

            }
        } catch(Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
        } catch(\Exception $e) {
            $result = array('error' => true, 'message' => __('Unable to upload'));
        }

        return $this('response')->json($result);
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
        $root = $this->normalizePath($this('path').'/'.$this('request')->get('root'));
        $path = $this->normalizePath($root.'/'.$this('request')->get('path').'/'.$path);

        return 0 === strpos($path, $root) ? $path : false;
    }

    protected function verify($write = false)
    {
        return $hash = $this('request')->get('hash')
            and $mode = $this('request')->get('mode')
            and (!$write or 'write' == $mode)
            and $this('finder')->verify($hash, $this('request')->get('root'), $mode);
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
}
