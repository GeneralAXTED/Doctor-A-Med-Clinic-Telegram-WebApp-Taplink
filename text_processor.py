import re
import urllib.request
import urllib.parse
import json

def translate_text(text, source_lang="uz", target_lang="en"):
    """
    Translates text from source_lang to target_lang using MyMemory translation API.
    Uses standard library urllib to remain completely zero-dependency.
    If the API fails, it falls back to the original text safely.
    """
    if not text or not text.strip():
        return text

    try:
        query = urllib.parse.quote(text.strip())
        langpair = urllib.parse.quote(f"{source_lang}|{target_lang}")
        url = f"https://api.mymemory.translated.net/get?q={query}&langpair={langpair}"

        req = urllib.request.Request(
            url,
            headers={'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'}
        )
        with urllib.request.urlopen(req, timeout=5) as response:
            if response.status == 200:
                data = json.loads(response.read().decode('utf-8'))
                translated = data.get("responseData", {}).get("translatedText")
                if translated:
                    return translated
    except Exception as e:
        print(f"[Warning] Translation API error: {e}")

    # Safe fallback: return original text
    return text

def process_and_translate_sections(text, target_lang="en"):
    """
    Captures 'Qisqacha mazmuni:' and 'Asosiy content:' sections from text,
    translates their values, and formats them.
    """
    if not text:
        return ""

    # Pattern to match 'Qisqacha mazmuni:' followed by any text up to 'Asosiy content:' or the end
    qisqacha_match = re.search(r'(?:Qisqacha\s+mazmuni\s*:?)(.*?)(?=(?:Asosiy\s+content)|$)', text, re.IGNORECASE | re.DOTALL)
    # Pattern to match 'Asosiy content:' followed by any text to the end
    asosiy_match = re.search(r'(?:Asosiy\s+content\s*:?)(.*)', text, re.IGNORECASE | re.DOTALL)

    translated_parts = []

    if qisqacha_match:
        qisqacha_content = qisqacha_match.group(1).strip()
        if qisqacha_content:
            translated_qisqacha = translate_text(qisqacha_content, "uz", target_lang)
            label = "Brief Summary:" if target_lang == "en" else "Краткое содержание:"
            translated_parts.append(f"<b>{label}</b> {translated_qisqacha}")

    if asosiy_match:
        asosiy_content = asosiy_match.group(1).strip()
        if asosiy_content:
            translated_asosiy = translate_text(asosiy_content, "uz", target_lang)
            label = "Main Content:" if target_lang == "en" else "Основной контент:"
            translated_parts.append(f"<b>{label}</b> {translated_asosiy}")

    if not translated_parts:
        # If neither section was matched, translate the entire text block safely
        translated_all = translate_text(text, "uz", target_lang)
        return translated_all

    return "\n".join(translated_parts)
