<?php
/**
 * Account Object
 *
 * @package surveygizmo-api-php
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;
use spacenate\SurveyGizmoApiWrapper;
/**
 * Account class provides access to the Account object
 *
 * @package surveygizmo-api-php
 */
class Account
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }
	
    /**
     * List details about an account
     *
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getList()
    {
        return $this->master->call('account/', 'GET');
    }
}