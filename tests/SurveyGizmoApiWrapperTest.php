<?php

namespace spacenate\Test;

use spacenate\SurveyGizmoApiWrapper;

class SurveyGizmoApiWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorCanBeCalledWithOrWithoutCredentials()
    {
        $sg = new SurveyGizmoApiWrapper("test@case.com", "plaintext_password", "pass");

        $result = $sg->getCredentials();
        $this->assertEquals("user:pass=test@case.com:plaintext_password", $result);
    }

    public function testConstructorCanBeCalledWithOrWithoutCredentials2()
    {
        $sg = new SurveyGizmoApiWrapper();

        $result = $sg->getCredentials();
        $this->assertEquals(false, $result);
    }

    /**
     * @dataProvider credentialsTypeProvider
     * @testdox When setting credentials, type may be md5, pass, or oauth
     */
    public function testTypeMayBeSpecifiedWhenSettingCredentials($type, $expected)
    {
        $sg = new SurveyGizmoApiWrapper();

        $result = $sg->setCredentials("test@case.com", "secret", $type);
        $this->assertEquals($expected, $result);
    }

    public function credentialsTypeProvider()
    {
        return array(
            array("md5", true),
            array("pass", true),
            array("oauth", true),
            array("foobar", false),
            array(1, false),
            array(false, false)
        );
    }

    public function testCredentialTypeDefaultsToPass()
    {
        $sg = new SurveyGizmoApiWrapper();

        $sg->setCredentials("test@case.com", "secret");

        $result = $sg->getCredentials();
        $this->assertEquals("user:pass=test@case.com:secret", $result);
    }

    /**
     * @dataProvider formatProvider
     * @testdox Return format can be set to either json, pson, xml, or debug
     */
    public function testReturnFormatMayBeSpecified($format, $expected)
    {
        $sg = new SurveyGizmoApiWrapper();

        $result = $sg->setFormat($format);
        $this->assertEquals($expected, $result);
    }

    public function formatProvider()
    {
        return array(
            array("json", true),
            array("pson", true),
            array("xml", true),
            array("debug", true),
            array("foobar", false),
            array(1, false),
            array(false, false)
        );
    }

    public function testFilterStringsCanBeCreatedFromSimpleArrays()
    {
        $sg = new SurveyGizmoApiWrapper();

        $filterArray = array("createdon", ">", "2015-05-15+12:00:00");

        $result = $sg->getFilterString($filterArray);
        $this->assertEquals("filter[field][0]=createdon&filter[operator][0]=>&filter[value][0]=2015-05-15+12:00:00", $result);
    }

    public function testFilterStringsCanBeCreatedFromMultidimensionalArrays()
    {
        $sg = new SurveyGizmoApiWrapper();

        $filterArray = array(
            array("createdon", ">", "2015-05-15+12:00:00"),
            array("status", "=", "Launched")
        );

        $result = $sg->getFilterString($filterArray);
        $this->assertEquals("filter[field][0]=createdon&filter[operator][0]=>&filter[value][0]=2015-05-15+12:00:00&filter[field][1]=status&filter[operator][1]==&filter[value][1]=Launched", $result);
    }

}