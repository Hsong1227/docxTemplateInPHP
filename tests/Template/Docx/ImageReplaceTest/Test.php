<?php

namespace icircle\tests\Template\Docx\ImageReplaceTest;

use icircle\Template\Docx\DocxTemplate;
use icircle\tests\Template\Util;

class Test extends \PHPUnit_Framework_TestCase{

    public function testMerge(){
        $templatePath = dirname(__FILE__).'/template.docx';

        $docxTemplate = new DocxTemplate($templatePath);
        $outputDir = Util::createTempDir('/icircle/template/docx');

        $outputPath = $outputDir.'/mergedOutput.docx';
        
        $record = array();
        $record["headerImage"] = dirname(__FILE__).'/headerImage.png';
        $record["bodyImage"]   = dirname(__FILE__).'/bodyImage.png';
        $record["footerImage"] = dirname(__FILE__).'/footerImage.png';

        $this->assertFalse(file_exists($outputPath));

        //testing merge method
        $docxTemplate->merge(array("record"=>$record),$outputPath);

        $this->assertTrue(file_exists($outputPath));

        $resultZip = new \ZipArchive();
        $resultZip->open($outputPath);
        $resultZip->extractTo($outputPath."_");

        $resultDoc = new \DOMDocument();
        $resultDoc->load($outputPath."_"."/word/_rels/header1.xml.rels");
        $relElements = $resultDoc->documentElement->getElementsByTagName('Relationship');
        $this->assertTrue($relElements->length == 2);

        $resultDoc = new \DOMDocument();
        $resultDoc->load($outputPath."_"."/word/_rels/document.xml.rels");
        $relElements = $resultDoc->documentElement->getElementsByTagName('Relationship');
        $this->assertTrue($relElements->length == 12);

        $resultDoc = new \DOMDocument();
        $resultDoc->load($outputPath."_"."/word/_rels/footer1.xml.rels");
        $relElements = $resultDoc->documentElement->getElementsByTagName('Relationship');
        $this->assertTrue($relElements->length == 2);

        $resultDoc = new \DOMDocument();
        $resultDoc->load($outputPath."_"."/word/document.xml");
        $imageElements = $resultDoc->documentElement->getElementsByTagNameNS("http://schemas.openxmlformats.org/wordprocessingml/2006/main","drawing");
        $this->assertTrue($imageElements->length == 2);

        $this->assertTrue(count(array_diff(scandir($outputPath."_"."/word/media"),array(".",".."))) == 4);

    }

    public function testMergeDevelopment(){
        $templatePath = dirname(__FILE__).'/templateDevelopment.docx';

        $docxTemplate = new DocxTemplate($templatePath);
        $outputDir = Util::createTempDir('/icircle/template/docx');

        $outputPath = $outputDir.'/mergedOutput.docx';

        $record = array();
        $record["headerImage"] = dirname(__FILE__).'/headerImage.png';
        $record["bodyImage"]   = dirname(__FILE__).'/bodyImage.png';
        $record["footerImage"] = dirname(__FILE__).'/footerImage.png';

        $this->assertFalse(file_exists($outputPath));

        //testing merge method
        $docxTemplate->merge(array("record"=>$record),$outputPath);

        $this->assertTrue(file_exists($outputPath));

        $resultZip = new \ZipArchive();
        $resultZip->open($outputPath);
        $resultZip->extractTo($outputPath."_");

        $resultDoc = new \DOMDocument();
        $resultDoc->load($outputPath."_"."/word/document.xml");
        $imageElements = $resultDoc->documentElement->getElementsByTagNameNS("http://schemas.openxmlformats.org/wordprocessingml/2006/main","drawing");
        $this->assertTrue($imageElements->length == 3);

        $this->assertTrue(count(array_diff(scandir($outputPath."_"."/word/media"),array(".",".."))) == 4);

    }

}

?>
