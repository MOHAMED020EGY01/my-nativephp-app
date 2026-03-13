<?php
// scripts/sync-gradle-props.php
// اقرأ من .env أولاً (لو موجود)، ولو مش موجود استخدم .env.example
$envFile = file_exists(__DIR__.'/../.env') ? '../.env' : '../.env.example';
$env = array_filter(explode("\n", file_get_contents(__DIR__.'/'.$envFile)), fn($l) => trim($l) && !str_starts_with(trim($l), '#'));
$mapping = [
    'NATIVEPHP_APP_ID' => 'nativephp.app_id',
    'NATIVEPHP_APP_VERSION_CODE' => 'nativephp.version_code',
    'NATIVEPHP_APP_VERSION' => 'nativephp.version_name',
    'ANDROID_KEYSTORE_FILE' => 'MYAPP_UPLOAD_STORE_FILE',
    'ANDROID_KEY_ALIAS' => 'MYAPP_UPLOAD_KEY_ALIAS',
    'ANDROID_KEYSTORE_PASSWORD' => 'MYAPP_UPLOAD_STORE_PASSWORD',
    'ANDROID_KEY_PASSWORD' => 'MYAPP_UPLOAD_KEY_PASSWORD',
];

$props = [];
foreach ($env as $line) {
    [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
    $key = trim($key); $value = trim($value);
    if (isset($mapping[$key])) {
        $props[$mapping[$key]] = $value;
    }
}

// اقرأ القيم الحالية لو موجودة
$existing = file_exists(__DIR__.'/../gradle.properties')
    ? parse_ini_string(file_get_contents(__DIR__.'/../gradle.properties'), false, INI_SCANNER_RAW)
    : [];

// ادمج واكتب
$output = "# Auto-generated from .env - DO NOT COMMIT\n";
foreach (array_merge($existing, $props) as $k => $v) {
    $output .= "$k=$v\n";
}
file_put_contents(__DIR__.'/../gradle.properties', $output);
echo "✅ Synced gradle.properties from .env\n";
