<?php
/**
 * Survey Object
 *
 * @package surveygizmo-api-php
 * @version 0.3.5
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;

/**
 * Survey class provides access to the Survey object
 *
 * @package surveygizmo-api-php
 */
class Survey
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }

    /**
     * List all the surveys in an account
     *
     * @param string|int $page (optional) page of results to fetch
     * @param string|int $limit (optional) number of results to fetch
     * @param array $filter (optional) one or more filter arrays
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getList( $page = 1, $limit = 50, $filter = array() )
    {
        $page = ($page) ? $page : 1;
        $limit = ($limit) ? $limit : 50;
        $_params = http_build_query(array("resultsperpage" => $limit, "page" => $page));
        if ($filter) $_params .= "&" . $this->master->getFilterString($filter);
        return $this->master->call('survey', 'GET', $_params);
    }

    /**
     * Get a specific survey
     *
     * @param string|int $surveyId Id of survey to fetch
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getSurvey( $surveyId )
    {
        return $this->master->call('survey/' . $surveyId, 'GET');
    }

    /**
     * Create a new survey
     *
     * @param string $title Title of new survey
     * @param string $type Type of new survey
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function createSurvey( $title, $type, $parameters = array() )
    {
        $parameters["title"] = $title;
        $parameters["type"] = $type;
        $allowed_params = array
        ("title", "type", "status", "theme", "team", "options[internal_title]", "blockby", "polloptions", "polltype", "pollwidth");

        $_params = http_build_query($master->getValidParameters($parameters, $allowed_params));
        return $this->master->call('survey/', 'PUT', $_params);
    }

    /**
     * Update and/or copy a specified survey
     *
     * @param string|int $surveyId Id of survey to update/copy
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function updateSurvey( $surveyId, $parameters = array() )
    {
        $allowed_params = array
        ("title", "status", "theme", "team", "options[internal_title]", "blockby", "copy", "polloptions", "polltype");

        $_params = http_build_query($master->getValidParameters($parameters, $allowed_params));
        return $this->master->call('survey/' . $surveyId, 'POST', $_params);
    }

    /**
     * Delete a specified survey
     *
     * @param string|int $surveyId Id of survey to delete
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function deleteSurvey( $surveyId )
    {
        return $this->master->call('survey/' . $surveyId, 'DELETE');
    }
}

