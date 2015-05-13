# SurveyGizmoApiWrapper
PHP wrapper for the SurveyGizmo RESTful API

## Installing with Composer:

{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/spacenate/SurveyGizmoApiWrapper"
        }
    ],
    "require": {
        "spacenate/SurveyGizmoApiWrapper": "dev-master"
    }
}

## Objects

Account

Account objects are customer account records of SurveyGizmo. Using the API you can create new accounts (note: there is an additional approval step to do so), and pull the company name and contact information for the account for which you have a login.
AccountTeams

Account teams are user teams, available in multi-user accounts, within SurveyGizmo. Using the API you can create and delete account teams and pull and change details of existing account teams.
AccountUser

Account users are individual users of a SurveyGizmo account. When you use OAuth with SurveyGizmo you are accessing the application with the privileges of a particular user.
ContactList

A contact list is a group of contacts that you can set up in the account's Email List system (our version of Contact Management). Think of these as mailing lists for sharing surveys. You can also use this as a basic CRM structure using the custom fields to store additional data, and survey responses linked to contacts.
Survey

Surveys are the heart-and-soul of SurveyGizmo (obviously). Surveys come in four flavors in SurveyGizmo: Surveys, Polls, Quizzes and Forms. For the purposes of the API all of these sub-types are accessed via the Survey object. There are a few things to keep in mind about the Survey object:

    Surveys are essentially collection of SurveyPages and SurveyQuestions
    All Surveys (except Polls) have at least two pages.
    Terminal pages are pages that flag a survey as complete and do not allow the respondent to move back in the survey.

SurveyResponse

Data is stored in SurveyGizmo databases as a survey response. You can submit and edit survey responses through the API. Survey responses have several statuses: In Progress, Hit, Saved, Partial, Complete, Abandoned, Disqualified and Overflow. Overflow responses cannot be accessed by the API as these are responses collected beyond the monthly limit for the particular account.
SurveyStatistic

This object pulls aggregate statistics about your collected survey data. The basic statistics are: total responses, sum, average, standard deviation, max and min values.
SurveyPage

The SurveyPage object is a container for SurveyQuestions. As a collection, they also outline the flow of the survey from beginning to end (unless logic intervenes). Pages have a couple of returned fields, however, for the most part, they act as simple containers to define the survey or form.
SurveyQuestion

The SurveyQuestion object is the most varied object in the SurveyGizmo platform. As with Survey object, questions come in multiple sub-types (over 40 of them). The most common types are “textbox,” ”radio” and ”checkbox.” These correlate with form elements that every web developer is familiar with. In some cases, questions act like parent containers for sub-questions. An example of this is a table of radio buttons, where each row of the table is represented by separate radio-type SurveyQuestion with a shared collection of survey options.
SurveyOption

The SurveyOption is a potential answer for a multiple-select/multiple-answer SurveyQuestion. For example, if you have radio button question with three possible answers, “lions,” ”tigers” and “bears,” these would be represented as three SurveyOption objects within a radio-type SurveyQuestion. SurveyOptions have their own sub-options for controlling order, exclusivity and language translations.

SurveyReport

The SurveyReport object allows you get list of reports for a given survey, get a specfied report as well as copy and delete reports.
SurveyCampaign

When surveys are created a default SurveyCampaign is created (a basic survey link). SurveyCampaigns come in many types and while many share basic link settings, they also have custom settings -- the most notable variation is the Email-Invitation subtype of the Survey Campaign.
EmailMessage

Email Messages are part of an EmailCampaign. They come in three types: Initial Message (sent to everyone in the campaign), Reminder (sent only to those who have not completed the survey) and Thank You (sent to those who do complete).
Contact

The Contact object represents a person (keyed by email address) which can receive Email Invitations.