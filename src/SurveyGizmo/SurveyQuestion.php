<?php
/**
 * SurveyQuestion Object
 *
 * @package surveygizmo-api-php
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;

/**
 * SurveyQuestion class provides access to the SurveyQuestion object
 *
 * @package surveygizmo-api-php
 */
class SurveyQuestion
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }

    /**
     * List all the questions in a survey
     *
     * @param string|int $surveyId Id of survey to get questions for
     * @param string|int $page (optional) page of results to fetch
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
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
     * @param string|int $surveyId Id of survey question is in
     * @param string|int $questionId Id of question to get
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getQuestion( $surveyId, $questionId )
    {
        return $this->master->call('survey/' . $surveyId . '/surveyquestion/' . $questionId, 'GET');
    }

    /**
     * Create a new question
     *
     * @param string|int $surveyId Id of survey to create new question in
     * @param string|int $pageId Id of page to put new question in
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function createQuestion( $surveyId, $pageId, $parameters = array() )
    {
        $allowed_params = array(
			"type", "title", "description", "after", "varname", "shortname", "properties[disabled]",
			"properties[exclude_number]", "properties[hide_after_response]", "properties[option_sort]",
			"properties[orientation]", "properties[labels_right]", "properties[question_description_above]",
			"properties[custom_css]", "properties[required]", "properties[soft-required]",
			"properties[force_numeric]", "properties[force_percent]", "properties[force_currency]",
			"properties[min_number]", "properties[max_number]", "properties[min_answers_per_row]",
			"properties[minimum_response]", "properties[inputmask]", "properties[defaulttext]",
			"properties[hidden]", "properties[piped_from]", "properties[max_total]",
			"properties[max_total_noshow]", "properties[must_be_max]", "properties[maxfiles]",
			"properties[extentions]", "properties[url]"
		);
		$regex_params = array(
			"/^properties\[outbound\]\[[0-9]+\]\[fieldname\]$/i",
			"/^properties\[outbound\]\[[0-9]+\]\[mapping\]$/i",
			"/^properties\[outbound\]\[[0-9]+\]\[default\]$/i"
		);
		
        $_params = http_build_query($master->getValidParameters($parameters, $allowed_params, $regex_params));
        return $this->master->call('survey/' . $surveyId . '/surveypage/' . $pageId . '/surveyquestion', 'PUT', $_params);
    }
	
    /**
     * Update a specified question
     *
     * @param string|int $surveyId Id of survey containing question
     * @param string|int $questionId Id of question to update
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function updateQuestion( $surveyId, $questionId, $parameters = array() )
    {
        $allowed_params = array(
			"title", "description", "varname", "shortname", "properties[disabled]",
			"properties[exclude_number]", "properties[hide_after_response]", "properties[option_sort]",
			"properties[orientation]", "properties[labels_right]", "properties[question_description_above]",
			"properties[custom_css]", "properties[required]", "properties[soft-required]",
			"properties[force_numeric]", "properties[force_percent]", "properties[force_currency]",
			"properties[min_number]", "properties[max_number]", "properties[min_answers_per_row]",
			"properties[minimum_response]", "properties[inputmask]", "properties[defaulttext]",
			"properties[hidden]", "properties[piped_from]", "properties[max_total]",
			"properties[max_total_noshow]", "properties[must_be_max]", "properties[maxfiles]",
			"properties[extentions]", "properties[url]"
		);
		$regex_params = array(
			"/^properties\[outbound\]\[[0-9]+\]\[fieldname\]$/i",
			"/^properties\[outbound\]\[[0-9]+\]\[mapping\]$/i",
			"/^properties\[outbound\]\[[0-9]+\]\[default\]$/i",
				
		);
		
        $_params = http_build_query($master->getValidParameters($parameters, $allowed_params, $regex_params));
        return $this->master->call('survey/' . $surveyId . '/surveyquestion/' . $questionId, 'POST', $_params);
    }
	
    /**
     * Delete a specified question
     *
     * @param string|int $surveyId Id of survey containing question
     * @param string|int $questionId Id of question to delete
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function deleteQuestion( $surveyId, $questionId )
    {
        return $this->master->call('survey/' . $surveyId . '/surveyquestion/' . $questionId, 'DELETE');
    }
}

