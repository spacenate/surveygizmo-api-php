<?php
/**
 * SurveyPage Object
 *
 * @package surveygizmo-api-php
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;

/**
 * SurveyPage class provides access to the SurveyPage object
 *
 * @package surveygizmo-api-php
 */
class SurveyPage
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }

    /**
     * List all the pages in a survey
     *
     * @param int $surveyId Id of survey to get pages for
     * @param int $page (optional) page of results to fetch
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getList( $surveyId, $page = 1 )
    {
        $page = ($page) ? $page : 1;
        $_params = http_build_query(array("page" => $page));
        return $this->master->call('survey/' . $surveyId . '/surveypage', 'GET', $_params);
    }

    /**
     * Get a specific page in a survey
     *
     * @param int $surveyId Id of survey containing page
     * @param int $pageId (optional) page to get
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getPage( $surveyId, $pageId )
    {
        return $this->master->call('survey/' . $surveyId . '/surveypage/' . $pageId, 'GET');
    }

    /**
     * Create a new page
     *
     * @param int $surveyId Id of survey to receive new page
     * @param string $title Title for new page
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function createPage( $surveyId, $title, $parameters = array() )
    {
        $parameters["title"] = $title;
        $allowed_params = array
        ("title", "description", "after", "properties[hidden]", "properties[piped_from]");

        $_params = http_build_query($this->master->getValidParameters($parameters, $allowed_params));
        return $this->master->call('survey/' . $surveyId . '/surveypage/', 'PUT', $_params);
    }

    /**
     * Update a specified page
     *
     * @param int $surveyId Id of survey containing page
     * @param int $pageId Id of page to update
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function updatePage( $surveyId, $pageId, $parameters = array() )
    {
        $allowed_params = array
        ("title", "description", "after", "properties[hidden]", "properties[piped_from]");

        $_params = http_build_query($this->master->getValidParameters($parameters, $allowed_params));
        return $this->master->call('survey/' . $surveyId . '/surveypage/' . $pageId, 'POST', $_params);
    }

    /**
     * Delete a specified page
     *
     * @param int $surveyId Id of survey containing page
     * @param int $pageId Id of page to delete
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function deletePage( $surveyId, $pageId )
    {
        return $this->master->call('survey/' . $surveyId . '/surveypage/' . $pageId, 'DELETE');
    }
}
