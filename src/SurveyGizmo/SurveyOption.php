<?php
/**
 * SurveyOption Object
 *
 * @package surveygizmo-api-php
 * @version 0.3
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;

class SurveyOption
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }
	
    /**
     * List all the options in a question
	 *
     * @param surveyId string|int survey containing question
	 * @param questionId string|int question to get options for
	 * @param page string|int page of results to fetch
	 * @return SG API object
     */
    public function getList( $surveyId, $questionId, $page = 1 )
	{
		$page = ($page) ? $page : 1;
        $_params = http_build_query(array("page" => $page));
        return $this->master->call('survey/' . $surveyId . '/surveyquestion/' . $questionId . '/surveyoption', 'GET', $_params);
    }
	
    /**
     * Get a specific option in a question
	 *
     * @param surveyId string|int survey containing question
	 * @param questionId string|int question containing option
	 * @param optionSku string|int option to get
	 * @return SG API object
     */
    public function getOption( $surveyId, $questionId, $optionSku )
	{
        return $this->master->call('survey/' . $surveyId . '/surveyquestion/' . $questionId . '/surveyoption/' . $optionSku, 'GET');
    }
	
}

