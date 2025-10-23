<?php
/**
 * Lightweight Markdown renderer for localized content.
 * Converts headings, lists, tables, blockquotes, and inline formatting.
 */

if (!defined('ABSPATH')) {
    exit;
}

class SVIC_Markdown
{
    private const BREAK_PLACEHOLDER = '{{SVIC_BR}}';

    /**
     * Convert Markdown text into sanitized HTML.
     */
    public static function to_html(string $markdown): string
    {
        $markdown = trim($markdown);
        if ($markdown === '') {
            return '';
        }

        $markdown = str_replace("\r\n", "\n", $markdown);
        $markdown = str_replace("\r", "\n", $markdown);
        $lines    = explode("\n", $markdown);
        $total    = count($lines);
        $blocks   = [];

        $index = 0;
        while ($index < $total) {
            $line    = $lines[$index];
            $trimmed = trim($line);

            if ($trimmed === '') {
                $index++;
                continue;
            }

            if (self::is_table_start($lines, $index, $total)) {
                [$html, $index] = self::consume_table($lines, $index, $total);
                $blocks[] = $html;
                continue;
            }

            if (self::is_blockquote_line($line)) {
                [$html, $index] = self::consume_blockquote($lines, $index, $total);
                $blocks[] = $html;
                continue;
            }

            $heading = self::match_heading($line);
            if ($heading !== null) {
                [$level, $text] = $heading;
                $blocks[] = sprintf('<h%d>%s</h%d>', $level, self::convert_inline($text), $level);
                $index++;
                continue;
            }

            $listType = self::detect_list_type($line);
            if ($listType !== null) {
                [$html, $index] = self::consume_list($lines, $index, $total, $listType);
                $blocks[] = $html;
                continue;
            }

            if (self::is_horizontal_rule($trimmed)) {
                $blocks[] = '<hr />';
                $index++;
                continue;
            }

            [$html, $index] = self::consume_paragraph($lines, $index, $total);
            $blocks[] = $html;
        }

        $output = implode("\n", $blocks);
        return wp_kses_post($output);
    }

    /**
     * Return true when the provided text already contains HTML tags.
     */
    public static function looks_like_html(string $text): bool
    {
        return (bool) preg_match('/<\/?[a-z][^>]*>/i', $text);
    }

    private static function consume_paragraph(array $lines, int $index, int $total): array
    {
        $buffer = [];
        $cursor = $index;

        while ($cursor < $total) {
            $line    = $lines[$cursor];
            $trimmed = trim($line);

            if ($trimmed === '') {
                break;
            }

            if ($cursor !== $index) {
                if (self::is_table_start($lines, $cursor, $total)) {
                    break;
                }
                if (self::is_blockquote_line($line)) {
                    break;
                }
                if (self::detect_list_type($line) !== null) {
                    break;
                }
                if (self::match_heading($line) !== null) {
                    break;
                }
                if (self::is_horizontal_rule($trimmed)) {
                    break;
                }
            }

            $buffer[] = $line;
            $cursor++;
        }

        $text = implode("\n", $buffer);
        $html = '<p>' . self::convert_inline($text) . '</p>';

        return [$html, $cursor];
    }

    private static function consume_blockquote(array $lines, int $index, int $total): array
    {
        $buffer = [];
        $cursor = $index;

        while ($cursor < $total) {
            $line    = $lines[$cursor];
            $trimmed = trim($line);

            if ($trimmed === '') {
                break;
            }

            if (!self::is_blockquote_line($line)) {
                break;
            }

            $buffer[] = preg_replace('/^\s{0,3}>\s?/', '', $line);
            $cursor++;
        }

        $content = implode("\n", $buffer);
        $html    = '<blockquote><p>' . self::convert_inline($content) . '</p></blockquote>';

        return [$html, $cursor];
    }

    private static function consume_list(array $lines, int $index, int $total, string $type): array
    {
        $items  = [];
        $cursor = $index;

        while ($cursor < $total) {
            $line = $lines[$cursor];
            if (trim($line) === '') {
                break;
            }

            $currentType = self::detect_list_type($line);
            if ($currentType !== $type) {
                break;
            }

            if ($type === 'ol') {
                $items[] = preg_replace('/^\s*\d+\.\s+/', '', $line);
            } else {
                $items[] = preg_replace('/^\s*[-*+]\s+/', '', $line);
            }

            $cursor++;
        }

        $tag  = $type === 'ol' ? 'ol' : 'ul';
        $html = "<{$tag}>";
        foreach ($items as $item) {
            $html .= '<li>' . self::convert_inline($item) . '</li>';
        }
        $html .= "</{$tag}>";

        return [$html, $cursor];
    }

    private static function consume_table(array $lines, int $index, int $total): array
    {
        $header  = $lines[$index];
        $divider = $index + 1 < $total ? $lines[$index + 1] : '';
        $rows    = [];
        $cursor  = $index + 2;

        while ($cursor < $total) {
            $line    = $lines[$cursor];
            $trimmed = trim($line);

            if ($trimmed === '' || !self::line_has_table_pipe($line)) {
                break;
            }

            $rows[] = $line;
            $cursor++;
        }

        $alignments = self::parse_table_alignment($divider);
        $headCells  = self::split_table_row($header);
        $bodyRows   = array_map([self::class, 'split_table_row'], $rows);

        $table  = '<div class="svic-markdown-table">';
        $table .= '<table>';
        $table .= '<thead><tr>';
        foreach ($headCells as $idx => $cell) {
            $align = $alignments[$idx] ?? '';
            $attr  = $align !== '' ? ' style="text-align:' . esc_attr($align) . ';"' : '';
            $table .= '<th' . $attr . '>' . self::convert_inline($cell) . '</th>';
        }
        $table .= '</tr></thead>';

        if (!empty($bodyRows)) {
            $table .= '<tbody>';
            foreach ($bodyRows as $row) {
                $table .= '<tr>';
                foreach ($row as $idx => $cell) {
                    $align = $alignments[$idx] ?? '';
                    $attr  = $align !== '' ? ' style="text-align:' . esc_attr($align) . ';"' : '';
                    $table .= '<td' . $attr . '>' . self::convert_inline($cell) . '</td>';
                }
                $table .= '</tr>';
            }
            $table .= '</tbody>';
        }

        $table .= '</table></div>';

        return [$table, $cursor];
    }

