<?php

namespace DevGroup\Multilingual\LanguageEvents;

use DevGroup\Multilingual\models\Language;
use yii\helpers\ArrayHelper;

class GettingLanguageByUrl implements GettingLanguage, AfterGettingLanguage
{
    public static function gettingLanguage(languageEvent $event)
    {
        if ($event->currentLanguageId === false) {
            $path = explode('/', \Yii::$app->request->pathInfo);
            $folder = array_shift($path);
            $languages = $event->languages;
            $domain = $event->domain;
            /** @var bool|Language $languageMatched */
            foreach ($languages as $language) {
                $matchedDomain = $language->domain === $domain;
                if (empty($language->folder)) {
                    $matchedFolder = $matchedDomain;
                } else {
                    $matchedFolder = $language->folder === $folder;
                }
                if ($matchedDomain && $matchedFolder) {
                    $event->currentLanguageId = $language->id;
                    if (!empty($language->folder) && $language->folder === $event->request->pathInfo) {
                        $event->needRedirect = true;
                    }
                    $event->resultClass = self::class;
                    return;
                }
            }
            $event->needRedirect = true;
        }
    }

    public static function afterGettingLanguage(languageEvent $event)
    {
        $languageMatched = $event->languages[$event->multilingual->language_id];
        if ($event->needRedirect === true && $languageMatched->folder) {
            if ($languageMatched->folder === $event->request->pathInfo) {
                $event->redirectUrl = '/' . $event->request->pathInfo . '/';
                $event->redirectCode = 301;
            } else {
                // no matched language and not in excluded routes - should redirect to user's regional domain with 302
                \Yii::$app->urlManager->forceHostInUrl = true;
                $event->redirectUrl = \Yii::$app->urlManager->createUrl(
                    ArrayHelper::merge(
                        [$event->request->pathInfo],
                        \Yii::$app->request->get(),
                        ['language_id' => $event->multilingual->language_id]
                    )
                );
                \Yii::$app->urlManager->forceHostInUrl = false;
                $event->redirectCode = 302;
            }
        }

        if (!empty($languageMatched->domain) && $languageMatched->domain !== $event->domain) {
            // no matched language and not in excluded routes - should redirect to user's regional domain with 302
            \Yii::$app->urlManager->forceHostInUrl = true;
            $event->redirectUrl = $event->sender->createUrl(
                ArrayHelper::merge(
                    [$event->request->pathInfo],
                    \Yii::$app->request->get(),
                    ['language_id' => $event->multilingual->language_id]
                )
            );
            $event->redirectCode = 302;
        }
    }
}
