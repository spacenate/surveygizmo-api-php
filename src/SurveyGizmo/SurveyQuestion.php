<?php
/**
 * SurveyQuestion Object
 *
 * @package surveygizmo-api-php
 * @version 0.1
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;

class SurveyQuestion
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }
	
    /**
     * List all the questions in a survey
	 *
     * @param surveyId string|int survey to get questions for
	 * @param page string|int page of results to fetch
	 * @return SG API object
     */
    public function getList( $surveyId, $page = 1 )
	{
		$page = ($page) ? $page : 1;
        $_params = http_build_query(array("page" => $page));
        return $this->master->call('survey/' . $surveyId . '/surveyquestion', 'GET', $_params);
    }
	
    /**
     * Get a specific question in a survey
	 *
     * @param surveyId string|int survey question is in
	 * @param questionId string|int question to get
	 * @return SG API object
     */
    public function getQuestion( $surveyId, $questionId )
	{
        return $this->master->call('survey/' . $surveyId . '/surveyquestion/' . $questionId, 'GET');
    }
	
}

