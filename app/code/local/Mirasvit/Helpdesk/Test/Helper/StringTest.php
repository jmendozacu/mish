<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Help Desk MX
 * @version   1.1.0
 * @build     1285
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Helpdesk_Test_Model_StringTest extends EcomDev_PHPUnit_Test_Case
{
    protected $helper;
    protected function setUp()
    {
        parent::setUp();
        $this->helper = Mage::helper('helpdesk/string');
    }

    /**
     * @test
     */
    public function generateTicketCode() {
        $result = $this->helper->generateTicketCode();
        $this->assertEquals(13, strlen($result));
    }



    /**
     * @test
     * @dataProvider convertToHtmlProvider
     */
    public function convertToHtml($input, $expected) {
        $result = $this->helper->convertToHtml($input);
        $this->assertEquals($expected, $result);
    }

    public function convertToHtmlProvider()
    {
        return array(
            array(
                ' aaaa@bbbb.com ',
                '&nbsp;<a href="mailto:aaaa@bbbb.com">aaaa@bbbb.com</a>&nbsp;',
                ),
            array(
                'https://www.evernote.com/shard/s405/sh/bfc9423b-9051-49ef-a3ea-37293055b1be/17c7264f76e1a8a08db7c964641abe52/deep/0/ftp.officerock.com---dev@officerock.com@ftp.officerock.com---FileZilla.png',
                '<a href="https://www.evernote.com/shard/s405/sh/bfc9423b-9051-49ef-a3ea-37293055b1be/17c7264f76e1a8a08db7c964641abe52/deep/0/ftp.officerock.com---dev@officerock.com@ftp.officerock.com---FileZilla.png">https://www.evernote.com/shard/s405/sh/bfc9423b-9051-49ef-a3ea-37293055b1be/17c7264f76e1a8a08db7c964641abe52/deep/0/ftp.officerock.com---dev@officerock.com@ftp.officerock.com---FileZilla.png</a>'
                ),
            array(
                '/var/www/vhosts/espace-camera.com/httpdocs/Observer.php',
                '/var/www/vhosts/espace-camera.com/httpdocs/Observer.php'
                ),
            array(
                'http://store.com/?aaaa=1&bbbb=2',
                '<a href="http://store.com/?aaaa=1&bbbb=2">http://store.com/?aaaa=1&bbbb=2</a>'
                ),
            array(
                ' www.espace-camera.com/httpdocs/',
                '<a href="http://www.espace-camera.com/httpdocs/">www.espace-camera.com/httpdocs/</a>',
                ),
            array(
                'http://espace-camera.com/httpdocs/',
                '<a href="http://espace-camera.com/httpdocs/">http://espace-camera.com/httpdocs/</a>',
                ),
            array(
                'https://espace-camera.com/httpdocs/',
                '<a href="https://espace-camera.com/httpdocs/">https://espace-camera.com/httpdocs/</a>',
                ),
        );
    }

    /**
     * @test
     * @dataProvider subjectProvider
     */
    public function getTicketCodeFromSubject($input, $expected) {
        $result = $this->helper->getTicketCodeFromSubject($input);
        $this->assertEquals($expected, $result);
    }

    public function subjectProvider()
    {
        return array(
            array('[#ION-465-43972] Bug', 'ION-465-43972'),
            array('Re: [#ION-465-43972] Bug', 'ION-465-43972'),
            array('Re:Re:[#ION-465-43972]Bug', 'ION-465-43972'),
            array('Re:Re:[ION-465-43972] Bug', false),
            array('Re:Re:#ION-465-43972 Bug',  'ION-465-43972'),
            #aw tickets
            array('Re:Re:[AWR-52708] Bug', false),
            array('Re:Re:#AWR-52708 Bug',  'AWR-52708'),
            array('Re: [#CER-84876] New Ticket created: test',  'CER-84876'),
            array('Re: [#HNR-12188] Ticket replied: Re: Your points at The Green Nursery are about to expire',  'HNR-12188'),

            #wm tickets
            #Re: Ticket #1000090 - test
            array('Re:Re:Ticket #1000090 - test',  'Ticket #1000090'),
            array('Re: Ticket #1000090 - test',  'Ticket #1000090'),
            array('Re: Ticket #1000090111 sss- test',  'Ticket #1000090111'),
            array('pipe flashing Pittsburg fold 0812',  false),

        );
    }




    /**
     * @test
     * @dataProvider body2parseProvider
     */
    public function getTicketCodeFromBody($input, $expected) {
        $result = $this->helper->getTicketCodeFromBody($input);
        $this->assertEquals($expected, $result);
    }

    public function body2parseProvider()
    {
        return array(
            array('Message-Id:--#AAA-123-45678--', 'AAA-123-45678'),
            array('Message-Id:--#abcedasdfwerwefasdfasdfsadf--', 'abcedasdfwerwefasdfasdfsadf'),
            array('asdfnsjdf askhudfbia sub Ticket Message-Id:--#AAA-123-45678-- 5%32423sfsd', 'AAA-123-45678'),
            array('#AAA-123-45678', false),
            array('dsfa #AAA-123-45678 asdf', false),
        );
    }


    protected function getFixt($code) {
        return file_get_contents(dirname(__FILE__)."/StringTest/fixtures/$code");
    }


    /**
     * @test
     * @dataProvider bodyProvider
     */
	public function parseBodyTest($expected, $format, $input) {
		$result = $this->helper->parseBody($this->getFixt($input), $format);
        // echo $result;die;
		$this->assertEquals($expected, $result);
	}

    public function bodyProvider()
    {
        return array(
array(
'So lösen Sie den Geschenkgutschein ein', Mirasvit_Helpdesk_Model_Config::FORMAT_HTML, 'email3.html'
),
array(
'H1 HEADER

H2 HEADER

H3 HEADER

p block
http://link.com
http://link.com
www.x.com

div block
italic
bold', Mirasvit_Helpdesk_Model_Config::FORMAT_HTML, 'email2.html'
),
array(
'line 1
line2

line3

line4', Mirasvit_Helpdesk_Model_Config::FORMAT_HTML, 'email1.html'
),
        );
    }


    /**
     * @test
     * @dataProvider timeProvider
     */
    public function removeTimeTest($timeExample) {
        $input = "aaaaaa\nbbbbbb\n$timeExample";
        $expected = "aaaaaa\nbbbbbb";

        $result = $this->helper->removeTime($input);
        // echo $result;
        $this->assertEquals($expected, $result);
    }

    public function timeProvider()
    {
        return array(
            array('2014-03-26 19:47 GMT+02:00 Sales <support2@mirasvit.com.ua>:'),
            array('2014-03-25 0:00 GMT+02:00 COPPERLAB Customer Support <support..m>:'),
            array('On Mon, Mar 24, 2014 at 10:58 PM, Sales wrote:'),
            array('2014-03-24 19:22 GMT+02:00 Sales :'),
            array('On Dec 8, 2014, at 9:24 AM, Mirasvit Support <a8v1oq0kggnvsinmg6dv@mirasvit.com> wrote:'),
            array('2014-12-12 9:52 GMT-03:00 Mirasvit Support <a8v1oq0kggnvsinmg6dv@mirasvit.com>:'),
            array('2014-12-05 11:31 GMT-03:00 Mirasvit Support <a8v1oq0kggnvsinmg6dv@mirasvit.com>:'),
            array('El 11-12-2014, a las 12:22 p.m., Mirasvit Support <a8v1oq0kggnvsinmg6dv@mirasvit.com> escribió:'),
        );
    }
}

