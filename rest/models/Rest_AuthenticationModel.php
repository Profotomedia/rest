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
            'oauthProviderHandle' => AttributeType::String,
            'scopes' => array(AttributeType::Mixed),
        );
    }

    public function getOAuthProvider()
    {
        return craft()->oauth->getProvider($this->oauthProviderHandle);
    }

    public function getToken()
    {
        craft()->rest->checkRequirements();

        return craft()->oauth->getTokenById($this->tokenId);
    }
}