<?php
/**
 * SurveyOption Object
 *
 * @package surveygizmo-api-php
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;


/**
 * SurveyOption class provides access to the SurveyOption object
 *
 * @package surveygizmo-api-php
 */
class SurveyOption
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }

    /**
     * List all the options in a question
     *
     * @param string|int $surveyId Id of survey containing question
     * @param string|int $questionId Id of question to get options for
     * @param string|int $page (optional) page of results to fetch
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
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
     * @param string|int $surveyId Id of survey containing question
     * @param string|int $questionId Id of question containing option
     * @param string|int $optionSku Sku of option to get
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getOption( $surveyId, $questionId, $optionSku )
    {
        return $this->master->call('survey/' . $surveyId . '/surveyquestion/' . $questionId . '/surveyoption/' . $optionSku, 'GET');
    }
	
    /**
     * Create a new question option
     *
     * @param string|int $surveyId Id of survey containing question
     * @param string|int $pageId Id of page containing question
     * @param string|int $questionId Id of question to receive new option
	 * @param string $title Title for new option
	 * @param string $value Reporting value for new option
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function createOption( $surveyId, $pageId, $questionId, $title, $value, $parameters = array() )
    {
        $parameters["title"] = $title;
        $parameters["value"] = $value;
        $allowed_params = array
        ("title", "value", "after", "properties[dependent]", "properties[other]", "properties[requireother]", "properties[na]", "properties[none]", "properties[all]", "properties[fixed]");

        $_params = http_build_query($master->getValidParameters($parameters, $allowed_params));
        return $this->master->call('survey/' . $surveyId . '/surveypage/' . $pageId . '/surveyquestion/' . $questionId . '/surveyoption/', 'PUT', $_params);
    }
	
    /**
     * Update a specified question option
     *
     * @param string|int $surveyId Id of survey containing question
     * @param string|int $questionId Id of question containing option
	 * @param string|int $optionSKU SKU of option to update
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function updateOption( $surveyId, $questionId, $optionSKU, $parameters = array() )
    {
        $allowed_params = array
        ("title", "value", "after", "properties[dependent]", "properties[other]", "properties[requireother]", "properties[na]", "properties[none]", "properties[all]", "properties[fixed]");

        $_params = http_build_query($master->getValidParameters($parameters, $allowed_params));
        return $this->master->call('survey/' . $surveyId . '/surveyquestion/' . $questionId . '/surveyoption/' . $optionSKU, 'POST', $_params);
    }
	
    /**
     * Delete a specified question option
     *
     * @param string|int $surveyId Id of survey containing question
     * @param string|int $questionId Id of question containing option
	 * @param string|int $optionSKU SKU of option to delete
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function deleteOption( $surveyId, $questionId, $optionSKU )
    {
        return $this->master->call('survey/' . $surveyId . '/surveyquestion/' . $questionId . '/surveyoption/' . $optionSKU, 'DELETE');
    }
}

