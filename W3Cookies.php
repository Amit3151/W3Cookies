<?php

namespace w3modules\w3cookies;

use Craft;
use yii\base\Module;
use craft\web\twig\variables\CraftVariable;
use yii\base\Event;
use w3modules\w3cookies\services\CookiesService;
use w3modules\w3cookies\variables\CookiesVariable;

/**
 * W3Cookies Module
 *
 * This module provides lightweight cookie consent handling for Craft CMS 5.
 * It exposes:
 *  - Frontend controller actions for accepting / declining cookies
 *  - A service for reading & writing consent cookies
 *  - A Twig variable (`craft.w3cookies`) for template usage
 *
 * Designed as a replacement for discontinued Craft 2/3 cookie plugins.
 *
 * @author  https://github.com/Amit3151
 * @since   1.0.0
 */
class W3Cookies extends Module
{
    /**
     * Static reference to the module instance
     *
     * Allows access via:
     *   W3Cookies::$instance
     *
     * @var self
     */
    public static W3Cookies $instance;

    /**
     * Initializes the module
     *
     * - Sets module alias
     * - Registers controllers namespace
     * - Registers services
     * - Registers Twig variables
     */
    public function init(): void
    {
        // Always call parent init first in Craft 5 modules
        parent::init();

        // Store module instance for global access
        self::$instance = $this;

        // Define module alias for internal path resolution
        Craft::setAlias('@w3modules/w3cookies', __DIR__);

        // Define where Craft should look for controller classes
        // This enables URLs like:
        // /actions/w3cookies/consent/accept
        $this->controllerNamespace = 'w3modules\\w3cookies\\controllers';

        /**
         * Register module services
         *
         * Accessible via:
         *   W3Cookies::$instance->cookies
         */
        $this->setComponents([
            'cookies' => CookiesService::class,
        ]);

        /**
         * Register Twig variables
         *
         * Exposes:
         *   craft.w3cookies.accepted()
         *   craft.w3cookies.declined()
         *   craft.w3cookies.doNotTrack()
         */
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $event->sender->set('w3cookies', CookiesVariable::class);
            }
        );
    }
}
