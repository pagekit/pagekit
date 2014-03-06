<?php

namespace Pagekit\System\Helper;

class LanguageHelper
{
    const DIRECTION_RTL = 1;

    /**
     * array
     */
    protected $languages;

    public static function getStandardList()
    {
        return array(
            'af' => array(__('Afrikaans'), 'Afrikaans'),
            'am' => array(__('Amharic'), 'አማርኛ'),
            'ar' => array(__('Arabic'), 'العربية', self::DIRECTION_RTL),
            'ast' => array(__('Asturian'), 'Asturianu'),
            'az' => array(__('Azerbaijani'), 'Azərbaycanca'),
            'be' => array(__('Belarusian'), 'Беларуская'),
            'bg' => array(__('Bulgarian'), 'Български'),
            'bn' => array(__('Bengali'), 'বাংলা'),
            'bo' => array(__('Tibetan'), 'བོད་སྐད་'),
            'bs' => array(__('Bosnian'), 'Bosanski'),
            'ca' => array(__('Catalan'), 'Català'),
            'cs' => array(__('Czech'), 'Čeština'),
            'cy' => array(__('Welsh'), 'Cymraeg'),
            'da' => array(__('Danish'), 'Dansk'),
            'de' => array(__('German'), 'Deutsch'),
            'dz' => array(__('Dzongkha'), 'རྫོང་ཁ'),
            'el' => array(__('Greek'), 'Ελληνικά'),
            'en' => array(__('English'), 'English'),
            'eo' => array(__('Esperanto'), 'Esperanto'),
            'es' => array(__('Spanish'), 'Español'),
            'et' => array(__('Estonian'), 'Eesti'),
            'eu' => array(__('Basque'), 'Euskera'),
            'fa' => array(__('Persian, Farsi'), 'فارسی', self::DIRECTION_RTL),
            'fi' => array(__('Finnish'), 'Suomi'),
            'fil' => array(__('Filipino'), 'Filipino'),
            'fo' => array(__('Faeroese'), 'Føroyskt'),
            'fr' => array(__('French'), 'Français'),
            'fy' => array(__('Frisian, Western'), 'Frysk'),
            'ga' => array(__('Irish'), 'Gaeilge'),
            'gd' => array(__('Scots Gaelic'), 'Gàidhlig'),
            'gl' => array(__('Galician'), 'Galego'),
            'gsw-berne' => array(__('Swiss German'), 'Schwyzerdütsch'),
            'gu' => array(__('Gujarati'), 'ગુજરાતી'),
            'he' => array(__('Hebrew'), 'עברית', self::DIRECTION_RTL),
            'hi' => array(__('Hindi'), 'हिन्दी'),
            'hr' => array(__('Croatian'), 'Hrvatski'),
            'ht' => array(__('Haitian Creole'), 'Kreyòl ayisyen'),
            'hu' => array(__('Hungarian'), 'Magyar'),
            'hy' => array(__('Armenian'), 'Հայերեն'),
            'id' => array(__('Indonesian'), 'Bahasa Indonesia'),
            'is' => array(__('Icelandic'), 'Íslenska'),
            'it' => array(__('Italian'), 'Italiano'),
            'ja' => array(__('Japanese'), '日本語'),
            'jv' => array(__('Javanese'), 'Basa Java'),
            'ka' => array(__('Georgian'), 'ქართული ენა'),
            'kk' => array(__('Kazakh'), 'Қазақ'),
            'km' => array(__('Khmer'), 'ភាសាខ្មែរ'),
            'kn' => array(__('Kannada'), 'ಕನ್ನಡ'),
            'ko' => array(__('Korean'), '한국어'),
            'ku' => array(__('Kurdish'), 'Kurdî'),
            'ky' => array(__('Kyrgyz'), 'Кыргызча'),
            'lo' => array(__('Lao'), 'ພາສາລາວ'),
            'lt' => array(__('Lithuanian'), 'Lietuvių'),
            'lv' => array(__('Latvian'), 'Latviešu'),
            'mg' => array(__('Malagasy'), 'Malagasy'),
            'mk' => array(__('Macedonian'), 'Македонски'),
            'ml' => array(__('Malayalam'), 'മലയാളം'),
            'mn' => array(__('Mongolian'), 'монгол'),
            'mr' => array(__('Marathi'), 'मराठी'),
            'ms' => array(__('Bahasa Malaysia'), 'بهاس ملايو'),
            'my' => array(__('Burmese'), 'ဗမာစကား'),
            'nb' => array(__('Norwegian Bokmål'), 'Bokmål'),
            'ne' => array(__('Nepali'), 'नेपाली'),
            'nl' => array(__('Dutch'), 'Nederlands'),
            'nn' => array(__('Norwegian Nynorsk'), 'Nynorsk'),
            'oc' => array(__('Occitan'), 'Occitan'),
            'pa' => array(__('Punjabi'), 'ਪੰਜਾਬੀ'),
            'pl' => array(__('Polish'), 'Polski'),
            'pt-br' => array(__('Portuguese, Brazil'), 'Português, Brasil'),
            'pt-pt' => array(__('Portuguese, Portugal'), 'Português, Portugal'),
            'ro' => array(__('Romanian'), 'Română'),
            'ru' => array(__('Russian'), 'Русский'),
            'sco' => array(__('Scots'), 'Scots'),
            'se' => array(__('Northern Sami'), 'Sámi'),
            'si' => array(__('Sinhala'), 'සිංහල'),
            'sk' => array(__('Slovak'), 'Slovenčina'),
            'sl' => array(__('Slovenian'), 'Slovenščina'),
            'sq' => array(__('Albanian'), 'Shqip'),
            'sr' => array(__('Serbian'), 'Српски'),
            'sv' => array(__('Swedish'), 'Svenska'),
            'sw' => array(__('Swahili'), 'Kiswahili'),
            'ta' => array(__('Tamil'), 'தமிழ்'),
            'ta-lk' => array(__('Tamil, Sri Lanka'), 'தமிழ், இலங்கை'),
            'te' => array(__('Telugu'), 'తెలుగు'),
            'th' => array(__('Thai'), 'ภาษาไทย'),
            'tr' => array(__('Turkish'), 'Türkçe'),
            'tyv' => array(__('Tuvan'), 'Тыва дыл'),
            'ug' => array(__('Uyghur'), 'Уйғур'),
            'uk' => array(__('Ukrainian'), 'Українська'),
            'ur' => array(__('Urdu'), 'اردو', self::DIRECTION_RTL),
            'vi' => array(__('Vietnamese'), 'Tiếng Việt'),
            'xx-lolspeak' => array(__('Lolspeak'), 'Lolspeak'),
            'zh-hans' => array(__('Chinese, Simplified'), '简体中文'),
            'zh-hant' => array(__('Chinese, Traditional'), '繁體中文'),
        );
    }

    /**
     * Gets an array of language code => array(name, original name, direction).
     *
     * @return array
     */
    public function getList()
    {
        if (!isset($this->languages)) {
            $this->languages = static::getStandardList();
        }

        return $this->languages;
    }

    /**
     * Converts isocode to language name
     *
     * @param string $isoCode
     * @return string
     */
	public function isoToName($isoCode)
    {
        $this->getList();
        return array_key_exists($isoCode, $this->languages) ? $this->languages[$isoCode][0] : false;
	}
}