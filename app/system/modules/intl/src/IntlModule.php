<?php

namespace Pagekit\Intl;

use Pagekit\Application as App;
use Pagekit\Intl\Loader\ArrayLoader;
use Pagekit\Intl\Loader\MoFileLoader;
use Pagekit\Intl\Loader\PhpFileLoader;
use Pagekit\Intl\Loader\PoFileLoader;
use Pagekit\Module\Module;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

class IntlModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app['translator'] = function () {

            $translator = new Translator($this->getLocale());
            $translator->addLoader('php', new PhpFileLoader());
            $translator->addLoader('mo', new MoFileLoader());
            $translator->addLoader('po', new PoFileLoader);
            $translator->addLoader('array', new ArrayLoader);

            $this->loadLocale($this->getLocale(), $translator);

            return $translator;
        };

        $app->extend('view', function ($view) {
            return $view->addGlobal('intl', $this);
        });

        require __DIR__.'/../functions.php';
    }

    /**
     * Gets the current locale id.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->config('locale');
    }

    /**
     * Sets the current locale id.
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->config['locale'] = $locale;
    }

    /**
     * Gets the current locale tag.
     *
     * @return string
     */
    public function getLocaleTag()
    {
        return str_replace('_', '-', $this->getLocale());
    }

    /**
     * Gets the system's available languages.
     *
     * @return array
     */
    public function getAvailableLanguages()
    {
        $languages = $this->getLanguages();
        $territories = $this->getTerritories();

        $available = [];
        foreach (Finder::create()->directories()->depth(0)->in('app/system/languages')->name('/^[a-z]{2,3}(_[A-Z]{2})?$/') as $dir) {

            $id = $dir->getFilename();
            list($lang, $country) = explode('_', $id);

            if (isset($languages[$lang])) {
                $available[$id] = $languages[$lang];

                if (isset($territories[$country])) {
                    $available[$id] .= ' - '.$territories[$country];
                }

            }
        }

        asort($available);

        return $available;
    }

    /**
     * Gets the languages list.
     *
     * @param  string $locale
     * @return array|null
     */
    public function getLanguages($locale = null)
    {
        return $this->getData('languages', $locale);
    }

    /**
     * Gets the territories list.
     *
     * @param  string $locale
     * @return array|null
     */
    public function getTerritories($locale = null)
    {
        return $this->getData('territories', $locale);
    }

    /**
     * Gets the locales formats data.
     *
     * @param  string $locale
     * @return array|null
     */
    public function getFormats($locale = null)
    {
        return $this->getData('formats', $locale);
    }

    /**
     * Loads language files.
     *
     * @param string              $locale
     * @param TranslatorInterface $translator
     */
    public function loadLocale($locale, TranslatorInterface $translator = null)
    {
        $translator = $translator ?: App::translator();

        foreach (App::module() as $module) {

            $domains = [];
            $path = $module->get('path').($module->get('languages') ?: '/languages');
            $files = glob("{$path}/{$locale}/*.php") ?: [];

            foreach ($files as $file) {

                $format = substr(strrchr($file, '.'), 1);
                $domain = basename($file, '.'.$format);

                if (in_array($domain, $domains)) {
                    continue;
                }

                $domains[] = $domain;

                $translator->addResource($format, $file, $locale, $domain);
            }
        }
    }

    /**
     * @param  string      $name
     * @param  string|null $locale
     * @return mixed|null
     */
    protected function getData($name, $locale = null)
    {
        $locale = $locale ?: $this->getLocale();

        if (App::file()->exists($file = "app/system/languages/{$locale}/{$name}.json")) {
            return json_decode(file_get_contents(App::file()->getPath($file)), true);
        }

        return null;
    }
}
