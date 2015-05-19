<?php
/**
 * SurveyPage Object
 *
 * @package surveygizmo-api-php
 * @version 0.2
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;

class SurveyPage
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }
	
    /**
     * List all the pages in a survey
	 *
     * @param surveyId string|int survey to get pages for
	 * @param page string|int page of results to fetch
	 * @return SG API object
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
     * @param surveyId string|int survey containing page
	 * @param pageId string|int page to get
	 * @return SG API object
     */
    public function getPage( $surveyId, $pageId )
	{
        return $this->master->call('survey/' . $surveyId . '/surveypage/' . $pageId, 'GET');
    }
	
}

