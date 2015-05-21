<?php
/**
 * SurveyQuestion Object
 *
 * @package surveygizmo-api-php
 * @version 0.3
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
	
	/**
	 * Create a new question
	 *
	 * @param surveyId string|int survey to create new question in
	 * @param pageId string|int page to put new question in
	 * @param parameters array key-value pairs of additional parameters
	 */
	public function createQuestion( $surveyId, $pageId, $parameters = array() )
	{
		$allowed_params = array
		("type", "title", "description", "after", "varname", "shortname", "properties[disabled]", "properties[exclude_number]", "properties[hide_after_response]", "properties[option_sort]", "properties[orientation]", "properties[labels_right]", "properties[question_description_above]", "properties[custom_css]");
		
		foreach ($parameters as $key => $value) {
			if(!in_array($key, $allowed_params)) {
				unset($parameters[$key]);
			}
		}
        $_params = http_build_query($parameters);
        return $this->master->call('survey/' . $surveyId . '/surveypage/' . $pageId . '/surveyquestion', 'PUT', $_params);
	}
	
}

