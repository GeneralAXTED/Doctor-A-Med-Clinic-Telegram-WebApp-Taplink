<?php

require_once 'text_processor.php';

class TestTextProcessor {
    public function runTests() {
        echo "Running PHP TextProcessor Tests...\n";

        $this->testTranslateTextFallback();
        $this->testProcessAndTranslateSectionsBoth();
        $this->testProcessAndTranslateSectionsQisqachaOnly();
        $this->testProcessAndTranslateSectionsNoMarkers();

        echo "\nAll PHP Tests Passed Successfully!\n";
    }

    private function testTranslateTextFallback() {
        assert(TextProcessor::translateText("") === "");
        assert(TextProcessor::translateText("   ") === "   ");
        echo "✓ testTranslateTextFallback Passed\n";
    }

    private function testProcessAndTranslateSectionsBoth() {
        $sampleText = "Qisqacha mazmuni: Ariza mazmuni\nAsosiy content: Men kardiolog qabuliga yozilmoqchiman";
        $result = TextProcessor::processAndTranslateSections($sampleText, 'en');

        assert(strpos($result, 'Brief Summary:') !== false);
        assert(strpos($result, 'Main Content:') !== false);
        echo "✓ testProcessAndTranslateSectionsBoth Passed\n";
        echo "   Result: $result\n";
    }

    private function testProcessAndTranslateSectionsQisqachaOnly() {
        $sampleText = "Qisqacha mazmuni: Faqat qisqacha ma'lumot berildi.";
        $result = TextProcessor::processAndTranslateSections($sampleText, 'en');

        assert(strpos($result, 'Brief Summary:') !== false);
        assert(strpos($result, 'Main Content:') === false);
        echo "✓ testProcessAndTranslateSectionsQisqachaOnly Passed\n";
        echo "   Result: $result\n";
    }

    private function testProcessAndTranslateSectionsNoMarkers() {
        $sampleText = "Salom, qandaysiz? Men shifokor bilan gaplashmoqchi edim.";
        $result = TextProcessor::processAndTranslateSections($sampleText, 'en');

        assert(!empty($result));
        echo "✓ testProcessAndTranslateSectionsNoMarkers Passed\n";
        echo "   Result: $result\n";
    }
}

$testRunner = new TestTextProcessor();
$testRunner->runTests();
