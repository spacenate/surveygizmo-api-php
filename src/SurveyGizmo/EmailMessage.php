<?php
/**
 * EmailMessage Object
 *
 * @package surveygizmo-api-php
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;

/**
 * EmailMessage class provides access to the EmailMessage sub-object
 *
 * @package surveygizmo-api-php
 */
class EmailMessage
{
    public function __construct(SurveyGizmoApiWrapper $master) {
        $this->master = $master;
    }

    /**
     * List all of the emailmessages in a campaign
     *
     * @param int $surveyId Id of survey campaign is in
     * @param int $campaignId Id of campaign to get messages for
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getList( $surveyId, $campaignId )
    {
        return $this->master->call('survey/' . $surveyId . '/surveycampaign/' . $campaignId . '/emailmessage/', 'GET');
    }

    /**
     * Get a specific emailmessage in a campaign
     *
     * @param int $surveyId Id of survey containing campaign
     * @param int $campaignId Id of campaign containing message
     * @param int $messageId Id of message to get
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function getMessage( $surveyId, $campaignId, $messageId )
    {
        return $this->master->call('survey/' . $surveyId . '/surveycampaign/' . $campaignId . '/emailmessage/' . $messageId, 'GET');
    }

    /**
     * Create a new emailmessage
     *
     * @param int $surveyId Id of survey to create message in
     * @param int $campaignId Id of campaign to create message in
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function createMessage( $surveyId, $campaignId, $parameters = array() )
    {
        $allowed_params = array("type", "from[name]", "from[email]", "replies", "subject", "messagetype", "body[text]", "body[html]", "send", "sfootercopy");
        $_params = http_build_query($this->master->getValidParameters($parameters, $allowed_params));
        return $this->master->call('survey/' . $surveyId . '/surveycampaign/' . $campaignId . '/emailmessage/', 'PUT', $_params);
    }

    /**
     * Update a specified emailmessage
     *
     * @param int $surveyId Id of survey containing campaign
     * @param int $campaignId Id of campaign containing message
     * @param int $messageId Id of message to update
     * @param array $parameters (optional) key-value pairs of additional parameters
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function updateMessage( $surveyId, $campaignId, $messageId, $parameters = array() )
    {
        $allowed_params = array("from[name]", "from[email]", "replies", "subject", "messagetype", "body[text]", "body[html]", "send", "sfootercopy");
        $_params = http_build_query($this->master->getValidParameters($parameters, $allowed_params));
        return $this->master->call('survey/' . $surveyId . '/surveycampaign/' . $campaignId . '/emailmessage/' . $messageId, 'POST', $_params);
    }

    /**
     * Delete a specified emailmessage
     *
     * @param int $surveyId Id of survey containing campaign
     * @param int $campaignId Id of campaign containing message
     * @param int $messageId Id of message to delete
     * @return string SG API object according to format specified in SurveyGizmoApiWrapper
     */
    public function deleteMessage( $surveyId, $campaignId )
    {
        return $this->master->call('survey/' . $surveyId . '/surveycampaign/' . $campaignId . '/emailmessage/' . $messageId, 'DELETE');
    }
}
