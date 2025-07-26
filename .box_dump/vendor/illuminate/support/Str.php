<?php

namespace Illuminate\Support;

use Closure;
use Illuminate\Support\Traits\Macroable;
use JsonException;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\MarkdownConverter;
use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Generator\CombGenerator;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Symfony\Component\Uid\Ulid;
use Throwable;
use Traversable;
use voku\helper\ASCII;

class Str
{
use Macroable;






const INVISIBLE_CHARACTERS = '\x{0009}\x{0020}\x{00A0}\x{00AD}\x{034F}\x{061C}\x{115F}\x{1160}\x{17B4}\x{17B5}\x{180E}\x{2000}\x{2001}\x{2002}\x{2003}\x{2004}\x{2005}\x{2006}\x{2007}\x{2008}\x{2009}\x{200A}\x{200B}\x{200C}\x{200D}\x{200E}\x{200F}\x{202F}\x{205F}\x{2060}\x{2061}\x{2062}\x{2063}\x{2064}\x{2065}\x{206A}\x{206B}\x{206C}\x{206D}\x{206E}\x{206F}\x{3000}\x{2800}\x{3164}\x{FEFF}\x{FFA0}\x{1D159}\x{1D173}\x{1D174}\x{1D175}\x{1D176}\x{1D177}\x{1D178}\x{1D179}\x{1D17A}\x{E0020}';






protected static $snakeCache = [];






protected static $camelCache = [];






protected static $studlyCache = [];






protected static $uuidFactory;






protected static $ulidFactory;






protected static $randomStringFactory;







public static function of($string)
{
return new Stringable($string);
}








public static function after($subject, $search)
{
return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
}








public static function afterLast($subject, $search)
{
if ($search === '') {
return $subject;
}

$position = strrpos($subject, (string) $search);

if ($position === false) {
return $subject;
}

return substr($subject, $position + strlen($search));
}








public static function ascii($value, $language = 'en')
{
return ASCII::to_ascii((string) $value, $language, replace_single_chars_only: false);
}









public static function transliterate($string, $unknown = '?', $strict = false)
{
return ASCII::to_transliterate($string, $unknown, $strict);
}








public static function before($subject, $search)
{
if ($search === '') {
return $subject;
}

$result = strstr($subject, (string) $search, true);

return $result === false ? $subject : $result;
}








public static function beforeLast($subject, $search)
{
if ($search === '') {
return $subject;
}

$pos = mb_strrpos($subject, $search);

if ($pos === false) {
return $subject;
}

return static::substr($subject, 0, $pos);
}









public static function between($subject, $from, $to)
{
if ($from === '' || $to === '') {
return $subject;
}

return static::beforeLast(static::after($subject, $from), $to);
}









public static function betweenFirst($subject, $from, $to)
{
if ($from === '' || $to === '') {
return $subject;
}

return static::before(static::after($subject, $from), $to);
}







public static function camel($value)
{
if (isset(static::$camelCache[$value])) {
return static::$camelCache[$value];
}

return static::$camelCache[$value] = lcfirst(static::studly($value));
}








public static function charAt($subject, $index)
{
$length = mb_strlen($subject);

if ($index < 0 ? $index < -$length : $index > $length - 1) {
return false;
}

return mb_substr($subject, $index, 1);
}








public static function chopStart($subject, $needle)
{
foreach ((array) $needle as $n) {
if (str_starts_with($subject, $n)) {
return substr($subject, strlen($n));
}
}

return $subject;
}








public static function chopEnd($subject, $needle)
{
foreach ((array) $needle as $n) {
if (str_ends_with($subject, $n)) {
return substr($subject, 0, -strlen($n));
}
}

return $subject;
}









public static function contains($haystack, $needles, $ignoreCase = false)
{
if (is_null($haystack)) {
return false;
}

if ($ignoreCase) {
$haystack = mb_strtolower($haystack);
}

if (! is_iterable($needles)) {
$needles = (array) $needles;
}

foreach ($needles as $needle) {
if ($ignoreCase) {
$needle = mb_strtolower($needle);
}

if ($needle !== '' && str_contains($haystack, $needle)) {
return true;
}
}

return false;
}









public static function containsAll($haystack, $needles, $ignoreCase = false)
{
foreach ($needles as $needle) {
if (! static::contains($haystack, $needle, $ignoreCase)) {
return false;
}
}

return true;
}









public static function doesntContain($haystack, $needles, $ignoreCase = false)
{
return ! static::contains($haystack, $needles, $ignoreCase);
}









public static function convertCase(string $string, int $mode = MB_CASE_FOLD, ?string $encoding = 'UTF-8')
{
return mb_convert_case($string, $mode, $encoding);
}








public static function deduplicate(string $string, string $character = ' ')
{
return preg_replace('/'.preg_quote($character, '/').'+/u', $character, $string);
}








public static function endsWith($haystack, $needles)
{
if (is_null($haystack)) {
return false;
}

if (! is_iterable($needles)) {
$needles = (array) $needles;
}

foreach ($needles as $needle) {
if ((string) $needle !== '' && str_ends_with($haystack, $needle)) {
return true;
}
}

return false;
}








public static function doesntEndWith($haystack, $needles)
{
return ! static::endsWith($haystack, $needles);
}









public static function excerpt($text, $phrase = '', $options = [])
{
$radius = $options['radius'] ?? 100;
$omission = $options['omission'] ?? '...';

preg_match('/^(.*?)('.preg_quote((string) $phrase, '/').')(.*)$/iu', (string) $text, $matches);

if (empty($matches)) {
return null;
}

$start = ltrim($matches[1]);

$start = Str::of(mb_substr($start, max(mb_strlen($start, 'UTF-8') - $radius, 0), $radius, 'UTF-8'))->ltrim()->unless(
fn ($startWithRadius) => $startWithRadius->exactly($start),
fn ($startWithRadius) => $startWithRadius->prepend($omission),
);

$end = rtrim($matches[3]);

$end = Str::of(mb_substr($end, 0, $radius, 'UTF-8'))->rtrim()->unless(
fn ($endWithRadius) => $endWithRadius->exactly($end),
fn ($endWithRadius) => $endWithRadius->append($omission),
);

return $start->append($matches[2], $end)->toString();
}








public static function finish($value, $cap)
{
$quoted = preg_quote($cap, '/');

return preg_replace('/(?:'.$quoted.')+$/u', '', $value).$cap;
}









public static function wrap($value, $before, $after = null)
{
return $before.$value.($after ?? $before);
}









public static function unwrap($value, $before, $after = null)
{
if (static::startsWith($value, $before)) {
$value = static::substr($value, static::length($before));
}

if (static::endsWith($value, $after ??= $before)) {
$value = static::substr($value, 0, -static::length($after));
}

return $value;
}









public static function is($pattern, $value, $ignoreCase = false)
{
$value = (string) $value;

if (! is_iterable($pattern)) {
$pattern = [$pattern];
}

foreach ($pattern as $pattern) {
$pattern = (string) $pattern;




if ($pattern === '*' || $pattern === $value) {
return true;
}

if ($ignoreCase && mb_strtolower($pattern) === mb_strtolower($value)) {
return true;
}

$pattern = preg_quote($pattern, '#');




$pattern = str_replace('\*', '.*', $pattern);

if (preg_match('#^'.$pattern.'\z#'.($ignoreCase ? 'isu' : 'su'), $value) === 1) {
return true;
}
}

return false;
}







public static function isAscii($value)
{
return ASCII::is_ascii((string) $value);
}







public static function isJson($value)
{
if (! is_string($value)) {
return false;
}

if (function_exists('json_validate')) {
return json_validate($value, 512);
}

try {
json_decode($value, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException) {
return false;
}

return true;
}








public static function isUrl($value, array $protocols = [])
{
if (! is_string($value)) {
return false;
}

$protocolList = empty($protocols)
? 'aaa|aaas|about|acap|acct|acd|acr|adiumxtra|adt|afp|afs|aim|amss|android|appdata|apt|ark|attachment|aw|barion|beshare|bitcoin|bitcoincash|blob|bolo|browserext|calculator|callto|cap|cast|casts|chrome|chrome-extension|cid|coap|coap\+tcp|coap\+ws|coaps|coaps\+tcp|coaps\+ws|com-eventbrite-attendee|content|conti|crid|cvs|dab|data|dav|diaspora|dict|did|dis|dlna-playcontainer|dlna-playsingle|dns|dntp|dpp|drm|drop|dtn|dvb|ed2k|elsi|example|facetime|fax|feed|feedready|file|filesystem|finger|first-run-pen-experience|fish|fm|ftp|fuchsia-pkg|geo|gg|git|gizmoproject|go|gopher|graph|gtalk|h323|ham|hcap|hcp|http|https|hxxp|hxxps|hydrazone|iax|icap|icon|im|imap|info|iotdisco|ipn|ipp|ipps|irc|irc6|ircs|iris|iris\.beep|iris\.lwz|iris\.xpc|iris\.xpcs|isostore|itms|jabber|jar|jms|keyparc|lastfm|ldap|ldaps|leaptofrogans|lorawan|lvlt|magnet|mailserver|mailto|maps|market|message|mid|mms|modem|mongodb|moz|ms-access|ms-browser-extension|ms-calculator|ms-drive-to|ms-enrollment|ms-excel|ms-eyecontrolspeech|ms-gamebarservices|ms-gamingoverlay|ms-getoffice|ms-help|ms-infopath|ms-inputapp|ms-lockscreencomponent-config|ms-media-stream-id|ms-mixedrealitycapture|ms-mobileplans|ms-officeapp|ms-people|ms-project|ms-powerpoint|ms-publisher|ms-restoretabcompanion|ms-screenclip|ms-screensketch|ms-search|ms-search-repair|ms-secondary-screen-controller|ms-secondary-screen-setup|ms-settings|ms-settings-airplanemode|ms-settings-bluetooth|ms-settings-camera|ms-settings-cellular|ms-settings-cloudstorage|ms-settings-connectabledevices|ms-settings-displays-topology|ms-settings-emailandaccounts|ms-settings-language|ms-settings-location|ms-settings-lock|ms-settings-nfctransactions|ms-settings-notifications|ms-settings-power|ms-settings-privacy|ms-settings-proximity|ms-settings-screenrotation|ms-settings-wifi|ms-settings-workplace|ms-spd|ms-sttoverlay|ms-transit-to|ms-useractivityset|ms-virtualtouchpad|ms-visio|ms-walk-to|ms-whiteboard|ms-whiteboard-cmd|ms-word|msnim|msrp|msrps|mss|mtqp|mumble|mupdate|mvn|news|nfs|ni|nih|nntp|notes|ocf|oid|onenote|onenote-cmd|opaquelocktoken|openpgp4fpr|pack|palm|paparazzi|payto|pkcs11|platform|pop|pres|prospero|proxy|pwid|psyc|pttp|qb|query|redis|rediss|reload|res|resource|rmi|rsync|rtmfp|rtmp|rtsp|rtsps|rtspu|s3|secondlife|service|session|sftp|sgn|shttp|sieve|simpleledger|sip|sips|skype|smb|sms|smtp|snews|snmp|soap\.beep|soap\.beeps|soldat|spiffe|spotify|ssh|steam|stun|stuns|submit|svn|tag|teamspeak|tel|teliaeid|telnet|tftp|tg|things|thismessage|tip|tn3270|tool|ts3server|turn|turns|tv|udp|unreal|urn|ut2004|v-event|vemmi|ventrilo|videotex|vnc|view-source|wais|webcal|wpid|ws|wss|wtai|wyciwyg|xcon|xcon-userid|xfire|xmlrpc\.beep|xmlrpc\.beeps|xmpp|xri|ymsgr|z39\.50|z39\.50r|z39\.50s'
: implode('|', $protocols);






$pattern = '~^
            (LARAVEL_PROTOCOLS)://                                 # protocol
            (((?:[\_\.\pL\pN-]|%[0-9A-Fa-f]{2})+:)?((?:[\_\.\pL\pN-]|%[0-9A-Fa-f]{2})+)@)?  # basic auth
            (
                ([\pL\pN\pS\-\_\.])+(\.?([\pL\pN]|xn\-\-[\pL\pN-]+)+\.?) # a domain name
                    |                                                 # or
                \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}                    # an IP address
                    |                                                 # or
                \[
                    (?:(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){6})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:::(?:(?:(?:[0-9a-f]{1,4})):){5})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){4})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,1}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){3})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,2}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){2})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,3}(?:(?:[0-9a-f]{1,4})))?::(?:(?:[0-9a-f]{1,4})):)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,4}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,5}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,6}(?:(?:[0-9a-f]{1,4})))?::))))
                \]  # an IPv6 address
            )
            (:[0-9]+)?                              # a port (optional)
            (?:/ (?:[\pL\pN\-._\~!$&\'()*+,;=:@]|%[0-9A-Fa-f]{2})* )*          # a path
            (?:\? (?:[\pL\pN\-._\~!$&\'\[\]()*+,;=:@/?]|%[0-9A-Fa-f]{2})* )?   # a query (optional)
            (?:\# (?:[\pL\pN\-._\~!$&\'()*+,;=:@/?]|%[0-9A-Fa-f]{2})* )?       # a fragment (optional)
        $~ixu';

return preg_match(str_replace('LARAVEL_PROTOCOLS', $protocolList, $pattern), $value) > 0;
}








public static function isUuid($value, $version = null)
{
if (! is_string($value)) {
return false;
}

if ($version === null) {
return preg_match('/^[\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}$/D', $value) > 0;
}

$factory = new UuidFactory;

try {
$factoryUuid = $factory->fromString($value);
} catch (InvalidUuidStringException) {
return false;
}

$fields = $factoryUuid->getFields();

if (! ($fields instanceof FieldsInterface)) {
return false;
}

if ($version === 0 || $version === 'nil') {
return $fields->isNil();
}

if ($version === 'max') {
return $fields->isMax();
}

return $fields->getVersion() === $version;
}







public static function isUlid($value)
{
if (! is_string($value)) {
return false;
}

return Ulid::isValid($value);
}







public static function kebab($value)
{
return static::snake($value, '-');
}








public static function length($value, $encoding = null)
{
return mb_strlen($value, $encoding);
}










public static function limit($value, $limit = 100, $end = '...', $preserveWords = false)
{
if (mb_strwidth($value, 'UTF-8') <= $limit) {
return $value;
}

if (! $preserveWords) {
return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')).$end;
}

$value = trim(preg_replace('/[\n\r]+/', ' ', strip_tags($value)));

$trimmed = rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8'));

if (mb_substr($value, $limit, 1, 'UTF-8') === ' ') {
return $trimmed.$end;
}

return preg_replace("/(.*)\s.*/", '$1', $trimmed).$end;
}







public static function lower($value)
{
return mb_strtolower($value, 'UTF-8');
}









public static function words($value, $words = 100, $end = '...')
{
preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

if (! isset($matches[0]) || static::length($value) === static::length($matches[0])) {
return $value;
}

return rtrim($matches[0]).$end;
}









public static function markdown($string, array $options = [], array $extensions = [])
{
$converter = new GithubFlavoredMarkdownConverter($options);

$environment = $converter->getEnvironment();

foreach ($extensions as $extension) {
$environment->addExtension($extension);
}

return (string) $converter->convert($string);
}









public static function inlineMarkdown($string, array $options = [], array $extensions = [])
{
$environment = new Environment($options);

$environment->addExtension(new GithubFlavoredMarkdownExtension());
$environment->addExtension(new InlinesOnlyExtension());

foreach ($extensions as $extension) {
$environment->addExtension($extension);
}

$converter = new MarkdownConverter($environment);

return (string) $converter->convert($string);
}











public static function mask($string, $character, $index, $length = null, $encoding = 'UTF-8')
{
if ($character === '') {
return $string;
}

$segment = mb_substr($string, $index, $length, $encoding);

if ($segment === '') {
return $string;
}

$strlen = mb_strlen($string, $encoding);
$startIndex = $index;

if ($index < 0) {
$startIndex = $index < -$strlen ? 0 : $strlen + $index;
}

$start = mb_substr($string, 0, $startIndex, $encoding);
$segmentLen = mb_strlen($segment, $encoding);
$end = mb_substr($string, $startIndex + $segmentLen);

return $start.str_repeat(mb_substr($character, 0, 1, $encoding), $segmentLen).$end;
}








public static function match($pattern, $subject)
{
preg_match($pattern, $subject, $matches);

if (! $matches) {
return '';
}

return $matches[1] ?? $matches[0];
}








public static function isMatch($pattern, $value)
{
$value = (string) $value;

if (! is_iterable($pattern)) {
$pattern = [$pattern];
}

foreach ($pattern as $pattern) {
$pattern = (string) $pattern;

if (preg_match($pattern, $value) === 1) {
return true;
}
}

return false;
}








public static function matchAll($pattern, $subject)
{
preg_match_all($pattern, $subject, $matches);

if (empty($matches[0])) {
return new Collection;
}

return new Collection($matches[1] ?? $matches[0]);
}







public static function numbers($value)
{
return preg_replace('/[^0-9]/', '', $value);
}









public static function padBoth($value, $length, $pad = ' ')
{
if (function_exists('mb_str_pad')) {
return mb_str_pad($value, $length, $pad, STR_PAD_BOTH);
}

$short = max(0, $length - mb_strlen($value));
$shortLeft = floor($short / 2);
$shortRight = ceil($short / 2);

return mb_substr(str_repeat($pad, $shortLeft), 0, $shortLeft).
$value.
mb_substr(str_repeat($pad, $shortRight), 0, $shortRight);
}









public static function padLeft($value, $length, $pad = ' ')
{
if (function_exists('mb_str_pad')) {
return mb_str_pad($value, $length, $pad, STR_PAD_LEFT);
}

$short = max(0, $length - mb_strlen($value));

return mb_substr(str_repeat($pad, $short), 0, $short).$value;
}









public static function padRight($value, $length, $pad = ' ')
{
if (function_exists('mb_str_pad')) {
return mb_str_pad($value, $length, $pad, STR_PAD_RIGHT);
}

$short = max(0, $length - mb_strlen($value));

return $value.mb_substr(str_repeat($pad, $short), 0, $short);
}








public static function parseCallback($callback, $default = null)
{
if (static::contains($callback, "@anonymous\0")) {
if (static::substrCount($callback, '@') > 1) {
return [
static::beforeLast($callback, '@'),
static::afterLast($callback, '@'),
];
}

return [$callback, $default];
}

return static::contains($callback, '@') ? explode('@', $callback, 2) : [$callback, $default];
}








public static function plural($value, $count = 2)
{
return Pluralizer::plural($value, $count);
}








public static function pluralStudly($value, $count = 2)
{
$parts = preg_split('/(.)(?=[A-Z])/u', $value, -1, PREG_SPLIT_DELIM_CAPTURE);

$lastWord = array_pop($parts);

return implode('', $parts).self::plural($lastWord, $count);
}








public static function pluralPascal($value, $count = 2)
{
return static::pluralStudly($value, $count);
}











public static function password($length = 32, $letters = true, $numbers = true, $symbols = true, $spaces = false)
{
$password = new Collection();

$options = (new Collection([
'letters' => $letters === true ? [
'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
] : null,
'numbers' => $numbers === true ? [
'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
] : null,
'symbols' => $symbols === true ? [
'~', '!', '#', '$', '%', '^', '&', '*', '(', ')', '-',
'_', '.', ',', '<', '>', '?', '/', '\\', '{', '}', '[',
']', '|', ':', ';',
] : null,
'spaces' => $spaces === true ? [' '] : null,
]))
->filter()
->each(fn ($c) => $password->push($c[random_int(0, count($c) - 1)]))
->flatten();

$length = $length - $password->count();

return $password->merge($options->pipe(
fn ($c) => Collection::times($length, fn () => $c[random_int(0, $c->count() - 1)])
))->shuffle()->implode('');
}










public static function position($haystack, $needle, $offset = 0, $encoding = null)
{
return mb_strpos($haystack, (string) $needle, $offset, $encoding);
}







public static function random($length = 16)
{
return (static::$randomStringFactory ?? function ($length) {
$string = '';

while (($len = strlen($string)) < $length) {
$size = $length - $len;

$bytesSize = (int) ceil($size / 3) * 3;

$bytes = random_bytes($bytesSize);

$string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
}

return $string;
})($length);
}







public static function createRandomStringsUsing(?callable $factory = null)
{
static::$randomStringFactory = $factory;
}








public static function createRandomStringsUsingSequence(array $sequence, $whenMissing = null)
{
$next = 0;

$whenMissing ??= function ($length) use (&$next) {
$factoryCache = static::$randomStringFactory;

static::$randomStringFactory = null;

$randomString = static::random($length);

static::$randomStringFactory = $factoryCache;

$next++;

return $randomString;
};

static::createRandomStringsUsing(function ($length) use (&$next, $sequence, $whenMissing) {
if (array_key_exists($next, $sequence)) {
return $sequence[$next++];
}

return $whenMissing($length);
});
}






public static function createRandomStringsNormally()
{
static::$randomStringFactory = null;
}








public static function repeat(string $string, int $times)
{
return str_repeat($string, $times);
}









public static function replaceArray($search, $replace, $subject)
{
if ($replace instanceof Traversable) {
$replace = Arr::from($replace);
}

$segments = explode($search, $subject);

$result = array_shift($segments);

foreach ($segments as $segment) {
$result .= self::toStringOr(array_shift($replace) ?? $search, $search).$segment;
}

return $result;
}








private static function toStringOr($value, $fallback)
{
try {
return (string) $value;
} catch (Throwable $e) {
return $fallback;
}
}










public static function replace($search, $replace, $subject, $caseSensitive = true)
{
if ($search instanceof Traversable) {
$search = Arr::from($search);
}

if ($replace instanceof Traversable) {
$replace = Arr::from($replace);
}

if ($subject instanceof Traversable) {
$subject = Arr::from($subject);
}

return $caseSensitive
? str_replace($search, $replace, $subject)
: str_ireplace($search, $replace, $subject);
}









public static function replaceFirst($search, $replace, $subject)
{
$search = (string) $search;

if ($search === '') {
return $subject;
}

$position = strpos($subject, $search);

if ($position !== false) {
return substr_replace($subject, $replace, $position, strlen($search));
}

return $subject;
}









public static function replaceStart($search, $replace, $subject)
{
$search = (string) $search;

if ($search === '') {
return $subject;
}

if (static::startsWith($subject, $search)) {
return static::replaceFirst($search, $replace, $subject);
}

return $subject;
}









public static function replaceLast($search, $replace, $subject)
{
$search = (string) $search;

if ($search === '') {
return $subject;
}

$position = strrpos($subject, $search);

if ($position !== false) {
return substr_replace($subject, $replace, $position, strlen($search));
}

return $subject;
}









public static function replaceEnd($search, $replace, $subject)
{
$search = (string) $search;

if ($search === '') {
return $subject;
}

if (static::endsWith($subject, $search)) {
return static::replaceLast($search, $replace, $subject);
}

return $subject;
}










public static function replaceMatches($pattern, $replace, $subject, $limit = -1)
{
if ($replace instanceof Closure) {
return preg_replace_callback($pattern, $replace, $subject, $limit);
}

return preg_replace($pattern, $replace, $subject, $limit);
}









public static function remove($search, $subject, $caseSensitive = true)
{
if ($search instanceof Traversable) {
$search = Arr::from($search);
}

return $caseSensitive
? str_replace($search, '', $subject)
: str_ireplace($search, '', $subject);
}







public static function reverse(string $value)
{
return implode(array_reverse(mb_str_split($value)));
}








public static function start($value, $prefix)
{
$quoted = preg_quote($prefix, '/');

return $prefix.preg_replace('/^(?:'.$quoted.')+/u', '', $value);
}







public static function upper($value)
{
return mb_strtoupper($value, 'UTF-8');
}







public static function title($value)
{
return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
}







public static function headline($value)
{
$parts = mb_split('\s+', $value);

$parts = count($parts) > 1
? array_map(static::title(...), $parts)
: array_map(static::title(...), static::ucsplit(implode('_', $parts)));

$collapsed = static::replace(['-', '_', ' '], '_', implode('_', $parts));

return implode(' ', array_filter(explode('_', $collapsed)));
}









public static function apa($value)
{
if (trim($value) === '') {
return $value;
}

$minorWords = [
'and', 'as', 'but', 'for', 'if', 'nor', 'or', 'so', 'yet', 'a', 'an',
'the', 'at', 'by', 'for', 'in', 'of', 'off', 'on', 'per', 'to', 'up', 'via',
'et', 'ou', 'un', 'une', 'la', 'le', 'les', 'de', 'du', 'des', 'par', 'à',
];

$endPunctuation = ['.', '!', '?', ':', '—', ','];

$words = mb_split('\s+', $value);

for ($i = 0; $i < count($words); $i++) {
$lowercaseWord = mb_strtolower($words[$i]);

if (str_contains($lowercaseWord, '-')) {
$hyphenatedWords = explode('-', $lowercaseWord);

$hyphenatedWords = array_map(function ($part) use ($minorWords) {
return (in_array($part, $minorWords) && mb_strlen($part) <= 3)
? $part
: mb_strtoupper(mb_substr($part, 0, 1)).mb_substr($part, 1);
}, $hyphenatedWords);

$words[$i] = implode('-', $hyphenatedWords);
} else {
if (in_array($lowercaseWord, $minorWords) &&
mb_strlen($lowercaseWord) <= 3 &&
! ($i === 0 || in_array(mb_substr($words[$i - 1], -1), $endPunctuation))) {
$words[$i] = $lowercaseWord;
} else {
$words[$i] = mb_strtoupper(mb_substr($lowercaseWord, 0, 1)).mb_substr($lowercaseWord, 1);
}
}
}

return implode(' ', $words);
}







public static function singular($value)
{
return Pluralizer::singular($value);
}










public static function slug($title, $separator = '-', $language = 'en', $dictionary = ['@' => 'at'])
{
$title = $language ? static::ascii($title, $language) : $title;


$flip = $separator === '-' ? '_' : '-';

$title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);


foreach ($dictionary as $key => $value) {
$dictionary[$key] = $separator.$value.$separator;
}

$title = str_replace(array_keys($dictionary), array_values($dictionary), $title);


$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', static::lower($title));


$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

return trim($title, $separator);
}








public static function snake($value, $delimiter = '_')
{
$key = $value;

if (isset(static::$snakeCache[$key][$delimiter])) {
return static::$snakeCache[$key][$delimiter];
}

if (! ctype_lower($value)) {
$value = preg_replace('/\s+/u', '', ucwords($value));

$value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value));
}

return static::$snakeCache[$key][$delimiter] = $value;
}








public static function trim($value, $charlist = null)
{
if ($charlist === null) {
$trimDefaultCharacters = " \n\r\t\v\0";

return preg_replace('~^[\s'.self::INVISIBLE_CHARACTERS.$trimDefaultCharacters.']+|[\s'.self::INVISIBLE_CHARACTERS.$trimDefaultCharacters.']+$~u', '', $value) ?? trim($value);
}

return trim($value, $charlist);
}








public static function ltrim($value, $charlist = null)
{
if ($charlist === null) {
$ltrimDefaultCharacters = " \n\r\t\v\0";

return preg_replace('~^[\s'.self::INVISIBLE_CHARACTERS.$ltrimDefaultCharacters.']+~u', '', $value) ?? ltrim($value);
}

return ltrim($value, $charlist);
}








public static function rtrim($value, $charlist = null)
{
if ($charlist === null) {
$rtrimDefaultCharacters = " \n\r\t\v\0";

return preg_replace('~[\s'.self::INVISIBLE_CHARACTERS.$rtrimDefaultCharacters.']+$~u', '', $value) ?? rtrim($value);
}

return rtrim($value, $charlist);
}







public static function squish($value)
{
return preg_replace('~(\s|\x{3164}|\x{1160})+~u', ' ', static::trim($value));
}








public static function startsWith($haystack, $needles)
{
if (is_null($haystack)) {
return false;
}

if (! is_iterable($needles)) {
$needles = [$needles];
}

foreach ($needles as $needle) {
if ((string) $needle !== '' && str_starts_with($haystack, $needle)) {
return true;
}
}

return false;
}








public static function doesntStartWith($haystack, $needles)
{
return ! static::startsWith($haystack, $needles);
}







public static function studly($value)
{
$key = $value;

if (isset(static::$studlyCache[$key])) {
return static::$studlyCache[$key];
}

$words = mb_split('\s+', static::replace(['-', '_'], ' ', $value));

$studlyWords = array_map(fn ($word) => static::ucfirst($word), $words);

return static::$studlyCache[$key] = implode($studlyWords);
}







public static function pascal($value)
{
return static::studly($value);
}










public static function substr($string, $start, $length = null, $encoding = 'UTF-8')
{
return mb_substr($string, $start, $length, $encoding);
}










public static function substrCount($haystack, $needle, $offset = 0, $length = null)
{
if (! is_null($length)) {
return substr_count($haystack, $needle, $offset, $length);
}

return substr_count($haystack, $needle, $offset);
}










public static function substrReplace($string, $replace, $offset = 0, $length = null)
{
if ($length === null) {
$length = strlen($string);
}

return substr_replace($string, $replace, $offset, $length);
}








public static function swap(array $map, $subject)
{
return strtr($subject, $map);
}








public static function take($string, int $limit): string
{
if ($limit < 0) {
return static::substr($string, $limit);
}

return static::substr($string, 0, $limit);
}







public static function toBase64($string): string
{
return base64_encode($string);
}








public static function fromBase64($string, $strict = false)
{
return base64_decode($string, $strict);
}







public static function lcfirst($string)
{
return static::lower(static::substr($string, 0, 1)).static::substr($string, 1);
}







public static function ucfirst($string)
{
return static::upper(static::substr($string, 0, 1)).static::substr($string, 1);
}







public static function ucsplit($string)
{
return preg_split('/(?=\p{Lu})/u', $string, -1, PREG_SPLIT_NO_EMPTY);
}








public static function wordCount($string, $characters = null)
{
return str_word_count($string, 0, $characters);
}










public static function wordWrap($string, $characters = 75, $break = "\n", $cutLongWords = false)
{
return wordwrap($string, $characters, $break, $cutLongWords);
}






public static function uuid()
{
return static::$uuidFactory
? call_user_func(static::$uuidFactory)
: Uuid::uuid4();
}







public static function uuid7($time = null)
{
return static::$uuidFactory
? call_user_func(static::$uuidFactory)
: Uuid::uuid7($time);
}






public static function orderedUuid()
{
if (static::$uuidFactory) {
return call_user_func(static::$uuidFactory);
}

$factory = new UuidFactory;

$factory->setRandomGenerator(new CombGenerator(
$factory->getRandomGenerator(),
$factory->getNumberConverter()
));

$factory->setCodec(new TimestampFirstCombCodec(
$factory->getUuidBuilder()
));

return $factory->uuid4();
}







public static function createUuidsUsing(?callable $factory = null)
{
static::$uuidFactory = $factory;
}








public static function createUuidsUsingSequence(array $sequence, $whenMissing = null)
{
$next = 0;

$whenMissing ??= function () use (&$next) {
$factoryCache = static::$uuidFactory;

static::$uuidFactory = null;

$uuid = static::uuid();

static::$uuidFactory = $factoryCache;

$next++;

return $uuid;
};

static::createUuidsUsing(function () use (&$next, $sequence, $whenMissing) {
if (array_key_exists($next, $sequence)) {
return $sequence[$next++];
}

return $whenMissing();
});
}







public static function freezeUuids(?Closure $callback = null)
{
$uuid = Str::uuid();

Str::createUuidsUsing(fn () => $uuid);

if ($callback !== null) {
try {
$callback($uuid);
} finally {
Str::createUuidsNormally();
}
}

return $uuid;
}






public static function createUuidsNormally()
{
static::$uuidFactory = null;
}







public static function ulid($time = null)
{
if (static::$ulidFactory) {
return call_user_func(static::$ulidFactory);
}

if ($time === null) {
return new Ulid();
}

return new Ulid(Ulid::generate($time));
}






public static function createUlidsNormally()
{
static::$ulidFactory = null;
}







public static function createUlidsUsing(?callable $factory = null)
{
static::$ulidFactory = $factory;
}








public static function createUlidsUsingSequence(array $sequence, $whenMissing = null)
{
$next = 0;

$whenMissing ??= function () use (&$next) {
$factoryCache = static::$ulidFactory;

static::$ulidFactory = null;

$ulid = static::ulid();

static::$ulidFactory = $factoryCache;

$next++;

return $ulid;
};

static::createUlidsUsing(function () use (&$next, $sequence, $whenMissing) {
if (array_key_exists($next, $sequence)) {
return $sequence[$next++];
}

return $whenMissing();
});
}







public static function freezeUlids(?Closure $callback = null)
{
$ulid = Str::ulid();

Str::createUlidsUsing(fn () => $ulid);

if ($callback !== null) {
try {
$callback($ulid);
} finally {
Str::createUlidsNormally();
}
}

return $ulid;
}






public static function flushCache()
{
static::$snakeCache = [];
static::$camelCache = [];
static::$studlyCache = [];
}
}
