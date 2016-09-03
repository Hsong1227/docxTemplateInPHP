<?php

namespace icircle\tests\Template\Docx\ParseXMLTest;

use icircle\Template\Docx\DocxTemplate;
use icircle\tests\Template\Util;

class Test extends \PHPUnit_Framework_TestCase{

    public function testParseXML(){

        $templateDoc = file_get_contents(dirname(__FILE__).'/document.xml');

        $outputDir = Util::createTempDir('/icircle/template/docx');
        
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->loadXML($templateDoc);

        $templatePath = $outputDir.'/template.docx';

        $template = new \ZipArchive();
        $template->open($templatePath,\ZipArchive::CREATE);
        $template->addFromString("word/document.xml",$document->saveXML());
        $template->close();

        $docxTemplate = new DocxTemplate($templatePath);
        $outputPath = $outputDir.'/mergedOutput.docx';

        $this->assertFalse(file_exists($outputPath));

        //testing merge method
        $docxTemplate->merge(array("host"=>array("name"=>"My Company","no"=>"0")),$outputPath);

        $resultZip = new \ZipArchive();
        $resultZip->open($outputPath);
        $resultZip->extractTo($outputPath."_");

        $resultDoc = new \DOMDocument();
        $resultDoc->load($outputPath."_"."/word/document.xml");

        $expectedXML = '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body><w:p w:rsidR="00927B0C" w:rsidRDefault="005E5E04"><w:r><w:t/></w:r><w:r w:rsidRPr="005E5E04"><w:t>My Company</w:t></w:r></w:p><w:p w:rsidR="005E5E04" w:rsidRDefault="005E5E04"/><w:p w:rsidR="005E5E04" w:rsidRDefault="005E5E04" w:rsidP="005E5E04"><w:pPr><w:pStyle w:val="Heading1"/></w:pPr><w:proofErr w:type="spellStart"/><w:r><w:t>Address : [host.addre</w:t><w:t>ss</w:t></w:r><w:proofErr w:type="spellEnd"/></w:p><w:p><w:r><w:t xml:space="preserve">Phone : </w:t><w:t/><w:t> </w:t></w:r></w:p><w:p><w:r><w:t xml:space="preserve">Host No : 0</w:t></w:r></w:p><w:sectPr w:rsidR="005E5E04" w:rsidSect="00927B0C"><w:pgSz w:w="12240" w:h="15840"/><w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="720" w:footer="720" w:gutter="0"/><w:cols w:space="720"/><w:docGrid w:linePitch="360"/></w:sectPr></w:body></w:document>';
        $this->assertTrue($resultDoc->saveXML($resultDoc->documentElement) == $expectedXML);
    }

    public function testParseXMLInDevelopmentMode(){

        $templateDoc = file_get_contents(dirname(__FILE__).'/document.xml');

        $outputDir = Util::createTempDir('/icircle/template/docx');
        
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->loadXML($templateDoc);

        $wtElems = $document->getElementsByTagNameNS("http://schemas.openxmlformats.org/wordprocessingml/2006/main","t");
        $firstWtElem = $wtElems->item(0);
        $firstWtElem->nodeValue = "[development]".$firstWtElem->textContent;

        $templatePath = $outputDir.'/template.docx';

        $template = new \ZipArchive();
        $template->open($templatePath,\ZipArchive::CREATE);
        $template->addFromString("word/document.xml",$document->saveXML());
        $template->close();

        $docxTemplate = new DocxTemplate($templatePath);
        $outputPath = $outputDir.'/mergedOutput.docx';

        $this->assertFalse(file_exists($outputPath));

        //testing merge method
        $docxTemplate->merge(array("host"=>array("name"=>"My Company","no"=>"0")),$outputPath);

        $resultZip = new \ZipArchive();
        $resultZip->open($outputPath);
        $resultZip->extractTo($outputPath."_");

        $resultDoc = new \DOMDocument();
        $resultDoc->load($outputPath."_"."/word/document.xml");

        $expectedXML = '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body><w:p w:rsidR="00927B0C" w:rsidRDefault="005E5E04"><w:r><w:t/></w:r><w:r w:rsidRPr="005E5E04"><w:t>My Company</w:t></w:r></w:p><w:p w:rsidR="005E5E04" w:rsidRDefault="005E5E04"/><w:p w:rsidR="005E5E04" w:rsidRDefault="005E5E04" w:rsidP="005E5E04"><w:pPr><w:pStyle w:val="Heading1"/></w:pPr><w:proofErr w:type="spellStart"/><w:r><w:t>Address : [host.addre</w:t><w:t>ss</w:t></w:r><w:proofErr w:type="spellEnd"/></w:p><w:p><w:r><w:t xml:space="preserve">Phone : </w:t><w:t/><w:t>[host.phone] </w:t></w:r></w:p><w:p><w:r><w:t xml:space="preserve">Host No : 0</w:t></w:r></w:p><w:sectPr w:rsidR="005E5E04" w:rsidSect="00927B0C"><w:pgSz w:w="12240" w:h="15840"/><w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="720" w:footer="720" w:gutter="0"/><w:cols w:space="720"/><w:docGrid w:linePitch="360"/></w:sectPr></w:body></w:document>';
        $this->assertTrue($resultDoc->saveXML($resultDoc->documentElement) == $expectedXML);
    }

}

?>
