import unittest
from text_processor import process_and_translate_sections, translate_text

class TestTextProcessor(unittest.TestCase):

    def test_translate_text_fallback(self):
        # Empty inputs should return unchanged
        self.assertEqual(translate_text(""), "")
        self.assertEqual(translate_text("   "), "   ")

    def test_process_and_translate_sections_both(self):
        sample_text = (
            "Qisqacha mazmuni: Ariza mazmuni\n"
            "Asosiy content: Men kardiolog qabuliga yozilmoqchiman"
        )
        result = process_and_translate_sections(sample_text, "en")

        # Check that both sections are captured in the translation output
        self.assertIn("Brief Summary:", result)
        self.assertIn("Main Content:", result)
        print("Test Both Sections Result:\n", result)

    def test_process_and_translate_sections_qisqacha_only(self):
        sample_text = "Qisqacha mazmuni: Faqat qisqacha ma'lumot berildi."
        result = process_and_translate_sections(sample_text, "en")

        self.assertIn("Brief Summary:", result)
        self.assertNotIn("Main Content:", result)
        print("Test Qisqacha Only Result:\n", result)

    def test_process_and_translate_sections_no_markers(self):
        sample_text = "Salom, qandaysiz? Men shifokor bilan gaplashmoqchi edim."
        result = process_and_translate_sections(sample_text, "en")

        # Should translate the entire text block safely
        self.assertTrue(len(result) > 0)
        print("Test No Markers Result:\n", result)

if __name__ == '__main__':
    unittest.main()