    private static function convert_inline(string $text): string
    {
        $text = preg_replace("/\s+$/", '', $text);
        $text = preg_replace("/\t/", '    ', $text);

        $text = preg_replace("/ {2,}\n/", self::BREAK_PLACEHOLDER . "\n", $text);
        $text = str_replace("\n", ' ', $text);

        $codePlaceholders = [];
        $codeIndex        = 0;
        $text = preg_replace_callback('/`([^`]+)`/', function ($matches) use (&$codePlaceholders, &$codeIndex) {
            $placeholder = '{{SVIC_CODE_' . $codeIndex . '}}';
            $codeIndex++;
            $codePlaceholders[$placeholder] = '<code>' . esc_html($matches[1]) . '</code>';
            return $placeholder;
        }, $text);

        $escaped = esc_html($text);

        $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped);
        $escaped = preg_replace('/__(.+?)__/s', '<strong>$1</strong>', $escaped);

        $escaped = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $escaped);
        $escaped = preg_replace('/(?<!_)_(?!_)(.+?)(?<!_)_(?!_)/s', '<em>$1</em>', $escaped);
        $escaped = preg_replace('/~~(.+?)~~/s', '<del>$1</del>', $escaped);

        $escaped = preg_replace_callback('/!\[([^\]]*)\]\(([^)\s]+)(?:\s+"([^"]*)")?\)/', function ($matches) {
            $alt   = esc_attr($matches[1]);
            $src   = esc_url($matches[2]);
            $title = isset($matches[3]) && $matches[3] !== '' ? ' title="' . esc_attr($matches[3]) . '"' : '';
            return '<img src="' . $src . '" alt="' . $alt . '"' . $title . ' />';
        }, $escaped);

        $escaped = preg_replace_callback('/\[([^\]]+)\]\(([^)\s]+)(?:\s+"([^"]+)")?\)/', function ($matches) {
            $label = $matches[1];
            $href  = esc_url($matches[2]);
            $title = isset($matches[3]) && $matches[3] !== '' ? ' title="' . esc_attr($matches[3]) . '"' : '';
            return '<a href="' . $href . '"' . $title . '>' . $label . '</a>';
        }, $escaped);

        if (!empty($codePlaceholders)) {
            $escaped = str_replace(array_keys($codePlaceholders), array_values($codePlaceholders), $escaped);
        }

        $escaped = str_replace(self::BREAK_PLACEHOLDER, '<br />', $escaped);

        return $escaped;
    }

    private static function split_table_row(string $line): array
    {
        $line = trim($line);
        $line = trim($line, '|');
        if ($line === '') {
            return [];
        }

        $parts = preg_split('/\|/', $line);
        $parts = array_map('trim', $parts);
        return $parts;
    }

    private static function parse_table_alignment(string $divider): array
    {
        $divider = trim($divider);
        if ($divider === '') {
            return [];
        }

        $parts = self::split_table_row($divider);
        $alignments = [];
        foreach ($parts as $part) {
            $part = trim($part);
            $left  = strlen($part) > 0 && $part[0] === ':';
            $right = strlen($part) > 0 && substr($part, -1) === ':';
            if ($left && $right) {
                $alignments[] = 'center';
            } elseif ($right) {
                $alignments[] = 'right';
            } elseif ($left) {
                $alignments[] = 'left';
            } else {
                $alignments[] = '';
            }
        }

        return $alignments;
    }

    private static function detect_list_type(string $line): ?string
    {
        if (preg_match('/^\s*\d+\.\s+/', $line)) {
            return 'ol';
        }

        if (preg_match('/^\s*[-*+]\s+/', $line)) {
            return 'ul';
        }

        return null;
    }

    private static function is_blockquote_line(string $line): bool
    {
        return (bool) preg_match('/^\s{0,3}>\s?.*/', $line);
    }

    private static function is_table_start(array $lines, int $index, int $total): bool
    {
        if ($index + 1 >= $total) {
            return false;
        }

        $header  = $lines[$index];
        $divider = $lines[$index + 1];

        if (!self::line_has_table_pipe($header)) {
            return false;
        }

        return self::is_table_divider($divider);
    }

    private static function line_has_table_pipe(string $line): bool
    {
        return strpos($line, '|') !== false;
    }

    private static function is_table_divider(string $line): bool
    {
        $trimmed = trim($line);
        if ($trimmed === '') {
            return false;
        }

        return (bool) preg_match('/^\s*\|?\s*:?[-]{3,}:?(\s*\|\s*:?[-]{3,}:?)*\s*\|?\s*$/', $trimmed);
    }

    private static function is_horizontal_rule(string $line): bool
    {
        return (bool) preg_match('/^([-*_])(\s*\1){2,}$/', $line);
    }

    private static function match_heading(string $line): ?array
    {
        if (preg_match('/^\s{0,3}(#{1,6})\s+(.*)$/', $line, $matches)) {
            $level = strlen($matches[1]);
            $text  = $matches[2];
            return [$level, $text];
        }

        return null;
    }
}
