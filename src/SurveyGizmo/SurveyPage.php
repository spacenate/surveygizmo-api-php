<?php
/**
 * SurveyPage Object
 *
 * @package surveygizmo-api-php
 * @version 0.3
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
     * @param string|int $surveyId Id of survey to get pages for
     * @param string|int $page (optional) page of results to fetch
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
     * @param string|int $surveyId Id of survey containing page
     * @param string|int $pageId (optional) page to get
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getPage( $surveyId, $pageId )
    {
        return $this->master->call('survey/' . $surveyId . '/surveypage/' . $pageId, 'GET');
    }
}

