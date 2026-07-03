<?php
/** Vapi Escalation Endpoint — accepts Vapi wrapped tool payloads. */

const VAPI_ESCALATION_SECRET = 'vapi-svic-pilot-escalation-2026';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('POST only'); }

$secret = $_SERVER['HTTP_X_VAPI_SECRET'] ?? ($_GET['token'] ?? '');
if ($secret !== VAPI_ESCALATION_SECRET) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);

function svic_find_vapi_args($value, $tool_call_id = '') {
    if (!is_array($value)) {
        return null;
    }

    $tool_call_id = $value['id'] ?? $value['toolCallId'] ?? $tool_call_id;

    if (!empty($value['caller_name']) && !empty($value['issue_summary'])) {
        return ['input' => $value, 'toolCallId' => $tool_call_id];
    }

    if (isset($value['arguments'])) {
        if (is_string($value['arguments'])) {
            $decoded = json_decode($value['arguments'], true);
            if (is_array($decoded) && !empty($decoded['caller_name']) && !empty($decoded['issue_summary'])) {
                return ['input' => $decoded, 'toolCallId' => $tool_call_id];
            }
        } elseif (is_array($value['arguments']) && !empty($value['arguments']['caller_name']) && !empty($value['arguments']['issue_summary'])) {
            return ['input' => $value['arguments'], 'toolCallId' => $tool_call_id];
        }
    }

    foreach ($value as $child) {
        $found = svic_find_vapi_args($child, $tool_call_id);
        if ($found !== null) {
            return $found;
        }
    }

    return null;
}

function svic_vapi_response($tool_call_id, $message, $is_error = false) {
    header('Content-Type: application/json');

    if ($tool_call_id !== '') {
        $row = ['toolCallId' => $tool_call_id];
        $row[$is_error ? 'error' : 'result'] = str_replace(["\r", "\n"], ' ', $message);
        echo json_encode(['results' => [$row]]);
        return;
    }

    echo json_encode($is_error ? ['error' => $message] : ['status' => 'ok', 'result' => $message]);
}

$found = svic_find_vapi_args($payload);
$input = $found['input'] ?? null;
$tool_call_id = $found['toolCallId'] ?? '';

if (!$input || empty($input['caller_name']) || empty($input['issue_summary'])) {
    svic_vapi_response($tool_call_id, 'Missing caller_name or issue_summary', true);
    exit;
}

$name  = trim($input['caller_name']);
$phone = trim($input['caller_phone'] ?? '');
$email = trim($input['caller_email'] ?? '');
$order = trim($input['order_number'] ?? '');
$cat   = trim($input['category'] ?? 'unknown');
$issue = trim($input['issue_summary']);
$lang  = trim($input['language'] ?? '');

$subject = "[Vapi Support] {$cat} — {$name}";
$message = "New escalation from phone agent\n\n";
$message .= "Caller: {$name}\n";
$message .= "Phone: {$phone}\n";
if ($email) $message .= "Email: {$email}\n";
if ($order) $message .= "Order #: {$order}\n";
$message .= "Language: {$lang}\n";
$message .= "Category: {$cat}\n\n";
$message .= "Issue:\n{$issue}\n\n";
$message .= "— Vapi Phone Support (Pilot)";

$headers = [
    'From: Vapi Agent <noreply@svicloudtvbox.us>',
    'Content-Type: text/plain; charset=UTF-8',
];

$wp_load = dirname(__DIR__, 3) . '/wp-load.php';
if (is_readable($wp_load)) {
    require_once $wp_load;
}

$sent = function_exists('wp_mail')
    ? wp_mail('support@svicloudtvbox.us', $subject, $message, $headers)
    : mail('support@svicloudtvbox.us', $subject, $message, implode("\r\n", $headers));

if (!$sent) {
    svic_vapi_response($tool_call_id, 'Email delivery failed', true);
    exit;
}

svic_vapi_response($tool_call_id, 'Escalation sent to support team');
