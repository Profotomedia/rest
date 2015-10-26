<?php

/**
* Craft REST by Dukt
 *
 * @package   Craft REST
 * @author    Benjamin David
 * @copyright Copyright (c) 2015, Dukt
 * @link      https://dukt.net/craft/rest/
 * @license   https://dukt.net/craft/rest/docs#license
 */

namespace Craft;

class Rest_AuthenticationModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'id'    => AttributeType::Number,
            'tokenId' => AttributeType::Number,
            'authenticationHandle' => AttributeType::String,
            'scopes' => array(AttributeType::Mixed),
            'customScopes' => array(AttributeType::Mixed),
        );
    }

    public function getAllScopes()
    {
        $allScopes = array_merge($this->scopes, $this->customScopes);

        return $allScopes;
    }

    public function getOAuthProvider()
    {
        $oauthProvider = craft()->oauth->getProvider($this->authenticationHandle);

        if(!$oauthProvider)
        {
            $api = craft()->rest_apis->getApi($this->authenticationHandle);

            if($api)
            {
                $oauthProviderHandle = $api->getOAuthProviderHandle();

                $oauthProvider = craft()->oauth->getProvider($oauthProviderHandle);
            }
        }

        return $oauthProvider;
    }

    public function getToken()
    {
        craft()->rest->checkRequirements();

        return craft()->oauth->getTokenById($this->tokenId);
    }
}