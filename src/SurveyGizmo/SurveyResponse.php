<?php
/**
 * SurveyResponse Object
 *
 * @package surveygizmo-api-php
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;

/**
 * SurveyResponse class provides access to the SurveyResponse sub-object
 *
 * @package surveygizmo-api-php
 */
class SurveyResponse
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }

    /**
     * List all of the responses a survey has collected
     *
     * @param int $surveyId Id of survey to get responses for
     * @param int $page (optional) page of results to fetch
     * @param int $limit (optional) number of results to fetch
     * @param array $filter (optional) one or more filter arrays
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getList( $surveyId, $page = 1, $limit = 50, $filter = array() )
    {
        $page = ($page) ? $page : 1;
        $limit = ($limit) ? $limit : 50;
        $_params = http_build_query(array("resultsperpage" => $limit, "page" => $page));
        if ($filter) $_params .= "&" . $this->master->getFilterString($filter);
        return $this->master->call('survey/' . $surveyId . '/surveyresponse', 'GET', $_params);
    }

    /**
     * Get a specific response to a survey
     *
     * @param int $surveyId Id of survey to get response from
     * @param int $responseId Id of response to get
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getResponse( $surveyId, $responseId )
    {
        return $this->master->call('survey/' . $surveyId . '/surveyresponse/' . $responseId, 'GET');
    }

    /**
     * Create a new response
     *
     * @param int $surveyId Id of survey to create response for
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     * @todo Verify data[shortname][SKU-other] and data[shortname][comment] work
     */
    public function createResponse( $surveyId, $parameters = array() )
    {
        $regex_params = array(
            "/^data\[[0-9]+\]\[[0-9]{5}(-other)?\]$/i",
            "/^data\[[0-9]+\]\[(value|comment)\]$/i",
            "/^data\[\S+\]\[[0-9]{5}(-other)?\]$/i",
            "/^data\[\S+\]\[(value|comment)\]$/i"
        );

        $_params = http_build_query($this->master->getValidParameters($parameters, array(), $regex_params));
        return $this->master->call('survey/' . $surveyId . '/surveyresponse/', 'PUT', $_params);
    }

    /**
     * Update a specified response
     *
     * @param int $surveyId Id of survey containing response
     * @param int $responseId Id of response to update
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function updateResponse( $surveyId, $responseId, $parameters = array() )
    {
        $regex_params = array(
            "/^data\[[0-9]+\]\[[0-9]{5}(-other)?\]$/i",
            "/^data\[[0-9]+\]\[(value|comment)\]$/i",
            "/^data\[\S+\]\[[0-9]{5}(-other)?\]$/i",
            "/^data\[\S+\]\[(value|comment)\]$/i"
        );

        $_params = http_build_query($this->master->getValidParameters($parameters, array(), $regex_params));
        return $this->master->call('survey/' . $surveyId . '/surveyresponse/' . $responseId, 'POST', $_params);
    }

    /**
     * Delete a specified response
     *
     * @param int $surveyId Id of survey containing response
     * @param int $responseId Id of response to delete
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function deleteResponse( $surveyId, $responseId )
    {
        return $this->master->call('survey/' . $surveyId . '/surveyresponse/' . $responseId, 'DELETE');
    }
}
