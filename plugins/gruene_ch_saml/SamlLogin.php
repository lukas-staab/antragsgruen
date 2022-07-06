<?php

namespace app\plugins\gruene_ch_saml;

use app\components\LoginProviderInterface;
use app\components\RequestContext;
use app\models\db\User;
use app\models\settings\AntragsgruenApp;
use SimpleSAML\Auth\Simple;

class SamlLogin implements LoginProviderInterface
{
    const PARAM_EMAIL = 'email';
    const PARAM_USERNAME = 'username';
    const PARAM_GIVEN_NAME = 'first_name';
    const PARAM_FAMILY_NAME = 'last_name';

    public function getId(): string
    {
        return Module::LOGIN_KEY;
    }

    public function getName(): string
    {
        return 'Grüne / Les Vert-E-S';
    }

    public function renderLoginForm(string $backUrl): string
    {
        return \Yii::$app->controller->renderPartial('@app/plugins/gruene_ch_saml/views/login', [
            'backUrl' => $backUrl
        ]);
    }

    /**
     * @throws \Exception
     */
    private function getOrCreateUser(array $params): User
    {
        $email = $params[static::PARAM_EMAIL][0];
        $givenname = (isset($params[static::PARAM_GIVEN_NAME]) ? $params[static::PARAM_GIVEN_NAME][0] : '');
        $familyname = (isset($params[static::PARAM_FAMILY_NAME]) ? $params[static::PARAM_FAMILY_NAME][0] : '');
        $username = $params[static::PARAM_USERNAME][0];
        $auth = Module::AUTH_KEY_USERS . ':' . $username;

        /** @var User|null $user */
        $user = User::findOne(['auth' => $auth]);
        if (!$user) {
            $user = new User();
        }

        $user->name = $givenname . ' ' . $familyname;
        $user->nameGiven = $givenname;
        $user->nameFamily = $familyname;
        $user->email = $email;
        $user->emailConfirmed = 1;
        $user->fixedData = 1;
        $user->auth = $auth;
        $user->status = User::STATUS_CONFIRMED;
        $user->organization = '';
        if (!$user->save()) {
            throw new \Exception('Could not create user');
        }

        return $user;
    }

    public function performLoginAndReturnUser(): User
    {
        $samlClient = new Simple('gruene-ch');

        $samlClient->requireAuth([]);
        if (!$samlClient->isAuthenticated()) {
            throw new \Exception('SimpleSaml: Something went wrong on requireAuth');
        }
        $params = $samlClient->getAttributes();

        $user = $this->getOrCreateUser($params);
        RequestContext::getUser()->login($user, AntragsgruenApp::getInstance()->autoLoginDuration);

        $user->dateLastLogin = date('Y-m-d H:i:s');
        $user->save();

        return $user;
    }

    public function userWasLoggedInWithProvider(?User $user): bool
    {
        if (!$user || !$user->auth) {
            return false;
        }
        $authParts = explode(':', $user->auth);

        return $authParts[0] === Module::AUTH_KEY_USERS;
    }

    public function logoutCurrentUserIfRelevant(): void
    {
        $user = User::getCurrentUser();
        if (!$this->userWasLoggedInWithProvider($user)) {
            return;
        }

        $samlClient = new Simple('gruene-ch');
        if ($samlClient->isAuthenticated()) {
            $samlClient->logout();
        }
    }
}
