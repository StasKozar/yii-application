<?php
namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\Widget;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Url;
use yii\web\Cookie;

class languageSwitcher extends Widget
{
    public $languages = [
        'en-US' => 'English',
        'ru-RU' => 'Russian',
    ];

    public function init()
    {
        if(php_sapi_name() === 'cli')
        {
            return true;
        }

        parent::init();

        $cookies = Yii::$app->response->cookies;
        $languageNew = Yii::$app->request->get('language');

        if($languageNew)
        {
            if(isset($this->languages[$languageNew]))
            {
                Yii::$app->language = $languageNew;
                $cookies->add(new Cookie([
                    'name' => 'language',
                    'value' => $languageNew,
                    'expire' => time() + 3600,
                ]));
            }
        }
        elseif(Yii::$app->request->cookies->has('language'))
        {
            Yii::$app->language = Yii::$app->request->cookies->getValue('language');
        }

    }

    public function run(){

        $languages = $this->languages;
        $current = $languages[Yii::$app->language];
        unset($languages[Yii::$app->language]);

        $items = [];
        foreach($languages as $code => $language)
        {
            $temp = [];
            $temp['label'] = Yii::t('app', $language);
            $temp['url'] = Url::current(['language' => $code]);
            array_push($items, $temp);
        }

        echo ButtonDropdown::widget([
            'label' => $current,
            'dropdown' => [
                'items' => $items,
            ],
        ]);
    }

}