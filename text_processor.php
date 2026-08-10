<?php

class TextProcessor {
    /**
     * Translates text from Uzbek to English or Russian using MyMemory API.
     * Safely falls back to original text on failure.
     */
    public static function translateText($text, $sourceLang = 'uz', $targetLang = 'en') {
        $trimmed = trim($text);
        if (empty($trimmed)) {
            return $text;
        }

        $url = "https://api.mymemory.translated.net/get?q=" . urlencode($trimmed) . "&langpair=" . urlencode("$sourceLang|$targetLang");

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['responseData']['translatedText'])) {
                    return $data['responseData']['translatedText'];
                }
            }
        } catch (\Exception $e) {
            // Safe fallback
        }

        return $text;
    }

    /**
     * Parses and translates sections 'Qisqacha mazmuni:' and 'Asosiy content:'.
     */
    public static function processAndTranslateSections($text, $targetLang = 'en') {
        if (empty($text)) {
            return "";
        }

        // Regex to capture Qisqacha mazmuni and Asosiy content sections
        $qisqachaPattern = '/Qisqacha\s+mazmuni\s*:?\s*(.*?)(?=Asosiy\s+content|$)/is';
        $asosiyPattern = '/Asosiy\s+content\s*:?\s*(.*)/is';

        $translatedParts = [];

        if (preg_match($qisqachaPattern, $text, $matches)) {
            $qisqachaContent = trim($matches[1]);
            if (!empty($qisqachaContent)) {
                $translatedQisqacha = self::translateText($qisqachaContent, 'uz', $targetLang);
                $label = ($targetLang === 'en') ? 'Brief Summary:' : 'Краткое содержание:';
                $translatedParts[] = "<b>$label</b> $translatedQisqacha";
            }
        }

        if (preg_match($asosiyPattern, $text, $matches)) {
            $asosiyContent = trim($matches[1]);
            if (!empty($asosiyContent)) {
                $translatedAsosiy = self::translateText($asosiyContent, 'uz', $targetLang);
                $label = ($targetLang === 'en') ? 'Main Content:' : 'Основной контент:';
                $translatedParts[] = "<b>$label</b> $translatedAsosiy";
            }
        }

        if (empty($translatedParts)) {
            // Translate whole text block if no sections matched
            return self::translateText($text, 'uz', $targetLang);
        }

        return implode("\n", $translatedParts);
    }
}
