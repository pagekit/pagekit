<?php

namespace Pagekit\Installer\Controller;

use Pagekit\Application as App;
use Pagekit\Installer\SelfUpdater;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * @Access("system: software updates", admin=true)
 */
class UpdateController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Update'),
                'name' => 'installer:views/update.php'
            ],
            '$data' => [
                'api' => App::get('system.api'),
                'version' => App::version(),
                'channel' => 'stable'
            ]
        ];
    }

    /**
     * @Request({"url": "string"}, csrf=true)
     */
    public function downloadAction($url)
    {
        $file = tempnam(App::get('path.temp'), 'update_');
        App::session()->set('system.update', $file);

        if (!file_put_contents($file, @fopen($url, 'r'))) {
            App::abort(500, 'Download failed or path not writable.');
        }

        return [];
    }

    /**
     * @Request(csrf=true)
     */
    public function updateAction()
    {
        if (!$file = App::session()->get('system.update')) {
            App::abort(400, __('You may not call this step directly.'));
        }
        App::session()->remove('system.update');

        return App::response()->stream(function () use ($file) {
            $output = new StreamOutput(fopen('php://output', 'w'));
            try {

                if (!file_exists($file) || !is_file($file)) {
                    throw new \RuntimeException('File does not exist.');
                }

                $updater = new SelfUpdater($output);
                $updater->update($file);

            } catch (\Exception $e) {
                $output->writeln(sprintf("\n<error>%s</error>", $e->getMessage()));
                $output->write("status=error");
            }

        });
    }
}
