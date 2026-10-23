<?php
/** Isolated fixture: actual handler/template, mocked WP/Woo/mail; never sends email. */
define('ABSPATH', __DIR__);
define('HOUR_IN_SECONDS', 3600);
define('MINUTE_IN_SECONDS', 60);
$theme = dirname(__DIR__, 2) . '/theme/svicloudtvbox-lumen';
function add_action(...$args) { $GLOBALS['hooks'][] = $args; }
function wp_unslash($s) { return stripslashes($s); }
function wp_salt($s) { return 'fixture-only-not-a-production-secret'; }
function wp_verify_nonce($n, $a) { return $n === 'fixture-nonce' && $a === 'svic_support_form'; }
function sanitize_text_field($s) { return trim(preg_replace('/[\r\n\t]+/', ' ', strip_tags($s))); }
function sanitize_textarea_field($s) { return strip_tags($s); }
function is_email($s) { return filter_var($s, FILTER_VALIDATE_EMAIL); }
function get_transient($k) { $v=$GLOBALS['transients'][$k]??null; return $v && $v[1]>time() ? $v[0] : false; }
function set_transient($k,$v,$t) { $GLOBALS['transients'][$k]=[$v,time()+$t]; return true; }
function get_option($k,$d=false) { return $GLOBALS['options'][$k]??$d; }
function delete_option($k) { unset($GLOBALS['options'][$k]); return true; }
function add_option($k,$v,...$args) { if (array_key_exists($k,$GLOBALS['options'])) return false; $GLOBALS['options'][$k]=$v; return true; }
function svic_support_form_recipient() { return 'support@example.test'; }
function wp_mail(...$args) { $GLOBALS['mail'][]=$args; if ($GLOBALS['mail_throws']??false) throw new RuntimeException('fixture transport error'); return $GLOBALS['mail_ok']; }
class FixtureOrder {
    public function __construct(public int $id, public string $email='buyer@example.test', public bool $paid=true, public bool $historically_paid=true, public string $type='shop_order') {}
    public function get_id() { return $this->id; }
    public function get_type() { return $this->type; }
    public function get_billing_email() { return $this->email; }
    public function is_paid() { return $this->paid; }
    public function get_date_paid() { return $this->historically_paid ? new stdClass() : null; }
}
function wc_get_order($id) { return $GLOBALS['orders'][$id]??false; }
class SVIC_Translator {
    public static function instance() { return new self(); }
    public function registry($locale) { global $theme; return require "$theme/lang/$locale.php"; }
}
require "$theme/inc/support-form.php";
function reset_fixture() {
    $GLOBALS['transients']=[]; $GLOBALS['options']=[]; $GLOBALS['mail']=[]; $GLOBALS['mail_ok']=true; $GLOBALS['mail_throws']=false;
    $GLOBALS['orders']=[42=>new FixtureOrder(42),43=>new FixtureOrder(43,'other@example.test'),44=>new FixtureOrder(44,'buyer@example.test',false,false),45=>new FixtureOrder(45,'buyer@example.test',false,true),46=>new FixtureOrder(46,'buyer@example.test',true,true,'shop_order_refund')];
    $_SERVER['REMOTE_ADDR']='192.0.2.10'; $_SERVER['REQUEST_METHOD']='POST';
}
function payload($changes=[]) { return array_replace(['svic_support_nonce'=>'fixture-nonce','svic_locale'=>'en_US','support_kind'=>'aftersales','support_name'=>'Fixture buyer','support_email'=>'buyer@example.test','support_order'=>'42','support_device'=>'10p','support_issue'=>'apps','support_message'=>'My remote will not respond.','support_website'=>''],$changes); }
function check($ok,$name) { if (!$ok) throw new RuntimeException('FAIL: '.$name); ++$GLOBALS['checks']; echo "PASS: $name\n"; }
function scenario($name,$changes,$expected,$mail=0) { reset_fixture(); check(svic_process_support_request(payload($changes))===$expected,$name); check(count($GLOBALS['mail'])===$mail,"$name: email count"); }

