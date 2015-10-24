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

class Rest_AuthenticationsService extends BaseApplicationComponent
{
    /**
     * Get Authentication By Handle
     */
    public function getAuthenticationByHandle($oauthProviderHandle)
    {
        $record = Rest_AuthenticationRecord::model()->find(
            array(
                'condition' => 'oauthProviderHandle=:oauthProviderHandle',
                'params' => array(':oauthProviderHandle' => $oauthProviderHandle)
            )
        );

        if($record)
        {
            return Rest_AuthenticationModel::populateModel($record);
        }
    }

    /**
     * Get Authentication By ID
     */
    public function getAuthenticationById($id)
    {
        $record = Rest_AuthenticationRecord::model()->findByPk($id);

        if($record)
        {
            return Rest_AuthenticationModel::populateModel($record);
        }
    }

    /**
     * Save Authentication Token
     */
    public function saveAuthenticationToken($providerHandle, $token)
    {
        craft()->rest->checkRequirements();

        // get authentication

        $authentication = $this->getAuthenticationByHandle($providerHandle);

        if(!$authentication)
        {
            $authentication = new Rest_AuthenticationModel;
        }


        // save token

        $token->id = $authentication->tokenId;
        $token->providerHandle = $providerHandle;
        $token->pluginHandle = 'rest';

        craft()->oauth->saveToken($token);


        // save authentication

        $authentication->oauthProviderHandle = $providerHandle;
        $authentication->tokenId = $token->id;

        $this->saveAuthentication($authentication);
    }

    /**
     * Delete Authentication By ID
     */
    public function deleteAuthenticationById($id)
    {
        craft()->rest->checkRequirements();

        $authentication = $this->getAuthenticationById($id);


        // delete token

        if($authentication->tokenId)
        {
            $token = craft()->oauth->getTokenById($authentication->tokenId);

            if($token)
            {
                craft()->oauth->deleteToken($token);
            }
        }

        return Rest_AuthenticationRecord::model()->deleteByPk($id);
    }

    /**
     * Get Authentications
     */
    public function getAuthentications()
    {
        $records = Rest_AuthenticationRecord::model()->findAll(array('order' => 't.id'));
        return Rest_AuthenticationModel::populateModels($records, 'id');
    }

    /**
     * Save Authentication
     */
    public function saveAuthentication(Rest_AuthenticationModel $model)
    {
        $record = Rest_AuthenticationRecord::model()->findByPk($model->id);

        if(!$record)
        {
            $record = new Rest_AuthenticationRecord;
        }

        $record->oauthProviderHandle = $model->oauthProviderHandle;
        $record->tokenId = $model->tokenId;

        if($record->save())
        {
            $model->setAttribute('id', $record->getAttribute('id'));
            return true;
        }
        else
        {
            $model->addErrors($record->getErrors());
            return false;
        }
    }
}