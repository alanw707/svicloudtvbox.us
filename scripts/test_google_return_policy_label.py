#!/usr/bin/env python3
"""Execute the theme's exact return-label repair block with isolated PHP fixtures."""
import json
from pathlib import Path
import subprocess
import unittest
import xml.etree.ElementTree as ET

ROOT = Path(__file__).resolve().parents[1]
SOURCE = (ROOT / 'theme/svicloudtvbox-lumen/functions.php').read_text()
START = SOURCE.index("            // A policy's display name is not an exception label.")
END = SOURCE.index("            if ($offer_key !== '' && isset($offer_images[$offer_key]))", START)
BLOCK = SOURCE[START:END]


def repair(item):
    php = '$patched_item = json_decode(stream_get_contents(STDIN), true);\n' + BLOCK
    php += '\necho json_encode($patched_item);'
    result = subprocess.run(['php', '-r', php], input=json.dumps(item), text=True, capture_output=True, check=True)
    return json.loads(result.stdout)


class ReturnPolicyLabelTests(unittest.TestCase):
    def test_legacy_display_name_is_repaired(self):
        value = '<item><g:return_policy_label>Standard for United States</g:return_policy_label></item>'
        self.assertEqual(repair(value), value.replace('Standard for United States', 'default'))

    def test_missing_label_with_condition(self):
        value = '<item>\n      <g:condition>new</g:condition>\n    </item>'
        self.assertIn('<g:return_policy_label>default</g:return_policy_label>', repair(value))

    def test_missing_label_without_condition(self):
        value = '<item>\n      <g:id>12</g:id>\n    </item>'
        self.assertIn('<g:return_policy_label>default</g:return_policy_label>', repair(value))

    def test_exception_label_is_preserved(self):
        value = '<item><g:return_policy_label>actual-exception</g:return_policy_label></item>'
        self.assertEqual(repair(value), value)

    def test_default_is_idempotent(self):
        value = '<item><g:return_policy_label>default</g:return_policy_label></item>'
        self.assertEqual(repair(repair(value)), value)

    def test_legacy_whitespace(self):
        value = '<item><g:return_policy_label>\n Standard for United States \n</g:return_policy_label></item>'
        self.assertEqual(repair(value), '<item><g:return_policy_label>default</g:return_policy_label></item>')

    def test_other_fields_unchanged(self):
        value = '<item>\n      <g:price>USD 287.99</g:price>\n      <g:shipping>existing</g:shipping>\n      <g:return_policy_label>Standard for United States</g:return_policy_label>\n    </item>'
        result = repair(value)
        self.assertEqual(result.replace('>default<', '>Standard for United States<'), value)


if __name__ == '__main__':
    unittest.main()
