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
        return [
            'af' => [__('Afrikaans'), 'Afrikaans'],
            'am' => [__('Amharic'), 'አማርኛ'],
            'ar' => [__('Arabic'), 'العربية', self::DIRECTION_RTL],
            'ast' => [__('Asturian'), 'Asturianu'],
            'az' => [__('Azerbaijani'), 'Azərbaycanca'],
            'be' => [__('Belarusian'), 'Беларуская'],
            'bg' => [__('Bulgarian'), 'Български'],
            'bn' => [__('Bengali'), 'বাংলা'],
            'bo' => [__('Tibetan'), 'བོད་སྐད་'],
            'bs' => [__('Bosnian'), 'Bosanski'],
            'ca' => [__('Catalan'), 'Català'],
            'cs' => [__('Czech'), 'Čeština'],
            'cy' => [__('Welsh'), 'Cymraeg'],
            'da' => [__('Danish'), 'Dansk'],
            'de' => [__('German'), 'Deutsch'],
            'dz' => [__('Dzongkha'), 'རྫོང་ཁ'],
            'el' => [__('Greek'), 'Ελληνικά'],
            'en' => [__('English'), 'English'],
            'eo' => [__('Esperanto'), 'Esperanto'],
            'es' => [__('Spanish'), 'Español'],
            'et' => [__('Estonian'), 'Eesti'],
            'eu' => [__('Basque'), 'Euskera'],
            'fa' => [__('Persian, Farsi'), 'فارسی', self::DIRECTION_RTL],
            'fi' => [__('Finnish'), 'Suomi'],
            'fil' => [__('Filipino'), 'Filipino'],
            'fo' => [__('Faeroese'), 'Føroyskt'],
            'fr' => [__('French'), 'Français'],
            'fy' => [__('Frisian, Western'), 'Frysk'],
            'ga' => [__('Irish'), 'Gaeilge'],
            'gd' => [__('Scots Gaelic'), 'Gàidhlig'],
            'gl' => [__('Galician'), 'Galego'],
            'gsw-berne' => [__('Swiss German'), 'Schwyzerdütsch'],
            'gu' => [__('Gujarati'), 'ગુજરાતી'],
            'he' => [__('Hebrew'), 'עברית', self::DIRECTION_RTL],
            'hi' => [__('Hindi'), 'हिन्दी'],
            'hr' => [__('Croatian'), 'Hrvatski'],
            'ht' => [__('Haitian Creole'), 'Kreyòl ayisyen'],
            'hu' => [__('Hungarian'), 'Magyar'],
            'hy' => [__('Armenian'), 'Հայերեն'],
            'id' => [__('Indonesian'), 'Bahasa Indonesia'],
            'is' => [__('Icelandic'), 'Íslenska'],
            'it' => [__('Italian'), 'Italiano'],
            'ja' => [__('Japanese'), '日本語'],
            'jv' => [__('Javanese'), 'Basa Java'],
            'ka' => [__('Georgian'), 'ქართული ენა'],
            'kk' => [__('Kazakh'), 'Қазақ'],
            'km' => [__('Khmer'), 'ភាសាខ្មែរ'],
            'kn' => [__('Kannada'), 'ಕನ್ನಡ'],
            'ko' => [__('Korean'), '한국어'],
            'ku' => [__('Kurdish'), 'Kurdî'],
            'ky' => [__('Kyrgyz'), 'Кыргызча'],
            'lo' => [__('Lao'), 'ພາສາລາວ'],
            'lt' => [__('Lithuanian'), 'Lietuvių'],
            'lv' => [__('Latvian'), 'Latviešu'],
            'mg' => [__('Malagasy'), 'Malagasy'],
            'mk' => [__('Macedonian'), 'Македонски'],
            'ml' => [__('Malayalam'), 'മലയാളം'],
            'mn' => [__('Mongolian'), 'монгол'],
            'mr' => [__('Marathi'), 'मराठी'],
            'ms' => [__('Bahasa Malaysia'), 'بهاس ملايو'],
            'my' => [__('Burmese'), 'ဗမာစကား'],
            'nb' => [__('Norwegian Bokmål'), 'Bokmål'],
            'ne' => [__('Nepali'), 'नेपाली'],
            'nl' => [__('Dutch'), 'Nederlands'],
            'nn' => [__('Norwegian Nynorsk'), 'Nynorsk'],
            'oc' => [__('Occitan'), 'Occitan'],
            'pa' => [__('Punjabi'), 'ਪੰਜਾਬੀ'],
            'pl' => [__('Polish'), 'Polski'],
            'pt-br' => [__('Portuguese, Brazil'), 'Português, Brasil'],
            'pt-pt' => [__('Portuguese, Portugal'), 'Português, Portugal'],
            'ro' => [__('Romanian'), 'Română'],
            'ru' => [__('Russian'), 'Русский'],
            'sco' => [__('Scots'), 'Scots'],
            'se' => [__('Northern Sami'), 'Sámi'],
            'si' => [__('Sinhala'), 'සිංහල'],
            'sk' => [__('Slovak'), 'Slovenčina'],
            'sl' => [__('Slovenian'), 'Slovenščina'],
            'sq' => [__('Albanian'), 'Shqip'],
            'sr' => [__('Serbian'), 'Српски'],
            'sv' => [__('Swedish'), 'Svenska'],
            'sw' => [__('Swahili'), 'Kiswahili'],
            'ta' => [__('Tamil'), 'தமிழ்'],
            'ta-lk' => [__('Tamil, Sri Lanka'), 'தமிழ், இலங்கை'],
            'te' => [__('Telugu'), 'తెలుగు'],
            'th' => [__('Thai'), 'ภาษาไทย'],
            'tr' => [__('Turkish'), 'Türkçe'],
            'tyv' => [__('Tuvan'), 'Тыва дыл'],
            'ug' => [__('Uyghur'), 'Уйғур'],
            'uk' => [__('Ukrainian'), 'Українська'],
            'ur' => [__('Urdu'), 'اردو', self::DIRECTION_RTL],
            'vi' => [__('Vietnamese'), 'Tiếng Việt'],
            'xx-lolspeak' => [__('Lolspeak'), 'Lolspeak'],
            'zh-hans' => [__('Chinese, Simplified'), '简体中文'],
            'zh-hant' => [__('Chinese, Traditional'), '繁體中文'],
        ];
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