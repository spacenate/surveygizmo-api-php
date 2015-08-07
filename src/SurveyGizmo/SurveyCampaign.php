<?php
/**
 * SurveyCampaign Object
 *
 * @package surveygizmo-api-php
 * @version 0.3.5
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;
use spacenate\SurveyGizmoApiWrapper;
/**
 * SurveyCampaign class provides access to the SurveyCampaign sub-object
 *
 * @package surveygizmo-api-php
 */
class SurveyCampaign
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }
	
    /**
     * List all of the campaigns in a survey
     *
     * @param string|int $surveyId Id of survey to get campaigns for
     * @param string|int $page (optional) page of results to fetch
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getList( $surveyId, $page = 1 )
    {
        $page = ($page) ? $page : 1;
        $_params = http_build_query(array("page" => $page));
        return $this->master->call('survey/' . $surveyId . '/surveycampaign/', 'GET', $_params);
    }
	
    /**
     * Get a specific campaign in a survey
     *
     * @param string|int $surveyId Id of survey to get campaign from
     * @param string|int $campaignId Id of campaign to get
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getCampaign( $surveyId, $campaignId )
    {
        return $this->master->call('survey/' . $surveyId . '/surveycampaign/' . $campaignId, 'GET');
    }
	
    /**
     * Create a new campaign
     *
     * @param string|int $surveyId Id of survey to create campaign in
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
	 */
    public function createCampaign( $surveyId, $parameters = array() )
    {
		$allowed_params = array("type", "name", "language", "status", "slug", "subtype", "privatename", "tokenvariables");
        $_params = http_build_query($master->getValidParameters($parameters, $allowed_params));
        return $this->master->call('survey/' . $surveyId . '/surveycampaign/', 'PUT', $_params);
    }
	
    /**
     * Update or copy a specified campaign
     *
     * @param string|int $surveyId Id of survey containing campaign
     * @param string|int $campaignId Id of campaign to update or copy
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function updateCampaign( $surveyId, $campaignId, $parameters = array() )
    {
		$allowed_params = array("name", "language", "status", "slug", "subtype", "privatename", "tokenvariables", "copy");
        $_params = http_build_query($master->getValidParameters($parameters, $allowed_params));
        return $this->master->call('survey/' . $surveyId . '/surveycampaign/' . $campaignId, 'POST', $_params);
    }
	
    /**
     * Delete a specified campaign
     *
     * @param string|int $surveyId Id of survey containing campaign
     * @param string|int $campaignId Id of campaign to delete
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function deleteCampaign( $surveyId, $campaignId )
    {
        return $this->master->call('survey/' . $surveyId . '/surveycampaign/' . $campaignId, 'DELETE');
    }
}