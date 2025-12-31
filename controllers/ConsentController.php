<?php

namespace w3modules\w3cookies\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use w3modules\w3cookies\W3Cookies;

/**
 * ConsentController
 *
 * Handles frontend cookie consent actions:
 *  - Accept cookies
 *  - Decline cookies
 *  - Reset cookie preference
 *
 * These actions are designed to be accessed from the frontend via POST requests:
 *   /actions/w3cookies/consent/accept
 *   /actions/w3cookies/consent/decline
 *   /actions/w3cookies/consent/reset
 *
 * All actions support both:
 *  - AJAX requests (JSON response)
 *  - Standard form submissions (redirect back)
 *
 * @since 1.0.0
 */
class ConsentController extends Controller
{
    /**
     * Allow anonymous access to all actions in this controller.
     *
     * Cookie consent must be available to unauthenticated users
     * in order to comply with privacy regulations.
     *
     * Craft 5 requires the exact type declaration below.
     *
     * @var array|int|bool
     */
    public array|int|bool $allowAnonymous = true;

    /**
     * Accept cookie consent.
     *
     * Sets the consent cookie to "accepted".
     * Does not remove any existing cookies.
     *
     * POST /actions/w3cookies/consent/accept
     */
    public function actionAccept(): Response
    {
        // Intentionally not enforcing POST here to allow flexible usage
        // Uncomment the following line if strict POST-only behavior is desired:
        // $this->requirePostRequest();

        W3Cookies::$instance->cookies->setConsent(true);

        return $this->respond();
    }

    /**
     * Decline cookie consent.
     *
     * - Removes all non-essential cookies
     * - Stores declined preference
     *
     * POST /actions/w3cookies/consent/decline
     */
    public function actionDecline(): Response
    {
        $this->requirePostRequest();

        W3Cookies::$instance->cookies->unsetNonEssentialCookies();
        W3Cookies::$instance->cookies->setConsent(false);

        return $this->respond();
    }

    /**
     * Reset cookie preference.
     *
     * - Removes all non-essential cookies
     * - Does NOT persist consent preference
     *
     * Useful for "change cookie settings" functionality.
     *
     * POST /actions/w3cookies/consent/reset
     */
    public function actionReset(): Response
    {
        $this->requirePostRequest();

        W3Cookies::$instance->cookies->unsetNonEssentialCookies(false);

        return $this->respond();
    }

    /**
     * Standard response handler.
     *
     * - Returns JSON for AJAX requests
     * - Redirects back to referrer for normal requests
     *
     * @return Response
     */
    private function respond(): Response
    {
        if (Craft::$app->getRequest()->getIsAjax()) {
            return $this->asJson(['ok' => 1]);
        }

        return $this->redirect(
            Craft::$app->getRequest()->getReferrer()
            ?? Craft::$app->getHomeUrl()
        );
    }
}