// These stubs render the real page template, not a hand-written facsimile.
function svic_current_locale() { return $GLOBALS['fixture_locale']; }
function svic_translate($key) { $v=SVIC_Translator::instance()->registry(svic_current_locale()); foreach(explode('.',$key) as $k) $v=$v[$k]??$key; return is_string($v)?$v:$key; }
function esc_attr($s) { return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8'); }
function esc_html($s) { return esc_attr($s); }
function esc_url($s) { return esc_attr($s); }
function esc_attr__($s,...$args) { return esc_attr($s); }
function svic_translate_html($key) { return esc_html(svic_translate($key)); }
function svic_url_with_lang($url,...$args) { return $url; }
function home_url($path) { return $path; }
function admin_url($path) { return '/wp-admin/'.$path; }
function svic_language_query_value(...$args) { return ''; }
function get_template_directory() { return $GLOBALS['theme']; }
function get_template_directory_uri() { return '/theme/svicloudtvbox-lumen'; }
function wp_enqueue_script(...$args) { $GLOBALS['script']=$args[1]; }
function wp_nonce_field(...$args) { echo '<input type="hidden" name="svic_support_nonce" value="fixture-nonce">'; }
function selected($a,$b) { if($a===$b) echo 'selected'; }
function get_header() { echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><link rel="stylesheet" href="/theme/svicloudtvbox-lumen/assets/css/style.css"><link rel="stylesheet" href="/theme/svicloudtvbox-lumen/assets/css/support.css"></head><body>'; }
function get_footer() { echo '<script src="'.esc_attr($GLOBALS['script']).'"></script></body></html>'; }
if (($argv[1]??'')==='--render') {
    $GLOBALS['fixture_locale']=$argv[2]??'en_US';
    if (!in_array($GLOBALS['fixture_locale'],['en_US','zh_TW','zh_CN'],true)) exit(1);
    $_GET['support_status']=$argv[3]??'';
    include "$theme/page-support.php";
    exit;
}
$GLOBALS['checks']=0;
scenario('paid guest order accepted',[],'success',1);
check(str_contains($GLOBALS['mail'][0][2],'Verified order number: 42'),'verified ID in mail');
check($GLOBALS['mail'][0][3][2]==='Reply-To: buyer@example.test','reply address pinned to verified checkout email');
check(empty($GLOBALS['options']),'send lock removed');
scenario('case-insensitive checkout email',['support_email'=>'BUYER@EXAMPLE.TEST'],'success',1);
scenario('prefixed order',['support_order'=>'WC-42'],'success',1);
scenario('hash-prefixed order',['support_order'=>'#42'],'success',1);
scenario('wrong email',['support_email'=>'attacker@example.test'],'verification');
scenario('nonexistent order',['support_order'=>'999'],'verification');
scenario('missing order',['support_order'=>''],'verification');
scenario('invalid order syntax',['support_order'=>'42junk'],'verification');
scenario('unpaid order',['support_order'=>'44'],'verification');
scenario('historically paid/refunded order',['support_order'=>'45'],'success',1);
scenario('refund record not an order',['support_order'=>'46'],'verification');
scenario('wrong customer order',['support_order'=>'43'],'verification');
scenario('honeypot silently discarded',['support_website'=>'https://spam.example.test'],'success');
scenario('invalid nonce',['svic_support_nonce'=>'expired'],'error');
scenario('array input',['support_email'=>['attack']],'error');
scenario('header injection',['support_email'=>"buyer@example.test\r\nBcc: attacker@example.test"],'error');
scenario('oversize body',['support_message'=>str_repeat('x',12001)],'error');
scenario('unknown route',['support_kind'=>'bogus'],'error');
scenario('unknown category',['support_issue'=>'bogus'],'error');
scenario('unknown device',['support_device'=>'bogus'],'error');
scenario('HTML-only empty body',['support_message'=>'<script></script>'],'error');
scenario('presales without order',['support_kind'=>'presales','support_issue'=>'shipping','support_email'=>'prospect@example.test','support_order'=>''],'success',1);
check(str_contains($GLOBALS['mail'][0][1],'[SVICLOUD Presales]'),'presales distinct subject');
check(str_contains($GLOBALS['mail'][0][2],'purchase not verified'),'presales not claimed verified');
scenario('presales cannot select troubleshooting',['support_kind'=>'presales','support_issue'=>'apps'],'error');
scenario('aftersales cannot select presales',['support_issue'=>'shipping'],'error');
scenario('missing-order help never emails',['support_kind'=>'order-help'],'order-help');
scenario('other seller never emails',['support_kind'=>'other-seller'],'other-seller');
reset_fixture();
check(svic_process_support_request(payload())==='success','first message accepted');
check(svic_process_support_request(payload())==='duplicate','repeat suppressed');
check(count($GLOBALS['mail'])===1,'duplicate produces only one email');
reset_fixture();
for($i=0;$i<5;$i++) check(svic_process_support_request(payload(['support_message'=>'Message '.$i]))==='success','IP request '.($i+1));
check(svic_process_support_request(payload(['support_message'=>'Message 6']))==='limited','sixth IP attempt blocked');
reset_fixture();
for($i=0;$i<5;$i++) { $_SERVER['REMOTE_ADDR']='192.0.2.'.($i+1); svic_process_support_request(payload(['support_message'=>'Message '.$i])); }
$_SERVER['REMOTE_ADDR']='192.0.2.99';
check(svic_process_support_request(payload(['support_message'=>'New message']))==='limited','email budget survives changed IP');
reset_fixture(); $_SERVER['HTTP_X_FORWARDED_FOR']='192.0.2.90';
for($i=0;$i<5;$i++) svic_process_support_request(payload(['support_email'=>'unknown'.$i.'@example.test']));
$_SERVER['HTTP_X_FORWARDED_FOR']='192.0.2.91';
check(svic_process_support_request(payload())==='limited','spoofed forwarding header cannot bypass limit');
reset_fixture(); $GLOBALS['mail_ok']=false;
check(svic_process_support_request(payload())==='error','transport false reported error');
$GLOBALS['mail_ok']=true;
check(svic_process_support_request(payload())==='success','failed send retry not marked duplicate');
check(empty($GLOBALS['options']),'failure and retry release lock');
reset_fixture(); add_option(svic_support_key('lock','buyer@example.test'),time()+120);
check(svic_process_support_request(payload())==='limited','concurrent send blocked');
check(count($GLOBALS['mail'])===0,'concurrent send no email');
$GLOBALS['options'][svic_support_key('lock','buyer@example.test')]=time()-1;
check(svic_process_support_request(payload())==='success','expired lock recovers');
reset_fixture(); $GLOBALS['mail_throws']=true;
try { svic_process_support_request(payload()); } catch(RuntimeException $e) {}
check(empty($GLOBALS['options']),'transport exception releases lock');
check(array_column($GLOBALS['hooks'],0)===['admin_post_svic_support_form','admin_post_nopriv_svic_support_form'],'same protected handler for guests and signed-in users');
foreach(['en_US','zh_TW','zh_CN'] as $locale) {
    $registry=SVIC_Translator::instance()->registry($locale);
    $expected_keys = ['policy', 'kind', 'aftersales', 'presales', 'order-help', 'other-seller', 'order_note', 'order_help', 'other_seller', 'presales_note', 'order_label', 'trap', 'verification', 'limited', 'duplicate', 'presales_issues'];
    check(array_keys($registry['support']['guard']) === $expected_keys,"$locale guard key parity");
    check(count($registry['support']['guard']['presales_issues'])===3,"$locale presales categories");
}
echo 'TOTAL '.$GLOBALS['checks']." assertions passed; no real mail or orders.\n";
