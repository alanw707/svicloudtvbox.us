import unittest

from scripts.build_css import minify_css


class MinifyCssTest(unittest.TestCase):
    def test_preserves_required_calc_whitespace_around_plus(self):
        css = '.menu { padding: calc(32px + env(safe-area-inset-bottom)); }'
        self.assertIn('calc(32px + env(safe-area-inset-bottom))', minify_css(css))


if __name__ == '__main__':
    unittest.main()
