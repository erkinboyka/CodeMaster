<?php
if (!function_exists('tfSeedLearningPack')) {
    require_once __DIR__ . '/includes/seed_pack.php';
    require_once __DIR__ . '/includes/seeder_learning_pack.php';
}

function tfRequestId()
{
    if (!empty($_SERVER['TF_REQUEST_ID'])) {
        return (string) $_SERVER['TF_REQUEST_ID'];
    }

    $incoming = (string) ($_SERVER['HTTP_X_REQUEST_ID'] ?? '');
    $incoming = preg_replace('/[^a-zA-Z0-9_-]/', '', $incoming);
    if ($incoming !== '') {
        $_SERVER['TF_REQUEST_ID'] = $incoming;
        return $incoming;
    }

    try {
        $generated = bin2hex(random_bytes(8));
    } catch (Throwable $e) {
        $generated = str_replace('.', '', uniqid('req_', true));
    }

    $_SERVER['TF_REQUEST_ID'] = $generated;
    return $generated;
}

function tfDebugLog($event, array $context = [])
{
    $encoded = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($encoded === false) {
        $encoded = '{}';
    }
    error_log('[TF_DEBUG][' . tfRequestId() . '][' . $event . '] ' . $encoded);
}

function tfNormalizeJsonStrings($value)
{
    if (is_string($value)) {
        return normalizeMojibakeText($value);
    }
    if (is_array($value)) {
        foreach ($value as $key => $item) {
            $value[$key] = tfNormalizeJsonStrings($item);
        }
        return $value;
    }
    if (is_object($value)) {
        foreach ($value as $key => $item) {
            $value->$key = tfNormalizeJsonStrings($item);
        }
        return $value;
    }
    return $value;
}

function tfNormalizeJsonOutputBuffer($buffer)
{
    if (!is_string($buffer) || $buffer === '') {
        return $buffer;
    }
    $decoded = json_decode($buffer, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return normalizeMojibakeText($buffer);
    }
    $decoded = tfNormalizeJsonStrings($decoded);
    $encoded = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $encoded !== false ? $encoded : $buffer;
}

function tfNormalizeHtmlOutputBuffer($buffer)
{
    if (!is_string($buffer) || $buffer === '') {
        return $buffer;
    }
    return normalizeMojibakeText($buffer);
}

function tfEnableJsonOutputNormalization()
{
    static $enabled = false;
    if ($enabled) {
        return;
    }
    $enabled = true;
    ob_start('tfNormalizeJsonOutputBuffer');
}

function tfEnableHtmlOutputNormalization()
{
    static $enabled = false;
    if ($enabled) {
        return;
    }
    $enabled = true;
    ob_start('tfNormalizeHtmlOutputBuffer');
}

function tfHttpRequest($method, $url, array $headers = [], $body = '', $timeout = 15)
{
    $method = strtoupper((string) $method);
    $raw = false;
    $status = 0;
    $error = '';

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, (int) $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if ($method !== 'GET' && $body !== '') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        $raw = curl_exec($ch);
        $error = (string) curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    } else {
        $headerText = '';
        if (!empty($headers)) {
            $headerText = implode("\r\n", $headers) . "\r\n";
        }
        $opts = [
            'http' => [
                'method' => $method,
                'header' => $headerText,
                'timeout' => (int) $timeout,
                'ignore_errors' => true
            ]
        ];
        if ($method !== 'GET' && $body !== '') {
            $opts['http']['content'] = $body;
        }
        $context = stream_context_create($opts);
        $raw = @file_get_contents($url, false, $context);
        if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', (string) $http_response_header[0], $m)) {
            $status = (int) $m[1];
        }
    }

    $bodyText = is_string($raw) ? $raw : '';
    $ok = ($raw !== false) && ($status >= 200 && $status < 300);

    return [
        'ok' => $ok,
        'status' => $status,
        'body' => $bodyText,
        'error' => $error
    ];
}

function tfVerifyRecaptchaToken($token)
{
    $secret = defined('RECAPTCHA_SECRET_KEY') ? trim((string) RECAPTCHA_SECRET_KEY) : '';
    if ($secret === '') {
        return ['ok' => true, 'skipped' => true];
    }

    $token = trim((string) $token);
    if ($token === '') {
        return ['ok' => false, 'message' => t('recaptcha_complete', 'Complete reCAPTCHA verification.')];
    }

    $payload = http_build_query([
        'secret' => $secret,
        'response' => $token,
        'remoteip' => (string) ($_SERVER['REMOTE_ADDR'] ?? '')
    ]);

    $response = tfHttpRequest(
        'POST',
        'https://www.google.com/recaptcha/api/siteverify',
        ['Content-Type: application/x-www-form-urlencoded'],
        $payload,
        15
    );

    if (!$response['ok']) {
        tfDebugLog('recaptcha.http_error', [
            'status' => (int) ($response['status'] ?? 0),
            'error' => (string) ($response['error'] ?? '')
        ]);
        return ['ok' => false, 'message' => t('recaptcha_verification_failed', 'reCAPTCHA verification failed.')];
    }

    $decoded = json_decode((string) $response['body'], true);
    if (!is_array($decoded) || empty($decoded['success'])) {
        tfDebugLog('recaptcha.failed', [
            'response' => $decoded
        ]);
        return ['ok' => false, 'message' => t('recaptcha_check_failed', 'Failed reCAPTCHA check.')];
    }

    return ['ok' => true];
}

function tfVerifyGoogleIdToken($idToken)
{
    $clientId = defined('GOOGLE_CLIENT_ID') ? trim((string) GOOGLE_CLIENT_ID) : '';
    if ($clientId === '') {
        return ['ok' => false, 'message' => t('google_signin_not_configured', 'Google sign-in is not configured.')];
    }

    $idToken = trim((string) $idToken);
    if ($idToken === '') {
        return ['ok' => false, 'message' => t('google_missing_credential', 'Missing Google credential.')];
    }

    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . rawurlencode($idToken);
    $response = tfHttpRequest('GET', $url, [], '', 15);
    if (!$response['ok']) {
        tfDebugLog('google.tokeninfo_http_error', [
            'status' => (int) ($response['status'] ?? 0),
            'error' => (string) ($response['error'] ?? '')
        ]);
        return ['ok' => false, 'message' => t('google_token_validation_failed', 'Google token validation failed.')];
    }

    $payload = json_decode((string) $response['body'], true);
    if (!is_array($payload)) {
        return ['ok' => false, 'message' => t('google_token_invalid_response', 'Invalid Google token response.')];
    }

    $aud = (string) ($payload['aud'] ?? '');
    if ($aud !== $clientId) {
        tfDebugLog('google.invalid_aud', ['aud' => $aud]);
        return ['ok' => false, 'message' => t('google_token_invalid_audience', 'Google token has invalid audience.')];
    }

    $iss = (string) ($payload['iss'] ?? '');
    if (!in_array($iss, ['accounts.google.com', 'https://accounts.google.com'], true)) {
        tfDebugLog('google.invalid_issuer', ['iss' => $iss]);
        return ['ok' => false, 'message' => t('google_token_invalid_issuer', 'Google token has invalid issuer.')];
    }

    $exp = (int) ($payload['exp'] ?? 0);
    if ($exp > 0 && $exp <= time()) {
        return ['ok' => false, 'message' => t('google_token_expired', 'Google token expired.')];
    }

    $email = trim((string) ($payload['email'] ?? ''));
    if ($email === '' || !isValidEmail($email)) {
        return ['ok' => false, 'message' => t('google_email_invalid', 'Google account email is invalid.')];
    }

    $emailVerified = strtolower((string) ($payload['email_verified'] ?? ''));
    if ($emailVerified !== 'true' && $emailVerified !== '1') {
        return ['ok' => false, 'message' => t('google_email_not_verified', 'Google email is not verified.')];
    }

    return [
        'ok' => true,
        'profile' => [
            'sub' => (string) ($payload['sub'] ?? ''),
            'email' => $email,
            'name' => trim((string) ($payload['name'] ?? '')),
            'picture' => trim((string) ($payload['picture'] ?? '')),
            'locale' => trim((string) ($payload['locale'] ?? '')),
        ]
    ];
}
// Регистрация пользователя
function handleRegistration()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные.']);
        return;
    }

    $missing = tfValidateRequiredFields($data, ['email', 'password', 'fullName', 'location', 'recaptchaToken']);
    if (!empty($missing)) {
        echo json_encode(['success' => false, 'message' => 'Missing fields: ' . implode(', ', $missing)]);
        return;
    }

    $recaptchaCheck = tfVerifyRecaptchaToken((string) ($data['recaptchaToken'] ?? ''));
    if (empty($recaptchaCheck['ok'])) {
        echo json_encode([
            'success' => false,
            'message' => (string) ($recaptchaCheck['message'] ?? 'Failed reCAPTCHA check.')
        ]);
        return;
    }

    $pdo = getDBConnection();

    if (!isValidEmail($data['email'])) {
        echo json_encode(['success' => false, 'message' => 'Неверный формат email.']);
        return;
    }

    if (!isValidPassword($data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Пароль должен быть не менее 8 символов, содержать цифру и заглавную букву.']);
        return;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Пользователь с таким email уже зарегистрирован.']);
        return;
    }

    try {
        $hashedPassword = hashPassword($data['password']);
        $avatar = getAvatarUrl($data['fullName']);
        $role = $data['role'] ?? 'seeker';
        $countryCode = strtoupper(trim((string) ($data['countryCode'] ?? '')));
        $countryName = trim((string) ($data['countryName'] ?? ''));
        $resolvedCountry = [];
        if ($countryCode !== '') {
            $resolvedCountry = tfResolveCountryByCode($countryCode);
        }
        if (empty($resolvedCountry) && $countryName !== '') {
            $resolvedCountry = tfResolveCountryByText($countryName);
        }
        if (empty($resolvedCountry)) {
            $resolvedCountry = tfResolveCountryByText((string) ($data['location'] ?? ''));
        }
        if (!empty($resolvedCountry)) {
            $countryCode = (string) ($resolvedCountry['country_code'] ?? $countryCode);
            $countryName = (string) ($resolvedCountry['country_name'] ?? $countryName);
        }
        $location = $countryName !== '' ? $countryName : (string) ($data['location'] ?? '');
        $title = $role === 'seeker' ? 'Junior Developer' : ($role === 'admin' ? 'Администратор' : 'HR Specialist');
        $bio = $role === 'seeker'
            ? 'Начинающий специалист, развиваюсь в IT и открыт(а) к новым возможностям.'
            : ($role === 'admin'
                ? 'Администратор платформы. Помогаю с модерацией и поддержкой.'
                : 'Рекрутер, помогаю находить таланты в IT.');

        $stmt = $pdo->prepare("
            INSERT INTO users (email, password, name, role, title, location, bio, avatar, country_code, country_name, is_verified)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE)
        ");
        $stmt->execute([
            $data['email'],
            $hashedPassword,
            $data['fullName'],
            $role,
            $title,
            $location,
            $bio,
            $avatar,
            $countryCode,
            $countryName
        ]);

        $userId = $pdo->lastInsertId();

        if (!empty($data['selectedSkills'])) {
            $skillsVerified = !empty($data['skillsVerified']) ? 1 : 0;
            foreach ($data['selectedSkills'] as $skill) {
                $stmt = $pdo->prepare("
                    INSERT INTO user_skills (user_id, skill_name, skill_level, category, is_verified)
                    VALUES (?, ?, 0, ?, ?)
                ");
                $category = in_array($skill, ['JavaScript', 'React', 'Node.js', 'Python', 'HTML/CSS', 'TypeScript', 'SQL', 'AWS', 'Judge0', 'Git']) ? 'technical' : 'soft';
                $stmt->execute([$userId, $skill, $category, $skillsVerified]);
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO user_activities (user_id, activity_type, activity_text)
            VALUES (?, 'course', 'Регистрация на платформе')
        ");
        $stmt->execute([$userId]);

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $data['email'];
        $_SESSION['just_registered'] = 1;

        echo json_encode(['success' => true, 'message' => 'Регистрация успешна!', 'userId' => $userId]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

// Авторизация пользователя
function handleLogin()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        tfDebugLog('login.invalid_json', [
            'raw_preview' => mb_substr((string) $raw, 0, 200),
            'content_type' => (string) ($_SERVER['CONTENT_TYPE'] ?? '')
        ]);
        echo json_encode(['success' => false, 'message' => 'Неверные данные.']);
        return;
    }

    $missing = tfValidateRequiredFields($data, ['email', 'password', 'recaptchaToken']);
    if (!empty($missing)) {
        echo json_encode(['success' => false, 'message' => 'Missing fields: ' . implode(', ', $missing)]);
        return;
    }

    $email = trim((string) ($data['email'] ?? ''));
    $password = (string) ($data['password'] ?? '');
    $recaptchaToken = (string) ($data['recaptchaToken'] ?? '');


    $recaptchaCheck = tfVerifyRecaptchaToken((string) ($data['recaptchaToken'] ?? ''));
    if (empty($recaptchaCheck['ok'])) {
        echo json_encode([
            'success' => false,
            'message' => (string) ($recaptchaCheck['message'] ?? 'Failed reCAPTCHA check.')
        ]);
        return;
    }

    tfDebugLog('login.start', [
        'email' => $email,
        'has_password' => $password !== '',
        'session_user' => $_SESSION['user_id'] ?? null
    ]);

    if ($email === '' || $password === '') {
        tfDebugLog('login.validation_failed', ['email' => $email]);
        echo json_encode(['success' => false, 'message' => 'Введите email и пароль.']);
        return;
    }

    $maxAttempts = defined('MAX_LOGIN_ATTEMPTS') ? (int) MAX_LOGIN_ATTEMPTS : 5;
    $lockSeconds = defined('LOCKOUT_TIME') ? (int) LOCKOUT_TIME : 900;

    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            tfDebugLog('login.user_not_found', ['email' => $email]);
            echo json_encode(['success' => false, 'message' => 'Пользователь с таким email не найден.']);
            return;
        }

        if (!empty($user['is_blocked'])) {
            tfDebugLog('login.user_blocked', ['user_id' => (int) $user['id']]);
            echo json_encode(['success' => false, 'message' => 'Аккаунт заблокирован. Обратитесь в поддержку.']);
            return;
        }

        $lockedUntilRaw = (string) ($user['locked_until'] ?? '');
        if ($lockedUntilRaw !== '') {
            $lockedUntilTs = strtotime($lockedUntilRaw);
            if ($lockedUntilTs !== false && $lockedUntilTs > time()) {
                $waitSeconds = $lockedUntilTs - time();
                tfDebugLog('login.locked_active', [
                    'user_id' => (int) $user['id'],
                    'wait_seconds' => $waitSeconds
                ]);
                echo json_encode(['success' => false, 'message' => 'Account is temporarily locked. Try again in ' . $waitSeconds . ' sec.']);
                return;
            }
        }

        if (!verifyPassword($password, (string) $user['password'])) {
            $stmt = $pdo->prepare("UPDATE users SET failed_login_attempts = failed_login_attempts + 1 WHERE id = ?");
            $stmt->execute([$user['id']]);
            $stmt = $pdo->prepare("SELECT failed_login_attempts FROM users WHERE id = ?");
            $stmt->execute([$user['id']]);
            $attempts = (int) ($stmt->fetch()['failed_login_attempts'] ?? 0);

            if ($attempts >= $maxAttempts) {
                $lockedUntil = date('Y-m-d H:i:s', time() + $lockSeconds);
                $stmt = $pdo->prepare("UPDATE users SET locked_until = ? WHERE id = ?");
                $stmt->execute([$lockedUntil, $user['id']]);
                tfDebugLog('login.lock_applied', [
                    'user_id' => (int) $user['id'],
                    'attempts' => $attempts,
                    'lock_seconds' => $lockSeconds
                ]);
                echo json_encode(['success' => false, 'message' => 'Слишком много неудачных попыток. Аккаунт заблокирован на 15 минут.']);
                return;
            }

            $remaining = max(0, $maxAttempts - $attempts);
            tfDebugLog('login.bad_password', [
                'user_id' => (int) $user['id'],
                'attempts' => $attempts,
                'remaining' => $remaining
            ]);
            echo json_encode(['success' => false, 'message' => 'Неверный пароль. Осталось попыток: ' . $remaining]);
            return;
        }

        $stmt = $pdo->prepare("UPDATE users SET failed_login_attempts = 0, locked_until = NULL, last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        unset($_SESSION['just_registered']);

        $firstName = explode(' ', (string) $user['name'])[0];
        $welcomeTemplate = function_exists('t') ? t('notif_welcome_back') : 'Добро пожаловать обратно, {name}!';
        $welcomeMessage = i18nFormat($welcomeTemplate, ['name' => $firstName]);
        if (!tfHasRecentNotification($pdo, (int) $user['id'], $welcomeMessage, 86400)) {
        tfAddNotification($pdo, (int) $user['id'], $welcomeMessage);
        } else {
            tfDedupNotificationMessage($pdo, (int) $user['id'], $welcomeMessage);
        }

        tfDebugLog('login.success', [
            'user_id' => (int) $user['id'],
            'role' => (string) ($user['role'] ?? '')
        ]);
        echo json_encode([
            'success' => true,
            'message' => 'Вход выполнен успешно!',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'avatar' => $user['avatar']
            ]
        ]);
    } catch (Throwable $e) {
        tfDebugLog('login.exception', [
            'email' => $email,
            'error' => $e->getMessage()
        ]);
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

// Вход через Google
function handleGoogleLogin()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request payload.']);
        return;
    }

    $verification = tfVerifyGoogleIdToken((string) ($data['credential'] ?? ''));
    if (empty($verification['ok'])) {
        echo json_encode([
            'success' => false,
            'message' => (string) ($verification['message'] ?? 'Google sign-in failed.')
        ]);
        return;
    }

    $profile = (array) ($verification['profile'] ?? []);
    $email = trim((string) ($profile['email'] ?? ''));
    $name = trim((string) ($profile['name'] ?? ''));
    $avatar = trim((string) ($profile['picture'] ?? ''));
    $locale = trim((string) ($profile['locale'] ?? ''));
    $browserLocale = trim((string) ($data['browserLocale'] ?? ''));
    $googleCountry = tfResolveCountryForGoogleAccount($profile, $browserLocale);
    $resolvedCountry = [];
    if (!empty($googleCountry['country_code']) || !empty($googleCountry['country_name'])) {
        $resolvedCountry = [
            'country_code' => (string) ($googleCountry['country_code'] ?? ''),
            'country_name' => (string) ($googleCountry['country_name'] ?? ''),
        ];
    }
    $googleLocale = (string) ($googleCountry['google_locale'] ?? $locale);

    if ($name === '') {
        $name = strstr($email, '@', true);
        if ($name === false || $name === '') {
            $name = 'Google User';
        }
    }

    try {
        $pdo = getDBConnection();
        ensureUserProfileSchema($pdo);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && !empty($user['is_blocked'])) {
            echo json_encode(['success' => false, 'message' => 'Your account is blocked.']);
            return;
        }

        if (!$user) {
            $hashedPassword = hashPassword(generateSecurePassword(24));
            $finalAvatar = $avatar !== '' ? $avatar : getAvatarUrl($name);
            $role = 'seeker';
            $title = 'Junior Developer';
            $location = (string) ($resolvedCountry['country_name'] ?? 'Not specified');
            $bio = 'Registered with Google account.';
            $countryCode = (string) ($resolvedCountry['country_code'] ?? '');
            $countryName = (string) ($resolvedCountry['country_name'] ?? '');

            $stmt = $pdo->prepare("
                INSERT INTO users (email, password, name, role, title, location, bio, avatar, country_code, country_name, google_locale, is_verified, last_login)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE, NOW())
            ");
            $stmt->execute([
                $email,
                $hashedPassword,
                $name,
                $role,
                $title,
                $location,
                $bio,
                $finalAvatar,
                $countryCode,
                $countryName,
                $googleLocale,
            ]);

            $userId = (int) $pdo->lastInsertId();
            $user = [
                'id' => $userId,
                'email' => $email,
                'name' => $name,
                'role' => $role,
                'avatar' => $finalAvatar,
                'country_code' => $countryCode,
                'country_name' => $countryName,
                'google_locale' => $googleLocale,
            ];
        } else {
            $userId = (int) ($user['id'] ?? 0);
            $updateSql = "UPDATE users SET last_login = NOW()";
            $params = [];
            if ($googleLocale !== '') {
                $updateSql .= ", google_locale = ?";
                $params[] = $googleLocale;
                $user['google_locale'] = $googleLocale;
            }
            if ($avatar !== '' && empty($user['avatar'])) {
                $updateSql .= ", avatar = ?";
                $params[] = $avatar;
                $user['avatar'] = $avatar;
            }
            if (!empty($resolvedCountry) && (empty($user['country_code']) || empty($user['country_name']) || empty($user['location']) || $user['location'] === 'Not specified')) {
                $updateSql .= ", country_code = ?, country_name = ?";
                $params[] = (string) ($resolvedCountry['country_code'] ?? '');
                $params[] = (string) ($resolvedCountry['country_name'] ?? '');
                $user['country_code'] = (string) ($resolvedCountry['country_code'] ?? '');
                $user['country_name'] = (string) ($resolvedCountry['country_name'] ?? '');
                if (empty($user['location']) || $user['location'] === 'Not specified') {
                    $updateSql .= ", location = ?";
                    $params[] = (string) ($resolvedCountry['country_name'] ?? '');
                    $user['location'] = (string) ($resolvedCountry['country_name'] ?? '');
                }
            }
            $updateSql .= " WHERE id = ?";
            $params[] = $userId;

            $stmt = $pdo->prepare($updateSql);
            $stmt->execute($params);
        }

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_email'] = (string) $user['email'];

        echo json_encode([
            'success' => true,
            'message' => 'Login successful.',
            'user' => [
                'id' => (int) $user['id'],
                'name' => (string) ($user['name'] ?? ''),
                'email' => (string) ($user['email'] ?? ''),
                'role' => (string) ($user['role'] ?? 'seeker'),
                'avatar' => (string) ($user['avatar'] ?? '')
            ]
        ]);
    } catch (Throwable $e) {
        tfDebugLog('google.login.exception', ['error' => $e->getMessage()]);
        echo json_encode(['success' => false, 'message' => 'Google login failed: ' . $e->getMessage()]);
    }
}

function handleLogout()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    $isBeacon = isset($_GET['beacon']) && (string) $_GET['beacon'] === '1';
    if ($isAjax || $isBeacon) {
        echo json_encode(['success' => true]);
        return;
    }

    header('Location: ?action=login');
    exit;
}

function handleSessionStatus()
{
    $isAuthenticated = !empty($_SESSION['user_id']);
    echo json_encode([
        'success' => true,
        'authenticated' => $isAuthenticated,
        'user_id' => $isAuthenticated ? (int) $_SESSION['user_id'] : null
    ]);
}

// Получение данных курса
function handleGetCourse()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;

    $courseId = $_GET['id'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;
    if (!$courseId || !$userId) {
        echo json_encode(['success' => false, 'message' => 'Не указан курс или пользователь.']);
        return;
    }

    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$courseId]);
    $course = $stmt->fetch();

    if (!$course) {
        echo json_encode(['success' => false, 'message' => 'Курс не найден.']);
        return;
    }

    ensurePracticeSchema($pdo);
    tfEnsureCourseFallbackPracticeTasks($pdo, (int) $courseId);
    $stmt = $pdo->prepare("
        SELECT DISTINCT l.*,
               (SELECT completed FROM user_lesson_progress ulp
                WHERE ulp.lesson_id = l.id AND ulp.user_id = ?) as completed,
               lpt.id as practice_task_id,
               lpt.language as practice_language,
               lpt.title as practice_title,
               lpt.prompt as practice_prompt,
               lpt.starter_code as practice_starter_code,
               lpt.tests_json as practice_tests_json,
               lpt.is_required as practice_required,
               (SELECT 1
                FROM practice_submissions ps
                WHERE ps.user_id = ? AND ps.task_id = lpt.id AND ps.passed = 1
                LIMIT 1) as practice_passed
        FROM lessons l
        LEFT JOIN lesson_practice_tasks lpt
               ON lpt.id = (
                    SELECT t.id
                    FROM lesson_practice_tasks t
                    WHERE t.lesson_id = l.id AND t.is_required = 1
                    ORDER BY t.id ASC
                    LIMIT 1
               )
        WHERE l.course_id = ?
        ORDER BY l.order_num ASC
    ");
    $stmt->execute([$userId, $userId, $courseId]);
    $lessons = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT progress, completed FROM user_course_progress WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$userId, $courseId]);
    $progress = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT * FROM course_skills WHERE course_id = ?");
    $stmt->execute([$courseId]);
    $course['skills'] = $stmt->fetchAll();

    foreach ($lessons as &$lesson) {
        foreach (['title', 'description', 'practice_title', 'practice_prompt', 'practice_starter_code'] as $field) {
            if (array_key_exists($field, $lesson) && $lesson[$field] !== null) {
                $lesson[$field] = normalizeMojibakeText((string) $lesson[$field]);
            }
        }
        if ($lesson['type'] === 'quiz') {
            $stmt = $pdo->prepare("
                SELECT qq.*, 
                       (SELECT GROUP_CONCAT(option_text ORDER BY option_order SEPARATOR '|||') 
                        FROM quiz_options qo 
                        WHERE qo.question_id = qq.id) as options_text
                FROM quiz_questions qq
                WHERE qq.lesson_id = ?
            ");
            $stmt->execute([$lesson['id']]);
            $lesson['questions'] = $stmt->fetchAll();
            foreach ($lesson['questions'] as &$question) {
                foreach (['question', 'options_text'] as $qField) {
                    if (array_key_exists($qField, $question) && $question[$qField] !== null) {
                        $question[$qField] = normalizeMojibakeText((string) $question[$qField]);
                    }
                }
            }
            unset($question);
        }
    }
    unset($lesson);

    $course['lessons'] = $lessons;
    $course['userProgress'] = $progress;

    echo json_encode(['success' => true, 'course' => $course]);
}

// Завершение урока
function handleCompleteLesson()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $respond = static function (array $payload): void {
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    };
    $isTrackableVideoUrl = static function (string $url): bool {
        $url = trim($url);
        if ($url === '') {
            return false;
        }
        if (preg_match('/\.(mp4|webm|ogg)(?:[?#].*)?$/i', $url)) {
            return true;
        }

        $parts = @parse_url($url);
        if (!is_array($parts)) {
            return false;
        }
        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = (string) ($parts['path'] ?? '');
        $query = (string) ($parts['query'] ?? '');

        $isYoutubeHost = in_array($host, [
            'youtube.com',
            'www.youtube.com',
            'm.youtube.com',
            'youtube-nocookie.com',
            'www.youtube-nocookie.com',
            'youtu.be',
        ], true);
        if (!$isYoutubeHost) {
            return false;
        }

        if ($host === 'youtu.be') {
            $segments = array_values(array_filter(explode('/', trim($path, '/')), static function ($segment) {
                return $segment !== '';
            }));
            return !empty($segments[0]);
        }

        $params = [];
        if ($query !== '') {
            parse_str($query, $params);
        }
        if (($params['listType'] ?? '') === 'search') {
            return false;
        }
        if (!empty($params['v'])) {
            return true;
        }

        $segments = array_values(array_filter(explode('/', trim($path, '/')), static function ($segment) {
            return $segment !== '';
        }));
        $embedIdx = array_search('embed', $segments, true);
        if ($embedIdx !== false) {
            $candidate = (string) ($segments[$embedIdx + 1] ?? '');
            if ($candidate !== '' && strtolower($candidate) !== 'videoseries') {
                return true;
            }
        }

        $shortsIdx = array_search('shorts', $segments, true);
        if ($shortsIdx !== false) {
            return !empty($segments[$shortsIdx + 1]);
        }

        return false;
    };

    $data = json_decode(file_get_contents('php://input'), true);
    $missing = tfValidateRequiredFields((array) $data, ['lessonId']);
    if (!empty($missing)) {
        $respond(['success' => false, 'message' => 'Missing fields: ' . implode(', ', $missing)]);
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    $lessonId = (int) $data['lessonId'];
    $videoCompleted = filter_var($data['videoCompleted'] ?? false, FILTER_VALIDATE_BOOLEAN);
    if (!$userId) {
        $respond(['success' => false, 'message' => 'Требуется авторизация.']);
        return;
    }
    if ($lessonId <= 0) {
        $respond(['success' => false, 'message' => 'Некорректный ID урока.']);
        return;
    }

    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("SELECT course_id, type, video_url FROM lessons WHERE id = ?");
        $stmt->execute([$lessonId]);
        $lessonRow = $stmt->fetch();
        if (!$lessonRow) {
            $respond(['success' => false, 'message' => 'Урок не найден.']);
            return;
        }
        $lessonType = (string) ($lessonRow['type'] ?? '');
        $lessonVideoUrl = trim((string) ($lessonRow['video_url'] ?? ''));
        $requiresVideoWatch = $lessonType !== 'quiz'
            && $lessonVideoUrl !== ''
            && $isTrackableVideoUrl($lessonVideoUrl);
        if ($requiresVideoWatch && !$videoCompleted) {
            $respond(['success' => false, 'message' => 'Для завершения урока нужно досмотреть видео.']);
            return;
        }

        ensurePracticeSchema($pdo);
        tfEnsureCourseFallbackPracticeTasks($pdo, (int) ($lessonRow['course_id'] ?? 0));
        $stmt = $pdo->prepare("
            SELECT id
            FROM lesson_practice_tasks
            WHERE lesson_id = ? AND is_required = 1
            ORDER BY id ASC
            LIMIT 1
        ");
        $stmt->execute([$lessonId]);
        $practiceTaskId = (int) ($stmt->fetch()['id'] ?? 0);
        if ($practiceTaskId > 0) {
            $stmt = $pdo->prepare("
                SELECT 1
                FROM practice_submissions
                WHERE user_id = ? AND task_id = ? AND passed = 1
                LIMIT 1
            ");
            $stmt->execute([(int) $userId, $practiceTaskId]);
            $practicePassed = (int) ($stmt->fetchColumn() ?: 0) === 1;
            if (!$practicePassed) {
                $respond([
                    'success' => false,
                    'needs_practice' => true,
                    'message' => 'Для завершения урока сначала выполните обязательную практику.'
                ]);
                return;
            }
        }
        $stmt = $pdo->prepare("SELECT completed FROM user_lesson_progress WHERE user_id = ? AND lesson_id = ? LIMIT 1");
        $stmt->execute([$userId, $lessonId]);
        $alreadyCompleted = (int) ($stmt->fetch()["completed"] ?? 0) === 1;
        $stmt = $pdo->prepare("INSERT INTO user_lesson_progress (user_id, lesson_id, completed, completed_at) VALUES (?, ?, TRUE, NOW()) ON DUPLICATE KEY UPDATE completed = TRUE, completed_at = NOW()");
        $stmt->execute([$userId, $lessonId]);

        $courseId = (int) $lessonRow['course_id'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM lessons WHERE course_id = ?");
        $stmt->execute([$courseId]);
        $totalLessons = $stmt->fetch()['total'];

        $stmt = $pdo->prepare("
            SELECT COUNT(*) as completed 
            FROM user_lesson_progress ulp
            JOIN lessons l ON ulp.lesson_id = l.id
            WHERE ulp.user_id = ? AND l.course_id = ? AND ulp.completed = TRUE
        ");
        $stmt->execute([$userId, $courseId]);
        $completedLessons = $stmt->fetch()['completed'];

        $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
        $completed = $progress >= 100;

        $stmt = $pdo->prepare("
            INSERT INTO user_course_progress (user_id, course_id, progress, completed)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE progress = ?, completed = ?
        ");
        $stmt->execute([$userId, $courseId, $progress, $completed, $progress, $completed]);

        $stmt = $pdo->prepare("SELECT title FROM lessons WHERE id = ?");
        $stmt->execute([$lessonId]);
        $lessonTitle = $stmt->fetch()['title'];

        if (!$alreadyCompleted) {
            $notificationText = "Вы завершили урок \"{$lessonTitle}\"";
            $activityText = "Завершен урок: {$lessonTitle}";
            tfAddNotification($pdo, (int) $userId, $notificationText);
            $stmt = $pdo->prepare("INSERT INTO user_activities (user_id, activity_type, activity_text) VALUES (?, 'lesson', ?)");
            $stmt->execute([$userId, $activityText]);
        }

        $respond(['success' => true, 'message' => 'Урок завершен!', 'progress' => $progress, 'completed' => $completed]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function tfPracticeNormalizeOutput($text)
{
    $text = is_string($text) ? $text : '';
    $text = str_replace("\r\n", "\n", $text);
    $text = str_replace("\r", "\n", $text);
    return rtrim($text);
}

function tfPracticeDeleteDir($dir)
{
    $dir = (string) $dir;
    if ($dir === '' || !is_dir($dir))
        return;
    $items = @scandir($dir);
    if (!is_array($items))
        return;
    foreach ($items as $item) {
        if ($item === '.' || $item === '..')
            continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            tfPracticeDeleteDir($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}

function tfProcRun(array $command, $stdin = '', $timeoutSec = 10, $maxBytes = 65536)
{
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $options = [
        'bypass_shell' => true,
        'suppress_errors' => true,
    ];

    $process = @proc_open($command, $descriptors, $pipes, null, null, $options);
    if (!is_resource($process)) {
        return [
            'ok' => false,
            'exit_code' => -1,
            'timed_out' => false,
            'stdout' => '',
            'stderr' => 'proc_open_failed',
        ];
    }

    $stdin = is_string($stdin) ? $stdin : '';
    fwrite($pipes[0], $stdin);
    fclose($pipes[0]);

    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    $stdout = '';
    $stderr = '';
    $start = microtime(true);
    $timedOut = false;

    while (true) {
        $status = proc_get_status($process);
        $stdoutLeft = max(0, (int) $maxBytes - strlen($stdout));
        $stderrLeft = max(0, (int) $maxBytes - strlen($stderr));

        if ($stdoutLeft > 0) {
            $chunk = stream_get_contents($pipes[1], $stdoutLeft);
            if (is_string($chunk) && $chunk !== '')
                $stdout .= $chunk;
        }
        if ($stderrLeft > 0) {
            $chunk = stream_get_contents($pipes[2], $stderrLeft);
            if (is_string($chunk) && $chunk !== '')
                $stderr .= $chunk;
        }

        if (!$status['running']) {
            break;
        }

        if ((microtime(true) - $start) > (int) $timeoutSec) {
            $timedOut = true;
            @proc_terminate($process);
            break;
        }

        usleep(50000);
    }

    $stdoutLeft = max(0, (int) $maxBytes - strlen($stdout));
    $stderrLeft = max(0, (int) $maxBytes - strlen($stderr));
    if ($stdoutLeft > 0) {
        $chunk = stream_get_contents($pipes[1], $stdoutLeft);
        if (is_string($chunk) && $chunk !== '')
            $stdout .= $chunk;
    }
    if ($stderrLeft > 0) {
        $chunk = stream_get_contents($pipes[2], $stderrLeft);
        if (is_string($chunk) && $chunk !== '')
            $stderr .= $chunk;
    }

    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = @proc_close($process);
    if (!is_int($exitCode))
        $exitCode = -1;

    return [
        'ok' => true,
        'exit_code' => $exitCode,
        'timed_out' => $timedOut,
        'stdout' => $stdout,
        'stderr' => $stderr,
    ];
}

function tfResolveNodeBin()
{
    static $cached = null;
    if (is_string($cached) && $cached !== '') {
        return $cached;
    }

    $env = getenv('CODEMASTER_NODE_BIN');
    if ((!is_string($env) || trim($env) === '') && function_exists('getenv')) {
        $env = getenv('ITSPHERE360_NODE_BIN');
    }
    if (is_string($env)) {
        $env = trim($env);
        if ($env !== '') {
            $cached = $env;
            return $cached;
        }
    }

    $bin = 'node';
    if (stripos(PHP_OS_FAMILY ?? PHP_OS, 'Windows') !== false) {
        $candidates = [
            'C:\\Program Files\\nodejs\\node.exe',
            'C:\\Program Files (x86)\\nodejs\\node.exe',
        ];
        foreach ($candidates as $path) {
            if (@is_file($path)) {
                $bin = $path;
                break;
            }
        }
    }

    $cached = $bin;
    return $cached;
}

function tfNodePreflight($nodeBin)
{
    static $cache = [];
    $nodeBin = (string) $nodeBin;
    if ($nodeBin === '') {
        $nodeBin = 'node';
    }
    if (isset($cache[$nodeBin])) {
        return $cache[$nodeBin];
    }

    $check = tfProcRun([$nodeBin, '-v'], '', 5, 4096);
    if (!$check['ok'] || (int) ($check['exit_code'] ?? 1) !== 0) {
        $cache[$nodeBin] = [
            'ok' => false,
            'error' => 'node_not_found',
            'stderr' => $check['stderr'] ?? 'node_exec_failed',
        ];
        return $cache[$nodeBin];
    }

    $cache[$nodeBin] = ['ok' => true];
    return $cache[$nodeBin];
}

function tfJudge0GetBaseUrl()
{
    $fromEnv = getenv('CODEMASTER_JUDGE0_URL');
    if ((!is_string($fromEnv) || trim($fromEnv) === '') && function_exists('getenv')) {
        $fromEnv = getenv('ITSPHERE360_JUDGE0_URL');
    }
    if (is_string($fromEnv) && trim($fromEnv) !== '') {
        return rtrim(trim($fromEnv), '/');
    }
    if (defined('JUDGE0_URL')) {
        $const = (string) JUDGE0_URL;
        if (trim($const) !== '') {
            return rtrim(trim($const), '/');
        }
    }
    return 'http://127.0.0.1:2358';
}

function tfJudge0GetToken()
{
    $fromEnv = getenv('CODEMASTER_JUDGE0_TOKEN');
    if ((!is_string($fromEnv) || trim($fromEnv) === '') && function_exists('getenv')) {
        $fromEnv = getenv('ITSPHERE360_JUDGE0_TOKEN');
    }
    if (is_string($fromEnv) && trim($fromEnv) !== '') {
        return trim($fromEnv);
    }
    if (defined('JUDGE0_TOKEN')) {
        $const = (string) JUDGE0_TOKEN;
        if (trim($const) !== '') {
            return trim($const);
        }
    }
    return '';
}

function tfJudge0LooksLikeConnectivityIssue($error, $code = 0)
{
    $msg = mb_strtolower(trim((string) $error));
    $code = (int) $code;
    if ($code === 0 && $msg === '') {
        return true;
    }

    $markers = [
        'timed out',
        'timeout',
        'operation timed out',
        'failed to connect',
        'could not resolve host',
        "couldn't resolve host",
        'connection refused',
        'connection reset',
        'network is unreachable',
        'empty reply',
        'ssl connect error',
        'could not connect',
    ];

    foreach ($markers as $marker) {
        if (strpos($msg, $marker) !== false) {
            return true;
        }
    }

    return $code === 0;
}

function tfJudge0GetBaseCandidates($primaryBase = '')
{
    $bases = [];
    $add = static function ($value) use (&$bases) {
        $value = rtrim(trim((string) $value), '/');
        if ($value === '')
            return;
        if (!isset($bases[$value])) {
            $bases[$value] = true;
        }
    };

    $add($primaryBase);
    $add(tfJudge0GetBaseUrl());

    $envFallbacks = getenv('CODEMASTER_JUDGE0_FALLBACKS');
    if ((!is_string($envFallbacks) || trim($envFallbacks) === '') && function_exists('getenv')) {
        $envFallbacks = getenv('ITSPHERE360_JUDGE0_FALLBACKS');
    }
    if (is_string($envFallbacks) && trim($envFallbacks) !== '') {
        $parts = preg_split('/[,\s;]+/', $envFallbacks);
        if (is_array($parts)) {
            foreach ($parts as $part) {
                $add($part);
            }
        }
    }

    if (defined('JUDGE0_FALLBACKS')) {
        $constFallbacks = constant('JUDGE0_FALLBACKS');
        if (is_array($constFallbacks)) {
            foreach ($constFallbacks as $part) {
                $add($part);
            }
        }
    }

    $add('http://127.0.0.1:2358');

    return array_keys($bases);
}

function tfJudge0Request($method, $url, $payload = null, array $extraHeaders = [], $timeoutSec = 20)
{
    $method = strtoupper((string) $method);
    $headers = array_merge(['Accept: application/json'], $extraHeaders);
    $hasPayload = $payload !== null;
    $body = $hasPayload ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
    if ($hasPayload && $body === false) {
        return ['ok' => false, 'error' => 'payload_encode_failed'];
    }
    if ($hasPayload) {
        $headers[] = 'Content-Type: application/json';
    }

    $attempts = ($method === 'GET') ? 2 : 3;
    $last = ['raw' => false, 'code' => 0, 'err' => '', 'rawText' => '', 'json' => null];

    for ($attempt = 1; $attempt <= $attempts; $attempt++) {
        $raw = false;
        $code = 0;
        $err = '';

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            $connectTimeout = min(10, max(3, (int) floor((int) $timeoutSec / 3)));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, max(1, (int) $timeoutSec));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            if (defined('CURL_IPRESOLVE_V4')) {
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            }
            if ($hasPayload) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
            $raw = curl_exec($ch);
            $err = (string) curl_error($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } else {
            $headerStr = implode("\r\n", $headers);
            $opts = [
                'http' => [
                    'method' => $method,
                    'header' => $headerStr . "\r\n",
                    'ignore_errors' => true,
                    'timeout' => max(1, (int) $timeoutSec),
                ]
            ];
            if ($hasPayload) {
                $opts['http']['content'] = $body;
            }
            $context = stream_context_create($opts);
            $raw = @file_get_contents($url, false, $context);
            if (isset($http_response_header[0]) && preg_match('/\\s(\\d{3})\\s/', $http_response_header[0], $m)) {
                $code = (int) $m[1];
            }
            if ($raw === false) {
                $err = 'http_request_failed';
            }
        }

        $rawText = is_string($raw) ? $raw : '';
        $json = $rawText !== '' ? json_decode($rawText, true) : null;
        $okHttp = $code >= 200 && $code < 300;

        if ($raw !== false && $okHttp) {
            return [
                'ok' => true,
                'code' => $code,
                'raw' => $rawText,
                'json' => is_array($json) ? $json : null,
            ];
        }

        $last = [
            'raw' => $raw,
            'code' => $code,
            'err' => $err !== '' ? $err : ('http_' . $code),
            'rawText' => $rawText,
            'json' => $json,
        ];

        if ($attempt < $attempts && tfJudge0LooksLikeConnectivityIssue($last['err'], $code)) {
            usleep(200000 * $attempt);
            continue;
        }
        break;
    }

    return [
        'ok' => false,
        'code' => (int) $last['code'],
        'error' => (string) $last['err'],
        'raw' => mb_substr((string) $last['rawText'], 0, 1600),
        'json' => is_array($last['json']) ? $last['json'] : null,
    ];
}

function tfJudge0NormalizeLanguage($language)
{
    $lang = strtolower(trim((string) $language));
    if ($lang === 'c++')
        return 'cpp';
    if ($lang === 'c#')
        return 'csharp';
    if ($lang === 'py')
        return 'python';
    if ($lang === 'javascript')
        return 'js';
    if ($lang === 'typescript')
        return 'ts';
    if ($lang === 'golang')
        return 'go';
    return $lang;
}

function tfJudge0ResolveLanguageId($language)
{
    static $resolved = [];
    $language = tfJudge0NormalizeLanguage($language);
    if (isset($resolved[$language])) {
        return (int) $resolved[$language];
    }

    $fallback = [
        'c' => 50,
        'csharp' => 51,
        'cpp' => 54,
        'java' => 62,
        'js' => 63,
        'python' => 71,
    ];

    $token = tfJudge0GetToken();
    $headers = [];
    if ($token !== '') {
        $headers[] = 'X-Auth-Token: ' . $token;
    }

    $bases = tfJudge0GetBaseCandidates(tfJudge0GetBaseUrl());
    foreach ($bases as $base) {
        $resp = tfJudge0Request('GET', $base . '/languages', null, $headers, 12);
        if (empty($resp['ok']) || !is_array($resp['json'])) {
            continue;
        }

        $list = $resp['json'];
        $want = null;
        foreach ($list as $item) {
            if (!is_array($item))
                continue;
            $id = (int) ($item['id'] ?? 0);
            $name = strtolower((string) ($item['name'] ?? ''));
            if ($id <= 0 || $name === '')
                continue;

            if ($language === 'cpp' && strpos($name, 'c++') !== false) {
                $want = $id;
                break;
            }
            if ($language === 'python' && strpos($name, 'python') !== false && strpos($name, '3') !== false) {
                $want = $id;
                break;
            }
            if ($language === 'java' && strpos($name, 'java') === 0) {
                $want = $id;
                break;
            }
            if ($language === 'csharp' && (strpos($name, 'c#') !== false || strpos($name, 'mono') !== false)) {
                $want = $id;
                break;
            }
            if ($language === 'c' && strpos($name, 'c++') === false && preg_match('/^c\\s*\\(/', $name)) {
                $want = $id;
                break;
            }
            if ($language === 'js' && strpos($name, 'javascript') !== false) {
                $want = $id;
                break;
            }
            if ($language === 'ts' && strpos($name, 'typescript') !== false) {
                $want = $id;
                break;
            }
            if ($language === 'go' && (preg_match('/^go\\b/', $name) || strpos($name, 'golang') !== false)) {
                $want = $id;
                break;
            }
            if ($language === 'rust' && strpos($name, 'rust') !== false) {
                $want = $id;
                break;
            }
            if ($language === 'php' && strpos($name, 'php') !== false) {
                $want = $id;
                break;
            }
            if ($language === 'ruby' && strpos($name, 'ruby') !== false) {
                $want = $id;
                break;
            }
            if ($language === 'swift' && strpos($name, 'swift') !== false) {
                $want = $id;
                break;
            }
            if ($language === 'kotlin' && strpos($name, 'kotlin') !== false) {
                $want = $id;
                break;
            }
            if ($language === 'scala' && strpos($name, 'scala') !== false) {
                $want = $id;
                break;
            }
            if ($language === 'dart' && strpos($name, 'dart') !== false) {
                $want = $id;
                break;
            }
            if ($language === 'sql' && strpos($name, 'sql') !== false) {
                $want = $id;
                break;
            }
        }
        if ($want !== null) {
            $resolved[$language] = (int) $want;
            return (int) $want;
        }
    }

    $resolved[$language] = (int) ($fallback[$language] ?? 0);
    return (int) $resolved[$language];
}

function tfJudge0SubmitAndWait(array $payload, string $base, array $headers, int $maxWaitSec = 90)
{
    $bases = tfJudge0GetBaseCandidates($base);
    $errors = [];
    $maxWaitSec = max(10, min(180, $maxWaitSec));
    $rounds = max(2, min(5, count($bases) + 1));

    for ($round = 1; $round <= $rounds; $round++) {
        foreach ($bases as $currentBase) {
            $submit = tfJudge0Request(
                'POST',
                $currentBase . '/submissions/?base64_encoded=false&wait=false',
                $payload,
                $headers,
                35
            );

            if (empty($submit['ok'])) {
                $stderr = (string) ($submit['error'] ?? 'judge0_submit_failed');
                $raw = (string) ($submit['raw'] ?? '');
                if ($raw !== '') {
                    $stderr .= ($stderr !== '' ? "\n" : '') . $raw;
                }
                $errors[] = '[round ' . $round . '][' . $currentBase . '] ' . $stderr;
                if (tfJudge0LooksLikeConnectivityIssue($stderr, (int) ($submit['code'] ?? 0))) {
                    continue;
                }
                if ($round < $rounds) {
                    continue;
                }
                return ['ok' => false, 'error' => 'judge0_submit_failed', 'stderr' => end($errors)];
            }

            $token = trim((string) ($submit['json']['token'] ?? ''));
            if ($token === '') {
                $errors[] = '[round ' . $round . '][' . $currentBase . '] Judge0 did not return a submission token.';
                continue;
            }

            $deadlineAt = microtime(true) + $maxWaitSec;
            $pollUrl = $currentBase . '/submissions/' . rawurlencode($token)
                . '?base64_encoded=false&fields=stdout,stderr,compile_output,status,exit_code,time,memory';

            $lastError = '';
            $sleepMicros = 300000;
            do {
                $poll = tfJudge0Request('GET', $pollUrl, null, $headers, 20);
                if (!empty($poll['ok']) && is_array($poll['json'])) {
                    $data = $poll['json'];
                    $status = is_array($data['status'] ?? null) ? $data['status'] : [];
                    $statusId = (int) ($status['id'] ?? 0);
                    if ($statusId > 2) {
                        return ['ok' => true, 'data' => $data, 'base' => $currentBase];
                    }
                } else {
                    $err = (string) ($poll['error'] ?? 'judge0_poll_failed');
                    $raw = (string) ($poll['raw'] ?? '');
                    $lastError = $err . ($raw !== '' ? "\n" . $raw : '');
                    if (tfJudge0LooksLikeConnectivityIssue($err, (int) ($poll['code'] ?? 0))) {
                        break;
                    }
                }

                usleep($sleepMicros);
                if ($sleepMicros < 1200000) {
                    $sleepMicros += 100000;
                }
            } while (microtime(true) < $deadlineAt);

            $stderr = 'Judge0 timeout while waiting for result.';
            if ($lastError !== '') {
                $stderr .= "\n" . $lastError;
            }
            $errors[] = '[round ' . $round . '][' . $currentBase . '] ' . $stderr;
        }

        if ($round < $rounds) {
            usleep(250000 * $round);
        }
    }

    $combined = implode("\n---\n", $errors);
    if ($combined === '') {
        $combined = 'Judge0 is unreachable.';
    }
    return ['ok' => false, 'error' => 'judge0_wait_timeout', 'stderr' => $combined];
}

function tfRunPracticeWithJudge0($language, $code, array $tests)
{
    // === 1. Валидация и санитизация входных данных ===

    $language = tfJudge0NormalizeLanguage((string) $language);
    $allowedLanguages = ['python', 'cpp', 'c', 'csharp', 'java', 'js', 'ts', 'go', 'rust', 'php', 'ruby', 'swift', 'kotlin', 'scala', 'dart', 'sql'];

    if (!in_array($language, $allowedLanguages, true)) {
        return ['ok' => false, 'error' => 'unsupported_language'];
    }

    // === 2. Ограничение размера кода (защита от огромных payload'ов) ===
    $code = (string) $code;
    $maxCodeBytes = 65536; // 64 KB — достаточно для любой учебной задачи
    if (strlen($code) > $maxCodeBytes) {
        return ['ok' => false, 'error' => 'code_too_large', 'max_bytes' => $maxCodeBytes];
    }
    if ($code === '') {
        return ['ok' => false, 'error' => 'empty_code'];
    }

    // === 3. Проверка конфигурации Judge0 ===
    $base = tfJudge0GetBaseUrl();
    if ($base === '') {
        return ['ok' => false, 'error' => 'judge0_not_configured'];
    }

    $token = tfJudge0GetToken();
    $headers = ['Content-Type: application/json'];
    if ($token !== '') {
        $headers[] = 'X-Auth-Token: ' . $token;
    }

    $languageId = tfJudge0ResolveLanguageId($language);
    if ($languageId <= 0) {
        return ['ok' => false, 'error' => 'unsupported_language'];
    }

    // === 4. Ограничение числа и размера тестов ===
    $maxTests = 20;
    $maxStdinBytes  = 65536;   // 64 KB на stdin
    $maxExpectedBytes = 65536; // 64 KB на expected_stdout

    $tests = array_slice($tests, 0, $maxTests);
    if (empty($tests)) {
        return ['ok' => false, 'error' => 'no_tests'];
    }

    // === 5. Глобальный таймаут на всю серию тестов ===
    $globalDeadline = time() + 300; // 5 минут максимум на все тесты

    try {
        $results  = [];
        $allPassed = true;

        foreach ($tests as $idx => $case) {
            // --- Защита от бесконечного цикла по времени ---
            if (time() >= $globalDeadline) {
                return [
                    'ok'      => false,
                    'error'   => 'global_timeout',
                    'message' => 'Exceeded 300s total execution limit',
                    'results' => $results,
                ];
            }

            // --- Санитизация stdin ---
            $stdin = (string) ($case['stdin'] ?? '');
            if (strlen($stdin) > $maxStdinBytes) {
                $stdin = substr($stdin, 0, $maxStdinBytes); // обрезаем, не падаем
            }

            // --- Санитизация expected_stdout ---
            $rawExpected = (string) (
                $case['expected_stdout'] ?? $case['stdout'] ?? $case['expected'] ?? ''
            );
            if (strlen($rawExpected) > $maxExpectedBytes) {
                $rawExpected = substr($rawExpected, 0, $maxExpectedBytes);
            }
            $expected = tfPracticeNormalizeOutput($rawExpected);

            // --- Таймаут одного теста ---
            $timeout = (int) ($case['timeout_sec'] ?? 3);
            $timeout = max(1, min(10, $timeout));

            // --- Queue wait ---
            $queueWaitSec = (int) ($case['queue_wait_sec'] ?? 90);
            $queueWaitSec = max(15, min(180, $queueWaitSec));

            // --- Payload без лишних данных ---
            $memoryLimitKb = (int) ($case['memory_limit_kb'] ?? 256000);
            $memoryLimitKb = max(32768, min(1048576, $memoryLimitKb));

            $payload = [
                'source_code'            => $code,
                'language_id'            => $languageId,
                'stdin'                  => $stdin,
                'expected_output'        => $expected,
                'cpu_time_limit'         => (float) $timeout,
                'wall_time_limit'        => (float) ($timeout + 1),
                'memory_limit'           => $memoryLimitKb,
                'redirect_stderr_to_stdout' => false,
            ];

            // --- Отправка в Judge0 ---
            $run = tfJudge0SubmitAndWait($payload, $base, $headers, $queueWaitSec);

            if (empty($run['ok'])) {
                $stderr = implode("\n", array_filter([
                    (string) ($run['error'] ?? ''),
                    (string) ($run['raw']   ?? ''),
                    (string) ($run['stderr'] ?? ''),
                ]));
                return ['ok' => false, 'error' => 'judge0_failed', 'stderr' => $stderr];
            }

            // --- Разбор ответа ---
            $data   = is_array($run['data'] ?? null) ? $run['data'] : [];
            $status = is_array($data['status'] ?? null) ? $data['status'] : [];

            $statusId   = (int)    ($status['id']          ?? 0);
            $statusDesc = (string) ($status['description'] ?? '');
            $exitCode   = (int)    ($data['exit_code']     ?? 0);

            $maxOutputBytes = 131072; // 128 KB
            $rawStdout = (string) ($data['stdout'] ?? '');
            $rawStderr = (string) ($data['stderr'] ?? '');
            $rawCompile = (string) ($data['compile_output'] ?? '');

            if (strlen($rawStdout)  > $maxOutputBytes) $rawStdout  = substr($rawStdout,  0, $maxOutputBytes) . "\n[truncated]";
            if (strlen($rawStderr)  > $maxOutputBytes) $rawStderr  = substr($rawStderr,  0, $maxOutputBytes) . "\n[truncated]";
            if (strlen($rawCompile) > $maxOutputBytes) $rawCompile = substr($rawCompile, 0, $maxOutputBytes) . "\n[truncated]";

            $actual = tfPracticeNormalizeOutput($rawStdout);
            $stderr = tfPracticeNormalizeOutput($rawStderr);

            $compileOutput = tfPracticeNormalizeOutput($rawCompile);
            if ($compileOutput !== '') {
                $stderr = $stderr !== '' ? ($compileOutput . "\n" . $stderr) : $compileOutput;
            }

            $timedOut = ($statusId === 5);
            $passed   = ($statusId === 3);

            if (!$passed) {
                $allPassed = false;
            }

            $results[] = [
                'idx'       => $idx,
                'passed'    => $passed,
                'timed_out' => $timedOut,
                'exit_code' => $exitCode,
                'time'      => isset($data['time']) ? (float) $data['time'] : 0,
                'memory'    => isset($data['memory']) ? (int) $data['memory'] : 0,
                'expected'  => $expected,
                'actual'    => $actual,
                'stderr'    => $stderr,
                'status_id' => $statusId,
                'status'    => $statusDesc,
            ];

            if (!$passed) {
                break;
            }

            unset($run, $data, $status, $payload, $rawStdout, $rawStderr, $rawCompile);
        }

        return [
            'ok'      => true,
            'passed'  => $allPassed,
            'results' => $results,
        ];

    } catch (Throwable $e) {
        return [
            'ok'      => false,
            'error'   => 'exception',
            'message' => $e->getMessage(),
            'trace'   => (defined('APP_DEBUG') && APP_DEBUG) ? $e->getTraceAsString() : null,
        ];
    }
}
function tfSqlExtractSingleStatement($sql)
{
    $sql = trim((string) $sql);
    $sql = preg_replace('/;+$/', '', $sql);
    if ($sql === '') {
        return ['ok' => false, 'error' => 'empty'];
    }
    if (strpos($sql, ';') !== false) {
        return ['ok' => false, 'error' => 'multi_statement'];
    }
    return ['ok' => true, 'sql' => $sql];
}

function tfSqlIsSafe($sql, array $allow, &$reason = '')
{
    $sql = trim((string) $sql);
    $allow = array_values(array_filter(array_map('strtolower', $allow)));
    if (empty($allow)) {
        $allow = ['select', 'with'];
    }

    if (!preg_match('/^\\s*([a-zA-Z_]+)/', $sql, $m)) {
        $reason = 'Не удалось определить тип SQL-запроса.';
        return false;
    }
    $first = strtolower($m[1]);
    if (!in_array($first, $allow, true)) {
        $reason = 'Разрешены только следующие операции: ' . implode(', ', $allow) . '.';
        return false;
    }

    $blocked = ['drop', 'alter', 'truncate', 'grant', 'revoke', 'commit', 'rollback', 'use', 'set'];
    foreach ($blocked as $kw) {
        if (preg_match('/\\b' . preg_quote($kw, '/') . '\\b/i', $sql)) {
            $reason = 'Запрос содержит запрещенную операцию: ' . $kw . '.';
            return false;
        }
    }
    return true;
}

function tfSqlNormalizeRows(array $rows)
{
    $norm = [];
    foreach ($rows as $row) {
        $normRow = [];
        foreach ($row as $val) {
            if ($val === null) {
                $normRow[] = null;
            } elseif (is_bool($val)) {
                $normRow[] = $val ? '1' : '0';
            } else {
                $normRow[] = (string) $val;
            }
        }
        $norm[] = $normRow;
    }
    return $norm;
}

function tfSqlGetPdo($engine)
{
    $engine = (string) $engine;
    if ($engine === 'mysql') {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
                ]
            );
            return ['ok' => true, 'pdo' => $pdo];
        } catch (Throwable $e) {
            return ['ok' => false, 'error' => 'sql_connect_failed', 'message' => $e->getMessage()];
        }
    }

    if ($engine === 'pgsql') {
        if (!defined('PG_DB') || (string) PG_DB === '') {
            return ['ok' => false, 'error' => 'sql_not_configured', 'message' => 'PostgreSQL не настроен (PG_DB не задан).'];
        }
        $host = defined('PG_HOST') ? PG_HOST : 'localhost';
        $port = defined('PG_PORT') ? PG_PORT : '5432';
        $user = defined('PG_USER') ? PG_USER : 'postgres';
        $pass = defined('PG_PASS') ? PG_PASS : '';
        try {
            $pdo = new PDO(
                "pgsql:host={$host};port={$port};dbname=" . PG_DB,
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            return ['ok' => true, 'pdo' => $pdo];
        } catch (Throwable $e) {
            return ['ok' => false, 'error' => 'sql_connect_failed', 'message' => $e->getMessage()];
        }
    }

    return ['ok' => false, 'error' => 'unsupported_language', 'message' => 'Unsupported SQL engine'];
}

function tfSqlQueryRows(PDO $pdo, $sql)
{
    $stmt = $pdo->query($sql);
    if (!$stmt) {
        return [];
    }
    return $stmt->fetchAll(PDO::FETCH_NUM);
}

function tfSqlIsReconnectableError(Throwable $e): bool
{
    $message = strtolower((string) $e->getMessage());
    if ($message === '') {
        return false;
    }

    if (strpos($message, 'server has gone away') !== false) {
        return true;
    }
    if (strpos($message, 'lost connection') !== false) {
        return true;
    }
    if (strpos($message, 'error: 2006') !== false) {
        return true;
    }
    if (strpos($message, 'error: 2013') !== false) {
        return true;
    }

    return false;
}

function tfDbIsReconnectableError(Throwable $e): bool
{
    return tfSqlIsReconnectableError($e);
}

function tfDbIsPacketTooLargeError(Throwable $e): bool
{
    $message = strtolower((string) $e->getMessage());
    if ($message === '') {
        return false;
    }
    if (strpos($message, 'max_allowed_packet') !== false) {
        return true;
    }
    if (strpos($message, 'packet bigger than') !== false) {
        return true;
    }
    if (strpos($message, 'error: 1153') !== false) {
        return true;
    }
    return false;
}

function tfRunPracticeSql($engine, $code, array $tests)
{
    $engine = (string) $engine;
    if (!in_array($engine, ['mysql', 'pgsql'], true)) {
        return ['ok' => false, 'error' => 'unsupported_language'];
    }

    $extract = tfSqlExtractSingleStatement($code);
    if (empty($extract['ok'])) {
        return ['ok' => false, 'error' => 'sql_invalid', 'message' => 'Неверный SQL-запрос: должен быть один корректный оператор без дополнительных разделителей.'];
    }
    $sql = (string) $extract['sql'];

    $maxTests = 20;
    $tests = array_slice($tests, 0, $maxTests);

    $results = [];
    $allPassed = true;

    foreach ($tests as $idx => $test) {
        if (!is_array($test)) {
            $test = [];
        }
        $allow = $test['allow'] ?? ['select', 'with'];
        $reason = '';
        if (!tfSqlIsSafe($sql, is_array($allow) ? $allow : ['select', 'with'], $reason)) {
            return ['ok' => false, 'error' => 'sql_unsafe', 'message' => $reason];
        }

        $attempts = 0;
        $maxAttempts = 2;
        while ($attempts < $maxAttempts) {
            $attempts++;
            $conn = tfSqlGetPdo($engine);
            if (empty($conn['ok'])) {
                return $conn;
            }
            $pdo = $conn['pdo'];
            try {
                if ($engine === 'mysql') {
                    $pdo->exec("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");
                    try {
                        $pdo->exec("SET SESSION MAX_EXECUTION_TIME=3000");
                    } catch (Throwable $e) {
                    }
                } else {
                    $pdo->exec("SET statement_timeout = '3000ms'");
                }

                $setup = $test['setup_sql'] ?? [];
                if (is_string($setup)) {
                    $setup = [$setup];
                }
                if (is_array($setup)) {
                    foreach ($setup as $setupSql) {
                        $setupSql = trim((string) $setupSql);
                        if ($setupSql === '') {
                            continue;
                        }
                        $pdo->exec($setupSql);
                    }
                }

                $expectedSql = trim((string) ($test['expected_sql'] ?? ''));
                if ($expectedSql === '') {
                    return ['ok' => false, 'error' => 'sql_expected_missing', 'message' => 'Отсутствует expected_sql в тесте.'];
                }

                $expectedRows = tfSqlNormalizeRows(tfSqlQueryRows($pdo, $expectedSql));
                $actualRows = tfSqlNormalizeRows(tfSqlQueryRows($pdo, $sql));
                $passed = $expectedRows === $actualRows;

                $results[] = [
                    'passed' => $passed,
                    'expected_count' => count($expectedRows),
                    'actual_count' => count($actualRows),
                ];
                if (!$passed) {
                    $allPassed = false;
                }
                break;
            } catch (Throwable $e) {
                if ($attempts < $maxAttempts && tfSqlIsReconnectableError($e)) {
                    $pdo = null;
                    continue;
                }
                return ['ok' => false, 'error' => 'sql_exec_error', 'message' => $e->getMessage()];
            } finally {
                $pdo = null;
            }
        }
    }

    return [
        'ok' => true,
        'passed' => $allPassed,
        'results' => $results,
    ];
}

function tfPracticeNormalizeFillAnswer($value): string
{
    $text = trim((string) $value);
    if (function_exists('mb_strtolower')) {
        $text = mb_strtolower($text, 'UTF-8');
    } else {
        $text = strtolower($text);
    }
    return preg_replace('/\s+/u', '', $text) ?? $text;
}

function tfRunPracticeFill(array $tests, array $answers)
{
    $test = $tests[0] ?? null;
    if (!is_array($test)) {
        return ['ok' => false, 'error' => 'fill_invalid_tests'];
    }
    $expected = $test['answers'] ?? null;
    if (!is_array($expected) || empty($expected)) {
        return ['ok' => false, 'error' => 'fill_invalid_tests'];
    }

    $results = [];
    $allPassed = true;
    foreach ($expected as $idx => $expectedValue) {
        $actualValue = $answers[$idx] ?? '';
        $isPassed = tfPracticeNormalizeFillAnswer($actualValue) === tfPracticeNormalizeFillAnswer($expectedValue);
        $results[] = [
            'blank' => $idx + 1,
            'passed' => $isPassed,
        ];
        if (!$isPassed) {
            $allPassed = false;
        }
    }

    return [
        'ok' => true,
        'passed' => $allPassed,
        'results' => $results,
    ];
}

function handleSubmitPractice()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Требуется авторизация.']);
        return;
    }

    $rateLimit = tfConsumeRateLimit('submit_practice:' . (int) $userId, 8, 60);
    if (empty($rateLimit['ok'])) {
        echo json_encode(['success' => false, 'message' => 'Слишком много посылок. Повторите позже.']);
        return;
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    @set_time_limit(300);

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Invalid request payload.']);
        return;
    }

    $missing = tfValidateRequiredFields($data, ['lessonId', 'language']);
    if (!empty($missing)) {
        echo json_encode(['success' => false, 'message' => 'Missing fields: ' . implode(', ', $missing)]);
        return;
    }

    $lessonId = (int) ($data['lessonId'] ?? 0);
    $language = isset($data['language']) ? (string) $data['language'] : '';
    $code = (string) ($data['code'] ?? '');
    $answers = is_array($data['answers'] ?? null) ? $data['answers'] : [];

    if ($lessonId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Некорректный ID урока.']);
        return;
    }

    $pdo = getDBConnection();
    ensurePracticeSchema($pdo);

    $params = [$lessonId];
    $sql = "
        SELECT *
        FROM lesson_practice_tasks
        WHERE lesson_id = ? AND is_required = 1
    ";
    if ($language !== '') {
        $sql .= " AND language = ?";
        $params[] = $language;
    }
    $sql .= " ORDER BY id ASC LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $task = $stmt->fetch();
    if (!$task) {
        echo json_encode(['success' => false, 'message' => 'Практическое задание не найдено.']);
        return;
    }

    $taskId = (int) ($task['id'] ?? 0);
    $taskLang = (string) ($task['language'] ?? '');
    $testsJson = (string) ($task['tests_json'] ?? '');
    $tests = $testsJson !== '' ? json_decode($testsJson, true) : null;
    if (!is_array($tests) || empty($tests)) {
        echo json_encode(['success' => false, 'message' => 'Тесты задания не найдены.']);
        return;
    }

    if ($taskLang !== 'fill' && $code === '') {
        echo json_encode(['success' => false, 'message' => 'Код не может быть пустым.']);
        return;
    }
    if ($taskLang !== 'fill' && mb_strlen($code) > 200000) {
        echo json_encode(['success' => false, 'message' => 'Код превышает допустимый размер.']);
        return;
    }

    if ($taskLang === 'fill') {
        if (empty($answers)) {
            echo json_encode(['success' => false, 'message' => 'Заполните ответы для всех пропусков.']);
            return;
        }
        foreach ($answers as $a) {
            if (mb_strlen((string) $a) > 2000) {
                echo json_encode(['success' => false, 'message' => 'Ответ слишком длинный.']);
                return;
            }
        }
        $run = tfRunPracticeFill($tests, $answers);
        $codeToStore = json_encode(['answers' => array_values(array_map('strval', $answers))], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($codeToStore === false) {
            $codeToStore = '';
        }
    } elseif (in_array($taskLang, ['mysql', 'pgsql'], true)) {
        $run = tfRunPracticeSql($taskLang, $code, $tests);
        $codeToStore = $code;
    } else {
        $run = tfRunPracticeWithJudge0($taskLang, $code, $tests);
        $codeToStore = $code;
    }
    if (!$run['ok']) {
        $err = (string) ($run['error'] ?? 'unknown');
        $stderr = tfPracticeNormalizeOutput((string) ($run['stderr'] ?? ''));
        $stdout = tfPracticeNormalizeOutput((string) ($run['stdout'] ?? ''));

        $trim = function ($text, $max = 1600) {
            $text = (string) $text;
            if (mb_strlen($text) <= $max)
                return $text;
            return mb_substr($text, 0, $max) . "\n...";
        };

        $message = 'Ошибка проверки.';
        switch ($err) {
            case 'judge0_not_configured':
                $message = "Judge0 не настроен. Укажите CODEMASTER_JUDGE0_URL или JUDGE0_URL.";
                break;
            case 'judge0_failed':
            if (tfJudge0LooksLikeConnectivityIssue($stderr)) {
                    $message = "Не удалось подключиться к Judge0. Проверьте доступность сервиса и адрес JUDGE0_URL/CODEMASTER_JUDGE0_URL.\n"
                        . "Можно указать запасные endpoint'ы в CODEMASTER_JUDGE0_FALLBACKS.";
                    if ($stderr !== '') {
                    $message .= "\n\n" . $trim($stderr);
                    }
            } else {
                    $message = "Сервис Judge0 вернул ошибку выполнения.";
                    if ($stderr !== '') {
                    $message .= "\n\n" . $trim($stderr);
                    } elseif ($stdout !== '') {
                    $message .= "\n\n" . $trim($stdout);
            }
                }
                break;
            case 'compile_error':
            $message = "Ошибка компиляции:\n" . ($stderr !== '' ? $trim($stderr) : $trim($stdout));
                break;
            case 'compile_timeout':
                $message = "Компиляция превысила лимит времени (таймаут).";
                break;
            case 'tmp_dir_failed':
                $message = "Не удалось создать временную директорию для запуска.";
                break;
            case 'node_not_found':
                $message = "Node.js не найден. Установите Node.js и добавьте node в PATH.\n"
                    . "Либо укажите путь в CODEMASTER_NODE_BIN (например, C:\\Program Files\\nodejs\\node.exe).";
                break;
            case 'sql_not_configured':
                $message = "PostgreSQL не настроен. Укажите PG_DB (и при необходимости PG_HOST/PG_USER/PG_PASS) в config.php.";
                break;
            case 'sql_connect_failed':
                $message = "Не удалось подключиться к SQL-движку.\n" . ($run['message'] ?? '');
                break;
            case 'sql_invalid':
                $message = "Некорректный SQL-запрос (ожидается один корректный оператор).";
                break;
            case 'sql_unsafe':
                $message = (string) ($run['message'] ?? 'Запрос содержит запрещенные операции.');
                break;
            case 'sql_expected_missing':
                $message = "Р’ тесте отсутствует expected_sql.";
                break;
            case 'sql_exec_error':
            $message = "Ошибка выполнения SQL:\n" . ($run['message'] ?? '');
                break;
            case 'fill_invalid_tests':
                $message = "Некорректные тесты для задания с пропусками.";
                break;
            case 'exception':
                $message = "Внутренняя ошибка при проверке.";
                break;
        }

        echo json_encode([
            'success' => false,
            'details' => $run,
            'message' => $message,
            'error' => $err,
            'stderr' => $trim($stderr),
            'stdout' => $trim($stdout),
        ]);
        return;
    }

    $passed = !empty($run['passed']);
    $detailsJson = json_encode(['results' => $run['results']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($detailsJson === false)
        $detailsJson = '{}';

    $stdout = '';
    $stderr = '';
    if (in_array($taskLang, ['mysql', 'pgsql', 'fill'], true)) {
        $stdout = json_encode($run['results'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($stdout === false)
            $stdout = '';
    } elseif (!empty($run['results']) && is_array($run['results'])) {
        $last = $run['results'][count($run['results']) - 1];
        $stderr = (string) ($last['stderr'] ?? '');
        $stdout = (string) ($last['actual'] ?? '');
    }

    $stmt = $pdo->prepare("
        INSERT INTO practice_submissions (user_id, task_id, code, passed, stdout, stderr, details_json)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([(int) $userId, $taskId, $codeToStore, $passed ? 1 : 0, $stdout, $stderr, $detailsJson]);

    echo json_encode([
        'success' => true,
        'passed' => $passed,
        'results' => $run['results'],
    ]);
}

function tfDetectFallbackPracticeTrack(array $lesson): string
{
    $courseTitle = trim((string) ($lesson['course_title'] ?? ''));
    $lessonTitle = trim((string) ($lesson['title'] ?? ''));
    $haystack = $courseTitle . ' ' . $lessonTitle;
    if (function_exists('mb_strtolower')) {
        $haystack = mb_strtolower($haystack, 'UTF-8');
    } else {
        $haystack = strtolower($haystack);
    }

    if (str_contains($haystack, 'english-a1') || str_contains($haystack, 'english a1') || str_contains($haystack, 'english_a1')) {
        return 'english_a1';
    }
    if (str_contains($haystack, 'english-a2') || str_contains($haystack, 'english a2') || str_contains($haystack, 'english_a2')) {
        return 'english_a2';
    }
    if (str_contains($haystack, 'english-b1') || str_contains($haystack, 'english b1') || str_contains($haystack, 'english_b1')) {
        return 'english_b1';
    }
    if (str_contains($haystack, 'html') || str_contains($haystack, 'css') || str_contains($haystack, 'верстк')) {
        return 'htmlcss';
    }
    if (str_contains($haystack, 'laravel')) {
        return 'laravel';
    }
    if (str_contains($haystack, 'php')) {
        return 'php';
    }
    if (str_contains($haystack, 'mysql') || str_contains($haystack, 'postgres') || str_contains($haystack, 'sql')) {
        return 'sql';
    }
    if (str_contains($haystack, 'nosql') || str_contains($haystack, 'mongo')) {
        return 'nosql';
    }
    if (str_contains($haystack, 'git') || str_contains($haystack, 'branch') || str_contains($haystack, 'commit')) {
        return 'git';
    }
    if (str_contains($haystack, 'devops') || str_contains($haystack, 'docker') || str_contains($haystack, 'kubernetes')) {
        return 'devops';
    }
    if (str_contains($haystack, 'design') || str_contains($haystack, 'ux') || str_contains($haystack, 'ui') || str_contains($haystack, 'дизайн')) {
        return 'design';
    }
    if (str_contains($haystack, 'mobile')) {
        return 'mobile';
    }
    if (str_contains($haystack, 'desktop')) {
        return 'desktop';
    }
    if (str_contains($haystack, 'javascript') || str_contains($haystack, 'js')) {
        return 'javascript';
    }
    return 'programming';
}

function tfIsLegacyFallbackPracticeTask(array $lesson): bool
{
    $language = (string) ($lesson['practice_language'] ?? '');
    if ($language !== 'fill') {
        return false;
    }

    $title = trim((string) ($lesson['practice_title'] ?? ''));
    $prompt = trim((string) ($lesson['practice_prompt'] ?? ''));
    if ($title === '' || $prompt === '') {
        return true;
    }

    foreach (['?', 'i??', '????'] as $marker) {
        if (str_contains($title, $marker) || str_contains($prompt, $marker)) {
            return true;
        }
    }
    if (preg_match('/\\?{3,}/', $title) || preg_match('/\\?{3,}/', $prompt)) {
        return true;
    }

    if ($title === 'Заполните пропуски в коде' && str_contains($prompt, 'Заполните пропуски')) {
        return true;
    }

    $normalizedTitle = normalizeMojibakeText($title);
    $normalizedPrompt = normalizeMojibakeText($prompt);
    return $normalizedTitle !== $title || $normalizedPrompt !== $prompt;
}

function tfBuildFallbackFillPracticeTemplate(array $lesson): array
{
    $lessonId = (int) ($lesson['id'] ?? 0);
    $courseTitle = trim(normalizeMojibakeText((string) ($lesson['course_title'] ?? '')));
    $lessonTitle = trim(normalizeMojibakeText((string) ($lesson['title'] ?? '')));
    $track = tfDetectFallbackPracticeTrack($lesson);

    $templatesByTrack = [
        'htmlcss' => [
            [
                'code' => "<button class=\"btn ___\">Send</button>\n<style>\n.btn { display: ___; }\n</style>",
                'answers' => ['primary', 'inline-block'],
            ],
            [
                'code' => "<div class=\"card\">\n  <h2>Title</h2>\n</div>\n.card {\n  padding: ___;\n  border-radius: ___;\n}",
                'answers' => ['16px', '8px'],
            ],
        ],
        'php' => [
            [
                'code' => "<?php\n\$items = [1, 2, 3];\nforeach (\$items as \$___) {\n    echo \$___ . PHP_EOL;\n}\n",
                'answers' => ['item', 'item'],
            ],
            [
                'code' => "<?php\n\$name = trim(\$_POST['name'] ?? '');\nif (\$name === '') {\n    throw new ___('Name is required');\n}\n",
                'answers' => ['Exception'],
            ],
        ],
        'laravel' => [
            [
                'code' => "Route::___('/profile', [ProfileController::class, 'show']);\n\$request->___([\n    'email' => 'required|email'\n]);",
                'answers' => ['get', 'validate'],
            ],
            [
                'code' => "\$users = User::___('active', 1)\n    ->orderBy('id', 'desc')\n    ->___();",
                'answers' => ['where', 'get'],
            ],
        ],
        'sql' => [
            [
                'code' => "SELECT ___\nFROM users\nWHERE age ___ 18;",
                'answers' => ['name', '>='],
            ],
            [
                'code' => "SELECT status, ___(*) AS total\nFROM orders\nGROUP BY ___;",
                'answers' => ['COUNT', 'status'],
            ],
        ],
        'nosql' => [
            [
                'code' => "db.users.___({ age: { \$gt: 18 } })\ndb.users.create___({ email: 1 })",
                'answers' => ['find', 'Index'],
            ],
            [
                'code' => "db.orders.___({ status: 'new' }, { \$set: { status: 'done' } })\ndb.orders.___({ status: 'done' })",
                'answers' => ['updateOne', 'countDocuments'],
            ],
        ],
        'git' => [
            [
                'code' => "git ___ feature/auth\ngit ___ -m \"Start auth feature\"",
                'answers' => ['checkout -b', 'commit'],
            ],
            [
                'code' => "git add .\ngit ___ -m \"Fix styles\"\ngit ___ origin main",
                'answers' => ['commit', 'push'],
            ],
        ],
        'devops' => [
            [
                'code' => "docker ___ -t app:latest .\ndocker ___ app:latest",
                'answers' => ['build', 'run'],
            ],
            [
                'code' => "kubectl ___ -f deployment.yaml\nkubectl ___ pods",
                'answers' => ['apply', 'get'],
            ],
        ],
        'design' => [
            [
                'code' => ":root {\n  --primary: ___;\n}\n.button {\n  font-size: ___;\n}",
                'answers' => ['#0ea5e9', '16px'],
            ],
            [
                'code' => ".layout {\n  display: ___;\n  grid-template-columns: repeat(___, 1fr);\n}",
                'answers' => ['grid', '12'],
            ],
        ],
        'mobile' => [
            [
                'code' => "function fetchUser() {\n  set___(true);\n  return api.get('/user').finally(() => set___(false));\n}",
                'answers' => ['Loading', 'Loading'],
            ],
            [
                'code' => "const [count, setCount] = use___(0);\nuse___(() => {\n  console.log(count);\n}, [count]);",
                'answers' => ['State', 'Effect'],
            ],
        ],
        'desktop' => [
            [
                'code' => "File config = new File(___);\nif (!config.___()) {\n    config.createNewFile();\n}",
                'answers' => ['\"app.conf\"', 'exists'],
            ],
            [
                'code' => "Settings s = loadSettings();\nif (s.___()) {\n    showError(\"Invalid settings\");\n}",
                'answers' => ['isInvalid'],
            ],
        ],
        'english_a1' => [
            [
                'code' => "I ___ a student.\nShe ___ from Dushanbe.",
                'answers' => ['am', 'is'],
            ],
            [
                'code' => "They ___ in the office.\nWe ___ ready.",
                'answers' => ['are', 'are'],
            ],
        ],
        'english_a2' => [
            [
                'code' => "I ___ just finished my homework.\nWe ___ to the mountains last weekend.",
                'answers' => ['have', 'went'],
            ],
            [
                'code' => "She is ___ than her brother.\nThere ___ many books on the table.",
                'answers' => ['taller', 'are'],
            ],
        ],
        'english_b1' => [
            [
                'code' => "If I ___ more time, I would join the workshop.\nThe report ___ by the team yesterday.",
                'answers' => ['had', 'was prepared'],
            ],
            [
                'code' => "By the time we arrived, the meeting ___ already started.\nHe suggested ___ the issue tomorrow.",
                'answers' => ['had', 'discussing'],
            ],
        ],
        'javascript' => [
            [
                'code' => "const nums = [1, 2, 3, 4];\nconst even = nums.___(n => n % 2 === 0);\nconsole.___(even.length);",
                'answers' => ['filter', 'log'],
            ],
            [
                'code' => "async function loadData() {\n  const res = await ___('/api/users');\n  return res.___();\n}",
                'answers' => ['fetch', 'json'],
            ],
        ],
        'programming' => [
            [
                'code' => "if (___) {\n    return ___;\n}",
                'answers' => ['a < b', 'a'],
            ],
            [
                'code' => "for (int i = 0; i < ___; ++i) {\n    sum += ___;\n}",
                'answers' => ['n', 'i'],
            ],
            [
                'code' => "int max2(int a, int b) {\n    return (a > b) ? ___ : ___;\n}",
                'answers' => ['a', 'b'],
            ],
        ],
    ];

    $templates = $templatesByTrack[$track] ?? $templatesByTrack['programming'];
    $pick = $templates[$lessonId % count($templates)];

    $title = 'Практика: ' . ($lessonTitle !== '' ? $lessonTitle : 'Заполните пропуски');
    if (function_exists('mb_substr')) {
        $title = mb_substr($title, 0, 240, 'UTF-8');
    } else {
        $title = substr($title, 0, 240);
    }

    $prompt = 'Выберите правильные варианты для каждого пропуска.';
    if ($courseTitle !== '' || $lessonTitle !== '') {
        $prompt = 'Р’ курсе «' . $courseTitle . '», в «' . $lessonTitle . '».' . "\n" . $prompt;
    }

    return [
        'title' => $title,
        'prompt' => $prompt,
        'starter_code' => $pick['code'],
        'tests' => [['answers' => $pick['answers']]],
    ];
}

function tfEnsureCourseFallbackPracticeTasks(PDO $pdo, int $courseId): void
{
    if ($courseId <= 0) {
        return;
    }

    ensurePracticeSchema($pdo);
    $stmt = $pdo->prepare("
        SELECT
            l.id,
            l.title,
            c.title AS course_title,
            lpt.id AS practice_task_id,
            lpt.language AS practice_language,
            lpt.title AS practice_title,
            lpt.prompt AS practice_prompt
        FROM lessons l
        JOIN courses c ON c.id = l.course_id
        LEFT JOIN lesson_practice_tasks lpt
            ON lpt.id = (
                SELECT t.id
                FROM lesson_practice_tasks t
                WHERE t.lesson_id = l.id AND t.is_required = 1
                ORDER BY t.id ASC
                LIMIT 1
            )
        WHERE l.course_id = ? AND l.type <> 'quiz'
        ORDER BY l.order_num ASC, l.id ASC
    ");
    $stmt->execute([$courseId]);
    $rows = $stmt->fetchAll();
    if (empty($rows)) {
        return;
    }

    $ins = $pdo->prepare("
        INSERT INTO lesson_practice_tasks (lesson_id, language, title, prompt, starter_code, tests_json, is_required)
        VALUES (?, 'fill', ?, ?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            prompt = VALUES(prompt),
            starter_code = VALUES(starter_code),
            tests_json = VALUES(tests_json),
            is_required = 1
    ");
    $upd = $pdo->prepare("
        UPDATE lesson_practice_tasks
        SET title = ?, prompt = ?, starter_code = ?, tests_json = ?, is_required = 1
        WHERE id = ?
    ");
    foreach ($rows as $lesson) {
        $practiceTaskId = (int) ($lesson['practice_task_id'] ?? 0);
        $needsInsert = $practiceTaskId <= 0;
        $needsLegacyUpdate = !$needsInsert && tfIsLegacyFallbackPracticeTask($lesson);
        if (!$needsInsert && !$needsLegacyUpdate) {
            continue;
        }

        $tpl = tfBuildFallbackFillPracticeTemplate($lesson);
        $testsJson = json_encode($tpl['tests'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($testsJson === false) {
            $testsJson = '[]';
        }

        if ($needsInsert) {
            $ins->execute([
                (int) ($lesson['id'] ?? 0),
                (string) ($tpl['title'] ?? ''),
                (string) ($tpl['prompt'] ?? ''),
                (string) ($tpl['starter_code'] ?? ''),
                $testsJson,
            ]);
            continue;
        }

        $upd->execute([
            (string) ($tpl['title'] ?? ''),
            (string) ($tpl['prompt'] ?? ''),
            (string) ($tpl['starter_code'] ?? ''),
            $testsJson,
            $practiceTaskId,
        ]);
    }
}

// Подготовка истории диалога для AI
function buildGeminiContents($userId, $limit = 12)
{
    $pdo = getDBConnection();
    ensureUserSkillsSchema($pdo);
    ensureUserSkillsSchema($pdo);
    $stmt = $pdo->prepare("SELECT sender, message_text FROM chat_messages WHERE user_id = ? ORDER BY sent_at DESC LIMIT ?");
    $stmt->execute([$userId, $limit]);
    $rows = array_reverse($stmt->fetchAll());

    $contents = [];
    foreach ($rows as $row) {
        $role = $row['sender'] === 'ai' ? 'model' : 'user';
        $contents[] = [
            'role' => $role,
            'parts' => [
                ['text' => $row['message_text']]
            ]
        ];
    }

    return $contents;
}

function tfGeminiKeyPool()
{
    $keys = [];
    $keyNames = [
        'GEMINI_API_KEY',
        'GEMINI_API_KEY_2',
        'GEMINI_API_KEY_3',
        'GEMINI_API_KEY_4',
        'GEMINI_API_KEY_5',
        'GEMINI_API_KEY_6'
    ];
    foreach ($keyNames as $name) {
        $key = tfGetEnvOrConst($name, $name);
        if ($key !== '') {
            $keys[] = $key;
        }
    }
    return array_values(array_unique($keys));
}

function tfGeminiIsRetryable($resp)
{
    $code = (int) ($resp['code'] ?? 0);
    return in_array($code, [429, 500, 502, 503, 504], true);
    }

function tfGeminiContentsToOpenAiMessages($contents)
{
    $messages = [];
    foreach ((array) $contents as $item) {
        $role = ($item['role'] ?? '') === 'model' ? 'assistant' : 'user';
        $parts = (array) ($item['parts'] ?? []);
        $texts = [];
        foreach ($parts as $part) {
            if (isset($part['text']) && $part['text'] !== '') {
                $texts[] = (string) $part['text'];
            }
        }
        $messages[] = [
            'role' => $role,
            'content' => trim(implode("\n", $texts))
        ];
    }
    return $messages;
}

function callGeminiApi($contents, $configOverrides = [])
{
    $keys = tfGeminiKeyPool();
    if (empty($keys)) {
        return ['ok' => false, 'error' => 'missing_key'];
    }

    $lastError = null;
    foreach ($keys as $key) {
        $resp = callGeminiApiWithKey($contents, $key, $configOverrides);
        if (!empty($resp['ok'])) {
            $resp['provider'] = 'gemini';
            return $resp;
        }
        $lastError = $resp;
        if (!tfGeminiIsRetryable($resp)) {
            continue;
        }
    }

    $fallbackUrl = tfGetEnvOrConst('AI_FALLBACK_URL', 'AI_FALLBACK_URL');
    $fallbackKey = tfGetEnvOrConst('AI_FALLBACK_KEY', 'AI_FALLBACK_KEY');
    $fallbackModel = tfGetEnvOrConst('AI_FALLBACK_MODEL', 'AI_FALLBACK_MODEL');
    if ($fallbackUrl !== '' && $fallbackModel !== '') {
        $messages = tfGeminiContentsToOpenAiMessages($contents);
        $resp = callOpenAiCompatibleApi($fallbackUrl, $fallbackKey, $fallbackModel, $messages, $configOverrides);
        if (!empty($resp['ok'])) {
            $resp['provider'] = 'fallback';
            return $resp;
    }
        $lastError = $resp;
    }

    return $lastError ?: ['ok' => false, 'error' => 'all_providers_failed'];
}
function extractJsonFromText($text)
{
    $text = trim($text);
    $firstObj = strpos($text, '{');
    $firstArr = strpos($text, '[');
    if ($firstObj === false && $firstArr === false) {
        return '';
    }
    $start = $firstObj === false ? $firstArr : ($firstArr === false ? $firstObj : min($firstObj, $firstArr));
    $end = strrpos($text, $start === $firstArr ? ']' : '}');
    if ($end === false || $end <= $start) {
        return '';
    }
    return substr($text, $start, $end - $start + 1);
}

function decodeJsonStrict($text)
{
    if ($text === '')
        return null;
    $data = json_decode($text, true);
    if (is_array($data))
        return $data;
    $slice = extractJsonFromText($text);
    if ($slice === '')
        return null;
    $data = json_decode($slice, true);
    return is_array($data) ? $data : null;
}

function validateCourseStructure($data)
{
    if (!is_array($data))
        return false;
    if (empty($data['course_title']) || empty($data['course_description']))
        return false;
    if (!isset($data['lessons']) || !is_array($data['lessons']) || count($data['lessons']) < 3)
        return false;
    foreach ($data['lessons'] as $lesson) {
        if (!is_array($lesson) || empty($lesson['title']) || empty($lesson['difficulty']))
            return false;
    }
    return true;
}

function validateMiniTest($data, $expectedCount)
{
    if (!is_array($data) || count($data) !== $expectedCount)
        return false;
    foreach ($data as $q) {
        if (!is_array($q))
            return false;
        if (empty($q['question']) || empty($q['type']))
            return false;
        if (!isset($q['correct_answer']) || !isset($q['points']))
            return false;
        $type = $q['type'];
        if (!in_array($type, ['mcq', 'true_false', 'short_answer', 'code'], true))
            return false;
        if ($type === 'mcq' && (empty($q['options']) || !is_array($q['options']) || count($q['options']) < 2))
            return false;
    }
    return true;
}

function validateSkillQuiz($data, $expectedCount = null)
{
    if (!is_array($data))
        return false;
    if (empty($data['questions']) || !is_array($data['questions']))
        return false;
    $count = count($data['questions']);
    if (is_int($expectedCount) && $expectedCount > 0) {
        if ($count !== $expectedCount)
            return false;
    } else {
        if ($count < 10 || $count > 100)
            return false;
    }
    foreach ($data['questions'] as $q) {
        if (!is_array($q))
            return false;
        if (empty($q['question']) || empty($q['correct_answer']))
            return false;
        if (!isset($q['options']) || !is_array($q['options']) || count($q['options']) < 3)
            return false;
    }
    return true;
}

function validateSkillQuizPartial($data, $minCount = 3, $maxCount = 10)
{
    if (!is_array($data))
        return false;
    if (empty($data['questions']) || !is_array($data['questions']))
        return false;
    $count = count($data['questions']);
    if ($count < $minCount)
        return false;
    if ($maxCount > 0 && $count > $maxCount)
        return false;
    foreach ($data['questions'] as $q) {
        if (!is_array($q))
            return false;
        if (empty($q['question']) || empty($q['correct_answer']))
            return false;
        if (!isset($q['options']) || !is_array($q['options']) || count($q['options']) < 3)
            return false;
    }
    return true;
}

function normalizeSkillQuizQuestion($q)
{
    if (!is_array($q))
        return null;
    $question = trim((string) ($q['question'] ?? ''));
    if ($question === '')
        return null;
    $optionsRaw = $q['options'] ?? [];
    if (!is_array($optionsRaw))
        return null;
    $options = [];
    foreach ($optionsRaw as $opt) {
        $opt = trim((string) $opt);
        if ($opt === '')
            continue;
        $options[] = $opt;
    }
    $options = array_values(array_unique($options));
    if (count($options) < 3)
        return null;

    $correct = trim((string) ($q['correct_answer'] ?? ''));
    if ($correct === '')
        $correct = $options[0];

    if (preg_match('/^\\d+$/', $correct)) {
        $idx = (int) $correct - 1;
        if (isset($options[$idx])) {
            $correct = $options[$idx];
        }
    } elseif (preg_match('/^[A-D]$/i', $correct)) {
        $idx = ord(strtoupper($correct)) - ord('A');
        if (isset($options[$idx])) {
            $correct = $options[$idx];
        }
    }

    $found = false;
    foreach ($options as $opt) {
        if (mb_strtolower($opt) === mb_strtolower($correct)) {
            $correct = $opt;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $correct = $options[0];
    }

    $q['question'] = $question;
    $q['options'] = $options;
    $q['correct_answer'] = $correct;
    return $q;
}

function tfIsItSkill($skill)
{
    if (!is_string($skill) || $skill === '')
        return false;
    $s = mb_strtolower(trim($skill));
    $deny = [
        'secretary',
        'accountant',
        'manager',
        'sales',
        'marketing',
        'waiter',
        'barista',
        'driver',
        'cook',
        'cashier',
        'secretar',
        'секретарь',
        'бухгалтер',
        'менеджер',
        'продавец',
        'маркетолог',
        'официант',
        'бариста',
        'водитель',
        'повар',
        'кассир',
        'администратор'
    ];
    foreach ($deny as $d) {
        if (strpos($s, $d) !== false)
            return false;
    }
    $patterns = [
        '/\b(html|css|js|javascript|typescript|ts|python|java|c\+\+|c#|c\b|go|golang|rust|php|ruby|swift|kotlin)\b/u',
        '/\b(react|vue|angular|svelte|node|nodejs|express|nest|next|nuxt|django|flask|fastapi|spring|laravel|symfony)\b/u',
        '/\b(sql|mysql|postgres|postgresql|mssql|sqlite|mongodb|redis|elastic|kafka)\b/u',
        '/\b(devops|docker|kubernetes|k8s|ci\/cd|git|linux|bash|terraform|ansible)\b/u',
        '/\b(aws|azure|gcp|cloud|backend|frontend|fullstack|api|microservices)\b/u',
        '/\b(qa|testing|test|automation|jest|pytest|junit|selenium)\b/u',
        '/\b(security|infosec|pentest|devsecops)\b/u',
        '/\b(data|ml|ai|machine learning|data science|analytics)\b/u',
        '/\b(ui|ux|design|figma|product design)\b/u',
        '/\b(android|ios|mobile|flutter|react native)\b/u',
    ];
    foreach ($patterns as $p) {
        if (preg_match($p, $s))
            return true;
    }
    return false;
}

function tfFilterItSkills($skills)
{
    if (!is_array($skills))
        return [];
    $filtered = [];
    foreach ($skills as $skill) {
        $skill = trim((string) $skill);
        if ($skill === '')
            continue;
        if (tfIsItSkill($skill)) {
            $filtered[] = $skill;
        }
    }
    return array_values(array_unique($filtered));
}

function callGeminiApiWithKey($contents, $apiKey, $configOverrides = [])
{
    $apiKey = trim((string) $apiKey);
    if ($apiKey === '') {
        return ['ok' => false, 'error' => 'missing_key'];
    }

    $model = defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-2.5-flash';
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

    $config = [
        'temperature' => 0.6,
        'maxOutputTokens' => 512
    ];
    if (is_array($configOverrides) && !empty($configOverrides)) {
        $config = array_merge($config, $configOverrides);
    }

    $payload = [
        'contents' => $contents,
        'generationConfig' => $config
    ];

    $raw = null;
    $err = null;
    $code = 0;

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-goog-api-key: ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $raw = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    } else {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n" .
                    "x-goog-api-key: " . $apiKey . "\r\n",
                'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'timeout' => 20
            ]
        ]);
        $raw = @file_get_contents($url, false, $context);
        if (isset($http_response_header[0]) && preg_match('/\\s(\\d{3})\\s/', $http_response_header[0], $m)) {
            $code = (int) $m[1];
        }
    }

    $rawText = is_string($raw) ? $raw : '';
    $data = $rawText !== '' ? json_decode($rawText, true) : null;

    if ($raw === false || $code < 200 || $code >= 300) {
        $retryAfter = 0;
        if (is_array($data) && isset($data['error']['message'])) {
            if (preg_match('/retry in\\s+([0-9\\.]+)s/i', $data['error']['message'], $m)) {
                $retryAfter = (int) ceil((float) $m[1]);
            }
        }
        return [
            'ok' => false,
            'error' => $err ?: ('http_' . $code),
            'code' => $code,
            'raw' => mb_substr($rawText, 0, 500),
            'retry_after' => $retryAfter
        ];
    }

    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

    return ['ok' => $text !== '', 'text' => $text];
}

function tfGetEnvOrConst($env, $const = '')
{
    $value = getenv($env);
    if ($value === false || $value === '') {
        if ($const !== '' && defined($const)) {
            $value = constant($const);
        } else {
            $value = '';
        }
    }
    return is_string($value) ? $value : '';
}

function callOpenAiCompatibleApi($url, $apiKey, $model, $messages, $configOverrides = [], $extraHeaders = [])
{
    if ($apiKey === '' || $url === '' || $model === '') {
        return ['ok' => false, 'error' => 'missing_key'];
    }
    $config = [
        'temperature' => 0.4,
        'max_tokens' => 900,
    ];
    if (is_array($configOverrides) && !empty($configOverrides)) {
        $config = array_merge($config, $configOverrides);
    }
    if (isset($config['maxOutputTokens']) && !isset($config['max_tokens'])) {
        $config['max_tokens'] = (int) $config['maxOutputTokens'];
    }
    if (isset($config['maxOutputTokens']) && !isset($config['max_tokens'])) {
        $config['max_tokens'] = (int) $config['maxOutputTokens'];
    }

    $payload = array_merge([
        'model' => $model,
        'messages' => $messages,
    ], $config);

    $raw = null;
    $err = null;
    $code = 0;
    $headers = array_merge([
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ], $extraHeaders);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $raw = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    } else {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers) . "\r\n",
                'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'timeout' => 20
            ]
        ]);
        $raw = @file_get_contents($url, false, $context);
        if (isset($http_response_header[0]) && preg_match('/\\s(\\d{3})\\s/', $http_response_header[0], $m)) {
            $code = (int) $m[1];
        }
    }

    $rawText = is_string($raw) ? $raw : '';
    $data = $rawText !== '' ? json_decode($rawText, true) : null;
    if ($raw === false || $code < 200 || $code >= 300) {
        return [
            'ok' => false,
            'error' => $err ?: ('http_' . $code),
            'code' => $code,
            'raw' => mb_substr($rawText, 0, 500),
        ];
    }

    $text = $data['choices'][0]['message']['content'] ?? '';
    return ['ok' => $text !== '', 'text' => $text];
}

function tfSkillQuizProviders()
{
    $providers = [];
    $keyNames = [
        'GEMINI_API_KEY',
        'GEMINI_API_KEY_2',
        'GEMINI_API_KEY_3',
        'GEMINI_API_KEY_4',
        'GEMINI_API_KEY_5',
        'GEMINI_API_KEY_6'
    ];
    foreach ($keyNames as $idx => $name) {
        $key = tfGetEnvOrConst($name, $name);
        if ($key === '') {
            continue;
        }
        $num = $idx + 1;
        $providers[] = [
            'id' => 'gemini_' . $num,
            'label' => 'Gemini #' . $num,
            'type' => 'gemini',
            'key' => $key
        ];
    }

    return $providers;
}

function callSkillQuizAi($prompt, $config = [])
{
    $contents = [
        [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ]
    ];
    return callGeminiApi($contents, $config);
}

function callSkillQuizProvider($provider, $prompt, $config = [])
{
    $type = $provider['type'] ?? '';
    if ($type === 'gemini') {
        $contents = [
            [
                'role' => 'user',
                'parts' => [['text' => $prompt]]
            ]
        ];
        $key = $provider['key'] ?? '';
        if ($key !== '') {
            return callGeminiApiWithKey($contents, $key, $config);
        }
        return callGeminiApi($contents, $config);
    }
    return ['ok' => false, 'error' => 'provider_unavailable'];
}

function tfSkillQuizDifficultyInstruction($difficulty)
{
    $diff = strtolower(trim((string) $difficulty));
    if ($diff === 'easy') {
        return 'Сложность: easy (базовые вопросы, короткие ответы, один шаг рассуждения).';
    }
    if ($diff === 'medium') {
        return 'Сложность: medium (практические ситуации, несколько шагов рассуждения).';
    }
    if ($diff === 'hard') {
        return 'Сложность: hard (углубленные вопросы, комплексные сценарии).';
    }
    return 'Сложность: смешанная (easy/medium/hard).';
}

function generateSkillQuizChunkWithProvider($skillsText, $count, $provider, $difficulty = 'mixed')
{
    $difficultyInstruction = tfSkillQuizDifficultyInstruction($difficulty);
    $prompt = "Сгенерируй {$count} вопросов по IT-навыкам: {$skillsText}.\n"
        . "Каждый вопрос: текст + 3-4 варианта ответов, один правильный.\n"
        . $difficultyInstruction . "\n"
        . "Верни только JSON без markdown в формате:\n"
        . "{\n"
        . "  \"questions\": [\n"
        . "    {\"question\": \"...\", \"options\": [\"A\",\"B\",\"C\"], \"correct_answer\": \"B\"}\n"
        . "  ]\n"
        . "}\n"
        . "Без дополнительного текста.";

    $resp = callSkillQuizProvider($provider, $prompt, ['maxOutputTokens' => 1200, 'temperature' => 0.4]);
    if (!$resp['ok']) {
        error_log('Skill quiz provider error: ' . ($provider['id'] ?? 'unknown') . ' err=' . ($resp['error'] ?? 'unknown'));
        return $resp;
    }
    $text = trim($resp['text'] ?? '');
    $data = decodeJsonStrict($text);
    if (!validateSkillQuizPartial($data, 3, max(3, $count + 2))) {
        error_log('Skill quiz invalid JSON (chunk): ' . mb_substr($text, 0, 400));
        $prompt2 = "Верни только JSON без markdown. Формат: {\"questions\":[{\"question\":\"...\",\"options\":[\"A\",\"B\",\"C\"],\"correct_answer\":\"B\"}]}.\n"
            . "Навыки: {$skillsText}. {$difficultyInstruction}";
        $resp2 = callSkillQuizProvider($provider, $prompt2, ['maxOutputTokens' => 1200, 'temperature' => 0.35]);
        if (!$resp2['ok'])
            return $resp2;
        $data2 = decodeJsonStrict(trim($resp2['text'] ?? ''));
        if (!validateSkillQuizPartial($data2, 3, max(3, $count + 2))) {
            error_log('Skill quiz retry invalid JSON (chunk): ' . mb_substr(trim($resp2['text'] ?? ''), 0, 400));
            return ['ok' => false, 'error' => 'invalid_json'];
        }
        return ['ok' => true, 'data' => $data2];
    }
    return ['ok' => true, 'data' => $data];
}

function generateSkillQuizChunk($skillsText, $count, $difficulty = 'mixed')
{
    $difficultyInstruction = tfSkillQuizDifficultyInstruction($difficulty);
    $prompt = "Сгенерируй {$count} вопросов по IT-навыкам: {$skillsText}.\n"
        . "Каждый вопрос: текст + 3-4 варианта ответов, один правильный.\n"
        . $difficultyInstruction . "\n"
        . "Верни только JSON без markdown в формате:\n"
        . "{\n"
        . "  \"questions\": [\n"
        . "    {\"question\": \"...\", \"options\": [\"A\",\"B\",\"C\"], \"correct_answer\": \"B\"}\n"
        . "  ]\n"
        . "}\n"
        . "Без дополнительного текста.";

    $resp = callSkillQuizAi($prompt, ['maxOutputTokens' => 1200, 'temperature' => 0.4]);
    if (!$resp['ok'])
        return $resp;
    $text = trim($resp['text'] ?? '');
    $data = decodeJsonStrict($text);
    if (!validateSkillQuiz($data, $count)) {
        $prompt2 = "Верни только JSON без markdown. Формат: {\"questions\":[{\"question\":\"...\",\"options\":[\"A\",\"B\",\"C\"],\"correct_answer\":\"B\"}]}.\n"
            . "Навыки: {$skillsText}. {$difficultyInstruction}\n";
        $resp2 = callSkillQuizAi($prompt2, ['maxOutputTokens' => 1200, 'temperature' => 0.4]);
        if (!$resp2['ok'])
            return $resp2;
        $data2 = decodeJsonStrict(trim($resp2['text'] ?? ''));
        if (!validateSkillQuiz($data2, $count)) {
            return ['ok' => false, 'error' => 'invalid_json'];
        }
        return ['ok' => true, 'data' => $data2];
    }
    return ['ok' => true, 'data' => $data];
}

function generateSkillQuiz($skillsText, $count = 30, $chunkSize = 5, $insurance = 12, $difficulty = 'mixed')
{
    $count = max(10, min(100, (int) $count));
    $chunkSize = max(3, min(10, (int) $chunkSize));
    $insurance = max(0, (int) $insurance);
    $skillsText = trim((string) $skillsText);
    if ($skillsText === '') {
        return ['ok' => false, 'error' => 'no_skills'];
    }

    $providers = tfSkillQuizProviders();
    if (empty($providers)) {
        return ['ok' => false, 'error' => 'missing_providers'];
    }

    $target = $count + $insurance;
    $questions = [];
    $seen = [];
    $usedProviders = [];
    $failedProviders = [];

    $attempts = 0;
    $effectiveChunk = max(1, $chunkSize - 1);
    $maxAttempts = max(10, (int) ceil($target / $effectiveChunk) + 6);
    $providerIdx = 0;

    while (count($questions) < $target && $attempts < $maxAttempts) {
        $provider = $providers[$providerIdx % count($providers)];
        $providerIdx++;
        $attempts++;

        $resp = generateSkillQuizChunkWithProvider($skillsText, $chunkSize, $provider, $difficulty);
        if (!$resp['ok']) {
            $failedProviders[$provider['id']] = true;
            continue;
        }
        $usedProviders[$provider['id']] = $provider['label'];
        $items = $resp['data']['questions'] ?? [];
        if (!is_array($items)) {
            $failedProviders[$provider['id']] = true;
            continue;
        }
        foreach ($items as $q) {
            $q = normalizeSkillQuizQuestion($q);
            if (!$q)
                continue;
            $questionText = $q['question'];
            $key = mb_strtolower($questionText);
            if (isset($seen[$key]))
                continue;
            $seen[$key] = true;
            $questions[] = $q;
            if (count($questions) >= $target)
                break;
        }
    }

    if (count($questions) < $count) {
        return [
            'ok' => false,
            'error' => 'insufficient_questions',
            'have' => count($questions),
            'needed' => $count,
            'meta' => [
                'providers_used' => array_values($usedProviders),
                'providers_failed' => array_keys($failedProviders),
                'attempts' => $attempts
            ]
        ];
    }

    $questions = array_slice($questions, 0, $count);
    $data = ['questions' => $questions];
    if (!validateSkillQuiz($data, $count)) {
        return ['ok' => false, 'error' => 'invalid_json'];
    }
    return [
        'ok' => true,
        'data' => $data,
        'meta' => [
            'providers_used' => array_values($usedProviders),
            'providers_failed' => array_keys($failedProviders),
            'insurance' => $insurance,
            'total' => count($questions),
        ],
    ];
}

function formatAiErrorMessage($step, $resp)
{
    $err = $resp['error'] ?? 'unknown';
    $code = $resp['code'] ?? 0;
    $retry = isset($resp['retry_after']) ? (int) $resp['retry_after'] : 0;
    if ($err === 'missing_key') {
        return "Не задан API ключ для шага {$step}. Проверьте настройку Gemini API.";
    }
    if ($err === 'rate_limited' || $code === 429) {
        $suffix = $retry > 0 ? " Повторите попытку через {$retry} сек." : '';
        return "Превышен лимит запросов на шаге {$step}. Попробуйте позже." . $suffix;
    }
    if ($err === 'invalid_json') {
        return "Некорректный JSON на шаге {$step}.";
    }
    return "Ошибка на шаге {$step}. Причина: {$err}.";
}

function generateCourseStructure($topic, $skills, $lessonCount = 3)
{
    $skillsText = is_array($skills) && count($skills) > 0 ? implode(', ', $skills) : '';
    $prompt = "You are an expert course designer.\n"
        . "Create a structured course on topic: \"{$topic}\".\n"
        . "Level: beginner.\n"
        . ($skillsText !== '' ? "Focus skills: {$skillsText}.\n" : "")
        . "Output JSON with:\n"
        . "- course_title\n"
        . "- course_description\n"
        . "- lessons (array)\n"
        . "Each lesson must have:\n"
        . "- title\n"
        . "- difficulty\n"
        . "Return ONLY valid JSON. No markdown, no extra text.\n"
        . "Return exactly {$lessonCount} lessons.\n"
        . "Make course_description 1-2 sentences.";

    $contents = [
        [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ]
    ];

    $resp = callGeminiApi($contents, ['maxOutputTokens' => 1024]);
    if (!$resp['ok'])
        return $resp;
    $text = trim($resp['text'] ?? '');
    $data = decodeJsonStrict($text);
    if (!validateCourseStructure($data)) {
        error_log('Course structure invalid JSON: ' . mb_substr($text, 0, 500));
        $prompt2 = "Return ONLY strict JSON (double quotes, no markdown, no comments).\n"
            . "Topic: \"{$topic}\". Level: beginner.\n"
            . "Return exactly {$lessonCount} lessons.\n"
            . "course_description: 1-2 sentences.\n"
            . "Schema:\n"
            . "{\n"
            . "  \"course_title\": \"...\",\n"
            . "  \"course_description\": \"...\",\n"
            . "  \"lessons\": [\n"
            . "    {\"title\": \"...\", \"difficulty\": \"easy|medium|hard\"}\n"
            . "  ]\n"
            . "}\n"
            . "Return ONLY JSON.";
        $contents2 = [
            [
                'role' => 'user',
                'parts' => [['text' => $prompt2]]
            ]
        ];
        $resp2 = callGeminiApi($contents2, ['maxOutputTokens' => 1024]);
        if (!$resp2['ok'])
            return $resp2;
        $text2 = trim($resp2['text'] ?? '');
        $data2 = decodeJsonStrict($text2);
        if (!validateCourseStructure($data2)) {
            error_log('Course structure retry invalid JSON: ' . mb_substr($text2, 0, 500));
            return ['ok' => false, 'error' => 'invalid_json'];
        }
        return ['ok' => true, 'data' => $data2];
    }
    return ['ok' => true, 'data' => $data];
}

function generateLessonTheory($title, $goals, $difficulty)
{
    $goalsText = is_array($goals) ? implode('; ', $goals) : (string) $goals;
    $prompt = "Generate a lesson content.\n"
        . "Topic: \"{$title}\"\n"
        . "Goals: {$goalsText}\n"
        . "Difficulty: {$difficulty}\n"
        . "Rules:\n"
        . "- Clear explanation\n"
        . "- Examples\n"
        . "- Use markdown\n"
        . "- Length: ~800 words";

    $contents = [
        [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ]
    ];

    $resp = callGeminiApi($contents);
    if (!$resp['ok'])
        return $resp;
    $text = trim($resp['text'] ?? '');
    if ($text === '')
        return ['ok' => false, 'error' => 'empty_text'];
    return ['ok' => true, 'text' => $text];
}

function generateLessonPackage($title, $difficulty, $topic, $questionCount = 3)
{
    $prompt = "Generate a lesson package.\n"
        . "Course topic: \"{$topic}\"\n"
        . "Lesson title: \"{$title}\"\n"
        . "Difficulty: {$difficulty}\n"
        . "Return ONLY strict JSON:\n"
        . "{\n"
        . "  \"theory_text\": \"...markdown...\",\n"
        . "  \"mini_test\": [\n"
        . "    {\"question\":\"\",\"type\":\"mcq\",\"options\":[\"A\",\"B\",\"C\",\"D\"],\"correct_answer\":\"B\",\"points\":2}\n"
        . "  ]\n"
        . "}\n"
        . "Rules:\n"
        . "- theory_text ~600-800 words\n"
        . "- {$questionCount} questions\n"
        . "- Types: mcq, true_false, short_answer, code (if IT)\n"
        . "Return ONLY JSON (no markdown fences).";

    $contents = [
        [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ]
    ];

    $resp = callGeminiApi($contents, ['maxOutputTokens' => 1200]);
    if (!$resp['ok'])
        return $resp;
    $text = trim($resp['text'] ?? '');
    $data = decodeJsonStrict($text);
    if (!is_array($data) || empty($data['theory_text']) || !isset($data['mini_test'])) {
        error_log('Lesson package invalid JSON: ' . mb_substr($text, 0, 500));
        $prompt2 = "Return ONLY strict JSON with keys theory_text and mini_test.\n"
            . "Lesson title: \"{$title}\"\n"
            . "Difficulty: {$difficulty}\n"
            . "mini_test must have {$questionCount} questions.\n"
            . "Return ONLY JSON.";
        $contents2 = [
            [
                'role' => 'user',
                'parts' => [['text' => $prompt2]]
            ]
        ];
        $resp2 = callGeminiApi($contents2, ['maxOutputTokens' => 1200]);
        if (!$resp2['ok'])
            return $resp2;
        $text2 = trim($resp2['text'] ?? '');
        $data2 = decodeJsonStrict($text2);
        if (!is_array($data2) || empty($data2['theory_text']) || !isset($data2['mini_test'])) {
            error_log('Lesson package retry invalid JSON: ' . mb_substr($text2, 0, 500));
            return ['ok' => false, 'error' => 'invalid_json'];
        }
        if (!validateMiniTest($data2['mini_test'], $questionCount)) {
            return ['ok' => false, 'error' => 'invalid_json'];
        }
        return ['ok' => true, 'data' => $data2];
    }
    if (!validateMiniTest($data['mini_test'], $questionCount)) {
        return ['ok' => false, 'error' => 'invalid_json'];
    }
    return ['ok' => true, 'data' => $data];
}

function generateLessonGoals($title, $difficulty, $topic)
{
    $prompt = "Generate 2-3 concise learning goals for a lesson.\n"
        . "Course topic: \"{$topic}\"\n"
        . "Lesson title: \"{$title}\"\n"
        . "Difficulty: {$difficulty}\n"
        . "Return ONLY JSON array of strings. Example:\n"
        . "[\"Goal 1\", \"Goal 2\"]";

    $contents = [
        [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ]
    ];

    $resp = callGeminiApi($contents, ['maxOutputTokens' => 256]);
    if (!$resp['ok'])
        return $resp;
    $data = decodeJsonStrict(trim($resp['text'] ?? ''));
    if (!is_array($data) || count($data) < 2) {
        return ['ok' => false, 'error' => 'invalid_json'];
    }
    foreach ($data as $g) {
        if (!is_string($g) || trim($g) === '') {
            return ['ok' => false, 'error' => 'invalid_json'];
        }
    }
    return ['ok' => true, 'data' => $data];
}

function generateMiniTest($lessonContent, $questionCount = 3)
{
    $content = $lessonContent;
    if (mb_strlen($content) > 2000) {
        $content = mb_substr($content, 0, 2000);
    }
    $prompt = "Create a mini-test for the lesson.\n"
        . "Lesson content:\n<<<TEXT>>>\n{$content}\n<<<END>>>\n"
        . "Rules:\n"
        . "- {$questionCount} questions\n"
        . "- Difficulty: medium\n"
        . "- Output strict JSON:\n"
        . "[\n"
        . "  {\n"
        . "    \"question\": \"\",\n"
        . "    \"type\": \"mcq\",\n"
        . "    \"options\": [\"A\",\"B\",\"C\",\"D\"],\n"
        . "    \"correct_answer\": \"B\",\n"
        . "    \"points\": 2\n"
        . "  }\n"
        . "]\n"
        . "Return ONLY valid JSON.";

    $contents = [
        [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ]
    ];

    $resp = callGeminiApi($contents, ['maxOutputTokens' => 600]);
    if (!$resp['ok'])
        return $resp;
    $text = trim($resp['text'] ?? '');
    $data = decodeJsonStrict($text);
    if (!validateMiniTest($data, $questionCount)) {
        error_log('Mini-test invalid JSON: ' . mb_substr($text, 0, 500));
        $prompt2 = "Return ONLY strict JSON (no markdown, no extra text).\n"
            . "Create {$questionCount} questions for the lesson.\n"
            . "Use types: mcq, true_false, short_answer, code.\n"
            . "Schema:\n"
            . "[{\"question\":\"\",\"type\":\"mcq\",\"options\":[\"A\",\"B\",\"C\",\"D\"],\"correct_answer\":\"B\",\"points\":2}]\n"
            . "Lesson content:\n<<<TEXT>>>\n{$content}\n<<<END>>>\n";
        $contents2 = [
            [
                'role' => 'user',
                'parts' => [['text' => $prompt2]]
            ]
        ];
        $resp2 = callGeminiApi($contents2, ['maxOutputTokens' => 600]);
        if (!$resp2['ok'])
            return $resp2;
        $text2 = trim($resp2['text'] ?? '');
        $data2 = decodeJsonStrict($text2);
        if (!validateMiniTest($data2, $questionCount)) {
            error_log('Mini-test retry invalid JSON: ' . mb_substr($text2, 0, 500));
            return ['ok' => false, 'error' => 'invalid_json'];
        }
        return ['ok' => true, 'data' => $data2];
    }
    return ['ok' => true, 'data' => $data];
}

function generateFinalExam($courseTitle, $lessons, $questionCount = 10)
{
    $lessonTitles = [];
    foreach ($lessons as $l) {
        if (!empty($l['title']))
            $lessonTitles[] = $l['title'];
    }
    $lessonsText = implode('; ', $lessonTitles);
    $prompt = "Generate a final exam for course: \"{$courseTitle}\".\n"
        . "Rules:\n"
        . "- {$questionCount} questions\n"
        . "- Mix of mcq, true_false, short_answer\n"
        . "- Cover all lessons evenly\n"
        . "- Output JSON array of objects with fields: question, type, options (for mcq), correct_answer, points\n"
        . "Lessons: {$lessonsText}\n"
        . "Return ONLY valid JSON.";

    $contents = [
        [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ]
    ];

    $resp = callGeminiApi($contents);
    if (!$resp['ok'])
        return $resp;
    $data = decodeJsonStrict(trim($resp['text'] ?? ''));
    if (!validateMiniTest($data, $questionCount)) {
        return ['ok' => false, 'error' => 'invalid_json'];
    }
    return ['ok' => true, 'data' => $data];
}

function checkShortAnswerWithAI($question, $correctConcept, $studentAnswer)
{
    $prompt = "Check student's answer.\n"
        . "Question: {$question}\n"
        . "Correct concept: {$correctConcept}\n"
        . "Student answer: {$studentAnswer}\n\n"
        . "Respond only with:\n"
        . "{\n"
        . " \"is_correct\": true/false,\n"
        . " \"confidence\": 0-100\n"
        . "}";

    $contents = [
        [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ]
    ];

    $resp = callGeminiApi($contents);
    if (!$resp['ok'])
        return $resp;
    $data = decodeJsonStrict(trim($resp['text'] ?? ''));
    if (!is_array($data) || !isset($data['is_correct']) || !isset($data['confidence'])) {
        return ['ok' => false, 'error' => 'invalid_json'];
    }
    return ['ok' => true, 'data' => $data];
}

function analyzeCheatingWithAI($answersText)
{
    $prompt = "Analyze if answers look copied or AI-generated.\n"
        . "Return probability of cheating as JSON:\n"
        . "{ \"cheating_probability\": 0-100 }\n\n"
        . "Answers:\n{$answersText}\n";

    $contents = [
        [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ]
    ];

    $resp = callGeminiApi($contents);
    if (!$resp['ok'])
        return $resp;
    $data = decodeJsonStrict(trim($resp['text'] ?? ''));
    if (!is_array($data) || !isset($data['cheating_probability'])) {
        return ['ok' => false, 'error' => 'invalid_json'];
    }
    return ['ok' => true, 'data' => $data];
}

function generateCoursePlan($goal, $skills)
{
    $skillsText = implode(', ', $skills);
    $prompt = "Составь план курса.\n"
        . "Цель: \"{$goal}\"\n"
        . "Навыки: [{$skillsText}]\n\n"
        . "Требования:\n"
        . "- Верни только JSON без markdown.\n"
        . "- 6-8 уроков.\n"
        . "- Р’ каждом уроке: title, type (video|article|quiz), content (краткое описание).\n"
        . "- Минимум 1 урок типа quiz.\n"
        . "- Учитывай цель и навыки.\n\n"
        . "JSON:\n"
        . "{\n"
        . "  \"title\": \"...\",\n"
        . "  \"description\": \"...\",\n"
        . "  \"category\": \"frontend|backend|design|devops|other\",\n"
        . "  \"level\": \"начальный|средний|продвинутый\",\n"
        . "  \"lessons\": [\n"
        . "    {\"title\":\"...\",\"type\":\"video|article|quiz\",\"content\":\"...\"}\n"
        . "  ]\n"
        . "}";

    $contents = [
        [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ]
    ];

    $resp = callGeminiApi($contents);
    if (!$resp['ok']) {
        $err = $resp['error'] ?? 'ai_error';
        $retryAfter = $resp['retry_after'] ?? 0;
        if ($err === 'http_429') {
            return ['ok' => false, 'error' => 'rate_limited', 'retry_after' => $retryAfter];
        }
        return ['ok' => false, 'error' => $err];
    }

    $text = trim($resp['text'] ?? '');
    $json = json_decode($text, true);
    if (!is_array($json)) {
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $slice = substr($text, $start, $end - $start + 1);
            $json = json_decode($slice, true);
        }
    }
    if (!is_array($json)) {
        return ['ok' => false, 'error' => 'invalid_json'];
    }

    return ['ok' => true, 'data' => $json];
}

function buildFallbackCoursePlan($goal, $skills)
{
    $skills = is_array($skills) ? array_values(array_filter($skills, function ($s) {
        return trim((string) $s) !== '';
    })) : [];
    $primarySkill = $skills[0] ?? 'основные навыки';
    $lessons = [
        ['title' => 'Введение в тему', 'type' => 'video', 'content' => 'Обзор целей и структуры курса.'],
        ['title' => 'Базовые понятия', 'type' => 'article', 'content' => 'Ключевые термины и определения.'],
        ['title' => 'Практика: первые шаги', 'type' => 'video', 'content' => 'Разбор простых примеров и упражнений.'],
        ['title' => 'ИнстрСѓмРµнты и окружение', 'type' => 'article', 'content' => 'Настройка среды и полезные инструменты.'],
        ['title' => 'Практика по теме: ' . $primarySkill, 'type' => 'video', 'content' => 'Применение навыка на мини-кейсе.'],
        ['title' => 'Проверка знаний', 'type' => 'quiz', 'content' => 'Короткий тест для закрепления материала.']
    ];

    return [
        'title' => $goal ?: 'ИндивидСѓальный план обучения',
        'description' => 'План обучения, составленный под вашу цель и навыки.',
        'category' => 'other',
        'level' => 'начальный',
        'lessons' => $lessons
    ];
}

function buildAiContextFromDb($message)
{
    $lower = mb_strtolower($message, 'UTF-8');
    $needCourses = false;
    $needVacancies = false;

    $courseKeywords = ['курс', 'курсы', 'обучение', 'урок', 'уроки', 'занятие', 'lesson', 'lessons'];
    $vacancyKeywords = ['вакансия', 'вакансии', 'работа', 'должность', 'job', 'jobs'];

    foreach ($courseKeywords as $kw) {
        if (mb_strpos($lower, $kw, 0, 'UTF-8') !== false) {
            $needCourses = true;
            break;
        }
    }
    foreach ($vacancyKeywords as $kw) {
        if (mb_strpos($lower, $kw, 0, 'UTF-8') !== false) {
            $needVacancies = true;
            break;
        }
    }

    if (!$needCourses && !$needVacancies) {
        return ['context' => '', 'needCourses' => false, 'needVacancies' => false, 'coursesCount' => 0, 'vacanciesCount' => 0];
    }

    $pdo = getDBConnection();
    $lines = [];
    $coursesCount = 0;
    $vacanciesCount = 0;

    if ($needCourses) {
        $stmt = $pdo->query("SELECT title, level, category, instructor FROM courses ORDER BY created_at DESC LIMIT 5");
        $courses = $stmt->fetchAll();
        $coursesCount = count($courses);
        if ($coursesCount > 0) {
            $lines[] = "Курсы (топ 5):";
            $i = 1;
            foreach ($courses as $c) {
                $lines[] = $i . ") " . $c['title'] . " — " . $c['level'] . ", " . $c['category'] . ", " . $c['instructor'];
                $i++;
            }
        } else {
            $lines[] = "Курсы: пока ничего не найдено.";
        }
    }

    if ($needVacancies) {
        $stmt = $pdo->query("SELECT title, company, location, type, salary_min, salary_max, salary_currency FROM vacancies ORDER BY created_at DESC LIMIT 5");
        $vacancies = $stmt->fetchAll();
        $vacanciesCount = count($vacancies);
        if ($vacanciesCount > 0) {
            $lines[] = "Вакансии (топ 5):";
            $i = 1;
            foreach ($vacancies as $v) {
                $salary = '';
                if (!empty($v['salary_min']) || !empty($v['salary_max'])) {
                    $currency = $v['salary_currency'] ?? 'TJS';
                    $salary = " " . $currency . " " . ($v['salary_min'] ?? '') . "-" . ($v['salary_max'] ?? '');
                }
                $lines[] = $i . ") " . $v['title'] . " — " . $v['company'] . ", " . $v['location'] . ", " . $v['type'] . $salary;
                $i++;
            }
        } else {
            $lines[] = "Вакансии: пока ничего не найдено.";
        }
    }

    return [
        'context' => implode("\n", $lines),
        'needCourses' => $needCourses,
        'needVacancies' => $needVacancies,
        'coursesCount' => $coursesCount,
        'vacanciesCount' => $vacanciesCount
    ];
}

function generateAIResponse($message, $userId)
{
    $lang = function_exists('currentLang') ? currentLang() : 'ru';
    if (!in_array($lang, ['ru', 'en', 'tg'], true)) {
        $lang = 'ru';
    }
    $systemPromptMap = [
        'ru' => "You are the AI tutor of the itsphere360 platform, not Gemini. Reply only in Russian, English, or Tajik; if the question is in another language, say you only support those three languages. "
            . "Do not provide final answers or complete solutions. Explain the steps and reasoning, and guide the user with questions and hints. "
            . "If the question is unrelated to IT learning/careers, the itsphere360 platform, courses, or vacancies, reply: \"This is not within my competence.\"",

        'en' => "You are the AI tutor of the itsphere360 platform, not Gemini. Reply only in Russian, English, or Tajik; if the question is in another language, say you only support those three languages. "
            . "Do not provide final answers or complete solutions. Explain the steps and reasoning, and guide the user with questions and hints. "
            . "If the question is unrelated to IT learning/careers, the itsphere360 platform, courses, or vacancies, reply: \"This is not within my competence.\"",

        'tg' => "You are the AI tutor of the itsphere360 platform, not Gemini. Reply only in Russian, English, or Tajik; if the question is in another language, say you only support those three languages. "
            . "Do not provide final answers or complete solutions. Explain the steps and reasoning, and guide the user with questions and hints. "
            . "If the question is unrelated to IT learning/careers, the itsphere360 platform, courses, or vacancies, reply: \"This is not within my competence.\"",
    ];
    $systemPrompt = $systemPromptMap[$lang];
    $contents = buildGeminiContents($userId, 12);
    if (!is_array($contents)) {
        $contents = [];
    }
    $contents[] = [
        'role' => 'user',
        'parts' => [['text' => $message]]
    ];
    if (empty($contents)) {
        $contents = [
            [
                'role' => 'user',
                'parts' => [['text' => $message]]
            ]
        ];
    }
    array_unshift($contents, [
        'role' => 'user',
        'parts' => [['text' => $systemPrompt]]
    ]);


    $ctx = buildAiContextFromDb($message);
    if ($ctx['needCourses'] && $ctx['coursesCount'] === 0 && !$ctx['needVacancies']) {
        return 'Сейчас нет подходящих курсов.';
    }
    if ($ctx['needVacancies'] && $ctx['vacanciesCount'] === 0 && !$ctx['needCourses']) {
        return 'Сейчас нет подходящих вакансий.';
    }
    if ($ctx['context'] !== '') {
        $lastIndex = count($contents) - 1;
        $contents[$lastIndex]['parts'][0]['text'] .= "\n\nКонтекст из базы данных (используй как справку):\n"
            . $ctx['context']
            . "\n\nНе выдумывай факты, которых нет в контексте. Если данных недостаточно, уточни вопрос.";
    }

    $result = callGeminiApi($contents);
    if (!$result['ok']) {
        $code = isset($result['code']) ? $result['code'] : 0;
        $err = $result['error'] ?? 'unknown';
        $raw = $result['raw'] ?? '';
        $retryAfter = isset($result['retry_after']) ? (int) $result['retry_after'] : 0;
        error_log('Gemini error: ' . $err . ' code=' . $code . ' raw=' . $raw);

        if ($code === 429) {
            if ($retryAfter > 0) {
                return 'Сервис временно перегружен. Повторите через ' . $retryAfter . ' сек.';
            }
            return 'Сервис временно перегружен. Попробуйте позже.';
        }
        if ($code === 401 || $code === 403) {
            return 'Ошибка авторизации API. Проверьте ключ Gemini.';
        }
        return 'AI временно недоступен. Попробуйте позже.';
    }

    return $result['text'];
}
function handleChatMessage()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data) || !isset($data['message'])) {
        tfDebugLog('chat.invalid_json', [
            'raw_preview' => mb_substr((string) $raw, 0, 200),
            'content_type' => (string) ($_SERVER['CONTENT_TYPE'] ?? '')
        ]);
        echo json_encode(['success' => false, 'message' => 'Неверные данные.']);
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    $message = trim((string) $data['message']);
    tfDebugLog('chat.start', [
        'user_id' => $userId !== null ? (int) $userId : null,
        'message_len' => mb_strlen($message),
        'has_session' => isset($_SESSION['user_id'])
    ]);

    if (!$userId || !$message) {
        tfDebugLog('chat.validation_failed', [
            'user_id' => $userId !== null ? (int) $userId : null,
            'message_len' => mb_strlen($message)
        ]);
        echo json_encode(['success' => false, 'message' => 'Ошибка запроса.']);
        return;
    }

    $now = time();
    if (isset($_SESSION['ai_next_allowed']) && $now < $_SESSION['ai_next_allowed']) {
        $wait = $_SESSION['ai_next_allowed'] - $now;
        tfDebugLog('chat.cooldown', ['user_id' => (int) $userId, 'wait_seconds' => $wait]);
        echo json_encode(['success' => false, 'message' => 'Слишком часто. Повторите через ' . $wait . ' сек.']);
        return;
    }

    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("INSERT INTO chat_messages (user_id, sender, message_text, sent_at) VALUES (?, 'user', ?, NOW())");
        $stmt->execute([$userId, $message]);
        trimUserChatMessages($pdo, (int) $userId, 15);

        $aiResponse = generateAIResponse($message, $userId);
        if (!is_string($aiResponse)) {
            $aiResponse = (string) $aiResponse;
        }
        if (is_string($aiResponse) && preg_match('/повторите через (\\d+) сек/iu', $aiResponse, $m)) {
            $_SESSION['ai_next_allowed'] = $now + (int) $m[1];
        }
        $stmt = $pdo->prepare("INSERT INTO chat_messages (user_id, sender, message_text, sent_at) VALUES (?, 'ai', ?, NOW())");
        $stmt->execute([$userId, $aiResponse]);
        trimUserChatMessages($pdo, (int) $userId, 15);

        tfDebugLog('chat.success', [
            'user_id' => (int) $userId,
            'ai_response_len' => mb_strlen($aiResponse)
        ]);
        echo json_encode(['success' => true, 'message' => 'Операция выполнена.', 'aiResponse' => $aiResponse]);

    } catch (Throwable $e) {
        tfDebugLog('chat.exception', [
            'user_id' => (int) $userId,
            'error' => $e->getMessage()
        ]);
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

// Обрезка истории чата
function trimUserChatMessages(PDO $pdo, int $userId, int $maxMessages = 15): void
{
    $stmt = $pdo->prepare("
        DELETE FROM chat_messages
        WHERE user_id = ?
          AND id NOT IN (
              SELECT id
              FROM (
                  SELECT id
                  FROM chat_messages
                  WHERE user_id = ?
                  ORDER BY sent_at DESC, id DESC
                  LIMIT ?
              ) AS latest_rows
          )
    ");
    $stmt->execute([$userId, $userId, $maxMessages]);
}

function handleGetChat()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Ошибка запроса.']);
        return;
    }

    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE user_id = ? ORDER BY sent_at DESC LIMIT 50");
    $stmt->execute([$userId]);
    $messages = array_reverse($stmt->fetchAll());

    echo json_encode(['success' => true, 'messages' => $messages]);
}

// Очистка истории чата
function handleClearChat()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Ошибка запроса.']);
        return;
    }

    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM chat_messages WHERE user_id = ?");
    $stmt->execute([$userId]);

    echo json_encode(['success' => true, 'message' => 'Операция выполнена.']);
}

function buildRatingsUsers()
{
    $pdo = getDBConnection();
    $users = $pdo->query("SELECT * FROM users WHERE is_blocked = FALSE AND role IN ('seeker','recruiter') ORDER BY created_at DESC")->fetchAll();

    $result = [];
    foreach ($users as $user) {
        $stmt = $pdo->prepare("SELECT * FROM user_skills WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $skills = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM user_experience WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $experience = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM user_education WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $education = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $certificates = $stmt->fetchAll();

        $pointsData = calculateUserPoints($user);
        $user['skills'] = $skills;
        $user['experience'] = $experience;
        $user['education'] = $education;
        $user['certificates'] = $certificates;
        $user['points'] = (int) ($pointsData['points'] ?? 0);
        $user['avatar'] = $user['avatar'] ?? getAvatarUrl($user['name']);
        $user = tfDecorateUserCountry($user);
        $user['experience_months'] = 0;
        foreach ($experience as $expRow) {
            $startRaw = trim((string) ($expRow['start_date'] ?? ''));
            if ($startRaw === '') {
                continue;
            }
            try {
                $startDate = new DateTime($startRaw);
                $endRaw = trim((string) ($expRow['end_date'] ?? ''));
                $endDate = $endRaw !== '' ? new DateTime($endRaw) : new DateTime();
                $diff = $startDate->diff($endDate);
                $user['experience_months'] += ($diff->y * 12) + $diff->m;
            } catch (Throwable $e) {
                continue;
            }
        }

        $result[] = array_merge($user, $pointsData);
    }

    usort($result, function ($a, $b) {
        return $b['points'] <=> $a['points'];
    });

    return $result;
}

// Создание вакансии
function handleCreateVacancy()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        echo json_encode(['success' => false, 'message' => t('admin_msg_invalid_data', 'Некорректные данные')]);
        return;
    }

    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => t('admin_msg_unauthorized', 'Пользователь не авторизован')]);
        return;
    }

    $pdo = getDBConnection();
    $roleStmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $roleStmt->execute([$userId]);
    $role = $roleStmt->fetch()['role'] ?? '';
    if (!in_array($role, ['admin', 'recruiter'], true)) {
        echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
        return;
    }
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO vacancies (title, company, location, type, salary_min, salary_max, salary_currency, description, company_description, verified, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE, ?)");
        $stmt->execute([
            $data['title'],
            $data['company'],
            $data['location'],
            $data['type'] ?? 'remote',
            $data['salary']['min'] ?? 0,
            $data['salary']['max'] ?? null,
            $data['salary']['currency'] ?? 'TJS',
            $data['description'],
            $data['companyDescription'] ?? '',
            $userId
        ]);
        $vacancyId = $pdo->lastInsertId();

        if (!empty($data['skills'])) {
            foreach ($data['skills'] as $skill) {
                $stmt = $pdo->prepare("INSERT INTO vacancy_skills (vacancy_id, skill_name) VALUES (?, ?)");
                $stmt->execute([$vacancyId, $skill]);
            }
        }

        if (!empty($data['requirements'])) {
            foreach ($data['requirements'] as $requirement) {
                $stmt = $pdo->prepare("INSERT INTO vacancy_requirements (vacancy_id, requirement_text) VALUES (?, ?)");
                $stmt->execute([$vacancyId, $requirement]);
            }
        }

        if (!empty($data['pluses'])) {
            foreach ($data['pluses'] as $plus) {
                $stmt = $pdo->prepare("INSERT INTO vacancy_pluses (vacancy_id, plus_text) VALUES (?, ?)");
                $stmt->execute([$vacancyId, $plus]);
            }
        }

        if (!empty($data['responsibilities'])) {
            foreach ($data['responsibilities'] as $responsibility) {
                $stmt = $pdo->prepare("INSERT INTO vacancy_responsibilities (vacancy_id, responsibility_text) VALUES (?, ?)");
                $stmt->execute([$vacancyId, $responsibility]);
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Вакансия успешно создана!', 'vacancyId' => $vacancyId]);

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}
function handleApplyToVacancy()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST")
        return;

    $respond = static function (array $payload): void {
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    };

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['vacancyId'])) {
        $respond(['success' => false, 'message' => 'Ошибка запроса.']);
        return;
    }

    $userId = (int) ($_SESSION['user_id'] ?? 0);
    $vacancyId = (int) ($data['vacancyId'] ?? 0);
    $currentRole = (string) (($GLOBALS['user']['role'] ?? '') ?: '');
    if ($userId <= 0) {
        $respond(['success' => false, 'message' => 'Ошибка запроса.']);
        return;
    }
    if ($currentRole !== 'seeker') {
        $respond(['success' => false, 'message' => 'Отклик доступен только соискателям.']);
        return;
    }
    if ($vacancyId <= 0) {
        $respond(['success' => false, 'message' => 'Ошибка запроса.']);
        return;
    }

    $pdo = getDBConnection();
    ensureVacancyChatTables($pdo);
    try {
        $stmt = $pdo->prepare('SELECT id, title, company, owner_id, verified FROM vacancies WHERE id = ? LIMIT 1');
        $stmt->execute([$vacancyId]);
        $vacancy = $stmt->fetch();
        if (!$vacancy) {
            $respond(['success' => false, 'message' => 'Ошибка запроса.']);
            return;
        }
        if ((int) ($vacancy['verified'] ?? 1) !== 1) {
            $respond(['success' => false, 'message' => 'Вакансия недоступна для отклика.']);
            return;
        }
        if ((int) ($vacancy['owner_id'] ?? 0) === $userId) {
            $respond(['success' => false, 'message' => 'Нельзя откликаться на собственную вакансию.']);
            return;
        }

        $stmt = $pdo->prepare('SELECT id FROM user_applications WHERE user_id = ? AND vacancy_id = ?');
        $stmt->execute([$userId, $vacancyId]);
        if ($stmt->fetch()) {
            $respond(['success' => false, 'message' => 'Ошибка запроса.']);
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO user_applications (user_id, vacancy_id, status, applied_at) VALUES (?, ?, 'applied', NOW())");
        $stmt->execute([$userId, $vacancyId]);
        $applicationId = (int) $pdo->lastInsertId();

        $vacancyTitle = (string) ($vacancy['title'] ?? '');
        $vacancyCompany = (string) ($vacancy['company'] ?? '');
        tfAddNotification(
            $pdo,
            $userId,
            "Вы отправили отклик на вакансию \"{$vacancyTitle}\" в {$vacancyCompany}."
        );

        $stmt = $pdo->prepare("INSERT INTO user_activities (user_id, activity_type, activity_text) VALUES (?, 'application', ?)");
        $stmt->execute([$userId, "Отклик на вакансию {$vacancyTitle} в {$vacancyCompany}"]);

        $respond(['success' => true, 'message' => 'Операция выполнена.', 'application_id' => $applicationId]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleVacancyChatGet()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    $appId = (int) ($_GET['app_id'] ?? 0);
    if ($appId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Не авторизован']);
        return;
    }

    $pdo = getDBConnection();
    ensureVacancyChatTables($pdo);
    $stmt = $pdo->prepare("
        SELECT ua.*, v.owner_id, v.title as vacancy_title, v.company
        FROM user_applications ua
        JOIN vacancies v ON ua.vacancy_id = v.id
        WHERE ua.id = ?
    ");
    $stmt->execute([$appId]);
    $app = $stmt->fetch();
    if (!$app) {
        echo json_encode(['success' => false, 'message' => 'Заявка не найдена']);
        return;
    }
    if ($userId !== (int) $app['user_id'] && $userId !== (int) ($app['owner_id'] ?? 0)) {
        echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
        return;
    }

    $stmt = $pdo->prepare("
        SELECT c.id, c.sender_id, u.name as sender_name, c.message_text, c.created_at
        FROM vacancy_chats c
        JOIN users u ON u.id = c.sender_id
        WHERE c.application_id = ?
        ORDER BY c.created_at ASC
        LIMIT 500
    ");
    $stmt->execute([$appId]);
    $messages = $stmt->fetchAll();
    echo json_encode(['success' => true, 'messages' => $messages]);
}

function handleVacancyChatSend()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $missing = tfValidateRequiredFields($data, ['app_id', 'message']);
    if (!empty($missing)) {
        echo json_encode(['success' => false, 'message' => 'Missing fields: ' . implode(', ', $missing)]);
        return;
    }

    $appId = (int) ($data['app_id'] ?? 0);
    $message = trim((string) ($data['message'] ?? ''));
    if ($appId <= 0 || $message === '' || mb_strlen($message) > 1000) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Не авторизован']);
        return;
    }

    $pdo = getDBConnection();
    ensureVacancyChatTables($pdo);
    $stmt = $pdo->prepare("
        SELECT ua.*, v.owner_id
        FROM user_applications ua
        JOIN vacancies v ON ua.vacancy_id = v.id
        WHERE ua.id = ?
    ");
    $stmt->execute([$appId]);
    $app = $stmt->fetch();
    if (!$app) {
        echo json_encode(['success' => false, 'message' => 'Заявка не найдена']);
        return;
    }
    $isAllowed = $userId === (int) $app['user_id'] || $userId === (int) ($app['owner_id'] ?? 0);
    if (!$isAllowed) {
        echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
        return;
    }

    $stmt = $pdo->prepare("INSERT INTO vacancy_chats (application_id, sender_id, message_text) VALUES (?, ?, ?)");
    $stmt->execute([$appId, $userId, $message]);
    echo json_encode(['success' => true]);
}

function handleVacancyDocumentsGet()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    $appId = (int) ($_GET['app_id'] ?? 0);
    if ($appId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Не авторизован']);
        return;
    }

    $pdo = getDBConnection();
    ensureVacancyChatTables($pdo);
    $stmt = $pdo->prepare("
        SELECT ua.*, v.owner_id
        FROM user_applications ua
        JOIN vacancies v ON ua.vacancy_id = v.id
        WHERE ua.id = ?
    ");
    $stmt->execute([$appId]);
    $app = $stmt->fetch();
    if (!$app) {
        echo json_encode(['success' => false, 'message' => 'Заявка не найдена']);
        return;
    }
    if ($userId !== (int) $app['user_id'] && $userId !== (int) ($app['owner_id'] ?? 0)) {
        echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
        return;
    }

    $stmt = $pdo->prepare("
        SELECT id, uploader_id, original_name, file_path, mime_type, size_bytes, created_at
        FROM vacancy_documents
        WHERE application_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$appId]);
    $docs = $stmt->fetchAll();
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    foreach ($docs as &$doc) {
        $doc['url'] = ($base ? $base : '') . '/' . ltrim($doc['file_path'], '/');
    }
    unset($doc);
    echo json_encode(['success' => true, 'documents' => $docs]);
}

function tfNormalizeUploadedOriginalName(string $name, int $maxLen = 180): string
{
    $name = trim(str_replace(["\0", "\r", "\n"], '', $name));
    $name = basename($name);
    $name = preg_replace('/[^\p{L}\p{N}\.\-_ ]/u', '_', $name) ?? '';
    $name = trim($name, '. _-');
    if ($name === '') {
        $name = 'file';
    }
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($name, 'UTF-8') > $maxLen) {
            $name = mb_substr($name, 0, $maxLen, 'UTF-8');
        }
    } elseif (strlen($name) > $maxLen) {
        $name = substr($name, 0, $maxLen);
    }
    return $name;
}

function tfDetectUploadedMime(string $tmpPath): string
{
    if ($tmpPath === '' || !is_file($tmpPath)) {
        return '';
    }
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = (string) finfo_file($finfo, $tmpPath);
            finfo_close($finfo);
            if ($mime !== '') {
                return strtolower(trim($mime));
            }
        }
    }
    return '';
}

function tfValidateUploadedFile(array $file, array $allowedByExt, int $maxBytes, array $options = []): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'message' => 'Файл не загружен'];
    }
    $tmpPath = (string) ($file['tmp_name'] ?? '');
    if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
        return ['ok' => false, 'message' => 'Некорректный источник файла'];
    }

    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0) {
        return ['ok' => false, 'message' => 'Пустой файл недопустим'];
    }
    if ($size > $maxBytes) {
        return ['ok' => false, 'message' => 'Файл слишком большой'];
    }

    $originalName = tfNormalizeUploadedOriginalName((string) ($file['name'] ?? ''));
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if ($ext === '' || !isset($allowedByExt[$ext])) {
        return ['ok' => false, 'message' => 'Недопустимое расширение файла'];
    }

    $mime = tfDetectUploadedMime($tmpPath);
    $allowedMimes = array_map(static fn($value) => strtolower((string) $value), (array) ($allowedByExt[$ext] ?? []));
    if ($mime === '' || !in_array($mime, $allowedMimes, true)) {
        return ['ok' => false, 'message' => 'Недопустимый MIME-тип файла'];
    }

    if (!empty($options['require_image'])) {
        $imageType = function_exists('exif_imagetype') ? @exif_imagetype($tmpPath) : false;
        $allowedImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
        if ($imageType === false || !in_array($imageType, $allowedImageTypes, true)) {
            return ['ok' => false, 'message' => 'Файл не является допустимым изображением'];
        }
    }

    return [
        'ok' => true,
        'mime' => $mime,
        'ext' => $ext,
        'size' => $size,
        'original_name' => $originalName,
        'tmp_name' => $tmpPath,
    ];
}

function tfConsumeRateLimit(string $bucket, int $limit, int $windowSeconds): array
{
    if ($limit < 1) {
        return ['ok' => true, 'remaining' => 0, 'retry_after' => 0];
    }
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }
    if (!isset($_SESSION['tf_rate_limits']) || !is_array($_SESSION['tf_rate_limits'])) {
        $_SESSION['tf_rate_limits'] = [];
    }

    $now = time();
    $entries = array_values(array_filter(
        (array) ($_SESSION['tf_rate_limits'][$bucket] ?? []),
        static fn($ts) => is_int($ts) && $ts > ($now - $windowSeconds)
    ));

    if (count($entries) >= $limit) {
        $oldest = (int) ($entries[0] ?? $now);
        $retryAfter = max(1, $windowSeconds - ($now - $oldest));
        $_SESSION['tf_rate_limits'][$bucket] = $entries;
        return ['ok' => false, 'remaining' => 0, 'retry_after' => $retryAfter];
    }

    $entries[] = $now;
    $_SESSION['tf_rate_limits'][$bucket] = $entries;
    return ['ok' => true, 'remaining' => max(0, $limit - count($entries)), 'retry_after' => 0];
}

function handleVacancyDocumentUpload()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $appId = (int) ($_POST['app_id'] ?? 0);
    if ($appId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Не авторизован']);
        return;
    }
    if (empty($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Файл не загружен']);
        return;
    }

    $pdo = getDBConnection();
    ensureVacancyChatTables($pdo);
    $stmt = $pdo->prepare("
        SELECT ua.*, v.owner_id
        FROM user_applications ua
        JOIN vacancies v ON ua.vacancy_id = v.id
        WHERE ua.id = ?
    ");
    $stmt->execute([$appId]);
    $app = $stmt->fetch();
    if (!$app) {
        echo json_encode(['success' => false, 'message' => 'Заявка не найдена']);
        return;
    }
    if ($userId !== (int) $app['user_id'] && $userId !== (int) ($app['owner_id'] ?? 0)) {
        echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
        return;
    }

    $file = $_FILES['document'];
    $validation = tfValidateUploadedFile($file, [
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
    ], 10 * 1024 * 1024);
    if (empty($validation['ok'])) {
        echo json_encode(['success' => false, 'message' => (string) ($validation['message'] ?? 'Недопустимый файл')]);
        return;
    }
    $file['name'] = $validation['original_name'];
    $file['type'] = $validation['mime'];
    $maxBytes = 10 * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        echo json_encode(['success' => false, 'message' => 'Файл слишком большой']);
        return;
    }

    $allowedExt = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        echo json_encode(['success' => false, 'message' => 'Недопустимый формат']);
        return;
    }

    $uploadsDir = __DIR__ . '/uploads/vacancy_docs';
    if (!is_dir($uploadsDir)) {
        @mkdir($uploadsDir, 0777, true);
    }
    $rand = bin2hex(random_bytes(6));
    $fileName = 'doc_' . (int) $appId . '_' . time() . '_' . $rand . '.' . $ext;
    $targetPath = $uploadsDir . '/' . $fileName;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo json_encode(['success' => false, 'message' => 'Не удалось сохранить файл']);
        return;
    }

    $relative = 'uploads/vacancy_docs/' . $fileName;
    $stmt = $pdo->prepare("INSERT INTO vacancy_documents (application_id, uploader_id, file_path, original_name, mime_type, size_bytes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$appId, $userId, $relative, $file['name'], $file['type'] ?? '', (int) $file['size']]);
    echo json_encode(['success' => true]);
}

function handleVacancyEmploymentStatus()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $appId = (int) ($data['app_id'] ?? 0);
    $status = $data['status'] ?? '';
    if ($appId <= 0 || !in_array($status, ['successful', 'unsuccessful', 'pending'], true)) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Не авторизован']);
        return;
    }

    $pdo = getDBConnection();
    ensureVacancyChatTables($pdo);
    $stmt = $pdo->prepare("
        SELECT ua.*, v.owner_id, v.title, v.company
        FROM user_applications ua
        JOIN vacancies v ON ua.vacancy_id = v.id
        WHERE ua.id = ?
    ");
    $stmt->execute([$appId]);
    $app = $stmt->fetch();
    if (!$app) {
        echo json_encode(['success' => false, 'message' => 'Заявка не найдена']);
        return;
    }
    if ($userId !== (int) ($app['owner_id'] ?? 0)) {
        echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
        return;
    }
    if ($status === 'pending') {
        $stmt = $pdo->prepare("UPDATE user_applications SET employment_status = 'pending', employment_updated_at = NOW(), status = 'applied' WHERE id = ?");
        $stmt->execute([$appId]);
        $stmt = $pdo->prepare("UPDATE vacancies SET verified = TRUE WHERE id = ?");
        $stmt->execute([(int) $app['vacancy_id']]);
        echo json_encode(['success' => true, 'message' => 'Вакансия снова активна']);
        return;
    }

    $newAppStatus = $status === 'successful' ? 'offer' : 'rejected';
    $stmt = $pdo->prepare("UPDATE user_applications SET employment_status = ?, employment_updated_at = NOW(), status = ? WHERE id = ?");
    $stmt->execute([$status, $newAppStatus, $appId]);

    if ($status === 'successful') {
        // Закрываем вакансию (скрываем из списка) после успешного оффера.
        $stmt = $pdo->prepare("UPDATE vacancies SET verified = FALSE WHERE id = ?");
        $stmt->execute([(int) $app['vacancy_id']]);
    } else {
        // После отказа вакансия должна оставаться активной.
        $stmt = $pdo->prepare("UPDATE vacancies SET verified = TRUE WHERE id = ?");
        $stmt->execute([(int) $app['vacancy_id']]);
    }

    $msg = $status === 'successful'
        ? "Вас приняли на вакансию \"{$app['title']}\" в {$app['company']}"
        : "По вакансии \"{$app['title']}\" в {$app['company']} принято решение: отказ";
    tfAddNotification($pdo, (int) $app['user_id'], $msg);

    echo json_encode(['success' => true]);
}

function handlePlatformReview()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Не авторизован']);
        return;
    }
    $rating = (int) ($data['rating'] ?? 0);
    $comment = trim((string) ($data['comment'] ?? ''));
    if ($rating < 1 || $rating > 5 || mb_strlen($comment) > 1000) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    ensureVacancyChatTables($pdo);
    $stmt = $pdo->prepare("INSERT INTO platform_reviews (user_id, rating, comment) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment), created_at = NOW()");
    $stmt->execute([$userId, $rating, $comment]);
    echo json_encode(['success' => true]);
}

// Обработчик отклика на вакансию
function handleGetTopUsers()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;

    $limit = $_GET['limit'] ?? 10;
    $topUsers = getTopUsers($limit);
    echo json_encode(['success' => true, 'users' => $topUsers]);
}

// Обработчик добавления опыта
function handleAddExperience()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['position']) || !isset($data['company'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("INSERT INTO user_experience (user_id, position, company, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $data['position'],
            $data['company'],
            tfNormalizeDateField($data['start'] ?? '', false),
            tfNormalizeDateField($data['end'] ?? '', true),
            $data['description'] ?? ''
        ]);
        echo json_encode(['success' => true, 'message' => 'Опыт работы добавлен']);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

// Обработчик добавления образования
function handleUpdateExperience()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['id']) || !isset($data['position']) || !isset($data['company'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }
    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("UPDATE user_experience SET position = ?, company = ?, start_date = ?, end_date = ?, description = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([
            $data['position'],
            $data['company'],
            tfNormalizeDateField($data['start'] ?? '', false),
            tfNormalizeDateField($data['end'] ?? '', true),
            $data['description'] ?? '',
            (int) $data['id'],
            (int) $userId
        ]);
        echo json_encode(['success' => true, 'message' => 'Опыт работы обновлен']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleAddEducation()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['degree']) || !isset($data['institution'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("INSERT INTO user_education (user_id, degree, institution, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $data['degree'],
            $data['institution'],
            tfNormalizeDateField($data['start'] ?? '', false),
            tfNormalizeDateField($data['end'] ?? '', true),
            $data['description'] ?? ''
        ]);
        echo json_encode(['success' => true, 'message' => 'Образование добавлено']);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

// Обработчик добавления навыка
function handleAddSkill()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['skillName'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $skillName = trim((string) $data['skillName']);
    if ($skillName === '') {
        echo json_encode(['success' => false, 'message' => 'Укажите название навыка']);
        return;
    }
    if (!tfIsItSkill($skillName)) {
        echo json_encode(['success' => false, 'message' => 'Разрешены только IT-навыки']);
        return;
    }

    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("SELECT id FROM user_skills WHERE user_id = ? AND skill_name = ?");
        $stmt->execute([$userId, $skillName]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Навык уже существует']);
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO user_skills (user_id, skill_name, skill_level, category, is_verified) VALUES (?, ?, 0, ?, 0)");
        $stmt->execute([
            $userId,
            $skillName,
            $data['category'] ?? 'technical'
        ]);
        echo json_encode(['success' => true, 'message' => 'Навык добавлен']);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function tfHostMatchesDomain($host, $domain): bool
{
    $host = strtolower(trim((string) $host));
    $domain = strtolower(trim((string) $domain));
    if ($host === '' || $domain === '') {
        return false;
    }
    if ($host === $domain) {
        return true;
    }
    return str_ends_with($host, '.' . $domain);
}

function tfSanitizeUrlInput($rawValue): string
{
    $value = trim((string) $rawValue);
    if ($value === '') {
        return '';
    }
    $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value) ?? $value;
    $value = preg_replace('/[\x{200B}\x{200C}\x{200D}\x{2060}\x{FEFF}]/u', '', $value) ?? $value;
    return trim($value);
}

function tfIsValidUrlHost($host): bool
{
    $host = strtolower(trim((string) $host));
    if ($host === '') {
        return false;
    }
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return true;
    }
    if ($host === 'localhost') {
        return true;
    }
    return (bool) preg_match('/^(?:[a-z0-9-]+\.)+[a-z0-9-]{2,}$/i', $host);
}

// Обработчик обновления профиля
function tfNormalizeProfileLink($rawValue, $type = 'generic')
{
    $value = tfSanitizeUrlInput($rawValue);
    if ($value === '') {
        return ['ok' => true, 'value' => ''];
    }

    if ($type === 'telegram') {
        if (preg_match('/^@?[A-Za-z0-9_]{3,}$/', $value)) {
            $value = ltrim($value, '@');
            $value = 'https://t.me/' . $value;
        } elseif (stripos($value, 't.me/') === 0) {
            $value = 'https://' . $value;
        }
    }

    if (!preg_match('#^[a-z][a-z0-9+.-]*://#i', $value) && preg_match('/^[A-Za-z0-9.-]+\.[A-Za-z]{2,}([\/?#:].*)?$/', $value)) {
        $value = 'https://' . $value;
    }

    if (stripos($value, 'www.') === 0) {
        $value = 'https://' . $value;
    }

    $parts = parse_url($value);
    if (!is_array($parts)) {
        return ['ok' => false, 'message' => 'Некорректная ссылка'];
    }
    $scheme = strtolower((string) ($parts['scheme'] ?? ''));
    $hostRaw = strtolower((string) ($parts['host'] ?? ''));
    $host = $hostRaw;
    if (function_exists('idn_to_ascii') && $hostRaw !== '') {
        $asciiHost = idn_to_ascii($hostRaw, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        if (is_string($asciiHost) && $asciiHost !== '') {
            $host = strtolower($asciiHost);
        }
    }
    if (!in_array($scheme, ['http', 'https'], true) || $host === '') {
        return ['ok' => false, 'message' => 'Некорректный URL.'];
    }
    if (!tfIsValidUrlHost($host)) {
        return ['ok' => false, 'message' => 'Некорректный домен.'];
    }

    if (mb_strlen($value) > 500) {
        return ['ok' => false, 'message' => 'Ссылка слишком длинная.'];
    }

    $domainRules = [
        'linkedin' => 'linkedin.com',
        'github' => 'github.com',
        'telegram' => 't.me',
    ];
    if (isset($domainRules[$type]) && !tfHostMatchesDomain($host, (string) $domainRules[$type])) {
        return ['ok' => false, 'message' => 'Ссылка не соответствует выбранному сервису.'];
    }

    return ['ok' => true, 'value' => $value];
}

function tfNormalizeUploadedAssetPath($rawValue)
{
    $value = trim((string) $rawValue);
    if ($value === '') {
        return '';
    }

    $path = (string) (parse_url($value, PHP_URL_PATH) ?? '');
    if ($path === '') {
        $path = $value;
    }
    $path = str_replace('\\', '/', $path);
    $pos = strpos($path, '/uploads/');
    if ($pos !== false) {
        $normalized = ltrim(substr($path, $pos + 1), '/');
        if ($normalized !== '') {
            return $normalized;
        }
    }
    if (strpos($value, '/uploads/') === 0) {
        return ltrim($value, '/');
    }
    if (strpos($value, 'uploads/') === 0) {
        return $value;
    }
    return $value;
}

function tfNormalizeDateField($rawValue, $emptyAsNull = false)
{
    $value = trim((string) $rawValue);
    if ($value === '') {
        return $emptyAsNull ? null : '';
    }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return $value;
    }
    if (preg_match('/^\d{4}-\d{2}$/', $value)) {
        return $value . '-01';
    }
    if (preg_match('/^\d{4}$/', $value)) {
        return $value . '-01-01';
    }
    $timestamp = strtotime($value);
    if ($timestamp !== false) {
        return date('Y-m-d', $timestamp);
    }
    return $emptyAsNull ? null : '';
}

function tfNormalizeAvatarLink($rawValue)
{
    $value = tfSanitizeUrlInput($rawValue);
    if ($value === '') {
        return ['ok' => true, 'value' => ''];
    }
    $normalizedUpload = tfNormalizeUploadedAssetPath($value);
    if (strpos($normalizedUpload, 'uploads/') === 0) {
        return ['ok' => true, 'value' => $normalizedUpload];
    }
    return tfNormalizeProfileLink($value);
}

function tfDefaultCvCustomizationState()
{
    return [
        'accent' => '#6366f1',
        'asideWidth' => 32,
        'gap' => 16,
        'theme' => 1,
        'layout' => 1,
        'fontScale' => 100,
        'cardRadius' => 14,
        'sectionStyle' => 'soft',
        'order' => [
            'aside' => ['social', 'skills', 'stats'],
            'main' => ['about', 'experience', 'education', 'portfolio', 'certificates']
        ]
    ];
}

function tfNormalizeCvCustomizationState($rawState)
{
    $defaults = tfDefaultCvCustomizationState();
    $state = is_array($rawState) ? $rawState : [];

    $accent = strtolower(trim((string) ($state['accent'] ?? $defaults['accent'])));
    if (!preg_match('/^#[0-9a-f]{6}$/', $accent)) {
        $accent = $defaults['accent'];
    }

    $asideWidth = (int) ($state['asideWidth'] ?? $defaults['asideWidth']);
    $gap = (int) ($state['gap'] ?? $defaults['gap']);
    $theme = (int) ($state['theme'] ?? $defaults['theme']);
    $layout = (int) ($state['layout'] ?? $defaults['layout']);
    $fontScale = (int) ($state['fontScale'] ?? $defaults['fontScale']);
    $cardRadius = (int) ($state['cardRadius'] ?? $defaults['cardRadius']);
    $sectionStyle = strtolower(trim((string) ($state['sectionStyle'] ?? $defaults['sectionStyle'])));

    $asideWidth = max(24, min(42, $asideWidth));
    $gap = max(10, min(24, $gap));
    $theme = max(1, min(5, $theme));
    $layout = max(1, min(5, $layout));
    $fontScale = max(90, min(120, $fontScale));
    $cardRadius = max(8, min(24, $cardRadius));
    if (!in_array($sectionStyle, ['soft', 'flat', 'outline'], true)) {
        $sectionStyle = $defaults['sectionStyle'];
    }

    $allowedAside = $defaults['order']['aside'];
    $allowedMain = $defaults['order']['main'];
    $order = ['aside' => [], 'main' => []];
    $rawOrder = $state['order'] ?? [];

    foreach (['aside', 'main'] as $key) {
        $source = [];
        if (is_array($rawOrder) && isset($rawOrder[$key]) && is_array($rawOrder[$key])) {
            $source = $rawOrder[$key];
        }
        $allowed = $key === 'aside' ? $allowedAside : $allowedMain;
        $seen = [];
        foreach ($source as $item) {
            $item = (string) $item;
            if (in_array($item, $allowed, true) && !isset($seen[$item])) {
                $seen[$item] = true;
                $order[$key][] = $item;
            }
        }
        foreach ($allowed as $fallbackItem) {
            if (!in_array($fallbackItem, $order[$key], true)) {
                $order[$key][] = $fallbackItem;
            }
        }
    }

    return [
        'accent' => $accent,
        'asideWidth' => $asideWidth,
        'gap' => $gap,
        'theme' => $theme,
        'layout' => $layout,
        'fontScale' => $fontScale,
        'cardRadius' => $cardRadius,
        'sectionStyle' => $sectionStyle,
        'order' => $order
    ];
}

function handleSaveCvCustomization()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $currentUserId = (int) ($_SESSION['user_id'] ?? 0);
    if ($currentUserId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $profileId = (int) ($data['profile_id'] ?? 0);
    if ($profileId <= 0 || $profileId !== $currentUserId) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Недостаточно прав']);
        return;
    }

    $normalized = tfNormalizeCvCustomizationState($data['settings'] ?? []);

    $pdo = getDBConnection();
    ensureUserCvCustomizationSchema($pdo);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO user_cv_customizations (user_id, settings_json)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE settings_json = VALUES(settings_json), updated_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([
            $currentUserId,
            json_encode($normalized, JSON_UNESCAPED_UNICODE)
        ]);
        echo json_encode(['success' => true, 'settings' => $normalized]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleResetCvCustomization()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $currentUserId = (int) ($_SESSION['user_id'] ?? 0);
    if ($currentUserId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $profileId = (int) ($data['profile_id'] ?? 0);
    if ($profileId <= 0 || $profileId !== $currentUserId) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Недостаточно прав']);
        return;
    }

    $pdo = getDBConnection();
    ensureUserCvCustomizationSchema($pdo);

    try {
        $stmt = $pdo->prepare("DELETE FROM user_cv_customizations WHERE user_id = ?");
        $stmt->execute([$currentUserId]);
        echo json_encode(['success' => true, 'settings' => tfDefaultCvCustomizationState()]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleUpdateProfile()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $name = trim((string) ($data['name'] ?? ''));
    $title = trim((string) ($data['title'] ?? ''));
    $location = trim((string) ($data['location'] ?? ''));
    $bio = trim((string) ($data['bio'] ?? ''));
    $countryCode = strtoupper(trim((string) ($data['countryCode'] ?? '')));
    $countryName = trim((string) ($data['countryName'] ?? ''));

    if ($name === '' || $title === '' || $location === '') {
        echo json_encode(['success' => false, 'message' => 'Заполните обязательные поля']);
        return;
    }
    if (mb_strlen($name) < 2 || mb_strlen($name) > 120) {
        echo json_encode(['success' => false, 'message' => 'Имя должно быть от 2 до 120 символов']);
        return;
    }
    if (mb_strlen($title) > 120 || mb_strlen($location) > 120) {
        echo json_encode(['success' => false, 'message' => 'Слишком длинное значение поля']);
        return;
    }
    if (mb_strlen($bio) > 2000) {
        echo json_encode(['success' => false, 'message' => 'Описание слишком длинное']);
        return;
    }

    $avatarResult = tfNormalizeAvatarLink($data['avatar'] ?? '');
    if (empty($avatarResult['ok'])) {
        echo json_encode([
            'success' => false,
            'message' => function_exists('t') ? t('profile_invalid_url', 'Некорректная ссылка') : 'Некорректная ссылка',
            'field' => 'avatar'
        ]);
        return;
    }
    $linkedinResult = tfNormalizeProfileLink($data['social_linkedin'] ?? '', 'linkedin');
    if (empty($linkedinResult['ok'])) {
        echo json_encode([
            'success' => false,
            'message' => function_exists('t') ? t('profile_invalid_linkedin', 'Укажите корректную ссылку LinkedIn') : 'Укажите корректную ссылку LinkedIn',
            'field' => 'social_linkedin'
        ]);
        return;
    }
    $githubResult = tfNormalizeProfileLink($data['social_github'] ?? '', 'github');
    if (empty($githubResult['ok'])) {
        echo json_encode([
            'success' => false,
            'message' => function_exists('t') ? t('profile_invalid_github', 'Укажите корректную ссылку GitHub') : 'Укажите корректную ссылку GitHub',
            'field' => 'social_github'
        ]);
        return;
    }
    $telegramResult = tfNormalizeProfileLink($data['social_telegram'] ?? '', 'telegram');
    if (empty($telegramResult['ok'])) {
        echo json_encode([
            'success' => false,
            'message' => function_exists('t') ? t('profile_invalid_telegram', 'Укажите корректную ссылку Telegram') : 'Укажите корректную ссылку Telegram',
            'field' => 'social_telegram'
        ]);
        return;
    }
    $websiteResult = tfNormalizeProfileLink($data['social_website'] ?? '', 'generic');
    if (empty($websiteResult['ok'])) {
        echo json_encode([
            'success' => false,
            'message' => function_exists('t') ? t('profile_invalid_website', 'Укажите корректную ссылку сайта') : 'Укажите корректную ссылку сайта',
            'field' => 'social_website'
        ]);
        return;
    }

    $pdo = getDBConnection();
    ensureUserProfileSchema($pdo);
    $resolvedCountry = [];
    if ($countryCode !== '') {
        $resolvedCountry = tfResolveCountryByCode($countryCode);
    }
    if (empty($resolvedCountry) && $countryName !== '') {
        $resolvedCountry = tfResolveCountryByText($countryName);
    }
    if (empty($resolvedCountry) && $location !== '') {
        $resolvedCountry = tfResolveCountryByText($location);
    }
    if (!empty($resolvedCountry)) {
        $countryCode = (string) ($resolvedCountry['country_code'] ?? $countryCode);
        $countryName = (string) ($resolvedCountry['country_name'] ?? $countryName);
    }
    try {
        $sql = "UPDATE users SET
            name = ?,
            title = ?,
            location = ?,
            bio = ?,
            avatar = ?,
            social_linkedin = ?,
            social_github = ?,
            social_telegram = ?,
            social_website = ?,
            country_code = ?,
            country_name = ?
            WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $name,
            $title,
            $location,
            $bio,
            $avatarResult['value'] ?? '',
            $linkedinResult['value'] ?? '',
            $githubResult['value'] ?? '',
            $telegramResult['value'] ?? '',
            $websiteResult['value'] ?? '',
            $countryCode,
            $countryName,
            (int) $userId
        ]);
        echo json_encode(['success' => true, 'message' => 'Операция выполнена.']);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}
function handleChangePassword()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        echo json_encode(['success' => false, 'message' => t('profile_password_invalid_payload', 'Неверные данные запроса.')]);
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => t('auth_required', 'Требуется авторизация.')]);
        return;
    }

    $current = (string) ($data['current_password'] ?? '');
    $new = (string) ($data['new_password'] ?? '');
    $confirm = (string) ($data['confirm_password'] ?? '');

    if ($current === '' || $new === '' || $confirm === '') {
        echo json_encode(['success' => false, 'message' => t('profile_password_required', 'Заполните все поля пароля.')]);
        return;
    }
    if ($new !== $confirm) {
        echo json_encode(['success' => false, 'message' => t('profile_password_mismatch', 'Пароли не совпадают.')]);
        return;
    }
    if (!isValidPassword($new)) {
        echo json_encode(['success' => false, 'message' => t('profile_password_weak', 'Пароль слишком простой.')]);
        return;
    }

    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([(int) $userId]);
        $row = $stmt->fetch();
        if (!$row || !verifyPassword($current, (string) ($row['password'] ?? ''))) {
            echo json_encode(['success' => false, 'message' => t('profile_password_wrong_current', 'Текущий пароль неверен.')]);
            return;
        }
        $hashedPassword = hashPassword($new);
        $upd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $upd->execute([$hashedPassword, (int) $userId]);
        echo json_encode(['success' => true, 'message' => t('profile_password_updated', 'Пароль обновлен.')]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleGetNotifications()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY notification_time DESC LIMIT 20");
    $stmt->execute([$userId]);
    $notifications = $stmt->fetchAll();
    foreach ($notifications as &$notification) {
        $notification['message'] = normalizeMojibakeText((string) ($notification['message'] ?? ''));
    }
    unset($notification);
    echo json_encode(['success' => true, 'notifications' => $notifications]);
}

// Отметить уведомления как прочитанные
function handleMarkNotificationsRead()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ?");
    $stmt->execute([$userId]);
    $message = function_exists('t') ? t('notif_marked_read') : 'Уведомления отмечены как прочитанные.';
    echo json_encode(['success' => true, 'message' => $message]);
}

// Обработчик создания персонального курса
function handleCreateCustomCourse()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['goal']) || empty($data['skills'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $pdo = getDBConnection();
    ensureAiTables($pdo);
    $roleStmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $roleStmt->execute([$userId]);
    $role = $roleStmt->fetch()['role'] ?? '';
    if ($role !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
        return;
    }
    try {
        $courseTopic = $data['goal'];
        $skills = $data['skills'];
        $lessonCount = 3;
        $miniTestCount = 3;
        $finalExamCount = 30;
        $cacheKey = 'ai_course_' . md5($courseTopic . '|' . implode(',', $skills));

        if (is_array($_SESSION[$cacheKey] ?? null) && isset($_SESSION[$cacheKey]['struct'])) {
            $struct = $_SESSION[$cacheKey]['struct'];
        } else {
            $structure = generateCourseStructure($courseTopic, $skills, $lessonCount);
            if (!$structure['ok']) {
                echo json_encode(['success' => false, 'message' => formatAiErrorMessage('Шаг 1. Генерация структуры курса', $structure)]);
                return;
            }
            $struct = $structure['data'];
            $_SESSION[$cacheKey]['struct'] = $struct;
        }

        $lessonPayloads = [];
        foreach ($struct['lessons'] as $idx => $lesson) {
            $titleL = $lesson['title'];
            $difficulty = $lesson['difficulty'];

            $pkgKey = $cacheKey . '_pkg_' . $idx;
            if (isset($_SESSION[$pkgKey]) && is_array($_SESSION[$pkgKey])) {
                $pkg = $_SESSION[$pkgKey];
            } else {
                $pkgResp = generateLessonPackage($titleL, $difficulty, $courseTopic, $miniTestCount);
                if (!$pkgResp['ok']) {
                    echo json_encode(['success' => false, 'message' => formatAiErrorMessage('Шаг 2/5. Генерация урока и мини-теста', $pkgResp)]);
                    return;
                }
                $pkg = $pkgResp['data'];
                $_SESSION[$pkgKey] = $pkg;
            }

            $lessonPayloads[] = [
                'title' => $titleL,
                'goals' => ["Понять ключевые понятия", "Закрепить знания на практике"],
                'difficulty' => $difficulty,
                'theory' => $pkg['theory_text'],
                'test' => $pkg['mini_test']
            ];
        }

        $examKey = $cacheKey . '_exam';
        if (isset($_SESSION[$examKey]) && is_array($_SESSION[$examKey])) {
            $examData = $_SESSION[$examKey];
        } else {
            $exam = generateFinalExam($struct['course_title'], $struct['lessons'], $finalExamCount);
            if (!$exam['ok']) {
                echo json_encode(['success' => false, 'message' => formatAiErrorMessage('Шаг 7. Финальный экзамен', $exam)]);
                return;
            }
            $examData = $exam['data'];
            $_SESSION[$examKey] = $examData;
        }

        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO courses (title, instructor, description, category, level, image_url, created_at) VALUES (?, 'AI-генератор', ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $struct['course_title'],
            $struct['course_description'],
            'other',
            'Начальный',
            'https://placehold.co/300x200/4f46e5/ffffff?text=Курс'
        ]);
        $courseId = $pdo->lastInsertId();

        foreach ($skills as $skill) {
            $stmt = $pdo->prepare("INSERT INTO course_skills (course_id, skill_name, skill_level) VALUES (?, ?, 0)");
            $stmt->execute([$courseId, $skill]);
        }

        $order = 0;
        foreach ($lessonPayloads as $payload) {
            $stmt = $pdo->prepare("INSERT INTO lessons (course_id, title, type, content, order_num, created_at) VALUES (?, ?, 'article', ?, ?, NOW())");
            $stmt->execute([$courseId, $payload['title'], $payload['theory'], $order++]);
            $lessonId = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO lesson_tests (lesson_id, test_json, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$lessonId, json_encode($payload['test'], JSON_UNESCAPED_UNICODE)]);
        }

        $stmt = $pdo->prepare("INSERT INTO course_exams (course_id, exam_json, time_limit_minutes, pass_percent, shuffle_questions, shuffle_options, created_at) VALUES (?, ?, 45, 70, TRUE, TRUE, NOW())");
        $stmt->execute([$courseId, json_encode($examData, JSON_UNESCAPED_UNICODE)]);

        $pdo->commit();
        tfAddNotification($pdo, (int) $userId, "Создан новый курс: \"{$courseTopic}\"");
        echo json_encode(['success' => true, 'message' => 'Персональный курс создан!', 'courseId' => $courseId]);
    } catch (PDOException $e) {
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleUpdateEducation()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['id']) || !isset($data['degree']) || !isset($data['institution'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }
    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("UPDATE user_education SET degree = ?, institution = ?, start_date = ?, end_date = ?, description = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([
            $data['degree'],
            $data['institution'],
            tfNormalizeDateField($data['start'] ?? '', false),
            tfNormalizeDateField($data['end'] ?? '', true),
            $data['description'] ?? '',
            (int) $data['id'],
            (int) $userId
        ]);
        echo json_encode(['success' => true, 'message' => 'Образование обновлено']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleAddPortfolio()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || trim((string) ($data['title'] ?? '')) === '') {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }
    $pdo = getDBConnection();
    try {
        ensureUserPortfolioSchema($pdo);
        $githubResult = tfNormalizeProfileLink($data['github_url'] ?? '', 'github');
        if (empty($githubResult['ok'])) {
            echo json_encode([
                'success' => false,
                'message' => function_exists('t') ? t('profile_invalid_github', 'Некорректная ссылка GitHub.') : 'Некорректная ссылка GitHub.',
                'field' => 'github_url'
            ]);
            return;
        }
        $stmt = $pdo->prepare("INSERT INTO user_portfolio (user_id, title, category, image_url, github_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            (int) $userId,
            $data['title'],
            $data['category'] ?? '',
            tfNormalizeUploadedAssetPath($data['image_url'] ?? ''),
            $githubResult['value'] ?? ''
        ]);
        echo json_encode(['success' => true, 'message' => 'Проект добавлен']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleUpdatePortfolio()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['id']) || empty($data['title'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }
    $pdo = getDBConnection();
    try {
        ensureUserPortfolioSchema($pdo);
        $githubResult = tfNormalizeProfileLink($data['github_url'] ?? '', 'github');
        if (empty($githubResult['ok'])) {
            echo json_encode([
                'success' => false,
                'message' => function_exists('t') ? t('profile_invalid_github', 'Некорректная ссылка GitHub.') : 'Некорректная ссылка GitHub.',
                'field' => 'github_url'
            ]);
            return;
        }
        $stmt = $pdo->prepare("UPDATE user_portfolio SET title = ?, category = ?, image_url = ?, github_url = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([
            $data['title'],
            $data['category'] ?? '',
            tfNormalizeUploadedAssetPath($data['image_url'] ?? ''),
            $githubResult['value'] ?? '',
            (int) $data['id'],
            (int) $userId
        ]);
        echo json_encode(['success' => true, 'message' => 'Проект обновлен']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleDeleteExperience()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("DELETE FROM user_experience WHERE id = ? AND user_id = ?");
        $stmt->execute([(int) $data['id'], $userId]);
        echo json_encode(['success' => true, 'message' => 'Опыт удален']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleDeleteEducation()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("DELETE FROM user_education WHERE id = ? AND user_id = ?");
        $stmt->execute([(int) $data['id'], $userId]);
        echo json_encode(['success' => true, 'message' => 'Образование удалено']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleDeletePortfolio()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("DELETE FROM user_portfolio WHERE id = ? AND user_id = ?");
        $stmt->execute([(int) $data['id'], $userId]);
        echo json_encode(['success' => true, 'message' => 'Проект удален']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleUpdateSkill()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['skillId'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $fields = [];
    $params = [];
    $skillNameChanged = false;
    if (isset($data['skillName']) && trim((string) $data['skillName']) !== '') {
        $skillName = trim((string) $data['skillName']);
        if (!tfIsItSkill($skillName)) {
            echo json_encode(['success' => false, 'message' => 'Разрешены только IT-навыки']);
            return;
        }
        $fields[] = 'skill_name = ?';
        $params[] = $skillName;
        $fields[] = 'skill_level = 0';
        $fields[] = 'is_verified = 0';
        $skillNameChanged = true;
    }
    if (isset($data['category']) && $data['category'] !== '') {
        $fields[] = 'category = ?';
        $params[] = $data['category'];
    }

    if (empty($fields)) {
        echo json_encode(['success' => false, 'message' => 'Нет данных для обновления']);
        return;
    }

    $pdo = getDBConnection();
    ensureUserSkillsSchema($pdo);
    try {
        $params[] = (int) $data['skillId'];
        $params[] = $userId;
        $sql = "UPDATE user_skills SET " . implode(', ', $fields) . " WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        if ($skillNameChanged) {
            ensureSkillAssessmentSchema($pdo);
            $delProgress = $pdo->prepare("DELETE FROM skill_assessment_progress WHERE user_id = ? AND skill_id = ?");
            $delProgress->execute([(int) $userId, (int) $data['skillId']]);
            $delAttempts = $pdo->prepare("DELETE FROM skill_assessment_attempts WHERE user_id = ? AND skill_id = ?");
            $delAttempts->execute([(int) $userId, (int) $data['skillId']]);
        }
        echo json_encode(['success' => true, 'message' => 'Навык обновлен']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleDeleteSkill()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['skillId'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("DELETE FROM user_skills WHERE id = ? AND user_id = ?");
        $stmt->execute([(int) $data['skillId'], $userId]);
        echo json_encode(['success' => true, 'message' => 'Навык удален']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleVerifySkill()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    echo json_encode([
        'success' => false,
        'message' => 'Верификация навыка сейчас недоступна. Попробуйте позже.'
    ]);
}

function tfSkillAssessmentRoundRules(): array
{
    return [
        1 => ['round' => 1, 'difficulty' => 'easy', 'questions' => 10, 'pass_percent' => 40, 'level_percent' => 40],
        2 => ['round' => 2, 'difficulty' => 'medium', 'questions' => 10, 'pass_percent' => 70, 'level_percent' => 70],
        3 => ['round' => 3, 'difficulty' => 'hard', 'questions' => 10, 'pass_percent' => 100, 'level_percent' => 100],
    ];
}

function tfSkillAssessmentRoundRule(int $round): array
{
    $rules = tfSkillAssessmentRoundRules();
    return $rules[$round] ?? [];
}

function tfSkillAssessmentLevelByRound(int $round): int
{
    $rule = tfSkillAssessmentRoundRule($round);
    return (int) ($rule['level_percent'] ?? 0);
}

function tfSkillAssessmentFetchSkill(PDO $pdo, int $userId, int $skillId): array
{
    $stmt = $pdo->prepare("SELECT id, skill_name, skill_level, is_verified FROM user_skills WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$skillId, $userId]);
    $skill = $stmt->fetch();
    if (!$skill) {
        return ['ok' => false, 'message' => 'Навык не найден.'];
    }
    $skillName = (string) ($skill['skill_name'] ?? '');
    if (!tfIsItSkill($skillName)) {
        return ['ok' => false, 'message' => 'Навык не относится к IT-навыкам.'];
    }
    return ['ok' => true, 'skill' => $skill];
}

function tfSkillAssessmentFetchProgress(PDO $pdo, int $userId, int $skillId): array
{
    ensureSkillAssessmentSchema($pdo);
    $stmt = $pdo->prepare("
        SELECT max_round, max_percent, status, updated_at
        FROM skill_assessment_progress
        WHERE user_id = ? AND skill_id = ?
        LIMIT 1
    ");
    $stmt->execute([$userId, $skillId]);
    $row = $stmt->fetch();
    if (!$row) {
        return [
            'max_round' => 0,
            'max_percent' => 0,
            'status' => 'not_started',
            'updated_at' => null,
        ];
    }
    $maxRound = max(0, min(3, (int) ($row['max_round'] ?? 0)));
    $maxPercent = max(0, min(100, (int) ($row['max_percent'] ?? 0)));
    $status = (string) ($row['status'] ?? 'not_started');
    if (!in_array($status, ['not_started', 'in_progress', 'surrendered', 'completed'], true)) {
        $status = 'in_progress';
    }
    return [
        'max_round' => $maxRound,
        'max_percent' => $maxPercent,
        'status' => $status,
        'updated_at' => $row['updated_at'] ?? null,
    ];
}

function tfSkillAssessmentFetchAttempts(PDO $pdo, int $userId, int $skillId): array
{
    ensureSkillAssessmentSchema($pdo);
    $stmt = $pdo->prepare("
        SELECT round_no, difficulty, score, total_questions, percent, passed, surrendered, attempt_date, created_at
        FROM skill_assessment_attempts
        WHERE user_id = ? AND skill_id = ?
        ORDER BY round_no ASC, id ASC
    ");
    $stmt->execute([$userId, $skillId]);
    $rows = $stmt->fetchAll();
    if (!is_array($rows)) {
        return [];
    }
    $out = [];
    foreach ($rows as $row) {
        $roundNo = (int) ($row['round_no'] ?? 0);
        if ($roundNo < 1 || $roundNo > 3) {
            continue;
        }
        $out[] = [
            'round_no' => $roundNo,
            'difficulty' => (string) ($row['difficulty'] ?? ''),
            'score' => (int) ($row['score'] ?? 0),
            'total_questions' => (int) ($row['total_questions'] ?? 0),
            'percent' => (int) ($row['percent'] ?? 0),
            'passed' => (int) ($row['passed'] ?? 0) === 1,
            'surrendered' => (int) ($row['surrendered'] ?? 0) === 1,
            'attempt_date' => (string) ($row['attempt_date'] ?? ''),
            'created_at' => (string) ($row['created_at'] ?? ''),
        ];
    }
    return $out;
}

function tfSkillAssessmentFindAttemptForRound(array $attempts, int $round): ?array
{
    foreach ($attempts as $attempt) {
        if ((int) ($attempt['round_no'] ?? 0) === $round) {
            return $attempt;
        }
    }
    return null;
}

function tfSkillAssessmentPublicQuiz(array $quiz): array
{
    $questions = [];
    foreach ((array) ($quiz['questions'] ?? []) as $idx => $q) {
        if (!is_array($q)) {
            continue;
        }
        $question = trim((string) ($q['question'] ?? ''));
        $options = array_values(array_filter(array_map(static function ($opt) {
            return trim((string) $opt);
        }, (array) ($q['options'] ?? [])), static function ($opt) {
            return $opt !== '';
        }));
        if ($question === '' || count($options) < 2) {
            continue;
        }
        $questions[] = [
            'id' => $idx + 1,
            'question' => $question,
            'options' => $options,
        ];
    }
    return ['questions' => $questions];
}

function tfSkillAssessmentSessionSetQuiz(int $userId, int $skillId, int $round, array $quiz): void
{
    if (!isset($_SESSION['skill_assessment_quizzes']) || !is_array($_SESSION['skill_assessment_quizzes'])) {
        $_SESSION['skill_assessment_quizzes'] = [];
    }
    if (!isset($_SESSION['skill_assessment_quizzes'][$userId]) || !is_array($_SESSION['skill_assessment_quizzes'][$userId])) {
        $_SESSION['skill_assessment_quizzes'][$userId] = [];
    }
    if (!isset($_SESSION['skill_assessment_quizzes'][$userId][$skillId]) || !is_array($_SESSION['skill_assessment_quizzes'][$userId][$skillId])) {
        $_SESSION['skill_assessment_quizzes'][$userId][$skillId] = [];
    }
    $_SESSION['skill_assessment_quizzes'][$userId][$skillId][$round] = [
        'date' => date('Y-m-d'),
        'quiz' => $quiz,
        'generated_at' => time(),
    ];
}

function tfSkillAssessmentSessionGetQuiz(int $userId, int $skillId, int $round): ?array
{
    $entry = $_SESSION['skill_assessment_quizzes'][$userId][$skillId][$round] ?? null;
    if (!is_array($entry)) {
        return null;
    }
    if (($entry['date'] ?? '') !== date('Y-m-d')) {
        return null;
    }
    $quiz = $entry['quiz'] ?? null;
    return is_array($quiz) ? $quiz : null;
}

function tfSkillAssessmentSessionClearRound(int $userId, int $skillId, int $round): void
{
    if (isset($_SESSION['skill_assessment_quizzes'][$userId][$skillId][$round])) {
        unset($_SESSION['skill_assessment_quizzes'][$userId][$skillId][$round]);
    }
}

function tfSkillAssessmentSessionClearSkill(int $userId, int $skillId): void
{
    if (isset($_SESSION['skill_assessment_quizzes'][$userId][$skillId])) {
        unset($_SESSION['skill_assessment_quizzes'][$userId][$skillId]);
    }
}

function tfSkillAssessmentSerializeState(array $progress, array $attempts = []): array
{
    $maxRound = max(0, min(3, (int) ($progress['max_round'] ?? 0)));
    $maxPercent = max(0, min(100, (int) ($progress['max_percent'] ?? 0)));
    $status = (string) ($progress['status'] ?? 'not_started');

    $nextRound = $maxRound >= 3 ? null : ($maxRound + 1);
    if ($status === 'completed' || $status === 'surrendered') {
        $nextRound = null;
    }
    if ($nextRound !== null && tfSkillAssessmentFindAttemptForRound($attempts, $nextRound) !== null) {
        $nextRound = null;
    }

    $roundAttempts = [];
    foreach (tfSkillAssessmentRoundRules() as $roundNo => $rule) {
        $attempt = tfSkillAssessmentFindAttemptForRound($attempts, (int) $roundNo);
        $roundAttempts[] = [
            'round' => (int) $roundNo,
            'difficulty' => (string) ($rule['difficulty'] ?? ''),
            'pass_percent' => (int) ($rule['pass_percent'] ?? 0),
            'level_percent' => (int) ($rule['level_percent'] ?? 0),
            'used' => $attempt !== null,
            'passed' => $attempt ? !empty($attempt['passed']) : false,
            'percent' => $attempt ? (int) ($attempt['percent'] ?? 0) : 0,
            'score' => $attempt ? (int) ($attempt['score'] ?? 0) : 0,
            'total_questions' => $attempt ? (int) ($attempt['total_questions'] ?? 0) : 0,
            'attempt_date' => $attempt ? (string) ($attempt['attempt_date'] ?? '') : '',
        ];
    }

    return [
        'max_round' => $maxRound,
        'max_percent' => $maxPercent,
        'status' => $status,
        'next_round' => $nextRound,
        'round_attempts' => $roundAttempts,
        'round_rules' => array_values(tfSkillAssessmentRoundRules()),
    ];
}

function handleSkillAssessmentState()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $skillId = (int) ($data['skillId'] ?? 0);
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }
    if ($skillId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный навык']);
        return;
    }

    $pdo = getDBConnection();
    ensureUserSkillsSchema($pdo);
    ensureSkillAssessmentSchema($pdo);

    $skillCheck = tfSkillAssessmentFetchSkill($pdo, $userId, $skillId);
    if (empty($skillCheck['ok'])) {
        echo json_encode(['success' => false, 'message' => (string) ($skillCheck['message'] ?? 'Навык не найден.')]);
        return;
    }

    $progress = tfSkillAssessmentFetchProgress($pdo, $userId, $skillId);
    $attempts = tfSkillAssessmentFetchAttempts($pdo, $userId, $skillId);
    echo json_encode([
        'success' => true,
        'state' => tfSkillAssessmentSerializeState($progress, $attempts),
    ]);
}

function handleSkillAssessmentRound(int $round)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    $rule = tfSkillAssessmentRoundRule($round);
    if (empty($rule)) {
        echo json_encode(['success' => false, 'message' => 'Неверный тур']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        $data = [];
    }
    $skillId = (int) ($data['skillId'] ?? 0);
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }
    if ($skillId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный навык']);
        return;
    }

    $pdo = getDBConnection();
    ensureUserSkillsSchema($pdo);
    ensureSkillAssessmentSchema($pdo);

    $skillCheck = tfSkillAssessmentFetchSkill($pdo, $userId, $skillId);
    if (empty($skillCheck['ok'])) {
        echo json_encode(['success' => false, 'message' => (string) ($skillCheck['message'] ?? 'Навык не найден.')]);
        return;
    }
    $skill = (array) ($skillCheck['skill'] ?? []);
    $skillName = trim((string) ($skill['skill_name'] ?? ''));

    $progress = tfSkillAssessmentFetchProgress($pdo, $userId, $skillId);
    $attempts = tfSkillAssessmentFetchAttempts($pdo, $userId, $skillId);

    if (in_array($progress['status'], ['completed', 'surrendered'], true)) {
        echo json_encode([
            'success' => false,
            'message' => $progress['status'] === 'completed'
                ? 'Вы уже завершили эту оценку навыка.'
                : 'Оценка уже остановлена. Вы можете начать новую попытку позже.',
            'state' => tfSkillAssessmentSerializeState($progress, $attempts),
        ]);
        return;
    }

    $maxRound = (int) ($progress['max_round'] ?? 0);
    if ($round > ($maxRound + 1)) {
        echo json_encode([
            'success' => false,
            'message' => 'Нельзя запрашивать следующий раунд раньше времени.',
            'state' => tfSkillAssessmentSerializeState($progress, $attempts),
        ]);
        return;
    }
    if ($round <= $maxRound) {
        echo json_encode([
            'success' => false,
            'message' => 'Этот раунд уже был отправлен.',
            'state' => tfSkillAssessmentSerializeState($progress, $attempts),
        ]);
        return;
    }

    $usedRoundAttempt = tfSkillAssessmentFindAttemptForRound($attempts, $round);
    if ($usedRoundAttempt !== null) {
        echo json_encode([
            'success' => false,
            'message' => 'Этот раунд уже был пройден. Дождитесь следующего раунда.',
            'state' => tfSkillAssessmentSerializeState($progress, $attempts),
        ]);
        return;
    }

    $answers = $data['answers'] ?? null;
    $isSubmitMode = is_array($answers);
    if ($isSubmitMode) {
        $quiz = tfSkillAssessmentSessionGetQuiz($userId, $skillId, $round);
        if (!is_array($quiz) || empty($quiz['questions']) || !is_array($quiz['questions'])) {
            echo json_encode(['success' => false, 'message' => 'Сессия теста истекла. Запустите тур заново.']);
            return;
        }

        $questions = (array) ($quiz['questions'] ?? []);
        $total = count($questions);
        if ($total <= 0) {
            echo json_encode(['success' => false, 'message' => 'Нет вопросов для проверки.']);
            return;
        }

        $normalizedAnswers = [];
        for ($i = 0; $i < $total; $i++) {
            $value = $answers[$i] ?? ($answers[(string) $i] ?? null);
            if (!is_string($value) && !is_numeric($value)) {
                echo json_encode(['success' => false, 'message' => 'Ответьте на все вопросы перед отправкой.']);
                return;
            }
            $selected = trim((string) $value);
            if ($selected === '') {
                echo json_encode(['success' => false, 'message' => 'Ответьте на все вопросы перед отправкой.']);
                return;
            }
            $normalizedAnswers[$i] = $selected;
        }

        $score = 0;
        foreach ($questions as $index => $question) {
            $actual = mb_strtolower(trim((string) ($normalizedAnswers[$index] ?? '')));
            $expected = mb_strtolower(trim((string) ($question['correct_answer'] ?? '')));
            if ($actual !== '' && $expected !== '' && $actual === $expected) {
                $score++;
            }
        }

        $percent = (int) round(($score / max(1, $total)) * 100);
        $passed = $percent >= (int) $rule['pass_percent'];

        $attemptStmt = $pdo->prepare("
            INSERT INTO skill_assessment_attempts
                (user_id, skill_id, round_no, difficulty, score, total_questions, percent, passed, surrendered, attempt_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, CURDATE())
        ");
        try {
        $attemptStmt->execute([
            $userId,
            $skillId,
            $round,
            (string) $rule['difficulty'],
            $score,
            $total,
            $percent,
            $passed ? 1 : 0,
        ]);
        } catch (PDOException $e) {
            $msg = (stripos((string) $e->getMessage(), 'Duplicate') !== false)
                ? 'Этот раунд уже был отправлен. Перейдите к следующему раунду.'
                : ('Ошибка сохранения: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $msg,
                'state' => tfSkillAssessmentSerializeState($progress, tfSkillAssessmentFetchAttempts($pdo, $userId, $skillId)),
            ]);
            return;
        }

        if (!$passed) {
            $progressStmt = $pdo->prepare("
                INSERT INTO skill_assessment_progress (user_id, skill_id, max_round, max_percent, status)
                VALUES (?, ?, ?, ?, 'in_progress')
                ON DUPLICATE KEY UPDATE
                    status = CASE
                        WHEN status = 'completed' THEN 'completed'
                        WHEN status = 'surrendered' THEN 'surrendered'
                        ELSE 'in_progress'
                    END,
                    updated_at = NOW()
            ");
            $progressStmt->execute([$userId, $skillId, $maxRound, (int) ($progress['max_percent'] ?? 0)]);
        }

        if ($passed) {
            $newMaxRound = max($maxRound, $round);
            $newMaxPercent = tfSkillAssessmentLevelByRound($newMaxRound);
            $newStatus = $newMaxRound >= 3 ? 'completed' : 'in_progress';

            $progressStmt = $pdo->prepare("
                INSERT INTO skill_assessment_progress (user_id, skill_id, max_round, max_percent, status)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    max_round = GREATEST(max_round, VALUES(max_round)),
                    max_percent = GREATEST(max_percent, VALUES(max_percent)),
                    status = VALUES(status),
                    updated_at = NOW()
            ");
            $progressStmt->execute([$userId, $skillId, $newMaxRound, $newMaxPercent, $newStatus]);

            $skillStmt = $pdo->prepare("
                UPDATE user_skills
                SET skill_level = GREATEST(skill_level, ?), is_verified = 1
                WHERE id = ? AND user_id = ?
            ");
            $skillStmt->execute([$newMaxPercent, $skillId, $userId]);
        }

        tfSkillAssessmentSessionClearRound($userId, $skillId, $round);
        $freshProgress = tfSkillAssessmentFetchProgress($pdo, $userId, $skillId);
        $freshAttempts = tfSkillAssessmentFetchAttempts($pdo, $userId, $skillId);

        echo json_encode([
            'success' => true,
            'passed' => $passed,
            'round' => $round,
            'difficulty' => $rule['difficulty'],
            'score' => $score,
            'total' => $total,
            'percent' => $percent,
            'pass_percent' => (int) $rule['pass_percent'],
            'message' => $passed ? 'Раунд успешно пройден.' : 'Раунд не пройден.',
            'state' => tfSkillAssessmentSerializeState($freshProgress, $freshAttempts),
        ]);
        return;
    }

    $cachedQuiz = tfSkillAssessmentSessionGetQuiz($userId, $skillId, $round);
    if (is_array($cachedQuiz) && !empty($cachedQuiz['questions'])) {
        echo json_encode([
            'success' => true,
            'round' => $round,
            'difficulty' => $rule['difficulty'],
            'pass_percent' => (int) $rule['pass_percent'],
            'quiz' => tfSkillAssessmentPublicQuiz($cachedQuiz),
            'state' => tfSkillAssessmentSerializeState($progress, $attempts),
        ]);
        return;
    }

    $resp = generateSkillQuiz($skillName, (int) $rule['questions'], 5, 4, (string) $rule['difficulty']);
    if (empty($resp['ok']) || !is_array($resp['data'] ?? null)) {
        $message = 'Не удалось сформировать вопросы. Попробуйте позже.';
        if (($resp['error'] ?? '') === 'missing_providers') {
            $message = 'Не настроены AI-провайдеры для генерации вопросов.';
        }
        echo json_encode(['success' => false, 'message' => $message]);
        return;
    }

    $quiz = (array) ($resp['data'] ?? []);
    if (!validateSkillQuiz($quiz, (int) $rule['questions'])) {
        echo json_encode(['success' => false, 'message' => 'Ошибка запроса.']);
        return;
    }
    tfSkillAssessmentSessionSetQuiz($userId, $skillId, $round, $quiz);

    echo json_encode([
        'success' => true,
        'round' => $round,
        'difficulty' => $rule['difficulty'],
        'pass_percent' => (int) $rule['pass_percent'],
        'quiz' => tfSkillAssessmentPublicQuiz($quiz),
        'state' => tfSkillAssessmentSerializeState($progress, $attempts),
    ]);
}

function handleSkillAssessmentSurrender()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $skillId = (int) ($data['skillId'] ?? 0);
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }
    if ($skillId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный навык']);
        return;
    }

    $pdo = getDBConnection();
    ensureUserSkillsSchema($pdo);
    ensureSkillAssessmentSchema($pdo);

    $skillCheck = tfSkillAssessmentFetchSkill($pdo, $userId, $skillId);
    if (empty($skillCheck['ok'])) {
        echo json_encode(['success' => false, 'message' => (string) ($skillCheck['message'] ?? 'Навык не найден')]);
        return;
    }

    $progress = tfSkillAssessmentFetchProgress($pdo, $userId, $skillId);
    $maxRound = (int) ($progress['max_round'] ?? 0);
    $maxPercent = (int) ($progress['max_percent'] ?? 0);

    $progressStmt = $pdo->prepare("
        INSERT INTO skill_assessment_progress (user_id, skill_id, max_round, max_percent, status)
        VALUES (?, ?, ?, ?, 'surrendered')
        ON DUPLICATE KEY UPDATE
            max_round = GREATEST(max_round, VALUES(max_round)),
            max_percent = GREATEST(max_percent, VALUES(max_percent)),
            status = 'surrendered',
            updated_at = NOW()
    ");
    $progressStmt->execute([$userId, $skillId, $maxRound, $maxPercent]);

    $verifyValue = $maxRound > 0 ? 1 : 0;
    $skillStmt = $pdo->prepare("
        UPDATE user_skills
        SET skill_level = GREATEST(skill_level, ?), is_verified = ?
        WHERE id = ? AND user_id = ?
    ");
    $skillStmt->execute([$maxPercent, $verifyValue, $skillId, $userId]);

    tfSkillAssessmentSessionClearSkill($userId, $skillId);
    $freshProgress = tfSkillAssessmentFetchProgress($pdo, $userId, $skillId);
    $attempts = tfSkillAssessmentFetchAttempts($pdo, $userId, $skillId);

    echo json_encode([
        'success' => true,
        'message' => 'Оценка остановлена. Вы можете начать новую попытку позже.',
        'state' => tfSkillAssessmentSerializeState($freshProgress, $attempts),
    ]);
}

function handleSkillQuiz()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8');
        }
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    if (!headers_sent()) {
        header('Content-Type: application/json; charset=UTF-8');
    }
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }
    ob_start();
    $skills = $data['skills'] ?? ($data['skillName'] ?? '');
    $count = 30;
    $insurance = 12;
    $noticeParts = [];

    if (is_array($skills)) {
        $rawSkills = array_values(array_filter(array_map('trim', $skills)));
        $filtered = tfFilterItSkills($rawSkills);
        if (count($filtered) < count($rawSkills)) {
            $noticeParts[] = 'Некоторые навыки не относятся к IT и были исключены';
        }
        $skillsText = implode(', ', $filtered);
    } else {
        $skillsText = trim((string) $skills);
        $rawSkills = array_values(array_filter(array_map('trim', preg_split('/[,;]+/', $skillsText))));
        $filtered = tfFilterItSkills($rawSkills);
        if (count($filtered) < count($rawSkills)) {
            $noticeParts[] = 'Некоторые навыки не относятся к IT и были исключены';
        }
        $skillsText = implode(', ', $filtered);
    }

    if ($skillsText === '') {
        @ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Укажите только IT-навыки для проверки']);
        return;
    }

    try {
        $resp = generateSkillQuiz($skillsText, $count, 5, $insurance);
    } catch (Throwable $e) {
        @ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => 'AI временно недоступен. Попробуйте позже.'
        ]);
        return;
    }

    $noise = ob_get_clean();
    if (!$resp['ok']) {
        $message = 'Не удалось сформировать тест. Попробуйте позже.';
        if (!empty($resp['error']) && $resp['error'] === 'missing_providers') {
            $message = 'Не настроены AI-провайдеры для генерации теста.';
        } elseif (!empty($resp['error']) && $resp['error'] === 'insufficient_questions') {
            $have = isset($resp['have']) ? (int) $resp['have'] : 0;
            $need = isset($resp['needed']) ? (int) $resp['needed'] : $count;
            $message = "Недостаточно вопросов: {$have}/{$need}. Проверьте настройки Gemini.";
            if (!empty($resp['meta']['providers_failed'])) {
                $message .= ' Не удалось обратиться к провайдерам: ' . implode(', ', $resp['meta']['providers_failed']) . '.';
            }
        }
        echo json_encode([
            'success' => false,
            'message' => $message
        ]);
        return;
    }

    if (!empty($resp['meta']['providers_used'])) {
        $noticeParts[] = 'ИспРѕльР·Рѕваны провайдеры: ' . implode(', ', $resp['meta']['providers_used']);
    }
    if (!empty($resp['meta']['providers_failed'])) {
        $noticeParts[] = 'Некоторые провайдеры недоступны';
    }
    $notice = !empty($noticeParts) ? implode('. ', $noticeParts) : null;

    $payload = ['success' => true, 'quiz' => $resp['data']];
    if ($notice) {
        $payload['notice'] = $notice;
    }
    echo json_encode($payload);
}

// Получение данных вакансии
function handleGetVacancy()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    $vacancyId = $_GET['id'] ?? null;
    if (!$vacancyId) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM vacancies WHERE id = ?");
    $stmt->execute([$vacancyId]);
    $vacancy = $stmt->fetch();
    if (!$vacancy) {
        echo json_encode(['success' => false, 'message' => 'Вакансия не найдена']);
        return;
    }

    $stmt = $pdo->prepare("SELECT skill_name FROM vacancy_skills WHERE vacancy_id = ?");
    $stmt->execute([$vacancyId]);
    $vacancy['skills'] = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT requirement_text FROM vacancy_requirements WHERE vacancy_id = ?");
    $stmt->execute([$vacancyId]);
    $vacancy['requirements'] = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT plus_text FROM vacancy_pluses WHERE vacancy_id = ?");
    $stmt->execute([$vacancyId]);
    $vacancy['pluses'] = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT responsibility_text FROM vacancy_responsibilities WHERE vacancy_id = ?");
    $stmt->execute([$vacancyId]);
    $vacancy['responsibilities'] = $stmt->fetchAll();

    $sanitizeUtf8 = function (&$value) use (&$sanitizeUtf8) {
        if (is_array($value)) {
            foreach ($value as &$item) {
                $sanitizeUtf8($item);
            }
            return;
        }
        if (!is_string($value)) {
            return;
        }
        if (preg_match('//u', $value)) {
            return;
        }
        if (function_exists('mb_convert_encoding')) {
            $value = @mb_convert_encoding($value, 'UTF-8', 'UTF-8,Windows-1251,CP1251,ISO-8859-1');
            return;
        }
        if (function_exists('iconv')) {
            $converted = @iconv('Windows-1251', 'UTF-8//IGNORE', $value);
            if ($converted !== false) {
                $value = $converted;
            }
        }
    };
    $sanitizeUtf8($vacancy);

    $json = json_encode(['success' => true, 'vacancy' => $vacancy], JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        echo json_encode(['success' => false, 'message' => 'Ошибка кодирования данных'], JSON_UNESCAPED_UNICODE);
        return;
    }
    echo $json;
}

function handleCheckShortAnswer()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['question']) || empty($data['correctConcept']) || !isset($data['studentAnswer'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $resp = checkShortAnswerWithAI($data['question'], $data['correctConcept'], $data['studentAnswer']);
    if (!$resp['ok']) {
        echo json_encode(['success' => false, 'message' => 'AI сервис временно недоступен']);
        return;
    }
    echo json_encode(['success' => true, 'result' => $resp['data']]);
}

function handleAnalyzeCheating()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['answers'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $answersText = is_array($data['answers']) ? json_encode($data['answers'], JSON_UNESCAPED_UNICODE) : (string) $data['answers'];
    $resp = analyzeCheatingWithAI($answersText);
    if (!$resp['ok']) {
        echo json_encode(['success' => false, 'message' => 'AI сервис временно недоступен']);
        return;
    }
    echo json_encode(['success' => true, 'result' => $resp['data']]);
}

function handleRoadmapGetData()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    try {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
            return;
        }
        $pdo = getDBConnection();
        ensureRoadmapTables($pdo);

        $view = strtolower(trim((string) ($_GET['view'] ?? 'full')));
        if (!in_array($view, ['full', 'list', 'roadmap'], true)) {
            $view = 'full';
        }
        $roadmapTitleFilter = trim((string) ($_GET['roadmap_title'] ?? ''));
        if ($view === 'roadmap' && $roadmapTitleFilter === '') {
            $stmtFirstRoadmap = $pdo->query("SELECT title FROM roadmap_list ORDER BY id ASC LIMIT 1");
            $roadmapTitleFilter = trim((string) ($stmtFirstRoadmap->fetchColumn() ?: ''));
            if ($roadmapTitleFilter === '') {
                $stmtFirstNodeRoadmap = $pdo->query("
                    SELECT COALESCE(NULLIF(TRIM(roadmap_title), ''), 'Общий план') AS title
                    FROM roadmap_nodes
                    ORDER BY id ASC
                    LIMIT 1
                ");
                $roadmapTitleFilter = trim((string) ($stmtFirstNodeRoadmap->fetchColumn() ?: ''));
            }
        }
        $includeDetails = $view !== 'list';

        $nodeFields = $includeDetails
            ? "id, title, roadmap_title, topic, materials, x, y, deps, is_exam"
            : "id, title, roadmap_title, topic, deps, is_exam";
        $sqlNodes = "SELECT {$nodeFields} FROM roadmap_nodes";
        $nodeParams = [];
        if ($roadmapTitleFilter !== '') {
            if ($roadmapTitleFilter === 'Общий план') {
                $sqlNodes .= " WHERE (roadmap_title = ? OR roadmap_title IS NULL OR roadmap_title = '')";
                $nodeParams[] = $roadmapTitleFilter;
            } else {
                $sqlNodes .= " WHERE roadmap_title = ?";
                $nodeParams[] = $roadmapTitleFilter;
            }
        }
        $sqlNodes .= " ORDER BY id ASC";
        $stmtNodes = $pdo->prepare($sqlNodes);
        $stmtNodes->execute($nodeParams);
        $nodes = $stmtNodes->fetchAll();
        $nodeIds = array_values(array_filter(array_map('intval', array_column($nodes, 'id'))));

        $lessonsByNode = [];
        $quizByNode = [];
        if ($includeDetails && !empty($nodeIds)) {
            $placeholders = implode(',', array_fill(0, count($nodeIds), '?'));

            $stmtLessons = $pdo->prepare("
                SELECT id, node_id, title, video_url, description, materials, order_index
                FROM roadmap_lessons
                WHERE node_id IN ($placeholders)
                ORDER BY node_id ASC, order_index ASC, id ASC
            ");
            $stmtLessons->execute($nodeIds);
            $lessonsRows = $stmtLessons->fetchAll();
            foreach ($lessonsRows as $lesson) {
                $nid = (int) ($lesson['node_id'] ?? 0);
                if ($nid <= 0) {
                    continue;
                }
                $lesson['materials'] = json_decode($lesson['materials'] ?? '[]', true) ?: [];
                unset($lesson['node_id']);
                if (!isset($lessonsByNode[$nid])) {
                    $lessonsByNode[$nid] = [];
                }
                $lessonsByNode[$nid][] = $lesson;
            }

            $stmtQuiz = $pdo->prepare("
                SELECT id, node_id, question, options
                FROM roadmap_quiz_questions
                WHERE node_id IN ($placeholders)
                ORDER BY node_id ASC, id ASC
            ");
            $stmtQuiz->execute($nodeIds);
            $quizRows = $stmtQuiz->fetchAll();
            foreach ($quizRows as $q) {
                $nid = (int) ($q['node_id'] ?? 0);
                if ($nid <= 0) {
                    continue;
                }
                $q['options'] = json_decode($q['options'] ?? '[]', true) ?: [];
                unset($q['node_id']);
                if (!isset($quizByNode[$nid])) {
                    $quizByNode[$nid] = [];
                }
                $quizByNode[$nid][] = $q;
            }
        }

        $countsByRoadmap = [];
        foreach ($nodes as &$node) {
            $nodeId = (int) ($node['id'] ?? 0);
            $titleKey = trim((string) ($node['roadmap_title'] ?? ''));
            if ($titleKey === '') {
                $titleKey = 'Общий план';
                $node['roadmap_title'] = $titleKey;
            }
            $countsByRoadmap[$titleKey] = (int) (($countsByRoadmap[$titleKey] ?? 0) + 1);
            $node['deps'] = json_decode($node['deps'] ?? '[]', true) ?: [];
            $node['topic'] = $node['topic'] ?? '';
            if ($includeDetails) {
                $node['materials'] = json_decode($node['materials'] ?? '[]', true) ?: [];
                $node['lessons'] = $lessonsByNode[$nodeId] ?? [];
                $node['quiz_questions'] = $quizByNode[$nodeId] ?? [];
            }
        }
        unset($node);

        $sqlRoadmaps = "SELECT title, description FROM roadmap_list";
        $roadmapParams = [];
        if ($roadmapTitleFilter !== '') {
            $sqlRoadmaps .= " WHERE title = ?";
            $roadmapParams[] = $roadmapTitleFilter;
        }
        $sqlRoadmaps .= " ORDER BY id ASC";
        $stmtRoadmaps = $pdo->prepare($sqlRoadmaps);
        $stmtRoadmaps->execute($roadmapParams);
        $roadmapRows = $stmtRoadmaps->fetchAll();
        $roadmapsMap = [];
        foreach ($roadmapRows as $row) {
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $roadmapsMap[$title] = [
                'title' => $title,
                'description' => (string) ($row['description'] ?? ''),
                'nodes_count' => (int) ($countsByRoadmap[$title] ?? 0)
            ];
        }
        foreach ($countsByRoadmap as $title => $count) {
            if (!isset($roadmapsMap[$title])) {
                $roadmapsMap[$title] = [
                    'title' => $title,
                    'description' => '',
                    'nodes_count' => (int) $count
                ];
            } else {
                $roadmapsMap[$title]['nodes_count'] = (int) $count;
            }
        }

        $rows = [];
        if (!empty($nodeIds)) {
            $placeholders = implode(',', array_fill(0, count($nodeIds), '?'));
            $stmtProgress = $pdo->prepare("
                SELECT node_id, lesson_done, quiz_score, quiz_total, completed_at
                FROM roadmap_user_progress
                WHERE user_id = ? AND node_id IN ($placeholders)
            ");
            $stmtProgress->execute(array_merge([$userId], $nodeIds));
            $rows = $stmtProgress->fetchAll();
        }
        $states = [];
        $completed = [];
        foreach ($rows as $row) {
            $nodeId = (int) ($row['node_id'] ?? 0);
            if ($nodeId <= 0) {
                continue;
            }
            $lessonDone = (int) ($row['lesson_done'] ?? 0);
            $quizTotal = (int) ($row['quiz_total'] ?? 0);
            $quizScore = (int) ($row['quiz_score'] ?? 0);
            $legacyDone = empty($row['completed_at']) && $lessonDone === 0 && $quizTotal === 0;
            $isCompleted = !empty($row['completed_at']) || $legacyDone;
            $states[$nodeId] = [
                'lesson_done' => $lessonDone,
                'quiz_score' => $quizScore,
                'quiz_total' => $quizTotal,
                'completed' => $isCompleted,
                'legacy' => $legacyDone
            ];
            if ($isCompleted) {
                $completed[] = $nodeId;
            }
        }

        echo json_encode([
            'success' => true,
            'nodes' => $nodes,
            'roadmaps' => array_values($roadmapsMap),
            'active_roadmap' => $roadmapTitleFilter !== '' ? $roadmapTitleFilter : null,
            'view' => $view,
            'progress' => array_values(array_unique(array_map('intval', $completed))),
            'states' => $states
        ]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function tfRoadmapIsLegacyProgressCompleted(array $row): bool
{
    return empty($row['completed_at'])
        && (int) ($row['lesson_done'] ?? 0) === 0
        && (int) ($row['quiz_total'] ?? 0) === 0;
}

function tfRoadmapIsProgressCompleted(array $row): bool
{
    return !empty($row['completed_at']) || tfRoadmapIsLegacyProgressCompleted($row);
}

function tfRoadmapNormalizeAnswerText($value): string
{
    $text = trim((string) $value);
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($text, 'UTF-8');
    }
    return strtolower($text);
}

function handleRoadmapSaveProgress()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $pdo = null;
    try {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
            return;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            $data = [];
        }
        $nodeId = (int) ($data['node_id'] ?? ($_POST['node_id'] ?? 0));
        if ($nodeId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Неверный ID']);
            return;
        }
        $stage = strtolower(trim((string) ($data['stage'] ?? 'complete')));
        if (!in_array($stage, ['lesson', 'quiz', 'exam', 'complete'], true)) {
            echo json_encode(['success' => false, 'message' => 'Неверный этап прогресса']);
            return;
        }
        $pdo = getDBConnection();
        ensureRoadmapTables($pdo);
        $stmtNode = $pdo->prepare("SELECT id, roadmap_title, deps, is_exam FROM roadmap_nodes WHERE id = ?");
        $stmtNode->execute([$nodeId]);
        $node = $stmtNode->fetch();
        if (!$node) {
            echo json_encode(['success' => false, 'message' => 'Узел не найден']);
            return;
        }
        $isExam = (int) $node['is_exam'] === 1;
        if ($stage === 'complete') {
            $stage = $isExam ? 'exam' : 'quiz';
        }

        $pdo->beginTransaction();
        $stmtProgress = $pdo->prepare("
            SELECT lesson_done, quiz_score, quiz_total, completed_at
            FROM roadmap_user_progress
            WHERE user_id = ? AND node_id = ?
            LIMIT 1
            FOR UPDATE
        ");
        $stmtProgress->execute([$userId, $nodeId]);
        $progressRow = $stmtProgress->fetch();
        if ($progressRow && tfRoadmapIsProgressCompleted($progressRow)) {
            $allowRecheck = in_array($stage, ['quiz', 'exam'], true) && (int) ($progressRow['quiz_total'] ?? 0) <= 0;
            if (!$allowRecheck) {
                $pdo->commit();
                echo json_encode([
                    'success' => true,
                    'completed' => true,
                    'already_completed' => true,
                    'score' => (int) ($progressRow['quiz_score'] ?? 0),
                    'total' => (int) ($progressRow['quiz_total'] ?? 0),
                    'state' => [
                        'lesson_done' => (int) ($progressRow['lesson_done'] ?? 0),
                        'quiz_score' => (int) ($progressRow['quiz_score'] ?? 0),
                        'quiz_total' => (int) ($progressRow['quiz_total'] ?? 0),
                        'completed' => true,
                        'legacy' => tfRoadmapIsLegacyProgressCompleted($progressRow)
                    ]
                ]);
                return;
            }
        }

        $deps = json_decode($node['deps'] ?? '[]', true) ?: [];
        $deps = array_values(array_unique(array_filter(array_map('intval', (array) $deps), static function ($depId) use ($nodeId) {
            return $depId > 0 && $depId !== $nodeId;
        })));
        if (!empty($deps)) {
            $placeholders = implode(',', array_fill(0, count($deps), '?'));
            $stmtDeps = $pdo->prepare("
                SELECT node_id, lesson_done, quiz_total, completed_at
                FROM roadmap_user_progress
                WHERE user_id = ? AND node_id IN ($placeholders)
            ");
            $stmtDeps->execute(array_merge([$userId], $deps));
            $depRows = $stmtDeps->fetchAll();
            $doneDeps = [];
            foreach ($depRows as $row) {
                if (tfRoadmapIsProgressCompleted($row)) {
                    $doneDeps[] = (int) $row['node_id'];
                }
            }
            if (count($doneDeps) !== count($deps)) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Сначала завершите предыдущие блоки.']);
                return;
            }
        }

        if ($isExam) {
            $stmtAll = $pdo->prepare("SELECT id FROM roadmap_nodes WHERE roadmap_title = ? AND is_exam = 0");
            $stmtAll->execute([$node['roadmap_title']]);
            $required = $stmtAll->fetchAll(PDO::FETCH_COLUMN);
            $required = array_values(array_filter(array_map('intval', (array) $required)));
            if (!empty($required)) {
                $placeholders = implode(',', array_fill(0, count($required), '?'));
                $stmtDone = $pdo->prepare("
                    SELECT node_id, lesson_done, quiz_total, completed_at
                    FROM roadmap_user_progress
                    WHERE user_id = ? AND node_id IN ($placeholders)
                ");
                $stmtDone->execute(array_merge([$userId], $required));
                $rows = $stmtDone->fetchAll();
                $done = [];
                foreach ($rows as $row) {
                    if (tfRoadmapIsProgressCompleted($row)) {
                        $done[] = (int) $row['node_id'];
                    }
                }
                if (count($done) !== count($required)) {
                    $pdo->rollBack();
                    echo json_encode(['success' => false, 'message' => 'Экзамен доступен после прохождения всех блоков.']);
                    return;
                }
            }
        }

        if ($stage === 'lesson') {
            $stmt = $pdo->prepare("
                INSERT INTO roadmap_user_progress (user_id, node_id, lesson_done)
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE lesson_done = GREATEST(lesson_done, 1)
            ");
            $stmt->execute([$userId, $nodeId]);
            $pdo->commit();
            echo json_encode([
                'success' => true,
                'completed' => false,
                'state' => [
                    'lesson_done' => 1,
                    'quiz_score' => (int) ($progressRow['quiz_score'] ?? 0),
                    'quiz_total' => (int) ($progressRow['quiz_total'] ?? 0),
                    'completed' => false,
                    'legacy' => false
                ]
            ]);
            return;
        }

        if ($stage === 'quiz' || $stage === 'exam') {
            if ($stage === 'exam' && !$isExam) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Экзамен доступен только для экзамен-узла.']);
                return;
            }
            if ($stage === 'quiz' && $isExam) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Мини-тест недоступен для экзамена.']);
                return;
            }

            $currentLessonDone = (int) ($progressRow['lesson_done'] ?? 0);
            if ($stage === 'quiz' && $currentLessonDone !== 1) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Сначала подтвердите изучение материалов.']);
                return;
            }

            $stmtQuestions = $pdo->prepare("
                SELECT id, correct_answer
                FROM roadmap_quiz_questions
                WHERE node_id = ?
                ORDER BY id ASC
            ");
            $stmtQuestions->execute([$nodeId]);
            $questions = $stmtQuestions->fetchAll();
            if ($stage === 'quiz') {
                $questions = array_slice($questions, 0, 5);
            } else {
                $questions = array_slice($questions, 0, 30);
            }

            $total = count($questions);
            if ($total <= 0) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Нет вопросов для проверки.']);
                return;
            }
            $requiredCount = ($stage === 'quiz') ? min(5, $total) : 30;
            if ($total < $requiredCount) {
                $pdo->rollBack();
                echo json_encode([
                    'success' => false,
                    'message' => "Недостаточно вопросов для проверки. Требуется минимум {$requiredCount}.",
                    'total' => $total,
                    'required' => $requiredCount
                ]);
                return;
            }

            $answers = $data['answers'] ?? null;
            if (!is_array($answers)) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Передайте ответы для проверки.']);
                return;
            }

            $normalizedAnswers = [];
            for ($index = 0; $index < $total; $index++) {
                $selected = $answers[$index] ?? ($answers[(string) $index] ?? null);
                if (!is_string($selected) && !is_numeric($selected)) {
                    $pdo->rollBack();
                    echo json_encode(['success' => false, 'message' => 'Ответьте на все вопросы перед отправкой.']);
                    return;
                }
                $selectedText = trim((string) $selected);
                if ($selectedText === '') {
                    $pdo->rollBack();
                    echo json_encode(['success' => false, 'message' => 'Ответьте на все вопросы перед отправкой.']);
                    return;
                }
                $normalizedAnswers[$index] = $selectedText;
            }

            $score = 0;
            foreach ($questions as $index => $question) {
                if (tfRoadmapNormalizeAnswerText($normalizedAnswers[$index]) === tfRoadmapNormalizeAnswerText($question['correct_answer'] ?? '')) {
                    $score++;
                }
            }

            $passPercent = 70;
            $passScore = (int) ceil($total * $passPercent / 100);
            $lessonDoneForWrite = ($stage === 'quiz') ? 1 : $currentLessonDone;

            if ($score < $passScore) {
                $stmt = $pdo->prepare("
                    INSERT INTO roadmap_user_progress (user_id, node_id, lesson_done, quiz_score, quiz_total)
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        lesson_done = GREATEST(lesson_done, VALUES(lesson_done)),
                        quiz_score = VALUES(quiz_score),
                        quiz_total = VALUES(quiz_total)
                ");
                $stmt->execute([$userId, $nodeId, $lessonDoneForWrite, $score, $total]);
                $pdo->commit();
                echo json_encode([
                    'success' => false,
                    'completed' => false,
                    'message' => 'Недостаточно правильных ответов. Попробуйте еще раз.',
                    'score' => $score,
                    'total' => $total,
                    'pass_score' => $passScore,
                    'state' => [
                        'lesson_done' => $lessonDoneForWrite,
                        'quiz_score' => $score,
                        'quiz_total' => $total,
                        'completed' => false,
                        'legacy' => false
                    ]
                ]);
                return;
            }

            $stmt = $pdo->prepare("
                INSERT INTO roadmap_user_progress (user_id, node_id, lesson_done, quiz_score, quiz_total, completed_at)
                VALUES (?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                    lesson_done = GREATEST(lesson_done, VALUES(lesson_done)),
                    quiz_score = GREATEST(quiz_score, VALUES(quiz_score)),
                    quiz_total = GREATEST(quiz_total, VALUES(quiz_total)),
                    completed_at = IFNULL(completed_at, NOW())
            ");
            $stmt->execute([$userId, $nodeId, $lessonDoneForWrite, $score, $total]);
            $pdo->commit();
            echo json_encode([
                'success' => true,
                'completed' => true,
                'score' => $score,
                'total' => $total,
                'pass_score' => $passScore,
                'state' => [
                    'lesson_done' => $lessonDoneForWrite,
                    'quiz_score' => $score,
                    'quiz_total' => $total,
                    'completed' => true,
                    'legacy' => false
                ]
            ]);
            return;
        }

        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Неверный этап прогресса']);
    } catch (Throwable $e) {
        if ($pdo instanceof PDO && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleRoadmapIssueCertificate()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $pdo = null;
    try {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
            return;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            $data = [];
        }
        $nodeId = (int) ($data['node_id'] ?? ($_POST['node_id'] ?? 0));
        if ($nodeId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Неверный ID']);
            return;
        }
        $pdo = getDBConnection();
        ensureRoadmapTables($pdo);
        $pdo->beginTransaction();

        $stmtNode = $pdo->prepare("SELECT id, roadmap_title, is_exam FROM roadmap_nodes WHERE id = ?");
        $stmtNode->execute([$nodeId]);
        $node = $stmtNode->fetch();
        if (!$node || (int) $node['is_exam'] !== 1) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Сертификат выдаётся только после экзамена.']);
            return;
        }

        $stmtCert = $pdo->prepare("
            SELECT cert_hash
            FROM roadmap_certificates
            WHERE user_id = ? AND node_id = ?
            LIMIT 1
            FOR UPDATE
        ");
        $stmtCert->execute([$userId, $nodeId]);
        $existingHash = (string) ($stmtCert->fetchColumn() ?: '');
        if ($existingHash !== '') {
            $pdo->commit();
            echo json_encode(['success' => true, 'cert_hash' => $existingHash, 'existing' => true]);
            return;
        }

        $stmtProgress = $pdo->prepare("
            SELECT lesson_done, quiz_score, quiz_total, completed_at
            FROM roadmap_user_progress
            WHERE user_id = ? AND node_id = ?
            LIMIT 1
            FOR UPDATE
        ");
        $stmtProgress->execute([$userId, $nodeId]);
        $progress = $stmtProgress->fetch();
        if (!$progress || !tfRoadmapIsProgressCompleted($progress)) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Сначала завершите экзамен.']);
            return;
        }

        $quizTotal = (int) ($progress['quiz_total'] ?? 0);
        $quizScore = (int) ($progress['quiz_score'] ?? 0);
        $passScore = $quizTotal > 0 ? (int) ceil($quizTotal * 70 / 100) : 0;
        if ($quizTotal <= 0 || $quizScore < $passScore) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Экзамен должен быть сдан с проходным баллом.']);
            return;
        }

        $stmtRequired = $pdo->prepare("SELECT id FROM roadmap_nodes WHERE roadmap_title = ? AND is_exam = 0");
        $stmtRequired->execute([$node['roadmap_title']]);
        $required = array_values(array_filter(array_map('intval', (array) $stmtRequired->fetchAll(PDO::FETCH_COLUMN))));
        if (!empty($required)) {
            $placeholders = implode(',', array_fill(0, count($required), '?'));
            $stmtDone = $pdo->prepare("
                SELECT node_id, lesson_done, quiz_total, completed_at
                FROM roadmap_user_progress
                WHERE user_id = ? AND node_id IN ($placeholders)
            ");
            $stmtDone->execute(array_merge([$userId], $required));
            $rows = $stmtDone->fetchAll();
            $done = [];
            foreach ($rows as $row) {
                if (tfRoadmapIsProgressCompleted($row)) {
                    $done[] = (int) $row['node_id'];
                }
            }
            if (count(array_unique($done)) !== count($required)) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Сначала завершите все блоки роадмапа.']);
                return;
            }
        }

        $insert = $pdo->prepare("INSERT INTO roadmap_certificates (user_id, node_id, cert_hash) VALUES (?, ?, ?)");
        $createdHash = '';
        for ($attempt = 0; $attempt < 8; $attempt++) {
            $candidate = strtoupper(bin2hex(random_bytes(8)));
            try {
                $insert->execute([$userId, $nodeId, $candidate]);
                $createdHash = $candidate;
                break;
            } catch (PDOException $e) {
                if (stripos($e->getMessage(), 'Duplicate entry') === false) {
                    throw $e;
                }
                $stmtCert->execute([$userId, $nodeId]);
                $raceHash = (string) ($stmtCert->fetchColumn() ?: '');
                if ($raceHash !== '') {
                    $pdo->commit();
                    echo json_encode(['success' => true, 'cert_hash' => $raceHash, 'existing' => true]);
                    return;
                }
            }
        }

        if ($createdHash === '') {
            throw new RuntimeException('Не удалось сгенерировать уникальный хэш сертификата.');
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'cert_hash' => $createdHash]);
    } catch (Throwable $e) {
        if ($pdo instanceof PDO && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function ensureAdmin()
{
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован.']);
        return false;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $role = $stmt->fetch()['role'] ?? '';
    if ($role !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Доступ запрещен.']);
        return false;
    }
    return true;
}

function tfSeedCourse(PDO $pdo, array $courseDef)
{
    $title = trim((string) ($courseDef['title'] ?? ''));
    if ($title === '') {
        return ['ok' => false, 'error' => 'missing_title'];
    }

    $stmt = $pdo->prepare("SELECT id FROM courses WHERE title = ? LIMIT 1");
    $stmt->execute([$title]);
    $existingId = (int) ($stmt->fetchColumn() ?: 0);
    if ($existingId > 0) {
        return ['ok' => true, 'created' => false, 'course_id' => $existingId];
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("
            INSERT INTO courses (title, instructor, description, category, image_url, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $title,
            (string) ($courseDef['instructor'] ?? 'CodeMaster Academy'),
            (string) ($courseDef['description'] ?? ''),
            (string) ($courseDef['category'] ?? 'other'),
            (string) ($courseDef['image_url'] ?? ''),
        ]);
        $courseId = (int) $pdo->lastInsertId();

        $skills = $courseDef['skills'] ?? [];
        if (is_array($skills)) {
            foreach ($skills as $skill) {
                $skill = trim((string) $skill);
                if ($skill === '') {
                    continue;
                }
                $stmt = $pdo->prepare("INSERT INTO course_skills (course_id, skill_name, skill_level) VALUES (?, ?, 0)");
                $stmt->execute([$courseId, $skill]);
            }
        }

        $lessons = $courseDef['lessons'] ?? [];
        if (!is_array($lessons)) {
            $lessons = [];
        }

        $order = 0;
        foreach ($lessons as $lesson) {
            if (!is_array($lesson)) {
                continue;
            }
            $lessonTitle = trim((string) ($lesson['title'] ?? ''));
            if ($lessonTitle === '') {
                continue;
            }

            $stmt = $pdo->prepare("
                INSERT INTO lessons (course_id, title, type, content, video_url, materials_title, materials_url, order_num, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $courseId,
                $lessonTitle,
                (string) ($lesson['type'] ?? 'article'),
                (string) ($lesson['content'] ?? ''),
                (string) ($lesson['video_url'] ?? ''),
                (string) ($lesson['materials_title'] ?? ''),
                (string) ($lesson['materials_url'] ?? ''),
                $order++,
            ]);
            $lessonId = (int) $pdo->lastInsertId();

            if (!empty($lesson['quiz']) && is_array($lesson['quiz'])) {
                upsertLessonQuiz($pdo, $lessonId, $lesson['quiz']);
            }

            if (!empty($lesson['practice']) && is_array($lesson['practice'])) {
                $practice = $lesson['practice'];
                $language = (string) ($practice['language'] ?? '');
                if (in_array($language, ['cpp', 'python', 'c', 'csharp', 'java', 'js', 'mysql', 'pgsql', 'fill'], true)) {
                    $tests = $practice['tests'] ?? null;
                    if (is_array($tests)) {
                        $testsJson = json_encode($tests, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        if ($testsJson === false) {
                            $testsJson = '[]';
                        }

                        $stmt = $pdo->prepare("
                            INSERT INTO lesson_practice_tasks (lesson_id, language, title, prompt, starter_code, tests_json, is_required)
                            VALUES (?, ?, ?, ?, ?, ?, 1)
                        ");
                        $stmt->execute([
                            $lessonId,
                            $language,
                            (string) ($practice['title'] ?? ''),
                            (string) ($practice['prompt'] ?? ''),
                            (string) ($practice['starter_code'] ?? ''),
                            $testsJson,
                        ]);
                    }
                }
            }
        }

        $exam = $courseDef['exam'] ?? null;
        if (is_array($exam) && !empty($exam['questions']) && is_array($exam['questions'])) {
            $stmt = $pdo->prepare("SELECT id FROM course_exams WHERE course_id = ? LIMIT 1");
            $stmt->execute([$courseId]);
            $hasExam = (int) ($stmt->fetchColumn() ?: 0) > 0;
            if (!$hasExam) {
                $examJson = json_encode($exam['questions'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if ($examJson === false) {
                    $examJson = '[]';
                }
                $timeLimit = (int) ($exam['time_limit_minutes'] ?? 45);
                if ($timeLimit < 5) {
                    $timeLimit = 5;
                }
                if ($timeLimit > 180) {
                    $timeLimit = 180;
                }
                $passPercent = (int) ($exam['pass_percent'] ?? 70);
                if ($passPercent < 10) {
                    $passPercent = 10;
                }
                if ($passPercent > 100) {
                    $passPercent = 100;
                }

                $stmt = $pdo->prepare("
                    INSERT INTO course_exams (course_id, exam_json, time_limit_minutes, pass_percent, shuffle_questions, shuffle_options, created_at)
                    VALUES (?, ?, ?, ?, TRUE, TRUE, NOW())
                ");
                $stmt->execute([$courseId, $examJson, $timeLimit, $passPercent]);
            }
        }

        $pdo->commit();
        return ['ok' => true, 'created' => true, 'course_id' => $courseId];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['ok' => false, 'error' => 'exception', 'message' => $e->getMessage()];
    }
}

function handleAdminSeedLearningPack()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    if (!ensureAdmin()) {
        return;
    }

    if (!function_exists('tfSeedLearningPackRich')) {
        echo json_encode([
            'success' => false,
            'message' => 'Rich learning pack seeder is unavailable.'
        ], JSON_UNESCAPED_UNICODE);
        return;
    }

    $pdo = getDBConnection();
    try {
            $res = tfSeedLearningPackRich($pdo);
        if (!is_array($res)) {
            $res = [];
        }

        $createdIds = [];
        $existingIds = [];
        foreach ($res as $item) {
            if (!is_array($item) || empty($item['ok'])) {
                continue;
            }
            $id = (int) ($item['course_id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            if (!empty($item['created'])) {
                $createdIds[] = $id;
            } else {
                $existingIds[] = $id;
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Учебный пакет готов',
            'created_course_ids' => $createdIds,
            'existing_course_ids' => $existingIds,
        ], JSON_UNESCAPED_UNICODE);
        return;
    } catch (Throwable $e) {
        tfDebugLog('admin.seed_learning_pack_error', ['error' => $e->getMessage()]);
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
        return;
    }
}

function handleAdminGetUser()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id, email, name, role, title, location, bio, avatar, is_blocked FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не найден']);
        return;
    }
    echo json_encode(['success' => true, 'user' => $user]);
}

function handleAdminCreateUser()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['email']) || empty($data['name'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    $password = $data['password'] ?? '';
    if ($password === '') {
        $password = generateSecurePassword();
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    try {
        $resolvedCountry = tfResolveCountryByText((string) ($data['location'] ?? ''));
        $stmt = $pdo->prepare("INSERT INTO users (email, password, name, role, title, location, bio, avatar, country_code, country_name, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE)");
        $stmt->execute([
            $data['email'],
            $hash,
            $data['name'],
            $data['role'] ?? 'seeker',
            $data['title'] ?? '',
            $data['location'] ?? '',
            $data['bio'] ?? '',
            $data['avatar'] ?? 'https://placehold.co/150x150/4f46e5/ffffff?text=U',
            (string) ($resolvedCountry['country_code'] ?? ''),
            (string) ($resolvedCountry['country_name'] ?? '')
        ]);
        echo json_encode(['success' => true, 'message' => 'Пользователь создан', 'password' => $password]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleAdminUpdateUser()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    $fields = [
        'email' => $data['email'] ?? null,
        'name' => $data['name'] ?? null,
        'role' => $data['role'] ?? null,
        'title' => $data['title'] ?? null,
        'location' => $data['location'] ?? null,
        'bio' => $data['bio'] ?? null,
        'avatar' => $data['avatar'] ?? null
    ];
    $set = [];
    $params = [];
    foreach ($fields as $k => $v) {
        if ($v !== null) {
            $set[] = "$k = ?";
            $params[] = $v;
        }
    }
    if (!empty($data['password'])) {
        $set[] = "password = ?";
        $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
    }
    if (empty($set)) {
        echo json_encode(['success' => false, 'message' => 'Нет данных для обновления']);
        return;
    }
    $params[] = (int) $data['id'];
    $stmt = $pdo->prepare("UPDATE users SET " . implode(', ', $set) . " WHERE id = ?");
    $stmt->execute($params);
    echo json_encode(['success' => true, 'message' => 'Пользователь обновлен']);
}

function handleAdminDeleteUser()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Пользователь удален']);
}

function handleAdminResetUserContestProgress()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    try {
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM practice_submissions WHERE user_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM task_progress WHERE user_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM task_sessions WHERE owner_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM contest_results WHERE user_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM contest_submissions WHERE user_id = ?")->execute([$id]);
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Решённые задачи, прогресс и контестный прогресс сброшены']);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleAdminResetContestSubmission()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }

    $pdo = getDBConnection();
    try {
        ensureContestsSchema($pdo);
        $stmt = $pdo->prepare("SELECT id, contest_id FROM contest_submissions WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $submission = $stmt->fetch();
        if (!$submission) {
            echo json_encode(['success' => false, 'message' => 'Посылка не найдена']);
            return;
        }

        $contestId = (int) ($submission['contest_id'] ?? 0);

        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM contest_submissions WHERE id = ?")->execute([$id]);
        if ($contestId > 0) {
            $pdo->prepare("DELETE FROM contest_results WHERE contest_id = ?")->execute([$contestId]);
        }
        $pdo->commit();

        if ($contestId > 0) {
            try {
                tfSnapshotContestResults($pdo, $contestId);
            } catch (Throwable $snapshotError) {
                // Snapshot rebuild is best-effort; the reset itself already succeeded.
            }
        }
        echo json_encode(['success' => true, 'message' => 'Решение сброшено']);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleAdminToggleUserBlock()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    $block = ($_GET['block'] ?? 'false') === 'true' ? 1 : 0;
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE users SET is_blocked = ? WHERE id = ?");
    $stmt->execute([$block, $id]);
    echo json_encode(['success' => true, 'message' => $block ? 'Пользователь заблокирован' : 'Пользователь разблокирован']);
}

function handleAdminUpdateUserRole()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    $role = $_GET['role'] ?? '';
    if ($id <= 0 || !in_array($role, ['seeker', 'recruiter', 'admin'], true)) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$role, $id]);
    echo json_encode(['success' => true, 'message' => 'Роль обновлена']);
}

function tfNormalizeJudgeTests(array $tests): array
{
    $result = [];
    foreach ($tests as $test) {
        if (!is_array($test)) {
            continue;
        }
        $stdin = (string) ($test['stdin'] ?? ($test['in'] ?? ($test['input'] ?? '')));
        $expected = (string) ($test['expected_stdout'] ?? ($test['stdout'] ?? ($test['expected'] ?? ($test['out'] ?? ($test['output'] ?? '')))));
        $timeout = (int) ($test['timeout_sec'] ?? ($test['timeout'] ?? 3));
        if ($timeout < 1)
            $timeout = 1;
        if ($timeout > 10)
            $timeout = 10;
        $result[] = [
            'stdin' => $stdin,
            'expected_stdout' => $expected,
            'timeout_sec' => $timeout,
        ];
    }
    return $result;
}

function tfBuildJudgeErrorMessage(array $run): string
{
    $err = (string) ($run['error'] ?? 'unknown');
    $stderr = tfPracticeNormalizeOutput((string) ($run['stderr'] ?? ''));
    $stdout = tfPracticeNormalizeOutput((string) ($run['stdout'] ?? ''));

    $trim = function ($text, $max = 1200) {
        $text = (string) $text;
        if (mb_strlen($text) <= $max)
            return $text;
        return mb_substr($text, 0, $max) . "\n...";
    };

    if ($err === 'judge0_not_configured') {
        return "Judge0 не настроен. Укажите CODEMASTER_JUDGE0_URL или JUDGE0_URL.";
    }
    if ($err === 'judge0_failed') {
        $msg = "Ошибка запроса к Judge0 во время проверки.";
        if ($stderr !== '')
            $msg .= "\n\n" . $trim($stderr);
        return $msg;
    }
    return 'Ошибка проверки решения.';
}

function getContestLeaderboard(PDO $pdo, int $limit = 100): array
{
    ensureContestsSchema($pdo);
    return tfBuildContestRatingSummary($pdo, null, $limit, false);
}

function getContestLeaderboardForContest(PDO $pdo, int $contestId, int $limit = 100): array
{
    ensureContestsSchema($pdo);
    return tfBuildContestRatingSummary($pdo, $contestId, $limit, false);
}

function tfContestResolveUserRank(int $points): array
{
    if ($points >= 2000) {
        return ['key' => 'gold', 'label' => 'Gold'];
    }
    if ($points >= 1000) {
        return ['key' => 'silver', 'label' => 'Silver'];
    }
    if ($points >= 500) {
        return ['key' => 'bronze', 'label' => 'Bronze'];
    }
    return ['key' => 'starter', 'label' => 'Starter'];
}

function tfContestDifficultyTier(string $difficulty): int
{
    $value = strtolower(trim($difficulty));
    if ($value === 'hard') {
        return 3;
    }
    if ($value === 'medium') {
        return 2;
    }
    return 1;
}

function tfContestDifficultyBasePoints(string $difficulty): int
{
    $tier = tfContestDifficultyTier($difficulty);
    if ($tier === 3) {
        return 30;
    }
    if ($tier === 2) {
        return 20;
    }
    return 10;
}

function tfContestResolveUserLevelTierByPoints(int $points): int
{
    if ($points >= 1500) {
        return 3;
    }
    if ($points >= 400) {
        return 2;
    }
    return 1;
}

function tfContestResolveUserLevelTier(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $userRow = $stmt->fetch() ?: [];
    $pointsData = !empty($userRow) ? calculateUserPoints($userRow) : ['points' => 0];
    $points = (int) ($pointsData['points'] ?? 0);

    return [
        'points' => $points,
        'tier' => tfContestResolveUserLevelTierByPoints($points),
    ];
}

function tfContestComputeSubmissionScore(int $attemptPoints, int $wrongAttempts, array $results, string $difficulty = 'easy', int $userLevelTier = 1): array
{
    $attemptNumber = max(1, (int) $attemptPoints);
    $wrongPenalty = max(0, $wrongAttempts);
    $timeSamples = [];
    $memorySamples = [];
    foreach ($results as $result) {
        if (isset($result['time']) && is_numeric($result['time'])) {
            $timeSamples[] = (float) $result['time'];
        }
        if (isset($result['memory']) && is_numeric($result['memory'])) {
            $memorySamples[] = (int) $result['memory'];
        }
    }
    $avgTime = !empty($timeSamples) ? array_sum($timeSamples) / count($timeSamples) : 0.0;
    $avgMemory = !empty($memorySamples) ? array_sum($memorySamples) / count($memorySamples) : 0.0;
    $taskTier = tfContestDifficultyTier($difficulty);
    $basePoints = tfContestDifficultyBasePoints($difficulty);
    $tierGap = $taskTier - max(1, $userLevelTier);
    $bonus = $tierGap > 0 ? ($tierGap * 10) : 0;
    $score = $taskTier < max(1, $userLevelTier) ? 0 : ($basePoints + $bonus);

    return [
        'score' => $score,
        'base_points' => $basePoints,
        'attempts' => $attemptNumber,
        'wrong_penalty' => $wrongPenalty,
        'attempts_penalty' => 0,
        'time_penalty' => 0,
        'memory_penalty' => 0,
        'bonus' => $bonus,
        'difficulty_tier' => $taskTier,
        'user_level_tier' => max(1, $userLevelTier),
        'avg_time_sec' => round($avgTime, 3),
        'avg_memory_kb' => (int) round($avgMemory),
    ];
}

function tfBuildContestRatingSummary(PDO $pdo, ?int $contestId = null, int $limit = 100, bool $keyByUserId = false): array
{
    ensureContestsSchema($pdo);
    $limit = max(1, min(5000, $limit));
    $sql = "
        SELECT
            cs.user_id,
            cs.contest_id,
            cs.task_id,
            cs.status,
            cs.points_awarded,
            cs.attempts,
            cs.wrong_attempts,
            cs.details_json,
            cs.updated_at,
            u.name,
            u.avatar
        FROM contest_submissions cs
        INNER JOIN users u ON u.id = cs.user_id
        WHERE u.is_blocked = FALSE
          AND u.role IN ('seeker', 'recruiter')
    ";
    $params = [];
    if ($contestId !== null) {
        $sql .= " AND cs.contest_id = ? ";
        $params[] = $contestId;
    }
    $sql .= " ORDER BY cs.updated_at ASC, cs.id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll() ?: [];

    $summary = [];
    foreach ($rows as $row) {
        $userId = (int) ($row['user_id'] ?? 0);
        if ($userId <= 0) {
            continue;
        }
        $status = (string) ($row['status'] ?? '');
        $calc = [
            'score' => $status === 'accepted' ? (int) ($row['points_awarded'] ?? 0) : 0,
            'attempts' => max(0, (int) ($row['attempts'] ?? 0)),
            'wrong_penalty' => max(0, (int) ($row['wrong_attempts'] ?? 0)),
            'attempts_penalty' => 0,
            'time_penalty' => 0,
            'memory_penalty' => 0,
            'bonus' => 0,
            'avg_time_sec' => 0,
            'avg_memory_kb' => 0,
        ];

        if (!isset($summary[$userId])) {
            $summary[$userId] = [
                'id' => $userId,
                'name' => (string) ($row['name'] ?? ''),
                'avatar' => (string) ($row['avatar'] ?? ''),
                'contest_points' => 0,
                'solved_count' => 0,
                'attempts_count' => 0,
                'wrong_penalties' => 0,
                'attempts_penalties' => 0,
                'time_penalties' => 0,
                'memory_penalties' => 0,
                'bonus_points' => 0,
                'last_submit_at' => (string) ($row['updated_at'] ?? ''),
            ];
        }

        $summary[$userId]['contest_points'] += (int) ($calc['score'] ?? 0);
        if ($status === 'accepted') {
            $summary[$userId]['solved_count'] += 1;
        }
        $summary[$userId]['attempts_count'] += (int) ($calc['attempts'] ?? 0);
        $summary[$userId]['wrong_penalties'] += (int) ($calc['wrong_penalty'] ?? 0);
        $summary[$userId]['attempts_penalties'] += (int) ($calc['attempts_penalty'] ?? 0);
        $summary[$userId]['bonus_points'] += (int) ($calc['bonus'] ?? 0);
        $lastSeen = strtotime((string) ($summary[$userId]['last_submit_at'] ?? '')) ?: 0;
        $currentSeen = strtotime((string) ($row['updated_at'] ?? '')) ?: 0;
        if ($currentSeen > $lastSeen) {
            $summary[$userId]['last_submit_at'] = (string) ($row['updated_at'] ?? '');
        }
    }

    if ($keyByUserId) {
        return $summary;
    }

    $list = array_values($summary);
    usort($list, static function ($a, $b) {
        $pointsCmp = (int) ($b['contest_points'] ?? 0) <=> (int) ($a['contest_points'] ?? 0);
        if ($pointsCmp !== 0) {
            return $pointsCmp;
        }
        $solvedCmp = (int) ($b['solved_count'] ?? 0) <=> (int) ($a['solved_count'] ?? 0);
        if ($solvedCmp !== 0) {
            return $solvedCmp;
        }
        $aTime = strtotime((string) ($a['last_submit_at'] ?? '')) ?: PHP_INT_MAX;
        $bTime = strtotime((string) ($b['last_submit_at'] ?? '')) ?: PHP_INT_MAX;
        return $aTime <=> $bTime;
    });

    return array_slice($list, 0, $limit);
}

function handleContestSubmit()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $rateLimit = tfConsumeRateLimit('contest_submit:' . $userId, 8, 60);
    if (empty($rateLimit['ok'])) {
        echo json_encode(['success' => false, 'message' => 'Слишком много посылок. Повторите позже.']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $taskId = (int) ($data['task_id'] ?? 0);
    $contestId = (int) ($data['contest_id'] ?? 0);
    $language = tfJudge0NormalizeLanguage((string) ($data['language'] ?? 'cpp'));
    $code = (string) ($data['code'] ?? '');
    if (!in_array($language, ['cpp', 'python', 'c', 'csharp', 'java', 'js', 'ts', 'go', 'rust', 'php', 'ruby', 'swift', 'kotlin', 'scala', 'dart', 'sql'], true) || $taskId <= 0 || $contestId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        return;
    }
    if (trim($code) === '' || mb_strlen($code) > 200000) {
        echo json_encode(['success' => false, 'message' => 'Invalid code']);
        return;
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    @set_time_limit(300);

    $pdo = getDBConnection();
    ensureContestsSchema($pdo);
    tfAutoLockExpiredContests($pdo);

    $stmt = $pdo->prepare("SELECT * FROM contest_tasks WHERE id = ? AND contest_id = ? LIMIT 1");
    $stmt->execute([$taskId, $contestId]);
    $task = $stmt->fetch();
    if (!$task) {
        echo json_encode(['success' => false, 'message' => 'Task not found']);
        return;
    }

    $stmt = $pdo->prepare("SELECT starts_at, ends_at, is_locked FROM contests WHERE id = ? LIMIT 1");
    $stmt->execute([$contestId]);
    $contestMeta = $stmt->fetch() ?: [];
    if (!empty($contestMeta['is_locked'])) {
        echo json_encode(['success' => false, 'message' => 'Contest is закрыт']);
        return;
    }
    $startsAt = !empty($contestMeta['starts_at']) ? strtotime((string) $contestMeta['starts_at']) : null;
    $endsAt = !empty($contestMeta['ends_at']) ? strtotime((string) $contestMeta['ends_at']) : null;
    $now = time();
    if ($startsAt !== null && $now < $startsAt) {
        echo json_encode(['success' => false, 'message' => 'Contest еще не начался']);
        return;
    }
    if ($endsAt !== null && $now >= $endsAt) {
        tfAutoLockExpiredContests($pdo);
        echo json_encode(['success' => false, 'message' => 'Contest завершен']);
        return;
    }

    $testsRaw = json_decode((string) ($task['tests_json'] ?? '[]'), true);
    $tests = tfNormalizeJudgeTests(is_array($testsRaw) ? $testsRaw : []);
    if (empty($tests)) {
        echo json_encode(['success' => false, 'message' => 'Tests are not configured for this task']);
        return;
    }

    $timeLimitSec = max(1, min(15, (int) ($task['time_limit_sec'] ?? 3)));
    $memoryLimitKb = 262144;
    foreach ($tests as &$testCase) {
        if (!is_array($testCase)) {
            $testCase = [];
        }
        $testCase['timeout_sec'] = $timeLimitSec;
        $testCase['memory_limit_kb'] = $memoryLimitKb;
    }
    unset($testCase);

    $run = tfRunPracticeWithJudge0($language, $code, $tests);
    if (empty($run['ok'])) {
        echo json_encode([
            'success' => false,
            'message' => tfBuildJudgeErrorMessage($run),
            'error' => (string) ($run['error'] ?? 'unknown'),
        ]);
        return;
    }

    $results = is_array($run['results'] ?? null) ? $run['results'] : [];
    $detailsJson = json_encode(['results' => $results], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($detailsJson === false) {
        $detailsJson = '{}';
    }
    $checksTotal = count($results);
    $checksPassed = 0;
    foreach ($results as $r) {
        if (!empty($r['passed'])) {
            $checksPassed++;
        }
    }
    $passed = !empty($run['passed']);

    $stmt = $pdo->prepare("SELECT * FROM contest_submissions WHERE user_id = ? AND task_id = ? LIMIT 1");
    $stmt->execute([$userId, $taskId]);
    $existing = $stmt->fetch();

    $userContestLevel = tfContestResolveUserLevelTier($pdo, $userId);
    $attempts = (int) ($existing['attempts'] ?? 0);
    $wrongAttempts = (int) ($existing['wrong_attempts'] ?? 0);
    $wasAccepted = ($existing && (string) ($existing['status'] ?? '') === 'accepted');
    $bestPoints = (int) ($existing['points_awarded'] ?? 0);
    $newAttempts = $wasAccepted ? $attempts : ($attempts + 1);

    if ($passed) {
        $taskDifficulty = (string) ($task['difficulty'] ?? 'easy');
        $scoreMeta = tfContestComputeSubmissionScore($newAttempts, $wrongAttempts, $results, $taskDifficulty, (int) ($userContestLevel['tier'] ?? 1));
        $newPoints = (int) ($scoreMeta['score'] ?? 0);
        if ($wasAccepted && $bestPoints > $newPoints) {
            $newPoints = $bestPoints;
        }
        $newWrongAttempts = $wasAccepted ? $wrongAttempts : $wrongAttempts;

        if ($existing) {
            $stmt = $pdo->prepare("
                UPDATE contest_submissions
                SET language = ?, code = ?, status = 'accepted', points_awarded = ?, checks_passed = ?, checks_total = ?, details_json = ?, attempts = ?, wrong_attempts = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$language, $code, $newPoints, $checksPassed, $checksTotal, $detailsJson, $newAttempts, $newWrongAttempts, (int) $existing['id']]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO contest_submissions
                    (user_id, contest_id, task_id, language, code, status, points_awarded, checks_passed, checks_total, details_json, attempts, wrong_attempts, created_at, updated_at)
                VALUES
                    (?, ?, ?, ?, ?, 'accepted', ?, ?, ?, ?, 1, 0, NOW(), NOW())
            ");
            $stmt->execute([$userId, $contestId, $taskId, $language, $code, $newPoints, $checksPassed, $checksTotal, $detailsJson]);
        }

        $leaderboard = getContestLeaderboardForContest($pdo, $contestId, 200);
        $rank = null;
        $totalPoints = 0;
        foreach ($leaderboard as $idx => $row) {
            if ((int) ($row['id'] ?? 0) === $userId) {
                $rank = $idx + 1;
                $totalPoints = (int) ($row['contest_points'] ?? 0);
                break;
            }
        }
        $userRankMeta = tfContestResolveUserRank($totalPoints);

        echo json_encode([
            'success' => true,
            'passed' => true,
            'message' => 'Решение принято',
            'points_awarded' => $newPoints,
            'total_points' => $totalPoints,
            'checks_passed' => $checksPassed,
            'checks_total' => $checksTotal,
            'score_meta' => $scoreMeta,
            'user_level_points' => (int) ($userContestLevel['points'] ?? 0),
            'rank' => $rank,
            'user_rank' => $userRankMeta,
            'time_limit_sec' => $timeLimitSec,
            'memory_limit_kb' => $memoryLimitKb,
            'results' => $results,
        ]);
        return;
    }

    if (!$wasAccepted) {
        if ($existing) {
            $stmt = $pdo->prepare("
                UPDATE contest_submissions
                SET language = ?, code = ?, status = 'rejected', points_awarded = 0, checks_passed = ?, checks_total = ?, details_json = ?, attempts = ?, wrong_attempts = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$language, $code, $checksPassed, $checksTotal, $detailsJson, $attempts + 1, $wrongAttempts + 1, (int) $existing['id']]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO contest_submissions
                    (user_id, contest_id, task_id, language, code, status, points_awarded, checks_passed, checks_total, details_json, attempts, wrong_attempts, created_at, updated_at)
                VALUES
                    (?, ?, ?, ?, ?, 'rejected', 0, ?, ?, ?, 1, 1, NOW(), NOW())
            ");
            $stmt->execute([$userId, $contestId, $taskId, $language, $code, $checksPassed, $checksTotal, $detailsJson]);
        }
    }

    echo json_encode([
        'success' => true,
        'passed' => false,
        'message' => 'Решение не прошло проверку',
        'points_awarded' => $bestPoints,
        'checks_passed' => $checksPassed,
        'checks_total' => $checksTotal,
        'time_limit_sec' => $timeLimitSec,
        'memory_limit_kb' => $memoryLimitKb,
        'results' => $results,
    ]);
}

function tfSanitizeUiMessage(string $text, int $maxLen = 800): string
{
    $text = preg_replace('/[^\P{C}\t\r\n]/u', '', $text) ?? '';
    $text = trim($text);
    if ($text === '') {
        return '';
    }
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text, 'UTF-8') > $maxLen) {
            $text = mb_substr($text, 0, $maxLen, 'UTF-8') . '...';
        }
    } elseif (strlen($text) > $maxLen) {
        $text = substr($text, 0, $maxLen) . '...';
    }
    return $text;
}

function tfSanitizeSubmissionDetails($value, int $depth = 0)
{
    if ($depth > 4) {
        return '[truncated]';
    }
    if (is_array($value)) {
        $clean = [];
        $count = 0;
        foreach ($value as $key => $item) {
            $clean[$key] = tfSanitizeSubmissionDetails($item, $depth + 1);
            $count++;
            if ($count >= 50) {
                $clean['__truncated__'] = true;
                break;
            }
        }
        return $clean;
    }
    if (is_string($value)) {
        return tfSanitizeUiMessage($value, 2000);
    }
    if (is_bool($value) || is_int($value) || is_float($value) || $value === null) {
        return $value;
    }
    return tfSanitizeUiMessage((string) $value, 2000);
}

function handleAdminSubmissionDetail()
{
    $user = getCurrentUser();
    if (($user['role'] ?? '') !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        return;
    }

    $kind = trim((string) ($_GET['kind'] ?? ''));
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0 || !in_array($kind, ['practice', 'contest'], true)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        return;
    }

    $pdo = getDBConnection();
    if ($kind === 'practice') {
        ensurePracticeSchema($pdo);
        $stmt = $pdo->prepare("
            SELECT ps.*, u.name AS user_name, lpt.title AS task_title, l.title AS lesson_title, c.title AS course_title
            FROM practice_submissions ps
            LEFT JOIN users u ON u.id = ps.user_id
            LEFT JOIN lesson_practice_tasks lpt ON lpt.id = ps.task_id
            LEFT JOIN lessons l ON l.id = lpt.lesson_id
            LEFT JOIN courses c ON c.id = l.course_id
            WHERE ps.id = ?
            LIMIT 1
        ");
    } else {
        ensureContestsSchema($pdo);
        $stmt = $pdo->prepare("
            SELECT cs.*, u.name AS user_name, ct.title AS task_title, c.title AS contest_title
            FROM contest_submissions cs
            LEFT JOIN users u ON u.id = cs.user_id
            LEFT JOIN contest_tasks ct ON ct.id = cs.task_id
            LEFT JOIN contests c ON c.id = cs.contest_id
            WHERE cs.id = ?
            LIMIT 1
        ");
    }
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Submission not found']);
        return;
    }

    $details = [];
    if (!empty($row['details_json'])) {
        $decoded = json_decode((string) $row['details_json'], true);
        if (is_array($decoded)) {
            $details = tfSanitizeSubmissionDetails($decoded);
        }
    }

    $code = (string) ($row['code'] ?? '');
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($code, 'UTF-8') > 100000) {
            $code = mb_substr($code, 0, 100000, 'UTF-8') . "\n...";
        }
    } elseif (strlen($code) > 100000) {
        $code = substr($code, 0, 100000) . "\n...";
    }

    echo json_encode([
        'success' => true,
        'submission' => [
            'id' => (int) ($row['id'] ?? 0),
            'kind' => $kind,
            'user_name' => normalizeMojibakeText((string) ($row['user_name'] ?? '')),
            'task_title' => normalizeMojibakeText((string) ($row['task_title'] ?? '')),
            'lesson_title' => normalizeMojibakeText((string) ($row['lesson_title'] ?? '')),
            'course_title' => normalizeMojibakeText((string) ($row['course_title'] ?? '')),
            'contest_title' => normalizeMojibakeText((string) ($row['contest_title'] ?? '')),
            'language' => (string) ($row['language'] ?? ''),
            'status' => (string) ($row['status'] ?? ''),
            'passed' => (int) ($row['passed'] ?? 0),
            'points_awarded' => (int) ($row['points_awarded'] ?? 0),
            'checks_passed' => (int) ($row['checks_passed'] ?? 0),
            'checks_total' => (int) ($row['checks_total'] ?? 0),
            'attempts' => (int) ($row['attempts'] ?? 0),
            'wrong_attempts' => (int) ($row['wrong_attempts'] ?? 0),
            'created_at' => (string) ($row['created_at'] ?? ''),
            'stdout' => tfSanitizeUiMessage((string) ($row['stdout'] ?? ''), 4000),
            'stderr' => tfSanitizeUiMessage((string) ($row['stderr'] ?? ''), 4000),
            'code' => $code,
            'details' => $details,
        ]
    ]);
}

function tfSeedDefaultContests(PDO $pdo)
{
    if (function_exists('tfSeedContestsRich')) {
        tfSeedContestsRich($pdo, false);
        return;
    }

    $baseContestTitle = 'Базовый контест';
    $baseContestDesc = 'Набор стартовых задач для тренировки навыков программирования.';
    $stmtBase = $pdo->prepare("SELECT id FROM contests WHERE slug = ? LIMIT 1");
    $stmtBase->execute(['base-contest']);
    $baseContestId = (int) ($stmtBase->fetchColumn() ?: 0);
    if ($baseContestId > 0) {
        $stmt = $pdo->prepare("UPDATE contests SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$baseContestTitle, $baseContestDesc, $baseContestId]);

        $updateTask = $pdo->prepare("
            UPDATE contest_tasks
            SET title = ?, statement = ?, input_spec = ?, output_spec = ?
            WHERE contest_id = ? AND order_num = ?
        ");
        $updateTask->execute([
            'A + B',
            'Даны два целых числа A и B. Выведите их сумму.',
            'Ввод: A B',
            'Вывод: A + B',
            $baseContestId,
            1
        ]);
        $updateTask->execute([
            'Палиндром',
            'Дана строка S. Выведите YES, если строка является палиндромом, иначе NO.',
            'Ввод: S',
            'Вывод: YES или NO',
            $baseContestId,
            2
        ]);
        $updateTask->execute([
            'Максимум массива',
            'Даны N и массив из N целых чисел. Найдите максимальный элемент.',
            "Ввод: N\nмассив из N чисел",
            'Вывод: максимальный элемент',
            $baseContestId,
            3
        ]);
    }

    $total = (int) ($pdo->query("SELECT COUNT(*) as total FROM contests")->fetch()['total'] ?? 0);
    if ($total > 0) {
        return;
    }

    $stmt = $pdo->prepare("INSERT INTO contests (title, slug, description, is_active, created_at) VALUES (?, ?, ?, 1, NOW())");
    $stmt->execute([
        $baseContestTitle,
        'base-contest',
        $baseContestDesc
    ]);
    $contestId = (int) $pdo->lastInsertId();
    if ($contestId <= 0) {
        return;
    }

    $insertTask = $pdo->prepare("
        INSERT INTO contest_tasks
            (contest_id, title, difficulty, statement, input_spec, output_spec, starter_cpp, starter_python, tests_json, order_num, created_at)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $insertTask->execute([
        $contestId,
        'A + B',
        'easy',
        'Даны два целых числа A и B. Выведите их сумму.',
        'Ввод: A B',
        'Вывод: A + B',
        "#include <iostream>\nusing namespace std;\n\nint main() {\n    long long a, b;\n    cin >> a >> b;\n\n    // your code\n\n    return 0;\n}\n",
        "def solve():\n    a, b = map(int, input().split())\n\n    # your code\n\nif __name__ == '__main__':\n    solve()\n",
        json_encode([['in' => '2 3', 'out' => '5'], ['in' => '-4 9', 'out' => '5'], ['in' => '100 1', 'out' => '101']], JSON_UNESCAPED_UNICODE),
        1
    ]);

    $insertTask->execute([
        $contestId,
        'Палиндром',
        'easy',
        'Дана строка S. Выведите YES, если строка является палиндромом, иначе NO.',
        'Ввод: S',
        'Вывод: YES или NO',
        "#include <iostream>\n#include <string>\nusing namespace std;\n\nint main() {\n    string s;\n    cin >> s;\n\n    // your code\n\n    return 0;\n}\n",
        "def solve():\n    s = input().strip()\n\n    # your code\n\nif __name__ == '__main__':\n    solve()\n",
        json_encode([['in' => 'level', 'out' => 'YES'], ['in' => 'abca', 'out' => 'NO'], ['in' => 'abba', 'out' => 'YES']], JSON_UNESCAPED_UNICODE),
        2
    ]);

    $insertTask->execute([
        $contestId,
        'Максимум массива',
        'medium',
        'Даны N и массив из N целых чисел. Найдите максимальный элемент.',
        "Ввод: N\nмассив из N чисел",
        'Вывод: максимальный элемент',
        "#include <iostream>\n#include <vector>\nusing namespace std;\n\nint main() {\n    int n;\n    cin >> n;\n    vector<long long> a(n);\n    for (int i = 0; i < n; i++) cin >> a[i];\n\n    // your code\n\n    return 0;\n}\n",
        "def solve():\n    n = int(input())\n    a = list(map(int, input().split()))\n\n    # your code\n\nif __name__ == '__main__':\n    solve()\n",
        json_encode([['in' => "5\n1 7 3 2 0", 'out' => '7'], ['in' => "4\n-1 -5 -2 -3", 'out' => '-1'], ['in' => "3\n10 10 9", 'out' => '10']], JSON_UNESCAPED_UNICODE),
        3
    ]);
}

function tfGetInterviewProblemsLegacy(): array
{
    return [
        [
            'id' => 1,
            'title' => 'Two Sum',
            'difficulty' => 'Easy',
            'category' => 'Array',
            'acceptance' => '53.2%',
            'companies' => ['Amazon', 'Google', 'Meta'],
            'statement' => 'Given an array of integers nums and an integer target, print indexes of two numbers such that they add up to target.',
            'input' => "n target\nnums[0] nums[1] ... nums[n-1]",
            'output' => 'i j (0-based indexes)',
            'starter_cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    int n, target;\n    cin >> n >> target;\n    vector<int> nums(n);\n    for (int i = 0; i < n; i++) cin >> nums[i];\n\n    // your code\n\n    return 0;\n}\n",
            'starter_python' => "def solve():\n    n, target = map(int, input().split())\n    nums = list(map(int, input().split()))\n\n    # your code\n\nif __name__ == '__main__':\n    solve()\n",
            'tests' => [
                ['in' => "4 9\n2 7 11 15", 'out' => '0 1'],
                ['in' => "3 6\n3 2 4", 'out' => '1 2'],
            ],
        ],
        [
            'id' => 2,
            'title' => 'Valid Parentheses',
            'difficulty' => 'Easy',
            'category' => 'Stack',
            'acceptance' => '41.0%',
            'companies' => ['Microsoft', 'Adobe', 'Bloomberg'],
            'statement' => 'Given a string containing just parentheses characters, print YES if it is valid and NO otherwise.',
            'input' => 's',
            'output' => 'YES or NO',
            'starter_cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    string s;\n    cin >> s;\n\n    // your code\n\n    return 0;\n}\n",
            'starter_python' => "def solve():\n    s = input().strip()\n\n    # your code\n\nif __name__ == '__main__':\n    solve()\n",
            'tests' => [
                ['in' => '()[]{}', 'out' => 'YES'],
                ['in' => '(]', 'out' => 'NO'],
            ],
        ],
        [
            'id' => 3,
            'title' => 'Merge Intervals',
            'difficulty' => 'Medium',
            'category' => 'Intervals',
            'acceptance' => '49.8%',
            'companies' => ['Meta', 'Amazon', 'TikTok'],
            'statement' => 'Merge all overlapping intervals and print result as "l r" per line in ascending order.',
            'input' => "n\nl1 r1\n...\nln rn",
            'output' => 'merged intervals each on new line',
            'starter_cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    int n;\n    cin >> n;\n    vector<pair<int,int>> a(n);\n    for (int i = 0; i < n; i++) cin >> a[i].first >> a[i].second;\n\n    // your code\n\n    return 0;\n}\n",
            'starter_python' => "def solve():\n    n = int(input())\n    a = [tuple(map(int, input().split())) for _ in range(n)]\n\n    # your code\n\nif __name__ == '__main__':\n    solve()\n",
            'tests' => [
                ['in' => "4\n1 3\n2 6\n8 10\n15 18", 'out' => "1 6\n8 10\n15 18"],
                ['in' => "2\n1 4\n4 5", 'out' => '1 5'],
            ],
        ],
        [
            'id' => 4,
            'title' => 'LRU Cache (Ops)',
            'difficulty' => 'Medium',
            'category' => 'Design',
            'acceptance' => '44.1%',
            'companies' => ['Google', 'Apple', 'Uber'],
            'statement' => 'Implement LRU cache commands and print values for GET operations.',
            'input' => "capacity q\nthen q commands: PUT key val | GET key",
            'output' => 'one line per GET result',
            'starter_cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    int cap, q;\n    cin >> cap >> q;\n\n    // your code\n\n    return 0;\n}\n",
            'starter_python' => "def solve():\n    cap, q = map(int, input().split())\n\n    # your code\n\nif __name__ == '__main__':\n    solve()\n",
            'tests' => [
                ['in' => "2 9\nPUT 1 1\nPUT 2 2\nGET 1\nPUT 3 3\nGET 2\nPUT 4 4\nGET 1\nGET 3\nGET 4", 'out' => "1\n-1\n-1\n3\n4"],
            ],
        ],
        [
            'id' => 5,
            'title' => 'Binary Tree Level Order Traversal',
            'difficulty' => 'Medium',
            'category' => 'Tree',
            'acceptance' => '67.4%',
            'companies' => ['Amazon', 'Microsoft', 'Yandex'],
            'statement' => 'Given binary tree in array form (level-order with -1 as null), print level order traversal by lines.',
            'input' => "n\na1 a2 ... an",
            'output' => 'values by tree levels, one line per level',
            'starter_cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    int n;\n    cin >> n;\n    vector<int> a(n);\n    for (int i = 0; i < n; i++) cin >> a[i];\n\n    // your code\n\n    return 0;\n}\n",
            'starter_python' => "def solve():\n    n = int(input())\n    a = list(map(int, input().split()))\n\n    # your code\n\nif __name__ == '__main__':\n    solve()\n",
            'tests' => [
                ['in' => "7\n3 9 20 -1 -1 15 7", 'out' => "3\n9 20\n15 7"],
            ],
        ],
        [
            'id' => 6,
            'title' => 'Longest Substring Without Repeating Characters',
            'difficulty' => 'Medium',
            'category' => 'Sliding Window',
            'acceptance' => '36.9%',
            'companies' => ['Meta', 'Google', 'Booking'],
            'statement' => 'Given a string s, print length of longest substring without repeating characters.',
            'input' => 's',
            'output' => 'max length',
            'starter_cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    string s;\n    cin >> s;\n\n    // your code\n\n    return 0;\n}\n",
            'starter_python' => "def solve():\n    s = input().strip()\n\n    # your code\n\nif __name__ == '__main__':\n    solve()\n",
            'tests' => [
                ['in' => 'abcabcbb', 'out' => '3'],
                ['in' => 'bbbbb', 'out' => '1'],
            ],
        ],
        [
            'id' => 7,
            'title' => 'Word Break',
            'difficulty' => 'Medium',
            'category' => 'Dynamic Programming',
            'acceptance' => '47.5%',
            'companies' => ['Amazon', 'Snap', 'Microsoft'],
            'statement' => 'Given string s and dictionary words, print YES if s can be segmented, else NO.',
            'input' => "s\nm\nword1 ... wordm",
            'output' => 'YES or NO',
            'starter_cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    string s;\n    cin >> s;\n    int m;\n    cin >> m;\n    vector<string> d(m);\n    for (int i = 0; i < m; i++) cin >> d[i];\n\n    // your code\n\n    return 0;\n}\n",
            'starter_python' => "def solve():\n    s = input().strip()\n    m = int(input())\n    d = input().split()\n\n    # your code\n\nif __name__ == '__main__':\n    solve()\n",
            'tests' => [
                ['in' => "leetcode\n2\nleet code", 'out' => 'YES'],
                ['in' => "catsandog\n5\ncats dog sand and cat", 'out' => 'NO'],
            ],
        ],
        [
            'id' => 8,
            'title' => 'Median of Two Sorted Arrays',
            'difficulty' => 'Hard',
            'category' => 'Binary Search',
            'acceptance' => '42.3%',
            'companies' => ['Google', 'Meta', 'Apple'],
            'statement' => 'Given two sorted arrays, print median value.',
            'input' => "n m\na1..an\nb1..bm",
            'output' => 'median (integer or .5)',
            'starter_cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    int n, m;\n    cin >> n >> m;\n    vector<int> a(n), b(m);\n    for (int i = 0; i < n; i++) cin >> a[i];\n    for (int i = 0; i < m; i++) cin >> b[i];\n\n    // your code\n\n    return 0;\n}\n",
            'starter_python' => "def solve():\n    n, m = map(int, input().split())\n    a = list(map(int, input().split()))\n    b = list(map(int, input().split()))\n\n    # your code\n\nif __name__ == '__main__':\n    solve()\n",
            'tests' => [
                ['in' => "2 1\n1 3\n2", 'out' => '2'],
                ['in' => "2 2\n1 2\n3 4", 'out' => '2.5'],
            ],
        ],
        [
            'id' => 9,
            'title' => 'Serialize and Deserialize Binary Tree',
            'difficulty' => 'Hard',
            'category' => 'Tree',
            'acceptance' => '58.6%',
            'companies' => ['Amazon', 'LinkedIn', 'Meta'],
            'statement' => 'For given level-order tree with -1 nulls, print serialized form using comma-separated values and # for null.',
            'input' => "n\na1..an",
            'output' => 'serialization string',
            'starter_cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    int n;\n    cin >> n;\n    vector<int> a(n);\n    for (int i = 0; i < n; i++) cin >> a[i];\n\n    // your code\n\n    return 0;\n}\n",
            'starter_python' => "def solve():\n    n = int(input())\n    a = list(map(int, input().split()))\n\n    # your code\n\nif __name__ == '__main__':\n    solve()\n",
            'tests' => [
                ['in' => "7\n1 2 3 -1 -1 4 5", 'out' => '1,2,3,#,#,4,5,#,#,#,#'],
            ],
        ],
        [
            'id' => 10,
            'title' => 'Trapping Rain Water',
            'difficulty' => 'Hard',
            'category' => 'Two Pointers',
            'acceptance' => '63.1%',
            'companies' => ['Google', 'Microsoft', 'ByteDance'],
            'statement' => 'Given elevation map, compute total trapped water.',
            'input' => "n\nh1 h2 ... hn",
            'output' => 'single integer',
            'starter_cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    int n;\n    cin >> n;\n    vector<int> h(n);\n    for (int i = 0; i < n; i++) cin >> h[i];\n\n    // your code\n\n    return 0;\n}\n",
            'starter_python' => "def solve():\n    n = int(input())\n    h = list(map(int, input().split()))\n\n    # your code\n\nif __name__ == '__main__':\n    solve()\n",
            'tests' => [
                ['in' => "12\n0 1 0 2 1 0 1 3 2 1 2 1", 'out' => '6'],
                ['in' => "6\n4 2 0 3 2 5", 'out' => '9'],
            ],
        ],
    ];
}

function tfGetTrendingCompaniesData(): array
{
    return [
        ['name' => 'Amazon', 'hiring' => 128, 'focus' => ['Array', 'Graph', 'System Design']],
        ['name' => 'Google', 'hiring' => 96, 'focus' => ['DP', 'Tree', 'Math']],
        ['name' => 'Meta', 'hiring' => 87, 'focus' => ['Graph', 'String', 'Design']],
        ['name' => 'Microsoft', 'hiring' => 74, 'focus' => ['Tree', 'Backtracking', 'Greedy']],
        ['name' => 'Apple', 'hiring' => 53, 'focus' => ['Array', 'Binary Search', 'Concurrency']],
        ['name' => 'TikTok', 'hiring' => 46, 'focus' => ['Sliding Window', 'Hash Table', 'Greedy']],
    ];
}

function handleInterviewSubmit()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    try {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Ошибка запроса.']);
            return;
        }

        $rateLimit = tfConsumeRateLimit('interview_submit:' . (int) $userId, 10, 60);
        if (empty($rateLimit['ok'])) {
            echo json_encode(['success' => false, 'message' => 'Слишком много посылок. Повторите позже.']);
            return;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        @set_time_limit(300);

        $data = json_decode(file_get_contents('php://input'), true) ?: [];
        $problemId = (int) ($data['problem_id'] ?? 0);
        $language = tfJudge0NormalizeLanguage((string) ($data['language'] ?? 'cpp'));
        $code = (string) ($data['code'] ?? '');
        if ($problemId <= 0 || !in_array($language, ['cpp', 'python', 'c', 'csharp', 'java', 'js', 'ts', 'go', 'rust', 'php', 'ruby', 'swift', 'kotlin', 'scala', 'dart', 'sql'], true) || trim($code) === '' || mb_strlen($code) > 200000) {
            echo json_encode(['success' => false, 'message' => 'Неверные параметры']);
            return;
        }

        $selected = null;
        $interviewSource = function_exists('tfGetInterviewProblemsDataRich')
            ? tfGetInterviewProblemsDataRich()
            : tfGetInterviewProblemsData();
        foreach ($interviewSource as $problem) {
            if ((int) ($problem['id'] ?? 0) === $problemId) {
                $selected = $problem;
                break;
            }
        }
        if (!$selected) {
            echo json_encode(['success' => false, 'message' => 'Задача не найдена']);
            return;
        }

        $tests = tfNormalizeJudgeTests((array) ($selected['tests'] ?? []));
        if (empty($tests)) {
            echo json_encode(['success' => false, 'message' => 'Тесты не настроены']);
            return;
        }

        $run = tfRunPracticeWithJudge0($language, $code, $tests);
        if (empty($run['ok'])) {
            echo json_encode([
                'success' => false,
                'message' => tfBuildJudgeErrorMessage($run),
                'error' => (string) ($run['error'] ?? 'unknown'),
            ]);
            return;
        }

        $results = is_array($run['results'] ?? null) ? $run['results'] : [];
        $checksTotal = count($results);
        $checksPassed = 0;
        foreach ($results as $r) {
            if (!empty($r['passed'])) {
                $checksPassed++;
            }
        }

        echo json_encode([
            'success' => true,
            'passed' => !empty($run['passed']),
            'checks_passed' => $checksPassed,
            'checks_total' => $checksTotal,
            'results' => $results,
        ]);
    } catch (Throwable $e) {
        error_log('[INTERVIEW_SUBMIT] ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Не удалось выполнить проверку кода.']);
    }
}

function handleInterviewAiCoach()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $rateLimit = tfConsumeRateLimit('interview_ai:' . $userId, 6, 60);
    if (empty($rateLimit['ok'])) {
        echo json_encode(['success' => false, 'message' => 'Слишком много AI-запросов. Повторите позже.']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $mode = trim((string) ($data['mode'] ?? 'mock'));
    $role = trim((string) ($data['role'] ?? ''));
    $level = trim((string) ($data['level'] ?? ''));
    $company = trim((string) ($data['company'] ?? ''));
    $stack = trim((string) ($data['stack'] ?? ''));
    $focus = trim((string) ($data['focus'] ?? ''));
    $experience = trim((string) ($data['experience'] ?? ''));
    $answerDraft = trim((string) ($data['answer_draft'] ?? ''));
    $resumeSummary = trim((string) ($data['resume_summary'] ?? ''));
    $vacancyText = trim((string) ($data['vacancy_text'] ?? ''));
    $sessionId = (int) ($data['session_id'] ?? 0);

    if (!in_array($mode, ['mock', 'review', 'behavioral', 'plan'], true)) {
        echo json_encode(['success' => false, 'message' => 'Invalid mode']);
        return;
    }
    if ($role === '') {
        echo json_encode(['success' => false, 'message' => 'Укажите целевую роль']);
        return;
    }

    $sections = [
        "Role: " . $role,
        "Level: " . ($level !== '' ? $level : 'not specified'),
        "Company target: " . ($company !== '' ? $company : 'not specified'),
        "Stack: " . ($stack !== '' ? $stack : 'not specified'),
        "Focus areas: " . ($focus !== '' ? $focus : 'not specified'),
        "Experience summary: " . ($experience !== '' ? $experience : 'not specified'),
        "Resume summary: " . ($resumeSummary !== '' ? $resumeSummary : 'not specified'),
        "Vacancy / JD context: " . ($vacancyText !== '' ? $vacancyText : 'not specified'),
    ];
    if ($answerDraft !== '') {
        $sections[] = "Draft answer / current response:\n" . $answerDraft;
    }

    $modePrompts = [
        'mock' => "Create a super-practical mock interview kit for this candidate. Give:
1. A short readiness summary.
2. 7 interview questions in order from warm-up to deep technical.
3. For each question: what the interviewer is checking and what must be in a strong answer.
4. 3 red flags to avoid.
5. A final verdict: what to train first this week.
Use concise sections and actionable bullets.",
        'review' => "Review the candidate's draft answer like a strong interviewer coach. Give:
1. Score from 1 to 10.
2. What is strong.
3. What is weak / missing.
4. How to improve structure using STAR or technical reasoning.
5. A stronger rewritten version outline, but not a perfect final script.
6. 3 follow-up questions the interviewer will probably ask.",
        'behavioral' => "Prepare a behavioral interview coaching pack. Give:
1. 8 behavioral questions tailored to the role.
2. For each: what kind of story to tell.
3. STAR hints.
4. Common mistakes.
5. A confidence checklist before the interview.",
        'plan' => "Build a 14-day interview preparation sprint. Give:
1. Daily plan.
2. Technical practice blocks.
3. Behavioral practice blocks.
4. Resume / portfolio polishing tasks.
5. A final mock interview checkpoint on the last day.
Make it realistic and intense, like a premium AI interview coach."
    ];

    $prompt = "You are an elite AI interview coach inside CodeMaster. "
        . "Be concrete, structured, and tough but supportive. "
        . "Do not write generic fluff. Tailor the output to the user's goal.\n\n"
        . implode("\n", $sections)
        . "\n\nTask:\n"
        . $modePrompts[$mode];

    $response = generateAIResponse($prompt, $userId);
    if (!is_string($response) || trim($response) === '') {
        echo json_encode(['success' => false, 'message' => 'AI временно недоступен']);
        return;
    }

    $pdo = getDBConnection();
    ensureInterviewsSchema($pdo);
    $title = $role . ' · ' . ucfirst($mode);
    $contextPayload = [
        'mode' => $mode,
        'role' => $role,
        'level' => $level,
        'company' => $company,
        'stack' => $stack,
        'focus' => $focus,
        'experience' => $experience,
        'resume_summary' => $resumeSummary,
        'answer_draft' => $answerDraft,
        'vacancy_text' => $vacancyText,
    ];
    $contextJson = json_encode($contextPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($contextJson === false) {
        $contextJson = '{}';
    }

    if ($sessionId > 0) {
        $stmt = $pdo->prepare("UPDATE interview_ai_sessions SET mode = ?, title = ?, context_json = ?, output_text = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->execute([$mode, $title, $contextJson, $response, $sessionId, $userId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO interview_ai_sessions (user_id, mode, title, context_json, output_text) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $mode, $title, $contextJson, $response]);
        $sessionId = (int) $pdo->lastInsertId();
    }

    echo json_encode([
        'success' => true,
        'message' => 'План готов',
        'content' => $response,
        'session_id' => $sessionId,
    ]);
}

function handleInterviewAiHistory()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    $pdo = getDBConnection();
    ensureInterviewsSchema($pdo);
    $stmt = $pdo->prepare("SELECT id, mode, title, updated_at FROM interview_ai_sessions WHERE user_id = ? ORDER BY updated_at DESC, id DESC LIMIT 24");
    $stmt->execute([$userId]);
    echo json_encode(['success' => true, 'sessions' => $stmt->fetchAll() ?: []]);
}

function handleInterviewAiSessionGet()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    $sessionId = (int) ($_GET['session_id'] ?? 0);
    if ($userId <= 0 || $sessionId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        return;
    }
    $pdo = getDBConnection();
    ensureInterviewsSchema($pdo);
    $stmt = $pdo->prepare("SELECT * FROM interview_ai_sessions WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$sessionId, $userId]);
    $session = $stmt->fetch();
    if (!$session) {
        echo json_encode(['success' => false, 'message' => 'Session not found']);
        return;
    }
    $context = [];
    if (!empty($session['context_json'])) {
        $decoded = json_decode((string) $session['context_json'], true);
        if (is_array($decoded)) {
            $context = $decoded;
        }
    }
    $msgStmt = $pdo->prepare("SELECT sender, message_text, created_at FROM interview_ai_messages WHERE session_id = ? ORDER BY id ASC");
    $msgStmt->execute([$sessionId]);
    echo json_encode([
        'success' => true,
        'session' => [
            'id' => (int) ($session['id'] ?? 0),
            'mode' => (string) ($session['mode'] ?? 'mock'),
            'title' => (string) ($session['title'] ?? ''),
            'output_text' => (string) ($session['output_text'] ?? ''),
            'context' => $context,
            'messages' => $msgStmt->fetchAll() ?: [],
        ]
    ]);
}

function handleInterviewAiChat()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    $rateLimit = tfConsumeRateLimit('interview_ai_chat:' . $userId, 8, 60);
    if (empty($rateLimit['ok'])) {
        echo json_encode(['success' => false, 'message' => 'Слишком много AI-запросов. Повторите позже.']);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $sessionId = (int) ($data['session_id'] ?? 0);
    $message = trim((string) ($data['message'] ?? ''));
    if ($sessionId <= 0 || $message === '') {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        return;
    }
    $pdo = getDBConnection();
    ensureInterviewsSchema($pdo);
    $stmt = $pdo->prepare("SELECT * FROM interview_ai_sessions WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$sessionId, $userId]);
    $session = $stmt->fetch();
    if (!$session) {
        echo json_encode(['success' => false, 'message' => 'Session not found']);
        return;
    }
    $context = [];
    if (!empty($session['context_json'])) {
        $decoded = json_decode((string) $session['context_json'], true);
        if (is_array($decoded)) {
            $context = $decoded;
        }
    }
    $historyStmt = $pdo->prepare("SELECT sender, message_text FROM interview_ai_messages WHERE session_id = ? ORDER BY id DESC LIMIT 8");
    $historyStmt->execute([$sessionId]);
    $history = array_reverse($historyStmt->fetchAll() ?: []);
    $historyText = [];
    foreach ($history as $row) {
        $prefix = (($row['sender'] ?? '') === 'ai') ? 'Coach' : 'User';
        $historyText[] = $prefix . ': ' . trim((string) ($row['message_text'] ?? ''));
    }
    $prompt = "You are a premium AI mock interviewer. Stay in interviewer-coach mode. "
        . "Ask sharp follow-up questions, critique weak claims, and help the candidate improve. "
        . "Do not go off-topic.\n\n"
        . "Session context:\n" . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        . "\n\nRecent chat:\n" . implode("\n", $historyText)
        . "\n\nUser message:\n" . $message
        . "\n\nReply as a strong interviewer coach.";
    $response = generateAIResponse($prompt, $userId);
    $ins = $pdo->prepare("INSERT INTO interview_ai_messages (session_id, sender, message_text) VALUES (?, ?, ?)");
    $ins->execute([$sessionId, 'user', $message]);
    $ins->execute([$sessionId, 'ai', (string) $response]);
    echo json_encode(['success' => true, 'reply' => (string) $response]);
}

function handleInterviewAiScore()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    $rateLimit = tfConsumeRateLimit('interview_ai_score:' . $userId, 6, 60);
    if (empty($rateLimit['ok'])) {
        echo json_encode(['success' => false, 'message' => 'Слишком много AI-запросов. Повторите позже.']);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $role = trim((string) ($data['role'] ?? ''));
    $answer = trim((string) ($data['answer'] ?? ''));
    if ($role === '' || $answer === '') {
        echo json_encode(['success' => false, 'message' => 'Нужны роль и ответ']);
        return;
    }
    $prompt = "Score this interview answer for the role {$role}. "
        . "Return concise coaching with sections: Overall score /10, Structure, Clarity, Technical depth, Confidence, What to fix next. "
        . "Do not be generic.\n\nAnswer:\n{$answer}";
    $response = generateAIResponse($prompt, $userId);
    echo json_encode(['success' => true, 'content' => (string) $response]);
}

function handleCommunityCreatePost()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $missing = tfValidateRequiredFields($data, ['title', 'content']);
    if (!empty($missing)) {
        echo json_encode(['success' => false, 'message' => 'Missing fields: ' . implode(', ', $missing)]);
        return;
    }
    $title = trim((string) ($data['title'] ?? ''));
    $content = trim((string) ($data['content'] ?? ''));
    if ($title === '' || $content === '') {
        echo json_encode(['success' => false, 'message' => 'Заполните заголовок и текст']);
        return;
    }
    if (mb_strlen($title) > 255 || mb_strlen($content) > 10000) {
        echo json_encode(['success' => false, 'message' => 'Слишком длинный текст']);
        return;
    }

    $pdo = getDBConnection();
    ensureCommunitySchema($pdo);
    $stmt = $pdo->prepare("INSERT INTO community_posts (user_id, title, content, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    $stmt->execute([$userId, $title, $content]);

    echo json_encode(['success' => true, 'post_id' => (int) $pdo->lastInsertId()]);
}

function handleCommunityCreateComment()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $missing = tfValidateRequiredFields($data, ['post_id', 'content']);
    if (!empty($missing)) {
        echo json_encode(['success' => false, 'message' => 'Missing fields: ' . implode(', ', $missing)]);
        return;
    }
    $postId = (int) ($data['post_id'] ?? 0);
    $content = trim((string) ($data['content'] ?? ''));
    if ($postId <= 0 || $content === '') {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    if (mb_strlen($content) > 5000) {
        echo json_encode(['success' => false, 'message' => 'Слишком длинный комментарий']);
        return;
    }

    $pdo = getDBConnection();
    ensureCommunitySchema($pdo);

    $stmt = $pdo->prepare("SELECT id FROM community_posts WHERE id = ? LIMIT 1");
    $stmt->execute([$postId]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Тема не найдена']);
        return;
    }

    $stmt = $pdo->prepare("INSERT INTO community_comments (post_id, user_id, content, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    $stmt->execute([$postId, $userId, $content]);
    echo json_encode(['success' => true, 'comment_id' => (int) $pdo->lastInsertId()]);
}

function handleCommunityUpdatePost()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $missing = tfValidateRequiredFields($data, ['post_id', 'title', 'content']);
    if (!empty($missing)) {
        echo json_encode(['success' => false, 'message' => 'Missing fields: ' . implode(', ', $missing)]);
        return;
    }
    $postId = (int) ($data['post_id'] ?? 0);
    $title = trim((string) ($data['title'] ?? ''));
    $content = trim((string) ($data['content'] ?? ''));
    if ($postId <= 0 || $title === '' || $content === '') {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    if (mb_strlen($title) > 255 || mb_strlen($content) > 10000) {
        echo json_encode(['success' => false, 'message' => 'Слишком длинный текст']);
        return;
    }

    $pdo = getDBConnection();
    ensureCommunitySchema($pdo);
    $stmt = $pdo->prepare("SELECT user_id FROM community_posts WHERE id = ? LIMIT 1");
    $stmt->execute([$postId]);
    $ownerId = (int) $stmt->fetchColumn();
    if ($ownerId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Тема не найдена']);
        return;
    }
    if ($ownerId !== $userId) {
        echo json_encode(['success' => false, 'message' => 'Нет доступа']);
        return;
    }

    $stmt = $pdo->prepare("UPDATE community_posts SET title = ?, content = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$title, $content, $postId]);
    echo json_encode(['success' => true]);
}

function handleCommunityDeletePost()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $postId = (int) ($data['post_id'] ?? 0);
    if ($postId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $pdo = getDBConnection();
    ensureCommunitySchema($pdo);
    $stmt = $pdo->prepare("SELECT user_id FROM community_posts WHERE id = ? LIMIT 1");
    $stmt->execute([$postId]);
    $ownerId = (int) $stmt->fetchColumn();
    if ($ownerId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Тема не найдена']);
        return;
    }
    if ($ownerId !== $userId) {
        echo json_encode(['success' => false, 'message' => 'Нет доступа']);
        return;
    }

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM community_comments WHERE post_id = ?");
        $stmt->execute([$postId]);
        $stmt = $pdo->prepare("DELETE FROM community_posts WHERE id = ?");
        $stmt->execute([$postId]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Ошибка удаления']);
        return;
    }

    echo json_encode(['success' => true]);
}

function handleCommunityLikePost()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $postId = (int) ($data['post_id'] ?? 0);
    if ($postId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }

    $pdo = getDBConnection();
    ensureCommunitySchema($pdo);

    $stmt = $pdo->prepare("SELECT id FROM community_posts WHERE id = ? LIMIT 1");
    $stmt->execute([$postId]);
    if (!(int) $stmt->fetchColumn()) {
        echo json_encode(['success' => false, 'message' => 'Тема не найдена']);
        return;
    }

    $inserted = false;
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT IGNORE INTO community_post_likes (post_id, user_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$postId, $userId]);
        $inserted = $stmt->rowCount() > 0;
        if ($inserted) {
            $stmt = $pdo->prepare("UPDATE community_posts SET likes_count = likes_count + 1 WHERE id = ?");
            $stmt->execute([$postId]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Ошибка лайка']);
        return;
    }

    $stmt = $pdo->prepare("SELECT likes_count FROM community_posts WHERE id = ? LIMIT 1");
    $stmt->execute([$postId]);
    $likes = (int) $stmt->fetchColumn();
    echo json_encode([
        'success' => true,
        'likes' => $likes,
        'liked' => $inserted,
        'already_liked' => !$inserted,
    ]);
}

function handleCommunityViewPost()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $postId = (int) ($data['post_id'] ?? 0);
    if ($postId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    ensureCommunitySchema($pdo);
    $stmt = $pdo->prepare("UPDATE community_posts SET views_count = views_count + 1 WHERE id = ?");
    $stmt->execute([$postId]);

    $stmt = $pdo->prepare("
        SELECT
            cp.*,
            u.name as author_name,
            u.avatar as author_avatar,
            (SELECT COUNT(*) FROM community_comments cc WHERE cc.post_id = cp.id) as comments_count,
            (SELECT COUNT(*) FROM community_post_likes cpl WHERE cpl.post_id = cp.id AND cpl.user_id = ?) as liked_by_me
        FROM community_posts cp
        INNER JOIN users u ON u.id = cp.user_id
        WHERE cp.id = ? AND u.is_blocked = FALSE
        LIMIT 1
    ");
    $viewerId = (int) ($_SESSION['user_id'] ?? 0);
    $stmt->execute([$viewerId, $postId]);
    $post = $stmt->fetch();
    if (!$post) {
        echo json_encode(['success' => false, 'message' => 'Тема не найдена']);
        return;
    }

    $stmt = $pdo->prepare("
        SELECT
            cc.*,
            u.name as author_name,
            u.avatar as author_avatar
        FROM community_comments cc
        INNER JOIN users u ON u.id = cc.user_id
        WHERE cc.post_id = ? AND u.is_blocked = FALSE
        ORDER BY cc.created_at ASC, cc.id ASC
    ");
    $stmt->execute([$postId]);
    $comments = $stmt->fetchAll() ?: [];

    echo json_encode(['success' => true, 'post' => $post, 'comments' => $comments]);
}

function handleAdminGetContest()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureContestsSchema($pdo);
    $stmt = $pdo->prepare("SELECT * FROM contests WHERE id = ?");
    $stmt->execute([$id]);
    $contest = $stmt->fetch();
    if (!$contest) {
        echo json_encode(['success' => false, 'message' => 'Контест не найден']);
        return;
    }
    echo json_encode(['success' => true, 'contest' => $contest]);
}

function tfAdminParseDateTime(?string $value): ?string
{
    $raw = trim((string) $value);
    if ($raw === '') {
        return null;
    }
    $normalized = str_replace('T', ' ', $raw);
    if (strlen($normalized) === 16) {
        $normalized .= ':00';
    }
    $ts = strtotime($normalized);
    if ($ts === false) {
        return null;
    }
    return date('Y-m-d H:i:s', $ts);
}

function tfAdminNormalizeContestSlug($value): ?string
{
    $slug = mb_strtolower(trim((string) $value));
    if ($slug === '') {
        return null;
    }
    $slug = preg_replace('/[^a-z0-9\-]+/u', '-', $slug);
    $slug = preg_replace('/-+/', '-', (string) $slug);
    $slug = trim((string) $slug, '-');
    if ($slug === '') {
        return null;
    }
    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
        return null;
    }
    return $slug;
}

function tfAdminNormalizeContestTests($testsRaw): ?string
{
    if (is_array($testsRaw)) {
        $decoded = $testsRaw;
    } else {
        $decoded = json_decode((string) $testsRaw, true);
    }
    $tests = tfNormalizeJudgeTests(is_array($decoded) ? $decoded : []);
    if (empty($tests)) {
        return null;
    }
    return json_encode($tests, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: null;
}

function tfAdminTrimUtf8ByBytes(string $text, int $maxBytes, string $suffix = ''): string
{
    $text = str_replace("\r\n", "\n", $text);
    $text = str_replace("\r", "\n", $text);
    if ($maxBytes <= 0 || $text === '') {
        return '';
    }
    if (strlen($text) <= $maxBytes) {
        return $text;
    }
    $suffixBytes = strlen($suffix);
    $targetBytes = max(0, $maxBytes - $suffixBytes);
    if (function_exists('mb_strcut')) {
        $cut = (string) mb_strcut($text, 0, $targetBytes, 'UTF-8');
    } else {
        $cut = substr($text, 0, $targetBytes);
    }
    return rtrim($cut) . $suffix;
}

function tfAdminFitJudgeTestsForImport(array $tests, array $fallbackTests = [], int $maxBytes = 49152, int $maxTests = 12): array
{
    $warnings = [];

    $primary = tfNormalizeJudgeTests($tests);
    $fallback = tfNormalizeJudgeTests($fallbackTests);

    if (count($primary) > $maxTests) {
        $warnings[] = "Тесты сокращены до первых {$maxTests} кейсов для импорта.";
        $primary = array_slice($primary, 0, $maxTests);
    }

    $primaryJson = tfAdminNormalizeContestTests($primary);
    if ($primaryJson !== null && strlen($primaryJson) <= $maxBytes) {
        return ['tests_json' => $primaryJson, 'warnings' => $warnings];
    }

    if (!empty($fallback)) {
        if (count($fallback) > $maxTests) {
            $fallback = array_slice($fallback, 0, $maxTests);
        }
        $fallbackJson = tfAdminNormalizeContestTests($fallback);
        if ($fallbackJson !== null && strlen($fallbackJson) <= $maxBytes) {
            $warnings[] = 'Файловые тесты слишком большие для хостинга, импортированы примеры из statement.xml.';
            return ['tests_json' => $fallbackJson, 'warnings' => $warnings];
        }
    }

    $count = count($primary);
    if ($count > 1) {
        $low = 1;
        $high = $count;
        $bestJson = null;
        $bestCount = 0;
        while ($low <= $high) {
            $mid = intdiv($low + $high, 2);
            $slice = array_slice($primary, 0, $mid);
            $sliceJson = tfAdminNormalizeContestTests($slice);
            if ($sliceJson !== null && strlen($sliceJson) <= $maxBytes) {
                $bestJson = $sliceJson;
                $bestCount = $mid;
                $low = $mid + 1;
            } else {
                $high = $mid - 1;
            }
        }
        if ($bestJson !== null && $bestCount > 0) {
            $warnings[] = "Тесты урезаны до {$bestCount} кейсов, чтобы уложиться в лимиты MySQL на хостинге.";
            return ['tests_json' => $bestJson, 'warnings' => $warnings];
        }
    }

    return [
        'tests_json' => null,
        'warnings' => array_merge($warnings, ['Тесты слишком большие для импорта на текущем хостинге.']),
    ];
}

function tfAdminPrepareTaskForDbImport(array $task, string $mode = 'normal'): array
{
    $isAggressive = ($mode === 'aggressive');
    $suffix = "\n\n[Truncated during import]";
    $statementLimit = $isAggressive ? 6000 : 16000;
    $inputLimit = $isAggressive ? 2000 : 4000;
    $outputLimit = $isAggressive ? 2000 : 4000;
    $testsBytes = $isAggressive ? 8192 : 24576;
    $testsCount = $isAggressive ? 4 : 8;

    $warnings = [];
    foreach ((array) ($task['warnings'] ?? []) as $warning) {
        $warnings[] = (string) $warning;
    }

    $statement = (string) ($task['statement'] ?? '');
    if (strlen($statement) > $statementLimit) {
        $warnings[] = $isAggressive
            ? 'Условие задачи сильно сокращено для прохождения лимитов хостинга.'
            : 'Условие задачи сокращено для импорта на хостинг.';
    }
    $inputSpec = (string) ($task['input_spec'] ?? '');
    if (strlen($inputSpec) > $inputLimit) {
        $warnings[] = 'Описание входных данных сокращено при импорте.';
    }
    $outputSpec = (string) ($task['output_spec'] ?? '');
    if (strlen($outputSpec) > $outputLimit) {
        $warnings[] = 'Описание выходных данных сокращено при импорте.';
    }

    $decodedTests = json_decode((string) ($task['tests_json'] ?? '[]'), true);
    $testsPack = tfAdminFitJudgeTestsForImport(is_array($decodedTests) ? $decodedTests : [], [], $testsBytes, $testsCount);
    foreach ((array) ($testsPack['warnings'] ?? []) as $warning) {
        $warnings[] = (string) $warning;
    }

    return [
        'title' => tfAdminTrimUtf8ByBytes((string) ($task['title'] ?? ''), 240, ''),
        'difficulty' => tfAdminNormalizeDifficulty((string) ($task['difficulty'] ?? 'easy')),
        'statement' => tfAdminTrimUtf8ByBytes($statement, $statementLimit, $suffix),
        'input_spec' => tfAdminTrimUtf8ByBytes($inputSpec, $inputLimit, $suffix),
        'output_spec' => tfAdminTrimUtf8ByBytes($outputSpec, $outputLimit, $suffix),
        'starter_cpp' => (string) ($task['starter_cpp'] ?? ''),
        'starter_python' => (string) ($task['starter_python'] ?? ''),
        'tests_json' => $testsPack['tests_json'] ?? null,
        'warnings' => array_values(array_unique(array_filter($warnings, static fn($item) => trim((string) $item) !== ''))),
    ];
}

function tfAdminDefaultStarterCode(string $language): string
{
    if (function_exists('tfSeedPackStarterCode')) {
        return (string) tfSeedPackStarterCode($language);
    }
    $fallbacks = [
        'cpp' => "#include <bits/stdc++.h>\nusing namespace std;\n\nint main() {\n    return 0;\n}\n",
        'python' => "def solve():\n    pass\n\nif __name__ == '__main__':\n    solve()\n",
        'c' => "#include <stdio.h>\n\nint main(void) {\n    return 0;\n}\n",
        'csharp' => "using System;\n\npublic class Program {\n    public static void Main() {\n    }\n}\n",
        'java' => "public class Main {\n    public static void main(String[] args) {\n    }\n}\n",
    ];
    return $fallbacks[$language] ?? '';
}

function tfAdminNormalizeDifficulty(string $difficulty): string
{
    $value = strtolower(trim($difficulty));
    return in_array($value, ['easy', 'medium', 'hard'], true) ? $value : 'easy';
}

function tfAdminNormalizeInterviewPrepPayload(array $data): array
{
    $title = normalizeMojibakeText(trim((string) ($data['title'] ?? '')));
    $slug = tfAdminNormalizeContestSlug((string) ($data['slug'] ?? ''));
    $difficulty = tfAdminNormalizeDifficulty((string) ($data['difficulty'] ?? 'easy'));
    $category = normalizeMojibakeText(trim((string) ($data['category'] ?? 'General')));
    if ($category === '') {
        $category = 'General';
    }
    return [
        'source_type' => 'contest_task',
        'source_task_id' => (int) ($data['source_task_id'] ?? 0) ?: null,
        'title' => $title,
        'slug' => $slug,
        'difficulty' => $difficulty,
        'category' => $category,
        'statement' => normalizeMojibakeText(trim((string) ($data['statement'] ?? ''))),
        'input_spec' => normalizeMojibakeText(trim((string) ($data['input_spec'] ?? ''))),
        'output_spec' => normalizeMojibakeText(trim((string) ($data['output_spec'] ?? ''))),
        'starter_cpp' => (string) ($data['starter_cpp'] ?? tfAdminDefaultStarterCode('cpp')),
        'starter_python' => (string) ($data['starter_python'] ?? tfAdminDefaultStarterCode('python')),
        'tests_json' => tfAdminNormalizeContestTests($data['tests_json'] ?? '[]'),
        'sort_order' => max(0, (int) ($data['sort_order'] ?? 0)),
        'is_active' => !empty($data['is_active']) ? 1 : 0,
    ];
}

function tfAdminContestTaskToInterviewPrepRow(array $task): array
{
    return [
        'source_task_id' => (int) ($task['id'] ?? 0),
        'title' => normalizeMojibakeText((string) ($task['title'] ?? '')),
        'slug' => tfAdminNormalizeContestSlug((string) ($task['title'] ?? '')),
        'difficulty' => tfAdminNormalizeDifficulty((string) ($task['difficulty'] ?? 'easy')),
        'category' => normalizeMojibakeText((string) ($task['contest_title'] ?? 'Contest')),
        'statement' => normalizeMojibakeText((string) ($task['statement'] ?? '')),
        'input_spec' => normalizeMojibakeText((string) ($task['input_spec'] ?? '')),
        'output_spec' => normalizeMojibakeText((string) ($task['output_spec'] ?? '')),
        'starter_cpp' => (string) ($task['starter_cpp'] ?? tfAdminDefaultStarterCode('cpp')),
        'starter_python' => (string) ($task['starter_python'] ?? tfAdminDefaultStarterCode('python')),
        'tests_json' => tfAdminNormalizeContestTests($task['tests_json'] ?? '[]'),
        'sort_order' => max(0, (int) ($task['order_num'] ?? 0)),
        'is_active' => 1,
    ];
}

function tfAdminXmlNodePlainText($node): string
{
    if (!$node) {
        return '';
    }
    $xml = $node instanceof SimpleXMLElement ? $node->asXML() : (string) $node;
    if (!is_string($xml) || $xml === '') {
        return '';
    }
    $text = preg_replace('/<\s*br\s*\/?>/i', "\n", $xml);
    $text = preg_replace('/<\/p>/i', "\n", (string) $text);
    $text = strip_tags((string) $text);
    $text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = normalizeMojibakeText((string) $text);
    $text = preg_replace("/\r\n?/", "\n", (string) $text);
    $text = preg_replace("/\n{3,}/", "\n\n", (string) $text);
    return trim((string) $text);
}

function tfAdminSelectStatementNode(SimpleXMLElement $xml): ?SimpleXMLElement
{
    if (!isset($xml->statement)) {
        return null;
    }
    $candidates = [];
    foreach ($xml->statement as $stmt) {
        if ($stmt instanceof SimpleXMLElement) {
            $candidates[] = $stmt;
        }
    }
    if (empty($candidates)) {
        return null;
    }
    $score = static function (SimpleXMLElement $stmt): int {
        $lang = strtolower((string) ($stmt['language'] ?? ''));
        if ($lang === 'ru_ru' || $lang === 'ru') {
            return 3;
        }
        if ($lang === 'en_us' || $lang === 'en') {
            return 2;
        }
        return 1;
    };
    usort($candidates, static function (SimpleXMLElement $a, SimpleXMLElement $b) use ($score): int {
        return $score($b) <=> $score($a);
    });
    return $candidates[0];
}

function tfAdminReadTextFilePreserveSpaces(string $path): string
{
    $raw = @file_get_contents($path);
    if (!is_string($raw)) {
        return '';
    }
    if (substr($raw, 0, 3) === "\xEF\xBB\xBF") {
        $raw = substr($raw, 3);
    }
    $raw = str_replace("\r\n", "\n", $raw);
    $raw = str_replace("\r", "\n", $raw);
    return preg_replace("/\n+$/", '', $raw) ?? '';
}

function tfAdminBuildTestsFromDirectory(string $testsDir): array
{
    if (!is_dir($testsDir)) {
        return [];
    }
    $inputs = glob($testsDir . DIRECTORY_SEPARATOR . '*.dat') ?: [];
    sort($inputs, SORT_NATURAL | SORT_FLAG_CASE);
    $tests = [];
    foreach ($inputs as $inputFile) {
        $base = pathinfo($inputFile, PATHINFO_FILENAME);
        $answerFile = $testsDir . DIRECTORY_SEPARATOR . $base . '.ans';
        if (!is_file($answerFile)) {
            continue;
        }
        $stdin = tfAdminReadTextFilePreserveSpaces($inputFile);
        $stdout = tfAdminReadTextFilePreserveSpaces($answerFile);
        $tests[] = [
            'stdin' => $stdin,
            'expected_stdout' => $stdout,
            'timeout_sec' => 3,
        ];
    }
    return $tests;
}

function tfAdminResolvePackagePath(string $path): ?string
{
    $raw = trim($path);
    if ($raw === '') {
        return null;
    }
    $root = realpath(__DIR__);
    if ($root === false) {
        return null;
    }
    $candidate = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $raw);
    $full = realpath($root . DIRECTORY_SEPARATOR . ltrim($candidate, DIRECTORY_SEPARATOR));
    if ($full === false || strpos($full, $root) !== 0) {
        return null;
    }
    return $full;
}

function tfAdminParseContestTaskPackage(string $packagePath): array
{
    $fullPath = tfAdminResolvePackagePath($packagePath);
    if ($fullPath === null || !is_dir($fullPath)) {
        throw new RuntimeException('Папка задачи не найдена');
    }
    $statementFile = $fullPath . DIRECTORY_SEPARATOR . 'statement.xml';
    $testsDir = $fullPath . DIRECTORY_SEPARATOR . 'tests';
    if (!is_file($statementFile)) {
        throw new RuntimeException('Р’ папке нет statement.xml');
    }
    $xmlRaw = file_get_contents($statementFile);
    if (!is_string($xmlRaw) || trim($xmlRaw) === '') {
        throw new RuntimeException('statement.xml пустой');
    }
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($xmlRaw);
    libxml_clear_errors();
    if (!$xml) {
        throw new RuntimeException('Не удалось разобрать statement.xml');
    }
    $statementNode = tfAdminSelectStatementNode($xml);
    if (!$statementNode) {
        throw new RuntimeException('Р’ statement.xml нет блока <statement>');
    }
    $title = normalizeMojibakeText(trim((string) ($statementNode->title ?? '')));
    $description = tfAdminXmlNodePlainText($statementNode->description ?? null);
    $inputSpec = tfAdminXmlNodePlainText($statementNode->input_format ?? ($statementNode->input ?? null));
    $outputSpec = tfAdminXmlNodePlainText($statementNode->output_format ?? ($statementNode->output ?? null));
    if ($title === '') {
        $title = basename($fullPath);
    }
    $tests = tfAdminBuildTestsFromDirectory($testsDir);
    $exampleTests = [];
    if (isset($xml->examples->example)) {
        foreach ($xml->examples->example as $example) {
            $stdin = tfAdminXmlNodePlainText($example->input ?? null);
            $stdout = tfAdminXmlNodePlainText($example->output ?? null);
            if ($stdin === '' && $stdout === '') {
                continue;
            }
            $exampleTests[] = [
                'stdin' => $stdin,
                'expected_stdout' => $stdout,
                'timeout_sec' => 3,
            ];
        }
    }
    if (empty($tests)) {
        $tests = $exampleTests;
    }
    $testsPack = tfAdminFitJudgeTestsForImport($tests, $exampleTests);
    $testsJson = $testsPack['tests_json'] ?? null;
    if ($testsJson === null) {
        $warningText = trim(implode(' ', (array) ($testsPack['warnings'] ?? [])));
        throw new RuntimeException($warningText !== '' ? $warningText : 'Не удалось собрать тесты из пакета');
    }
    return [
        'title' => $title,
        'difficulty' => 'easy',
        'statement' => $description,
        'input_spec' => $inputSpec,
        'output_spec' => $outputSpec,
        'starter_cpp' => tfAdminDefaultStarterCode('cpp'),
        'starter_python' => tfAdminDefaultStarterCode('python'),
        'tests_json' => $testsJson,
        'warnings' => $testsPack['warnings'] ?? [],
        'package_path' => $packagePath,
    ];
}

function tfAdminFindTaskPackages(string $basePath): array
{
    $fullBase = tfAdminResolvePackagePath($basePath);
    if ($fullBase === null || !is_dir($fullBase)) {
        return [];
    }
    $packages = [];
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($fullBase, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iter as $file) {
        if (!$file->isFile()) {
            continue;
        }
        if (strtolower($file->getFilename()) !== 'statement.xml') {
            continue;
        }
        $packages[] = $file->getPath();
    }
    $packages = array_values(array_unique($packages));
    sort($packages, SORT_NATURAL | SORT_FLAG_CASE);
    return $packages;
}

function tfAdminCollectEjudgePackages(array $paths): array
{
    $packages = [];
    $errors = [];
    foreach ($paths as $pathRaw) {
        $path = trim((string) $pathRaw);
        if ($path === '') {
            continue;
        }
        $found = tfAdminFindTaskPackages($path);
        if (empty($found)) {
            $errors[] = "Не найдены задачи в: {$path}";
            continue;
        }
        $packages = array_merge($packages, $found);
    }
    $packages = array_values(array_unique($packages));
    sort($packages, SORT_NATURAL | SORT_FLAG_CASE);
    return [$packages, $errors];
}

function handleAdminEjudgeScan()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    if (!ensureAdmin()) {
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $paths = $data['paths'] ?? [];
    if (!is_array($paths) || empty($paths)) {
        $paths = ['tasks'];
    }

    [$packages, $errors] = tfAdminCollectEjudgePackages($paths);
    $preview = [];
    foreach (array_slice($packages, 0, 30) as $full) {
        $rel = tfAdminMakeRelativePath($full);
        if ($rel === null) {
            $errors[] = "Путь вне проекта: {$full}";
            continue;
        }
        try {
            $task = tfAdminParseContestTaskPackage($rel);
            $preview[] = [
                'title' => $task['title'] ?? '',
                'path' => $rel,
            ];
        } catch (Throwable $e) {
            $errors[] = "{$rel}: " . $e->getMessage();
        }
    }

    echo json_encode([
        'success' => true,
        'found' => count($packages),
        'preview' => $preview,
        'errors' => $errors,
    ]);
}

function handleAdminEjudgeImport()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    if (!ensureAdmin()) {
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $paths = $data['paths'] ?? [];
    if (!is_array($paths) || empty($paths)) {
        $paths = ['tasks'];
    }
    $importInterview = !empty($data['import_interview']);
    $importContest = !empty($data['import_contest']);
    $contestId = (int) ($data['contest_id'] ?? 0);
    $category = trim((string) ($data['interview_category'] ?? 'Ejudge'));
    if ($category === '') {
        $category = 'Ejudge';
    }
    $difficulty = tfAdminNormalizeDifficulty((string) ($data['difficulty'] ?? 'easy'));

    if (!$importInterview && !$importContest) {
        echo json_encode(['success' => false, 'message' => 'Выберите хотя бы один тип импорта']);
        return;
    }
    if ($importContest && $contestId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Выберите контест для импорта']);
        return;
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    @set_time_limit(300);
    [$packages, $errors] = tfAdminCollectEjudgePackages($paths);

    $connectImportDb = static function (): PDO {
        $pdo = getDBConnection();
        ensureContestsSchema($pdo);
        return $pdo;
    };

    $pdo = $connectImportDb();
    if ($importContest) {
        $contestCheck = $pdo->prepare("SELECT id FROM contests WHERE id = ? LIMIT 1");
        $contestCheck->execute([$contestId]);
        if (!(int) $contestCheck->fetchColumn()) {
            echo json_encode(['success' => false, 'message' => 'Контест не найден']);
            return;
        }
    }

    $addedContest = 0;
    $addedInterview = 0;
    $skipped = 0;

    $nextOrderContest = 1;
    if ($importContest) {
        $nextOrderContest = (int) ($pdo->query("SELECT COALESCE(MAX(order_num), 0) AS mx FROM contest_tasks WHERE contest_id = {$contestId}")->fetch()['mx'] ?? 0);
        $nextOrderContest++;
    }
    $nextOrderInterview = (int) ($pdo->query("SELECT COALESCE(MAX(sort_order), 0) AS mx FROM interview_prep_tasks")->fetch()['mx'] ?? 0);
    $nextOrderInterview++;

    foreach ($packages as $full) {
        $rel = tfAdminMakeRelativePath($full);
        if ($rel === null) {
            $errors[] = "Путь вне проекта: {$full}";
            continue;
        }
        try {
            $task = tfAdminParseContestTaskPackage($rel);
            $preparedTask = tfAdminPrepareTaskForDbImport($task, 'normal');
            foreach ((array) ($preparedTask['warnings'] ?? []) as $warning) {
                $errors[] = "{$rel}: {$warning}";
            }
            $title = (string) ($preparedTask['title'] ?? '');
            $statement = (string) ($preparedTask['statement'] ?? '');
            if ($title === '' || $statement === '') {
                $skipped++;
                continue;
            }

            $contestTask = null;
            if ($importContest) {
                $testsJson = $preparedTask['tests_json'] ?? null;
                if ($testsJson === null) {
                    $errors[] = "{$rel}: Некорректные тесты";
                } else {
                    $contestTask = [
                        'contest_id' => $contestId,
                        'title' => $title,
                        'difficulty' => $difficulty,
                        'statement' => $statement,
                        'input_spec' => (string) ($preparedTask['input_spec'] ?? ''),
                        'output_spec' => (string) ($preparedTask['output_spec'] ?? ''),
                        'starter_cpp' => (string) ($preparedTask['starter_cpp'] ?? ''),
                        'starter_python' => (string) ($preparedTask['starter_python'] ?? ''),
                        'tests_json' => $testsJson,
                    ];
                }
            }

            $interviewTask = null;
            if ($importInterview) {
                $payload = tfAdminNormalizeInterviewPrepPayload([
                    'title' => $title,
                    'slug' => tfAdminNormalizeContestSlug($title),
                    'difficulty' => $difficulty,
                    'category' => $category,
                    'statement' => $statement,
                    'input_spec' => (string) ($preparedTask['input_spec'] ?? ''),
                    'output_spec' => (string) ($preparedTask['output_spec'] ?? ''),
                    'starter_cpp' => (string) ($preparedTask['starter_cpp'] ?? ''),
                    'starter_python' => (string) ($preparedTask['starter_python'] ?? ''),
                    'tests_json' => $preparedTask['tests_json'] ?? '[]',
                    'sort_order' => $nextOrderInterview,
                    'is_active' => 1,
                ]);
                if ($payload['tests_json'] === null) {
                    $errors[] = "{$rel}: Некорректные тесты";
                } else {
                    $interviewTask = $payload;
                }
            }

            if ($contestTask === null && $interviewTask === null) {
                continue;
            }

            $attempt = 0;
            $maxAttempts = 2;
            while (true) {
                $packageAddedContest = 0;
                $packageAddedInterview = 0;
                $packageSkipped = 0;
                $packageNextOrderContest = $nextOrderContest;
                $packageNextOrderInterview = $nextOrderInterview;
                try {
                    $pdo = $connectImportDb();

                    if ($contestTask !== null) {
                        $existsStmt = $pdo->prepare("SELECT id FROM contest_tasks WHERE contest_id = ? AND title = ? AND statement = ? LIMIT 1");
                        $existsStmt->execute([$contestId, $contestTask['title'], $contestTask['statement']]);
                        if ($existsStmt->fetch()) {
                            $packageSkipped++;
                        } else {
                            $stmt = $pdo->prepare("
                                INSERT INTO contest_tasks
                                    (contest_id, title, difficulty, statement, input_spec, output_spec, starter_cpp, starter_python, tests_json, order_num, created_at)
                                VALUES
                                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                            ");
                            $stmt->execute([
                                $contestTask['contest_id'],
                                $contestTask['title'],
                                $contestTask['difficulty'],
                                $contestTask['statement'],
                                $contestTask['input_spec'],
                                $contestTask['output_spec'],
                                $contestTask['starter_cpp'],
                                $contestTask['starter_python'],
                                $contestTask['tests_json'],
                                $packageNextOrderContest,
                            ]);
                            $packageAddedContest++;
                            $packageNextOrderContest++;
                        }
                    }

                    if ($interviewTask !== null) {
                        $existsStmt = $pdo->prepare("SELECT id FROM interview_prep_tasks WHERE title = ? AND category = ? AND statement = ? LIMIT 1");
                        $existsStmt->execute([$interviewTask['title'], $interviewTask['category'], $interviewTask['statement']]);
                        if ($existsStmt->fetch()) {
                            $packageSkipped++;
                        } else {
                            $insert = $pdo->prepare("
                                INSERT INTO interview_prep_tasks
                                    (source_type, source_task_id, title, slug, difficulty, category, statement, input_spec, output_spec, starter_cpp, starter_python, tests_json, sort_order, is_active, created_at)
                                VALUES
                                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                            ");
                            $insert->execute([
                                $interviewTask['source_type'],
                                $interviewTask['source_task_id'],
                                $interviewTask['title'],
                                $interviewTask['slug'],
                                $interviewTask['difficulty'],
                                $interviewTask['category'],
                                $interviewTask['statement'],
                                $interviewTask['input_spec'],
                                $interviewTask['output_spec'],
                                $interviewTask['starter_cpp'],
                                $interviewTask['starter_python'],
                                $interviewTask['tests_json'],
                                $packageNextOrderInterview,
                                $interviewTask['is_active'],
                            ]);
                            $packageAddedInterview++;
                            $packageNextOrderInterview++;
                        }
                    }

                    $addedContest += $packageAddedContest;
                    $addedInterview += $packageAddedInterview;
                    $skipped += $packageSkipped;
                    $nextOrderContest = $packageNextOrderContest;
                    $nextOrderInterview = $packageNextOrderInterview;
                    break;
                } catch (Throwable $e) {
                    if ($attempt < $maxAttempts && tfDbIsPacketTooLargeError($e)) {
                        $attempt++;
                        $preparedTask = tfAdminPrepareTaskForDbImport($task, 'aggressive');
                        foreach ((array) ($preparedTask['warnings'] ?? []) as $warning) {
                            $errors[] = "{$rel}: {$warning}";
                        }

                        $title = (string) ($preparedTask['title'] ?? '');
                        $statement = (string) ($preparedTask['statement'] ?? '');
                        $contestTask = null;
                        if ($importContest && $title !== '' && $statement !== '' && !empty($preparedTask['tests_json'])) {
                            $contestTask = [
                                'contest_id' => $contestId,
                                'title' => $title,
                                'difficulty' => $difficulty,
                                'statement' => $statement,
                                'input_spec' => (string) ($preparedTask['input_spec'] ?? ''),
                                'output_spec' => (string) ($preparedTask['output_spec'] ?? ''),
                                'starter_cpp' => (string) ($preparedTask['starter_cpp'] ?? ''),
                                'starter_python' => (string) ($preparedTask['starter_python'] ?? ''),
                                'tests_json' => (string) $preparedTask['tests_json'],
                            ];
                        }
                        $interviewTask = null;
                        if ($importInterview && $title !== '' && $statement !== '' && !empty($preparedTask['tests_json'])) {
                            $interviewTask = tfAdminNormalizeInterviewPrepPayload([
                                'title' => $title,
                                'slug' => tfAdminNormalizeContestSlug($title),
                                'difficulty' => $difficulty,
                                'category' => $category,
                                'statement' => $statement,
                                'input_spec' => (string) ($preparedTask['input_spec'] ?? ''),
                                'output_spec' => (string) ($preparedTask['output_spec'] ?? ''),
                                'starter_cpp' => (string) ($preparedTask['starter_cpp'] ?? ''),
                                'starter_python' => (string) ($preparedTask['starter_python'] ?? ''),
                                'tests_json' => (string) $preparedTask['tests_json'],
                                'sort_order' => $nextOrderInterview,
                                'is_active' => 1,
                            ]);
                        }
                        $pdo = null;
                        usleep(200000);
                        continue;
                    }
                    if ($attempt < $maxAttempts && tfDbIsReconnectableError($e)) {
                        $attempt++;
                        $pdo = null;
                        usleep(200000);
                        continue;
                    }
                    throw $e;
                }
            }
        } catch (Throwable $e) {
            $errors[] = "{$rel}: " . $e->getMessage();
        }
    }

    echo json_encode([
        'success' => true,
        'added_contest' => $addedContest,
        'added_interview' => $addedInterview,
        'skipped' => $skipped,
        'errors' => $errors,
    ]);
}

function tfAdminMakeRelativePath(string $fullPath): ?string
{
    $root = realpath(__DIR__);
    if ($root === false) {
        return null;
    }
    $full = realpath($fullPath);
    if ($full === false || strpos($full, $root) !== 0) {
        return null;
    }
    $rel = ltrim(substr($full, strlen($root)), DIRECTORY_SEPARATOR);
    return $rel === '' ? null : $rel;
}

function handleAdminCreateContest()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $title = trim((string) ($data['title'] ?? ''));
    if ($title === '') {
        echo json_encode(['success' => false, 'message' => 'Введите название контеста']);
        return;
    }
    $slugRaw = trim((string) ($data['slug'] ?? ''));
    $slug = tfAdminNormalizeContestSlug($slugRaw);
    if ($slugRaw !== '' && $slug === null) {
        echo json_encode(['success' => false, 'message' => 'Некорректный slug']);
        return;
    }
    $description = trim((string) ($data['description'] ?? ''));
    $isActive = !empty($data['is_active']) ? 1 : 0;
    $startsAtRaw = trim((string) ($data['starts_at'] ?? ''));
    $durationMinutes = max(0, (int) ($data['duration_minutes'] ?? 0));
    $startsAt = tfAdminParseDateTime($startsAtRaw);
    if ($startsAtRaw !== '' && $startsAt === null) {
        echo json_encode(['success' => false, 'message' => 'Некорректная дата старта']);
        return;
    }
    $endsAt = null;
    if ($startsAt !== null && $durationMinutes > 0) {
        $endsAt = date('Y-m-d H:i:s', strtotime($startsAt) + $durationMinutes * 60);
    }

    $pdo = getDBConnection();
    ensureContestsSchema($pdo);
    $stmt = $pdo->prepare("INSERT INTO contests (title, slug, description, is_active, starts_at, ends_at, duration_minutes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    try {
        $stmt->execute([$title, $slug, $description, $isActive, $startsAt, $endsAt, $durationMinutes > 0 ? $durationMinutes : null]);
    } catch (Throwable $e) {
        if ((string) $e->getCode() === '23000') {
            echo json_encode(['success' => false, 'message' => 'Slug уже используется']);
            return;
        }
        throw $e;
    }
    echo json_encode(['success' => true, 'message' => 'Контест создан']);
}

function handleAdminUpdateContest()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = (int) ($data['id'] ?? 0);
    $title = trim((string) ($data['title'] ?? ''));
    if ($id <= 0 || $title === '') {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $slugRaw = trim((string) ($data['slug'] ?? ''));
    $slug = tfAdminNormalizeContestSlug($slugRaw);
    if ($slugRaw !== '' && $slug === null) {
        echo json_encode(['success' => false, 'message' => 'Некорректный slug']);
        return;
    }
    $description = trim((string) ($data['description'] ?? ''));
    $isActive = !empty($data['is_active']) ? 1 : 0;
    $startsAtRaw = trim((string) ($data['starts_at'] ?? ''));
    $durationMinutes = max(0, (int) ($data['duration_minutes'] ?? 0));
    $startsAt = tfAdminParseDateTime($startsAtRaw);
    if ($startsAtRaw !== '' && $startsAt === null) {
        echo json_encode(['success' => false, 'message' => 'Некорректная дата старта']);
        return;
    }
    $endsAt = null;
    if ($startsAt !== null && $durationMinutes > 0) {
        $endsAt = date('Y-m-d H:i:s', strtotime($startsAt) + $durationMinutes * 60);
    }

    $pdo = getDBConnection();
    ensureContestsSchema($pdo);
    $stmt = $pdo->prepare("UPDATE contests SET title = ?, slug = ?, description = ?, is_active = ?, starts_at = ?, ends_at = ?, duration_minutes = ? WHERE id = ?");
    try {
        $stmt->execute([$title, $slug, $description, $isActive, $startsAt, $endsAt, $durationMinutes > 0 ? $durationMinutes : null, $id]);
    } catch (Throwable $e) {
        if ((string) $e->getCode() === '23000') {
            echo json_encode(['success' => false, 'message' => 'Slug уже используется']);
            return;
        }
        throw $e;
    }
    echo json_encode(['success' => true, 'message' => 'Контест обновлен']);
}

function handleAdminDeleteContest()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureContestsSchema($pdo);
    $stmt = $pdo->prepare("DELETE FROM contests WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Контест удален']);
}

function handleAdminGetContestTask()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureContestsSchema($pdo);
    $stmt = $pdo->prepare("SELECT * FROM contest_tasks WHERE id = ?");
    $stmt->execute([$id]);
    $task = $stmt->fetch();
    if (!$task) {
        echo json_encode(['success' => false, 'message' => 'Задача не найдена']);
        return;
    }
    echo json_encode(['success' => true, 'task' => $task]);
}

function handleAdminCreateContestTask()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $contestId = (int) ($data['contest_id'] ?? 0);
    $title = trim((string) ($data['title'] ?? ''));
    if ($contestId <= 0 || $title === '') {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $difficulty = (string) ($data['difficulty'] ?? 'easy');
    if (!in_array($difficulty, ['easy', 'medium', 'hard'], true)) {
        $difficulty = 'easy';
    }
    $statement = trim((string) ($data['statement'] ?? ''));
    if ($statement === '') {
        echo json_encode(['success' => false, 'message' => 'Укажите условие задачи']);
        return;
    }

    $pdo = getDBConnection();
    ensureContestsSchema($pdo);
    $contestCheck = $pdo->prepare("SELECT id FROM contests WHERE id = ? LIMIT 1");
    $contestCheck->execute([$contestId]);
    if (!(int) $contestCheck->fetchColumn()) {
        echo json_encode(['success' => false, 'message' => 'Контест не найден']);
        return;
    }
    $testsJson = tfAdminNormalizeContestTests($data['tests_json'] ?? '[]');
    if ($testsJson === null) {
        echo json_encode(['success' => false, 'message' => 'Некорректный tests_json']);
        return;
    }
    $timeLimitSec = max(1, min(15, (int) ($data['time_limit_sec'] ?? 3)));
    $memoryLimitKb = max(32768, min(1048576, (int) ($data['memory_limit_kb'] ?? 262144)));
    $stmt = $pdo->prepare("
        INSERT INTO contest_tasks
            (contest_id, title, difficulty, statement, input_spec, output_spec, time_limit_sec, memory_limit_kb, starter_cpp, starter_python, tests_json, order_num, created_at)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $contestId,
        $title,
        $difficulty,
        $statement,
        trim((string) ($data['input_spec'] ?? '')),
        trim((string) ($data['output_spec'] ?? '')),
        $timeLimitSec,
        $memoryLimitKb,
        (string) ($data['starter_cpp'] ?? ''),
        (string) ($data['starter_python'] ?? ''),
        $testsJson,
        (int) ($data['order_num'] ?? 0),
    ]);
    echo json_encode(['success' => true, 'message' => 'Задача добавлена']);
}

function handleAdminUpdateContestTask()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = (int) ($data['id'] ?? 0);
    $contestId = (int) ($data['contest_id'] ?? 0);
    $title = trim((string) ($data['title'] ?? ''));
    if ($id <= 0 || $contestId <= 0 || $title === '') {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $difficulty = (string) ($data['difficulty'] ?? 'easy');
    if (!in_array($difficulty, ['easy', 'medium', 'hard'], true)) {
        $difficulty = 'easy';
    }
    $pdo = getDBConnection();
    ensureContestsSchema($pdo);
    $contestCheck = $pdo->prepare("SELECT id FROM contests WHERE id = ? LIMIT 1");
    $contestCheck->execute([$contestId]);
    if (!(int) $contestCheck->fetchColumn()) {
        echo json_encode(['success' => false, 'message' => 'Контест не найден']);
        return;
    }
    $statement = trim((string) ($data['statement'] ?? ''));
    if ($statement === '') {
        echo json_encode(['success' => false, 'message' => 'Укажите условие задачи']);
        return;
    }
    $testsJson = tfAdminNormalizeContestTests($data['tests_json'] ?? '[]');
    if ($testsJson === null) {
        echo json_encode(['success' => false, 'message' => 'Некорректный tests_json']);
        return;
    }
    $timeLimitSec = max(1, min(15, (int) ($data['time_limit_sec'] ?? 3)));
    $memoryLimitKb = max(32768, min(1048576, (int) ($data['memory_limit_kb'] ?? 262144)));
    $stmt = $pdo->prepare("
        UPDATE contest_tasks
        SET contest_id = ?, title = ?, difficulty = ?, statement = ?, input_spec = ?, output_spec = ?, time_limit_sec = ?, memory_limit_kb = ?, starter_cpp = ?, starter_python = ?, tests_json = ?, order_num = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $contestId,
        $title,
        $difficulty,
        $statement,
        trim((string) ($data['input_spec'] ?? '')),
        trim((string) ($data['output_spec'] ?? '')),
        $timeLimitSec,
        $memoryLimitKb,
        (string) ($data['starter_cpp'] ?? ''),
        (string) ($data['starter_python'] ?? ''),
        $testsJson,
        (int) ($data['order_num'] ?? 0),
        $id,
    ]);
    echo json_encode(['success' => true, 'message' => 'Задача обновлена']);
}

function handleAdminDeleteContestTask()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureContestsSchema($pdo);
    $stmt = $pdo->prepare("DELETE FROM contest_tasks WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Задача удалена']);
}

function handleAdminImportContestTaskPackage()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $packagePath = (string) ($data['package_path'] ?? '');
    $contestId = (int) ($data['contest_id'] ?? 0);
    try {
        $task = tfAdminParseContestTaskPackage($packagePath);
        if ($contestId > 0) {
            $pdo = getDBConnection();
            ensureContestsSchema($pdo);
            $stmt = $pdo->prepare("SELECT COALESCE(MAX(order_num), 0) AS mx FROM contest_tasks WHERE contest_id = ?");
            $stmt->execute([$contestId]);
            $task['suggested_order_num'] = ((int) ($stmt->fetch()['mx'] ?? 0)) + 1;
        }
        echo json_encode(['success' => true, 'task' => $task]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function handleAdminImportInterviewPrepFolders()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $paths = $data['paths'] ?? null;
    if (!is_array($paths) || empty($paths)) {
        $paths = ['tasks'];
    }

    $pdo = getDBConnection();
    ensureContestsSchema($pdo);

    $maxOrder = (int) ($pdo->query("SELECT COALESCE(MAX(sort_order), 0) AS mx FROM interview_prep_tasks")->fetch()['mx'] ?? 0);
    $nextOrder = $maxOrder + 1;

    $added = 0;
    $skipped = 0;
    $errors = [];

    foreach ($paths as $pathRaw) {
        $path = trim((string) $pathRaw);
        if ($path === '') {
            continue;
        }
        $category = basename(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path));
        if ($category === '') {
            $category = 'General';
        }
        $packages = tfAdminFindTaskPackages($path);
        foreach ($packages as $packageDir) {
            $rel = tfAdminMakeRelativePath($packageDir);
            if ($rel === null) {
                $errors[] = "Путь вне проекта: {$packageDir}";
                continue;
            }
            try {
                $task = tfAdminParseContestTaskPackage($rel);
                foreach ((array) ($task['warnings'] ?? []) as $warning) {
                    $errors[] = "{$rel}: {$warning}";
                }
                $payload = tfAdminNormalizeInterviewPrepPayload([
                    'title' => $task['title'] ?? '',
                    'slug' => tfAdminNormalizeContestSlug((string) ($task['title'] ?? '')),
                    'difficulty' => $task['difficulty'] ?? 'easy',
                    'category' => $category,
                    'statement' => $task['statement'] ?? '',
                    'input_spec' => $task['input_spec'] ?? '',
                    'output_spec' => $task['output_spec'] ?? '',
                    'starter_cpp' => $task['starter_cpp'] ?? '',
                    'starter_python' => $task['starter_python'] ?? '',
                    'tests_json' => $task['tests_json'] ?? '[]',
                    'sort_order' => $nextOrder,
                    'is_active' => 1,
                ]);

                if ($payload['title'] === '' || $payload['statement'] === '' || $payload['tests_json'] === null) {
                    $errors[] = "Пропуск задачи без данных: {$rel}";
                    $skipped++;
                    continue;
                }

                $existsStmt = $pdo->prepare("SELECT id FROM interview_prep_tasks WHERE title = ? AND category = ? LIMIT 1");
                $existsStmt->execute([$payload['title'], $payload['category']]);
                if ($existsStmt->fetch()) {
                    $skipped++;
                    continue;
                }

                $insert = $pdo->prepare("
                    INSERT INTO interview_prep_tasks
                        (source_type, source_task_id, title, slug, difficulty, category, statement, input_spec, output_spec, starter_cpp, starter_python, tests_json, sort_order, is_active, created_at)
                    VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $insert->execute([
                    $payload['source_type'],
                    $payload['source_task_id'],
                    $payload['title'],
                    $payload['slug'],
                    $payload['difficulty'],
                    $payload['category'],
                    $payload['statement'],
                    $payload['input_spec'],
                    $payload['output_spec'],
                    $payload['starter_cpp'],
                    $payload['starter_python'],
                    $payload['tests_json'],
                    $payload['sort_order'],
                    $payload['is_active'],
                ]);
                $added++;
                $nextOrder++;
            } catch (Throwable $e) {
                $errors[] = "{$rel}: " . $e->getMessage();
            }
        }
    }

    echo json_encode([
        'success' => true,
        'added' => $added,
        'skipped' => $skipped,
        'errors' => $errors,
    ]);
}

function handleAdminGetInterviewPrepTask()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureContestsSchema($pdo);
    $stmt = $pdo->prepare("SELECT * FROM interview_prep_tasks WHERE id = ?");
    $stmt->execute([$id]);
    $task = $stmt->fetch();
    if (!$task) {
        echo json_encode(['success' => false, 'message' => 'Задача подготовки не найдена']);
        return;
    }
    echo json_encode(['success' => true, 'task' => $task]);
}

function handleAdminCreateInterviewPrepTask()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $sourceTaskId = (int) ($data['source_task_id'] ?? 0);
    $pdo = getDBConnection();
    ensureContestsSchema($pdo);
    if ($sourceTaskId > 0) {
        $stmt = $pdo->prepare("
            SELECT ct.*, c.title AS contest_title
            FROM contest_tasks ct
            LEFT JOIN contests c ON c.id = ct.contest_id
            WHERE ct.id = ?
            LIMIT 1
        ");
        $stmt->execute([$sourceTaskId]);
        $sourceTask = $stmt->fetch();
        if (!$sourceTask) {
            echo json_encode(['success' => false, 'message' => 'ИсхРѕдная задача не найдена']);
            return;
        }
        $data = array_merge(tfAdminContestTaskToInterviewPrepRow($sourceTask), $data);
        $data['source_task_id'] = $sourceTaskId;
    }
    $payload = tfAdminNormalizeInterviewPrepPayload($data);
    if ($payload['title'] === '' || $payload['statement'] === '' || $payload['tests_json'] === null) {
        echo json_encode(['success' => false, 'message' => 'Заполните название, условие и тесты']);
        return;
    }
    $stmt = $pdo->prepare("
        INSERT INTO interview_prep_tasks
            (source_type, source_task_id, title, slug, difficulty, category, statement, input_spec, output_spec, starter_cpp, starter_python, tests_json, sort_order, is_active, created_at)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $payload['source_type'],
        $payload['source_task_id'],
        $payload['title'],
        $payload['slug'],
        $payload['difficulty'],
        $payload['category'],
        $payload['statement'],
        $payload['input_spec'],
        $payload['output_spec'],
        $payload['starter_cpp'],
        $payload['starter_python'],
        $payload['tests_json'],
        $payload['sort_order'],
        $payload['is_active'],
    ]);
    echo json_encode(['success' => true, 'message' => 'Задача для interview prep добавлена']);
}

function handleAdminUpdateInterviewPrepTask()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = (int) ($data['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $payload = tfAdminNormalizeInterviewPrepPayload($data);
    if ($payload['title'] === '' || $payload['statement'] === '' || $payload['tests_json'] === null) {
        echo json_encode(['success' => false, 'message' => 'Заполните название, условие и тесты']);
        return;
    }
    $pdo = getDBConnection();
    ensureContestsSchema($pdo);
    $stmt = $pdo->prepare("
        UPDATE interview_prep_tasks
        SET source_type = ?, source_task_id = ?, title = ?, slug = ?, difficulty = ?, category = ?, statement = ?, input_spec = ?, output_spec = ?, starter_cpp = ?, starter_python = ?, tests_json = ?, sort_order = ?, is_active = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $payload['source_type'],
        $payload['source_task_id'],
        $payload['title'],
        $payload['slug'],
        $payload['difficulty'],
        $payload['category'],
        $payload['statement'],
        $payload['input_spec'],
        $payload['output_spec'],
        $payload['starter_cpp'],
        $payload['starter_python'],
        $payload['tests_json'],
        $payload['sort_order'],
        $payload['is_active'],
        $id,
    ]);
    echo json_encode(['success' => true, 'message' => 'Задача для interview prep обновлена']);
}

function handleAdminDeleteInterviewPrepTask()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureContestsSchema($pdo);
    $stmt = $pdo->prepare("DELETE FROM interview_prep_tasks WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Задача для interview prep удалена']);
}

function handleAdminGetCourse()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$id]);
    $course = $stmt->fetch();
    if (!$course) {
        echo json_encode(['success' => false, 'message' => 'Курс не найден']);
        return;
    }
    echo json_encode(['success' => true, 'course' => $course]);
}

function handleAdminGetCourseExam()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    if (!ensureAdmin())
        return;
    $courseId = (int) ($_GET['course_id'] ?? 0);
    if ($courseId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Некорректный ID']);
        return;
    }

    try {
        $pdo = getDBConnection();
        ensureCourseExamsSchema($pdo);

        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ? LIMIT 1");
        $stmt->execute([$courseId]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Курс не найден']);
            return;
        }

        $stmt = $pdo->prepare("SELECT * FROM course_exams WHERE course_id = ? LIMIT 1");
        $stmt->execute([$courseId]);
        $exam = $stmt->fetch();
        echo json_encode(['success' => true, 'exam' => $exam]);
    } catch (Throwable $e) {
        tfDebugLog('admin.exam.get.exception', ['course_id' => $courseId, 'error' => $e->getMessage()]);
        echo json_encode(['success' => false, 'message' => 'Ошибка загрузки экзамена']);
    }
}

function handleAdminSaveCourseExam()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['course_id'])) {
        echo json_encode(['success' => false, 'message' => 'Некорректные данные']);
        return;
    }

    $courseId = (int) $data['course_id'];
    $timeLimit = max(5, min(300, (int) ($data['time_limit_minutes'] ?? 45)));
    $passPercent = max(1, min(100, (int) ($data['pass_percent'] ?? 70)));
    $shuffleQ = !empty($data['shuffle_questions']) ? 1 : 0;
    $shuffleO = !empty($data['shuffle_options']) ? 1 : 0;

    $decodedExam = [];
    if (is_string($data['exam_json'] ?? null)) {
        $decodedExam = json_decode((string) $data['exam_json'], true);
    } elseif (is_array($data['exam_json'] ?? null)) {
        $decodedExam = $data['exam_json'];
    }
    if (!is_array($decodedExam)) {
        echo json_encode(['success' => false, 'message' => 'Неверный формат экзамена']);
        return;
    }

    $questions = [];
    foreach ($decodedExam as $row) {
        if (!is_array($row))
            continue;

        $question = trim((string) ($row['question'] ?? $row['question_text'] ?? ''));
        if ($question !== '') {
            $row['question'] = $question;
        }

        if (empty($row['type'])) {
            if (!empty($row['options']) || !empty($row['options_text']) || isset($row['correct_option'])) {
                $row['type'] = 'mc_single';
            }
        }

        if (empty($row['options']) && !empty($row['options_text'])) {
            $opts = array_values(array_filter(array_map(function ($v) {
                return trim((string) $v);
            }, explode('|||', (string) $row['options_text'])), function ($v) {
                return $v !== '';
            }));
            if (!empty($opts)) {
                $row['options'] = $opts;
            }
        }

        if (empty($row['correct_answer']) && isset($row['correct_option']) && !empty($row['options'])) {
            $idx = (int) $row['correct_option'] - 1;
            if ($idx >= 0 && isset($row['options'][$idx])) {
                $row['correct_answer'] = $row['options'][$idx];
            }
        }

        $hasQuestion = ($question !== '');
        $hasPool = !empty($row['pool']) && is_array($row['pool']);
        if (!$hasQuestion && !$hasPool) {
            continue;
        }

        $questions[] = $row;
    }

    if (count($questions) === 0) {
        echo json_encode(['success' => false, 'message' => 'Добавьте минимум 1 корректный вопрос']);
        return;
    }

    $examJson = json_encode($questions, JSON_UNESCAPED_UNICODE);
    if (!is_string($examJson)) {
        echo json_encode(['success' => false, 'message' => 'Ошибка кодирования экзамена']);
        return;
    }

    try {
        $pdo = getDBConnection();
        ensureCourseExamsSchema($pdo);

        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ? LIMIT 1");
        $stmt->execute([$courseId]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Курс не найден']);
            return;
        }

        $stmt = $pdo->prepare("SELECT id FROM course_exams WHERE course_id = ? LIMIT 1");
        $stmt->execute([$courseId]);
        $exists = $stmt->fetch();

        if ($exists) {
            $stmt = $pdo->prepare("UPDATE course_exams SET exam_json = ?, time_limit_minutes = ?, pass_percent = ?, shuffle_questions = ?, shuffle_options = ? WHERE course_id = ?");
            $stmt->execute([$examJson, $timeLimit, $passPercent, $shuffleQ, $shuffleO, $courseId]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO course_exams (course_id, exam_json, time_limit_minutes, pass_percent, shuffle_questions, shuffle_options, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$courseId, $examJson, $timeLimit, $passPercent, $shuffleQ, $shuffleO]);
        }

        tfDebugLog('admin.exam.save.success', ['course_id' => $courseId, 'questions' => count($questions)]);
        echo json_encode(['success' => true, 'message' => 'Экзамен сохранен']);
    } catch (Throwable $e) {
        tfDebugLog('admin.exam.save.exception', ['course_id' => $courseId, 'error' => $e->getMessage()]);
        echo json_encode(['success' => false, 'message' => 'Ошибка сохранения экзамена']);
    }
}

function handleAdminDeleteCourseExam()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;

    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $courseId = (int) ($data['course_id'] ?? $_GET['course_id'] ?? 0);
    if ($courseId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Некорректный ID курса']);
        return;
    }

    try {
        $pdo = getDBConnection();
        ensureCourseExamsSchema($pdo);
        $stmt = $pdo->prepare("DELETE FROM course_exams WHERE course_id = ?");
        $stmt->execute([$courseId]);
        tfDebugLog('admin.exam.delete', ['course_id' => $courseId, 'deleted' => $stmt->rowCount()]);
        echo json_encode(['success' => true, 'message' => 'Экзамен удален']);
    } catch (Throwable $e) {
        tfDebugLog('admin.exam.delete.exception', ['course_id' => $courseId, 'error' => $e->getMessage()]);
        echo json_encode(['success' => false, 'message' => 'Ошибка удаления экзамена']);
    }
}
function handleCourseIssueCertificate()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $courseId = (int) ($data['course_id'] ?? 0);
    if ($courseId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный курс']);
        return;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id FROM certificates WHERE user_id = ? AND course_id = ? LIMIT 1");
    $stmt->execute([$userId, $courseId]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => true, 'message' => 'Сертификат уже выдан']);
        return;
    }
    $stmt = $pdo->prepare("SELECT title FROM courses WHERE id = ?");
    $stmt->execute([$courseId]);
    $courseTitle = $stmt->fetch()['title'] ?? 'Курс';
    $certHash = bin2hex(random_bytes(16));
    $stmt = $pdo->prepare("INSERT INTO certificates (user_id, course_id, cert_hash, certificate_name, issuer, issue_date, certificate_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $courseId, $certHash, 'Сертификат Рѕ прохождении', 'Itsphere360', date('Y-m-d'), '']);
    $certId = (int) $pdo->lastInsertId();
    $fixedName = function_exists('t') ? t('cert_heading') : 'Certificate';
    if ($certId > 0 && $fixedName !== '') {
        $fix = $pdo->prepare("UPDATE certificates SET certificate_name = ? WHERE id = ?");
        $fix->execute([$fixedName, $certId]);
    }
    $courseTitle = normalizeMojibakeText((string) $courseTitle);
    echo json_encode(['success' => true, 'course_title' => $courseTitle]);
}

function handleCertificateView()
{
    $user = getCurrentUser();
    $userId = $user['id'] ?? null;
    if (!$userId) {
        header('Location: index.php?action=login');
        exit;
    }
    $certId = (int) ($_GET['id'] ?? 0);
    if ($certId <= 0) {
        header('Location: index.php?action=profile&tab=certificates');
        exit;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT c.*, u.name as user_name, co.title as course_title
        FROM certificates c
        JOIN users u ON u.id = c.user_id
        JOIN courses co ON co.id = c.course_id
        WHERE c.id = ? AND c.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$certId, $userId]);
    $cert = $stmt->fetch();
    if (!$cert) {
        header('Location: index.php?action=profile&tab=certificates');
        exit;
    }
    require_once __DIR__ . '/templates/certificate.php';
}

function handleCertificatePublicView()
{
    $hash = (string) ($_GET['hash'] ?? '');
    $hash = trim($hash);
    if ($hash === '') {
        header('Location: index.php?action=home');
        exit;
    }

    $pdo = getDBConnection();
    try {
        ensureCertificatesSchema($pdo);
    } catch (Throwable $e) {
    }

    $stmt = $pdo->prepare("
        SELECT c.*, u.name as user_name, co.title as course_title
        FROM certificates c
        JOIN users u ON u.id = c.user_id
        JOIN courses co ON co.id = c.course_id
        WHERE c.cert_hash = ?
        LIMIT 1
    ");
    $stmt->execute([$hash]);
    $cert = $stmt->fetch();
    if (!$cert) {
        header('Location: index.php?action=home');
        exit;
    }

    $certPublicView = true;
    require_once __DIR__ . '/templates/certificate.php';
}

function handleAdminCreateCourse()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['title']) || empty($data['instructor'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO courses (title, instructor, description, category, level, progress, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        trim((string) $data['title']),
        $data['instructor'],
        $data['description'] ?? '',
        $data['category'] ?? 'frontend',
        $data['level'] ?? 'начальный',
        (int) ($data['progress'] ?? 0),
        $data['image_url'] ?? ''
    ]);
    echo json_encode(['success' => true, 'message' => 'Курс создан']);
}

function handleAdminUpdateCourse()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (
        !$data
        || empty($data['id'])
        || trim((string) ($data['title'] ?? '')) === ''
    ) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE courses SET title = ?, instructor = ?, description = ?, category = ?, level = ?, progress = ?, image_url = ? WHERE id = ?");
    $stmt->execute([
        trim((string) ($data['title'] ?? '')),
        $data['instructor'] ?? '',
        $data['description'] ?? '',
        $data['category'] ?? 'frontend',
        $data['level'] ?? 'начальный',
        (int) ($data['progress'] ?? 0),
        $data['image_url'] ?? '',
        (int) $data['id']
    ]);
    echo json_encode(['success' => true, 'message' => 'Курс обновлен']);
}

function handleAdminDeleteCourse()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Курс удален']);
}

function handleAdminGetLesson()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ?");
    $stmt->execute([$id]);
    $lesson = $stmt->fetch();
    if ($lesson && ($lesson['type'] ?? '') === 'quiz') {
        ensureQuizSchema($pdo);
        ensureQuizSchema($pdo);
        $stmt = $pdo->prepare("
            SELECT qq.*, 
                   (SELECT GROUP_CONCAT(option_text ORDER BY option_order SEPARATOR '|||') 
                    FROM quiz_options qo 
                    WHERE qo.question_id = qq.id) as options_text
            FROM quiz_questions qq
            WHERE qq.lesson_id = ?
        ");
        $stmt->execute([$lesson['id']]);
        $lesson['questions'] = $stmt->fetchAll();
    }
    if ($lesson) {
        ensurePracticeSchema($pdo);
        $stmt = $pdo->prepare("
            SELECT id, language, title, prompt, starter_code, tests_json, is_required
            FROM lesson_practice_tasks
            WHERE lesson_id = ?
            ORDER BY id ASC
            LIMIT 1
        ");
        $stmt->execute([(int) $lesson['id']]);
        $lesson['practice'] = $stmt->fetch() ?: null;
    }

    if (!$lesson) {
        echo json_encode(['success' => false, 'message' => 'Урок не найден']);
        return;
    }
    echo json_encode(['success' => true, 'lesson' => $lesson]);
}

function upsertLessonQuiz(PDO $pdo, int $lessonId, $quizData)
{
    $stmt = $pdo->prepare("DELETE FROM quiz_questions WHERE lesson_id = ?");
    $stmt->execute([$lessonId]);

    if (!is_array($quizData) || empty($quizData)) {
        return;
    }

    foreach ($quizData as $q) {
        $question = trim($q['question'] ?? '');
        $options = $q['options'] ?? [];
        $correctIndex = (int) ($q['correct_index'] ?? 0);
        $correctOptionsRaw = $q['correct_options'] ?? null;
        $correctOptions = [];
        if ($question === '' || !is_array($options) || count($options) < 2) {
            continue;
        }
        if (is_array($correctOptionsRaw)) {
            foreach ($correctOptionsRaw as $opt) {
                if (is_numeric($opt)) {
                    $idx = (int) $opt;
                    if ($idx >= 1 && $idx <= count($options)) {
                        $correctOptions[] = $idx;
                    }
                } else {
                    $pos = array_search((string) $opt, $options, true);
                    if ($pos !== false) {
                        $correctOptions[] = $pos + 1;
                    }
                }
            }
        }

        $correctOptions = array_values(array_unique(array_filter($correctOptions)));
        if (empty($correctOptions)) {
        if ($correctIndex < 1 || $correctIndex > count($options)) {
            $correctIndex = 1;
        }
        } else {
            $correctIndex = (int) ($correctOptions[0] ?? 1);
        }

        $correctOptionsCsv = !empty($correctOptions) ? implode(',', $correctOptions) : null;
        $stmt = $pdo->prepare("INSERT INTO quiz_questions (lesson_id, question_text, correct_option, correct_options) VALUES (?, ?, ?, ?)");
        $stmt->execute([$lessonId, $question, $correctIndex, $correctOptionsCsv]);
        $questionId = (int) $pdo->lastInsertId();
        $order = 1;
        foreach ($options as $opt) {
            $optText = trim((string) $opt);
            if ($optText === '')
                continue;
            $stmt = $pdo->prepare("INSERT INTO quiz_options (question_id, option_text, option_order) VALUES (?, ?, ?)");
            $stmt->execute([$questionId, $optText, $order++]);
        }
    }
}

function upsertLessonPractice(PDO $pdo, int $lessonId, $practiceData)
{
    ensurePracticeSchema($pdo);

    if (!is_array($practiceData)) {
        $stmt = $pdo->prepare("DELETE FROM lesson_practice_tasks WHERE lesson_id = ?");
        $stmt->execute([$lessonId]);
        return;
    }

    $enabled = filter_var($practiceData['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
    if (!$enabled) {
        $stmt = $pdo->prepare("DELETE FROM lesson_practice_tasks WHERE lesson_id = ?");
        $stmt->execute([$lessonId]);
        return;
    }

    $language = (string) ($practiceData['language'] ?? '');
    if (!in_array($language, ['python', 'cpp', 'c', 'csharp', 'java', 'js', 'mysql', 'pgsql', 'fill'], true)) {
        $language = 'python';
    }

    $title = trim((string) ($practiceData['title'] ?? ''));
    $prompt = (string) ($practiceData['prompt'] ?? '');
    $starter = (string) ($practiceData['starter_code'] ?? '');
    $testsJsonRaw = $practiceData['tests_json'] ?? '';

    $testsJson = '';
    if (is_array($testsJsonRaw)) {
        $testsJson = json_encode($testsJsonRaw, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($testsJson === false)
            $testsJson = '[]';
    } else {
        $testsJson = trim((string) $testsJsonRaw);
        if ($testsJson === '') {
            $testsJson = '[]';
        } else {
            $decoded = json_decode($testsJson, true);
            if (!is_array($decoded)) {
                $testsJson = '[]';
            } else {
                $normalized = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $testsJson = $normalized !== false ? $normalized : '[]';
            }
        }
    }

    $stmt = $pdo->prepare("DELETE FROM lesson_practice_tasks WHERE lesson_id = ? AND language <> ?");
    $stmt->execute([$lessonId, $language]);

    $stmt = $pdo->prepare("
        INSERT INTO lesson_practice_tasks (lesson_id, language, title, prompt, starter_code, tests_json, is_required)
        VALUES (?, ?, ?, ?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            prompt = VALUES(prompt),
            starter_code = VALUES(starter_code),
            tests_json = VALUES(tests_json),
            is_required = 1
    ");
    $stmt->execute([$lessonId, $language, $title !== '' ? $title : null, $prompt, $starter, $testsJson]);
}

function handleAdminCreateLesson()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['course_id']) || empty($data['title'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO lessons (course_id, title, type, content, video_url, materials_title, materials_url, order_num) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        (int) $data['course_id'],
        $data['title'],
        $data['type'] ?? 'article',
        $data['content'] ?? '',
        $data['video_url'] ?? '',
        $data['materials_title'] ?? '',
        $data['materials_url'] ?? '',
        (int) ($data['order_num'] ?? 0)
    ]);
    $lessonId = (int) $pdo->lastInsertId();
    if (($data['type'] ?? '') === 'quiz') {
        upsertLessonQuiz($pdo, $lessonId, $data['quiz_json'] ?? null);
    }
    upsertLessonPractice($pdo, $lessonId, $data['practice'] ?? null);
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Урок создан']);
}

function handleAdminUpdateLesson()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (
        !$data
        || empty($data['id'])
        || empty($data['course_id'])
        || trim((string) ($data['title'] ?? '')) === ''
    ) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("UPDATE lessons SET course_id = ?, title = ?, type = ?, content = ?, video_url = ?, materials_title = ?, materials_url = ?, order_num = ? WHERE id = ?");
    $stmt->execute([
        (int) ($data['course_id'] ?? 0),
        $data['title'] ?? '',
        $data['type'] ?? 'article',
        $data['content'] ?? '',
        $data['video_url'] ?? '',
        $data['materials_title'] ?? '',
        $data['materials_url'] ?? '',
        (int) ($data['order_num'] ?? 0),
        (int) $data['id']
    ]);
    if (($data['type'] ?? '') === 'quiz') {
        upsertLessonQuiz($pdo, (int) $data['id'], $data['quiz_json'] ?? null);
    } else {
        $stmt = $pdo->prepare("DELETE FROM quiz_questions WHERE lesson_id = ?");
        $stmt->execute([(int) $data['id']]);
    }
    upsertLessonPractice($pdo, (int) $data['id'], $data['practice'] ?? null);
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Урок обновлен']);
}

function handleAdminDeleteLesson()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM lessons WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Урок удален']);
}

function handleAdminCreateVacancy()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['title']) || empty($data['company'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    ensureVacancyChatTables($pdo);
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO vacancies (title, company, location, type, salary_min, salary_max, salary_currency, description, company_description, verified, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE, ?)");
        $stmt->execute([
            $data['title'],
            $data['company'],
            $data['location'] ?? '',
            $data['type'] ?? 'remote',
            $data['salary_min'] ?? 0,
            $data['salary_max'] ?? null,
            $data['salary_currency'] ?? 'TJS',
            $data['description'] ?? '',
            $data['company_description'] ?? '',
            $_SESSION['user_id'] ?? null
        ]);
        $vacancyId = $pdo->lastInsertId();

        if (!empty($data['skills'])) {
            foreach ($data['skills'] as $skill) {
                $stmt = $pdo->prepare("INSERT INTO vacancy_skills (vacancy_id, skill_name) VALUES (?, ?)");
                $stmt->execute([$vacancyId, $skill]);
            }
        }

        if (!empty($data['requirements'])) {
            foreach ($data['requirements'] as $req) {
                $stmt = $pdo->prepare("INSERT INTO vacancy_requirements (vacancy_id, requirement_text) VALUES (?, ?)");
                $stmt->execute([$vacancyId, $req]);
            }
        }

        if (!empty($data['pluses'])) {
            foreach ($data['pluses'] as $plus) {
                $stmt = $pdo->prepare("INSERT INTO vacancy_pluses (vacancy_id, plus_text) VALUES (?, ?)");
                $stmt->execute([$vacancyId, $plus]);
            }
        }

        if (!empty($data['responsibilities'])) {
            foreach ($data['responsibilities'] as $resp) {
                $stmt = $pdo->prepare("INSERT INTO vacancy_responsibilities (vacancy_id, responsibility_text) VALUES (?, ?)");
                $stmt->execute([$vacancyId, $resp]);
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Вакансия успешно создана!', 'vacancyId' => $vacancyId]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}
function handleAdminUpdateVacancy()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (
        !$data
        || empty($data['id'])
        || trim((string) ($data['title'] ?? '')) === ''
        || trim((string) ($data['company'] ?? '')) === ''
    ) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE vacancies SET title = ?, company = ?, location = ?, type = ?, salary_min = ?, salary_max = ?, salary_currency = ?, description = ?, company_description = ? WHERE id = ?");
        $stmt->execute([
            $data['title'] ?? '',
            $data['company'] ?? '',
            $data['location'] ?? '',
            $data['type'] ?? 'remote',
            $data['salary_min'] ?? 0,
            $data['salary_max'] ?? null,
            $data['salary_currency'] ?? 'TJS',
            $data['description'] ?? '',
            $data['company_description'] ?? '',
            (int) $data['id']
        ]);
        $vacancyId = (int) $data['id'];
        $pdo->prepare("DELETE FROM vacancy_skills WHERE vacancy_id = ?")->execute([$vacancyId]);
        $pdo->prepare("DELETE FROM vacancy_requirements WHERE vacancy_id = ?")->execute([$vacancyId]);
        $pdo->prepare("DELETE FROM vacancy_pluses WHERE vacancy_id = ?")->execute([$vacancyId]);
        $pdo->prepare("DELETE FROM vacancy_responsibilities WHERE vacancy_id = ?")->execute([$vacancyId]);

        $skills = $data['skills'] ?? [];
        foreach ($skills as $skill) {
            $stmt = $pdo->prepare("INSERT INTO vacancy_skills (vacancy_id, skill_name) VALUES (?, ?)");
            $stmt->execute([$vacancyId, $skill]);
        }
        $requirements = $data['requirements'] ?? [];
        foreach ($requirements as $req) {
            $stmt = $pdo->prepare("INSERT INTO vacancy_requirements (vacancy_id, requirement_text) VALUES (?, ?)");
            $stmt->execute([$vacancyId, $req]);
        }
        $pluses = $data['pluses'] ?? [];
        foreach ($pluses as $plus) {
            $stmt = $pdo->prepare("INSERT INTO vacancy_pluses (vacancy_id, plus_text) VALUES (?, ?)");
            $stmt->execute([$vacancyId, $plus]);
        }
        $responsibilities = $data['responsibilities'] ?? [];
        foreach ($responsibilities as $resp) {
            $stmt = $pdo->prepare("INSERT INTO vacancy_responsibilities (vacancy_id, responsibility_text) VALUES (?, ?)");
            $stmt->execute([$vacancyId, $resp]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Вакансия обновлена']);
    } catch (PDOException $e) {
        if ($pdo->inTransaction())
            $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}
function handleAdminDeleteVacancy()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM vacancies WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Вакансия удалена']);
}

function handleAdminCreateNotification()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['message'])) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    $userId = isset($data['user_id']) && $data['user_id'] !== '' ? (int) $data['user_id'] : null;
    if ($userId) {
        tfAddNotification($pdo, (int) $userId, $data['message']);
    } else {
        $stmt = $pdo->query("SELECT id FROM users");
        $users = $stmt->fetchAll();
        foreach ($users as $u) {
            tfAddNotification($pdo, (int) $u['id'], $data['message']);
        }
    }
    echo json_encode(['success' => true, 'message' => 'Уведомление создано']);
}

function handleAdminDeleteNotification()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Уведомление удалено']);
}

function handleAdminRoadmapGetNode()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    $stmt = $pdo->prepare("SELECT * FROM roadmap_nodes WHERE id = ?");
    $stmt->execute([$id]);
    $node = $stmt->fetch();
    if (!$node) {
        echo json_encode(['success' => false, 'message' => 'Узел не найден']);
        return;
    }
    $node['materials'] = json_decode($node['materials'] ?? '[]', true) ?: [];
    $node['topic'] = $node['topic'] ?? '';
    echo json_encode(['success' => true, 'node' => $node]);
}

function handleAdminRoadmapGetRoadmap()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    $stmt = $pdo->prepare("SELECT * FROM roadmap_list WHERE id = ?");
    $stmt->execute([$id]);
    $roadmap = $stmt->fetch();
    if (!$roadmap) {
        echo json_encode(['success' => false, 'message' => 'Не найдено']);
        return;
    }
    echo json_encode(['success' => true, 'roadmap' => $roadmap]);
}

function handleAdminRoadmapCreateRoadmap()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $title = trim((string) ($data['title'] ?? ''));
    if ($title === '') {
        echo json_encode(['success' => false, 'message' => 'Название обязательно']);
        return;
    }
    $description = trim((string) ($data['description'] ?? ''));
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    try {
        $stmt = $pdo->prepare("INSERT INTO roadmap_list (title, description) VALUES (?, ?)");
        $stmt->execute([$title, $description]);
        echo json_encode(['success' => true, 'message' => 'Роадмап создан']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleAdminRoadmapUpdateRoadmap()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $id = (int) ($data['id'] ?? 0);
    $title = trim((string) ($data['title'] ?? ''));
    if ($id <= 0 || $title === '') {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $description = trim((string) ($data['description'] ?? ''));
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    try {
        $stmt = $pdo->prepare("UPDATE roadmap_list SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $description, $id]);
        echo json_encode(['success' => true, 'message' => 'Роадмап обновлен']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleAdminRoadmapDeleteRoadmap()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    try {
        $stmt = $pdo->prepare("DELETE FROM roadmap_list WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Роадмап удален']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
    }
}

function handleAdminRoadmapCreateNode()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || trim((string) ($data['title'] ?? '')) === '') {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    $materials = json_encode($data['materials'] ?? [], JSON_UNESCAPED_UNICODE);
    $roadmapTitle = trim((string) ($data['roadmap_title'] ?? '')) ?: 'Основной';
    $stmt = $pdo->prepare("INSERT INTO roadmap_nodes (title, roadmap_title, topic, materials, x, y, deps, is_exam) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        trim((string) $data['title']),
        $roadmapTitle,
        $data['topic'] ?? null,
        $materials,
        (int) ($data['x'] ?? 0),
        (int) ($data['y'] ?? 0),
        $data['deps'] ?? '[]',
        !empty($data['is_exam']) ? 1 : 0
    ]);
    $ins = $pdo->prepare("INSERT IGNORE INTO roadmap_list (title) VALUES (?)");
    $ins->execute([$roadmapTitle]);
    echo json_encode(['success' => true, 'message' => 'Узел создан']);
}

function handleAdminRoadmapUpdateNode()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['id']) || trim((string) ($data['title'] ?? '')) === '') {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    $materials = json_encode($data['materials'] ?? [], JSON_UNESCAPED_UNICODE);
    $roadmapTitle = trim((string) ($data['roadmap_title'] ?? '')) ?: 'Основной';
    $stmt = $pdo->prepare("UPDATE roadmap_nodes SET title = ?, roadmap_title = ?, topic = ?, materials = ?, x = ?, y = ?, deps = ?, is_exam = ? WHERE id = ?");
    $stmt->execute([
        trim((string) ($data['title'] ?? '')),
        $roadmapTitle,
        $data['topic'] ?? null,
        $materials,
        (int) ($data['x'] ?? 0),
        (int) ($data['y'] ?? 0),
        $data['deps'] ?? '[]',
        !empty($data['is_exam']) ? 1 : 0,
        (int) $data['id']
    ]);
    $ins = $pdo->prepare("INSERT IGNORE INTO roadmap_list (title) VALUES (?)");
    $ins->execute([$roadmapTitle]);
    echo json_encode(['success' => true, 'message' => 'Узел обновлен']);
}

function handleAdminRoadmapDeleteNode()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    $stmt = $pdo->prepare("DELETE FROM roadmap_nodes WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Узел удален']);
}

function handleAdminRoadmapGetLesson()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    $stmt = $pdo->prepare("SELECT * FROM roadmap_lessons WHERE id = ?");
    $stmt->execute([$id]);
    $lesson = $stmt->fetch();
    if (!$lesson) {
        echo json_encode(['success' => false, 'message' => 'Урок не найден']);
        return;
    }
    echo json_encode(['success' => true, 'lesson' => $lesson]);
}

function handleAdminRoadmapCreateLesson()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['node_id']) || trim((string) ($data['title'] ?? '')) === '') {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    $stmt = $pdo->prepare("INSERT INTO roadmap_lessons (node_id, title, video_url, description, materials, order_index) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        (int) $data['node_id'],
        trim((string) $data['title']),
        $data['video_url'] ?? '',
        $data['description'] ?? '',
        $data['materials'] ?? '[]',
        (int) ($data['order_index'] ?? 0)
    ]);
    echo json_encode(['success' => true, 'message' => 'Урок создан']);
}

function handleAdminRoadmapUpdateLesson()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['id']) || empty($data['node_id']) || trim((string) ($data['title'] ?? '')) === '') {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    $stmt = $pdo->prepare("UPDATE roadmap_lessons SET node_id = ?, title = ?, video_url = ?, description = ?, materials = ?, order_index = ? WHERE id = ?");
    $stmt->execute([
        (int) ($data['node_id'] ?? 0),
        trim((string) ($data['title'] ?? '')),
        $data['video_url'] ?? '',
        $data['description'] ?? '',
        $data['materials'] ?? '[]',
        (int) ($data['order_index'] ?? 0),
        (int) $data['id']
    ]);
    echo json_encode(['success' => true, 'message' => 'Урок обновлен']);
}

function handleAdminRoadmapDeleteLesson()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    $stmt = $pdo->prepare("DELETE FROM roadmap_lessons WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Урок удален']);
}

function handleAdminRoadmapGetQuiz()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    $stmt = $pdo->prepare("SELECT * FROM roadmap_quiz_questions WHERE id = ?");
    $stmt->execute([$id]);
    $quiz = $stmt->fetch();
    if (!$quiz) {
        echo json_encode(['success' => false, 'message' => 'Вопрос не найден']);
        return;
    }
    echo json_encode(['success' => true, 'quiz' => $quiz]);
}

function handleAdminRoadmapCreateQuiz()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['node_id']) || trim((string) ($data['question'] ?? '')) === '') {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $optionsRaw = $data['options'] ?? '[]';
    $options = is_array($optionsRaw) ? $optionsRaw : json_decode((string) $optionsRaw, true);
    if (!is_array($options)) {
        $options = [];
    }
    $options = array_values(array_filter(array_map(static fn($v) => trim((string) $v), $options), static fn($v) => $v !== ''));
    $correctAnswer = trim((string) ($data['correct_answer'] ?? ''));
    if (count($options) < 2 || $correctAnswer === '' || !in_array($correctAnswer, $options, true)) {
        echo json_encode(['success' => false, 'message' => 'Некорректные варианты ответа']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    $stmt = $pdo->prepare("INSERT INTO roadmap_quiz_questions (node_id, question, options, correct_answer) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        (int) $data['node_id'],
        trim((string) $data['question']),
        json_encode($options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        $correctAnswer
    ]);
    echo json_encode(['success' => true, 'message' => 'Вопрос создан']);
}

function handleAdminRoadmapUpdateQuiz()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['id']) || empty($data['node_id']) || trim((string) ($data['question'] ?? '')) === '') {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        return;
    }
    $optionsRaw = $data['options'] ?? '[]';
    $options = is_array($optionsRaw) ? $optionsRaw : json_decode((string) $optionsRaw, true);
    if (!is_array($options)) {
        $options = [];
    }
    $options = array_values(array_filter(array_map(static fn($v) => trim((string) $v), $options), static fn($v) => $v !== ''));
    $correctAnswer = trim((string) ($data['correct_answer'] ?? ''));
    if (count($options) < 2 || $correctAnswer === '' || !in_array($correctAnswer, $options, true)) {
        echo json_encode(['success' => false, 'message' => 'Некорректные варианты ответа']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    $stmt = $pdo->prepare("UPDATE roadmap_quiz_questions SET node_id = ?, question = ?, options = ?, correct_answer = ? WHERE id = ?");
    $stmt->execute([
        (int) ($data['node_id'] ?? 0),
        trim((string) ($data['question'] ?? '')),
        json_encode($options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        $correctAnswer,
        (int) $data['id']
    ]);
    echo json_encode(['success' => true, 'message' => 'Вопрос обновлен']);
}

function handleAdminRoadmapDeleteQuiz()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;
    if (!ensureAdmin())
        return;
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Неверный ID']);
        return;
    }
    $pdo = getDBConnection();
    ensureRoadmapTables($pdo);
    $stmt = $pdo->prepare("DELETE FROM roadmap_quiz_questions WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Вопрос удален']);
}

// Обработчик генерации пароля
function handleGeneratePassword()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET')
        return;

    $password = generateSecurePassword();
    echo json_encode(['success' => true, 'password' => $password, 'strength' => getPasswordStrength($password)]);
}

function handleUploadImage()
{
    header('Content-Type: application/json; charset=UTF-8');
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
    if (function_exists('ob_get_level')) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        return;

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        return;
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Файл не загружен']);
        return;
    }

    $file = $_FILES['image'];
    $validation = tfValidateUploadedFile($file, [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'webp' => ['image/webp'],
        'gif' => ['image/gif'],
    ], 5 * 1024 * 1024, ['require_image' => true]);
    if (empty($validation['ok'])) {
        echo json_encode(['success' => false, 'message' => (string) ($validation['message'] ?? 'Недопустимый файл')]);
        return;
    }
    $file['type'] = $validation['mime'];
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'Файл слишком большой (макс. 5MB)']);
        return;
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif'
    ];

    $mime = '';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
        }
    }
    if ($mime === '' && !empty($file['type'])) {
        $mime = $file['type'];
    }

    if (!isset($allowed[$mime])) {
        echo json_encode(['success' => false, 'message' => 'Недопустимый тип файла']);
        return;
    }

    $uploadsDir = __DIR__ . '/uploads';
    if (!is_dir($uploadsDir)) {
        @mkdir($uploadsDir, 0777, true);
    }

    $ext = $allowed[$mime];
    $rand = bin2hex(random_bytes(4));
    $fileName = 'img_' . (int) $userId . '_' . time() . '_' . $rand . '.' . $ext;
    $targetPath = $uploadsDir . '/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo json_encode(['success' => false, 'message' => 'Не удалось сохранить файл']);
        return;
    }

    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $prefix = $host ? ($scheme . '://' . $host) : '';
    $relativePath = 'uploads/' . $fileName;
    $absoluteUrl = $prefix . ($base ? $base : '') . '/' . $relativePath;
    echo json_encode([
        'success' => true,
        'url' => $relativePath,
        'path' => $relativePath,
        'absolute_url' => $absoluteUrl
    ]);
}

// Главный роутер
function ensureHomeEngagementTables(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS home_likes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            entity_type VARCHAR(20) NOT NULL,
            entity_id INT NOT NULL,
            user_id INT NULL,
            session_id VARCHAR(128) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_user_like (entity_type, entity_id, user_id),
            UNIQUE KEY uniq_session_like (entity_type, entity_id, session_id)
        )
    ");
}

function ensureSupportRequestsTable(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS support_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            name VARCHAR(120) NOT NULL,
            email VARCHAR(190) NOT NULL,
            subject VARCHAR(190) NOT NULL,
            message TEXT NOT NULL,
            priority ENUM('low', 'normal', 'high') NOT NULL DEFAULT 'normal',
            status ENUM('new', 'in_progress', 'resolved') NOT NULL DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_status_created (status, created_at),
            INDEX idx_user_created (user_id, created_at)
        )
    ");
}

function getHomeLikeViewer(): array
{
    $userId = $_SESSION['user_id'] ?? null;
    $sessionId = session_id();
    if ($sessionId === '' && session_status() === PHP_SESSION_NONE) {
        @session_start();
        $sessionId = session_id();
    }
    if ($sessionId === '') {
        $cookieName = 'tf_home_viewer';
        $cookieValue = trim((string) ($_COOKIE[$cookieName] ?? ''));
        if ($cookieValue === '') {
            try {
                $cookieValue = bin2hex(random_bytes(16));
            } catch (Throwable $e) {
                $cookieValue = hash('sha256', microtime(true) . '|' . (string) mt_rand());
            }
            $expires = time() + 31536000;
            if (PHP_VERSION_ID >= 70300) {
                setcookie($cookieName, $cookieValue, [
                    'expires' => $expires,
                    'path' => '/',
                    'samesite' => 'Lax',
                ]);
            } else {
                setcookie($cookieName, $cookieValue, $expires, '/');
            }
            $_COOKIE[$cookieName] = $cookieValue;
        }
        $sessionId = $cookieValue;
    }
    return [
        'user_id' => $userId ? (int) $userId : null,
        'session_id' => $sessionId !== '' ? $sessionId : null
    ];
}

function getHomeLikesForEntities(PDO $pdo, string $entityType, array $ids, ?int $userId, ?string $sessionId): array
{
    $ids = array_values(array_filter(array_map('intval', $ids), static fn($v) => $v > 0));
    if (empty($ids)) {
        return ['counts' => [], 'liked' => []];
    }

    $in = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT entity_id, COUNT(*) AS cnt FROM home_likes WHERE entity_type = ? AND entity_id IN ($in) GROUP BY entity_id");
    $stmt->execute(array_merge([$entityType], $ids));
    $counts = [];
    foreach ($stmt->fetchAll() as $row) {
        $counts[(int) $row['entity_id']] = (int) $row['cnt'];
    }

    $liked = [];
    if ($userId !== null) {
        $stmt = $pdo->prepare("SELECT entity_id FROM home_likes WHERE entity_type = ? AND entity_id IN ($in) AND user_id = ?");
        $stmt->execute(array_merge([$entityType], $ids, [$userId]));
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $entityId) {
            $liked[(int) $entityId] = true;
        }
    } elseif (!empty($sessionId)) {
        $stmt = $pdo->prepare("SELECT entity_id FROM home_likes WHERE entity_type = ? AND entity_id IN ($in) AND session_id = ?");
        $stmt->execute(array_merge([$entityType], $ids, [$sessionId]));
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $entityId) {
            $liked[(int) $entityId] = true;
        }
    }

    return ['counts' => $counts, 'liked' => $liked];
}

function handleHomeLikeToggle()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $entityType = (string) ($data['entity_type'] ?? '');
    $entityId = (int) ($data['entity_id'] ?? 0);
    if (!in_array($entityType, ['course', 'vacancy', 'review'], true) || $entityId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid payload']);
        return;
    }

    $pdo = getDBConnection();
    ensureHomeEngagementTables($pdo);
    $viewer = getHomeLikeViewer();
    $userId = $viewer['user_id'];
    $sessionId = $viewer['session_id'];
    if ($userId === null && empty($sessionId)) {
        echo json_encode(['success' => false, 'message' => 'No viewer identity']);
        return;
    }

    if ($userId !== null) {
        $stmt = $pdo->prepare("SELECT id FROM home_likes WHERE entity_type = ? AND entity_id = ? AND user_id = ?");
        $stmt->execute([$entityType, $entityId, $userId]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM home_likes WHERE entity_type = ? AND entity_id = ? AND session_id = ?");
        $stmt->execute([$entityType, $entityId, $sessionId]);
    }
    $existingId = (int) ($stmt->fetchColumn() ?: 0);

    if ($existingId > 0) {
        $stmt = $pdo->prepare("DELETE FROM home_likes WHERE id = ?");
        $stmt->execute([$existingId]);
        $liked = false;
    } else {
        if ($userId !== null) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO home_likes (entity_type, entity_id, user_id) VALUES (?, ?, ?)");
            $stmt->execute([$entityType, $entityId, $userId]);
        } else {
            $stmt = $pdo->prepare("INSERT IGNORE INTO home_likes (entity_type, entity_id, session_id) VALUES (?, ?, ?)");
            $stmt->execute([$entityType, $entityId, $sessionId]);
        }
        $liked = true;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM home_likes WHERE entity_type = ? AND entity_id = ?");
    $stmt->execute([$entityType, $entityId]);
    $count = (int) $stmt->fetchColumn();

    echo json_encode(['success' => true, 'liked' => $liked, 'count' => $count]);
}

function handleHomeLikeState()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $entityType = (string) ($_GET['entity_type'] ?? '');
    $entityId = (int) ($_GET['entity_id'] ?? 0);
    if (!in_array($entityType, ['course', 'vacancy', 'review'], true) || $entityId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid query']);
        return;
    }

    $pdo = getDBConnection();
    ensureHomeEngagementTables($pdo);
    $viewer = getHomeLikeViewer();
    $userId = $viewer['user_id'];
    $sessionId = $viewer['session_id'];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM home_likes WHERE entity_type = ? AND entity_id = ?");
    $stmt->execute([$entityType, $entityId]);
    $count = (int) $stmt->fetchColumn();

    $liked = false;
    if ($userId !== null) {
        $stmt = $pdo->prepare("SELECT 1 FROM home_likes WHERE entity_type = ? AND entity_id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$entityType, $entityId, $userId]);
        $liked = (bool) $stmt->fetchColumn();
    } elseif (!empty($sessionId)) {
        $stmt = $pdo->prepare("SELECT 1 FROM home_likes WHERE entity_type = ? AND entity_id = ? AND session_id = ? LIMIT 1");
        $stmt->execute([$entityType, $entityId, $sessionId]);
        $liked = (bool) $stmt->fetchColumn();
    }

    echo json_encode(['success' => true, 'liked' => $liked, 'count' => $count]);
}

function handleHomeAiInsights()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $lang = (string) ($data['lang'] ?? 'ru');
    if (!in_array($lang, ['ru', 'en', 'tg'], true)) {
        $lang = 'ru';
    }

    $pdo = getDBConnection();
    $stats = [
        'users' => (int) ($pdo->query("SELECT COUNT(*) as total FROM users WHERE role IN ('seeker','recruiter') AND is_blocked = FALSE")->fetch()['total'] ?? 0),
        'courses' => (int) ($pdo->query("SELECT COUNT(*) as total FROM courses")->fetch()['total'] ?? 0),
        'vacancies' => (int) ($pdo->query("SELECT COUNT(*) as total FROM vacancies")->fetch()['total'] ?? 0)
    ];
    $courses = $pdo->query("SELECT title FROM courses ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_COLUMN);
    $vacancies = $pdo->query("SELECT title, company FROM vacancies ORDER BY created_at DESC LIMIT 3")->fetchAll();

    $courseText = !empty($courses) ? implode(', ', array_map('strval', $courses)) : '-';
    $vacancyText = '-';
    if (!empty($vacancies)) {
        $items = [];
        foreach ($vacancies as $v) {
            $items[] = trim((string) $v['title'] . ' @ ' . (string) ($v['company'] ?? ''));
        }
        $vacancyText = implode('; ', $items);
    }

    $prompt = "Language: {$lang}. Generate 4 short bullets for homepage insights.\n"
        . "Stats: users={$stats['users']}, courses={$stats['courses']}, vacancies={$stats['vacancies']}.\n"
        . "Latest courses: {$courseText}.\n"
        . "Latest vacancies: {$vacancyText}.\n"
        . "Rules: no fake numbers, max 18 words per bullet.";

    $contents = [
        [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ]
    ];
    $resp = callGeminiApi($contents, ['temperature' => 0.4, 'maxOutputTokens' => 220]);
    if (!empty($resp['ok']) && !empty($resp['text'])) {
        echo json_encode(['success' => true, 'text' => trim((string) $resp['text']), 'source' => 'ai']);
        return;
    }

    $fallback = $lang === 'en'
        ? "- Users: {$stats['users']}\n- Courses: {$stats['courses']}\n- Vacancies: {$stats['vacancies']}\n- AI temporarily unavailable."
        : ($lang === 'tg'
            ? "- Корбарон: {$stats['users']}\n- КурсТіРѕ: {$stats['courses']}\n- Т¶ойТіои корУЈ: {$stats['vacancies']}\n- AI муваТ›Т›атан дастрас нест."
            : "- Пользователи: {$stats['users']}\n- Курсы: {$stats['courses']}\n- Вакансии: {$stats['vacancies']}\n- AI временно недоступен.");

    echo json_encode(['success' => true, 'text' => $fallback, 'source' => 'fallback']);
}

function tfInterviewFetchSession(PDO $pdo, string $code): array
{
    $stmt = $pdo->prepare("
        SELECT s.*, i.user_id as interview_user_id, i.title, i.question_count, i.status, i.created_at as interview_created_at
        FROM interview_sessions s
        INNER JOIN interviews i ON i.id = s.interview_id
        WHERE s.code = ? LIMIT 1
    ");
    $stmt->execute([$code]);
    $session = $stmt->fetch();
    if (!$session) {
        return [];
    }
    $membersStmt = $pdo->prepare("
        SELECT u.id, u.name, u.avatar, p.role, p.joined_at
        FROM interview_participants p
        INNER JOIN users u ON u.id = p.user_id
        WHERE p.session_id = ?
        ORDER BY p.joined_at ASC
    ");
    $membersStmt->execute([(int) $session['id']]);
    $session['participants'] = $membersStmt->fetchAll() ?: [];

    $msgStmt = $pdo->prepare("
        SELECT m.id, m.message, m.created_at, u.name
        FROM interview_messages m
        INNER JOIN users u ON u.id = m.user_id
        WHERE m.session_id = ?
        ORDER BY m.created_at ASC
        LIMIT 100
    ");
    $msgStmt->execute([(int) $session['id']]);
    $session['messages'] = $msgStmt->fetchAll() ?: [];
    if (!empty($session['boards_snapshot'])) {
        $decoded = json_decode((string) $session['boards_snapshot'], true);
        if (is_array($decoded)) {
            $session['boards_snapshot'] = $decoded;
        }
    }

    return $session;
}

function tfInterviewFindParticipantRole(PDO $pdo, int $sessionId, int $userId): ?string
{
    if ($sessionId <= 0 || $userId <= 0) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT role FROM interview_participants WHERE session_id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$sessionId, $userId]);
    $role = $stmt->fetchColumn();
    return $role !== false ? (string) $role : null;
}

function tfInterviewEnsureParticipant(PDO $pdo, array $session, int $userId, bool $autoJoin = false): ?string
{
    $sessionId = (int) ($session['id'] ?? 0);
    if ($sessionId <= 0 || $userId <= 0) {
        return null;
    }
    $role = tfInterviewFindParticipantRole($pdo, $sessionId, $userId);
    if ($role !== null) {
        return $role;
    }
    if (!$autoJoin) {
        return null;
    }
    $insertRole = ((int) ($session['interview_user_id'] ?? 0) === $userId) ? 'owner' : 'member';
    $ins = $pdo->prepare("INSERT INTO interview_participants (session_id, user_id, role) VALUES (?, ?, ?)");
    $ins->execute([$sessionId, $userId, $insertRole]);
    return $insertRole;
}

function handleInterviewCreate()
{
    try {
        $user = getCurrentUser();
        $userId = (int) ($user['id'] ?? 0);
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || $userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }
        $title = trim((string) ($data['title'] ?? 'Interview'));
        $questionCount = max(1, (int) ($data['question_count'] ?? 1));
        if ($title === '') {
            $title = 'Interview';
        }
        if (mb_strlen($title) > 255) {
            $title = mb_substr($title, 0, 255);
        }

        $pdo = getDBConnection();
        ensureInterviewsSchema($pdo);
        $stmt = $pdo->prepare("INSERT INTO interviews (user_id, title, question_count) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $title, $questionCount]);
        $interviewId = (int) $pdo->lastInsertId();

        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code = strtoupper(bin2hex(random_bytes(3)));
            $check = $pdo->prepare("SELECT id FROM interview_sessions WHERE code = ? LIMIT 1");
            $check->execute([$code]);
            if (!$check->fetch()) break;
            $code = '';
        }
        if ($code === '') {
            echo json_encode(['success' => false, 'message' => 'Unable to create session.']);
            return;
        }
        $sessStmt = $pdo->prepare("
            INSERT INTO interview_sessions (interview_id, code, code_snapshot, remaining_seconds, is_running)
            VALUES (?, ?, '', 0, 0)
        ");
        $sessStmt->execute([$interviewId, $code]);
        $sessionId = (int) $pdo->lastInsertId();

        $partStmt = $pdo->prepare("INSERT INTO interview_participants (session_id, user_id, role) VALUES (?, ?, 'owner')");
        $partStmt->execute([$sessionId, $userId]);

        $session = tfInterviewFetchSession($pdo, $code);
        echo json_encode(['success' => true, 'session' => $session]);
    } catch (Throwable $e) {
        error_log('[INTERVIEW_CREATE] ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Не удалось создать собеседование.']);
    }
}

function handleInterviewJoin()
{
    $user = getCurrentUser();
    $userId = (int) ($user['id'] ?? 0);
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data) || $userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        return;
    }
    $code = trim((string) ($data['code'] ?? ''));
    if ($code === '') {
        echo json_encode(['success' => false, 'message' => 'Invalid code.']);
        return;
    }
    $pdo = getDBConnection();
    ensureInterviewsSchema($pdo);
    $session = tfInterviewFetchSession($pdo, $code);
    if (!$session) {
        echo json_encode(['success' => false, 'message' => 'Session not found.']);
        return;
    }
    $sessionId = (int) $session['id'];
    $check = $pdo->prepare("SELECT 1 FROM interview_participants WHERE session_id = ? AND user_id = ? LIMIT 1");
    $check->execute([$sessionId, $userId]);
    if (!$check->fetch()) {
        $ins = $pdo->prepare("INSERT INTO interview_participants (session_id, user_id, role) VALUES (?, ?, 'member')");
        $ins->execute([$sessionId, $userId]);
    }
    $session = tfInterviewFetchSession($pdo, $code);
    echo json_encode(['success' => true, 'session' => $session]);
}

function handleInterviewGet()
{
    try {
        $user = getCurrentUser();
        $userId = (int) ($user['id'] ?? 0);
        $code = trim((string) ($_GET['code'] ?? ''));
        if ($code === '' || $userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid code.']);
            return;
        }
        $pdo = getDBConnection();
        ensureInterviewsSchema($pdo);
        $session = tfInterviewFetchSession($pdo, $code);
        if (!$session) {
            echo json_encode(['success' => false, 'message' => 'Session not found.']);
            return;
        }
        $role = tfInterviewEnsureParticipant($pdo, $session, $userId, false);
        if ($role === null) {
            echo json_encode(['success' => false, 'message' => 'No access to session.']);
            return;
        }
        $session = tfInterviewFetchSession($pdo, $code);
        $session['current_user_role'] = $role;
        echo json_encode(['success' => true, 'session' => $session]);
    } catch (Throwable $e) {
        error_log('[INTERVIEW_GET] ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Не удалось загрузить комнату.']);
    }
}

function handleInterviewMessage()
{
    try {
        $user = getCurrentUser();
        $userId = (int) ($user['id'] ?? 0);
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || $userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }
        $code = trim((string) ($data['code'] ?? ''));
        $message = trim((string) ($data['message'] ?? ''));
        if ($code === '' || $message === '') {
            echo json_encode(['success' => false, 'message' => 'Invalid data.']);
            return;
        }
        if (mb_strlen($message) > 4000) {
            echo json_encode(['success' => false, 'message' => 'Сообщение слишком длинное.']);
            return;
        }
        $pdo = getDBConnection();
        ensureInterviewsSchema($pdo);
        $session = tfInterviewFetchSession($pdo, $code);
        if (!$session) {
            echo json_encode(['success' => false, 'message' => 'Session not found.']);
            return;
        }
        if (tfInterviewEnsureParticipant($pdo, $session, $userId, false) === null) {
            echo json_encode(['success' => false, 'message' => 'No access to session.']);
            return;
        }
        $ins = $pdo->prepare("INSERT INTO interview_messages (session_id, user_id, message) VALUES (?, ?, ?)");
        $ins->execute([(int) $session['id'], $userId, $message]);
        echo json_encode(['success' => true]);
    } catch (Throwable $e) {
        error_log('[INTERVIEW_MESSAGE] ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Не удалось отправить сообщение.']);
    }
}

function handleInterviewCodeSave()
{
    try {
        $user = getCurrentUser();
        $userId = (int) ($user['id'] ?? 0);
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || $userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }
        $code = trim((string) ($data['code'] ?? ''));
        $snapshot = (string) ($data['snapshot'] ?? '');
        $boardsSnapshot = $data['boards'] ?? null;
        if ($code === '') {
            echo json_encode(['success' => false, 'message' => 'Invalid code.']);
            return;
        }
        $pdo = getDBConnection();
        ensureInterviewsSchema($pdo);
        $session = tfInterviewFetchSession($pdo, $code);
        if (!$session) {
            echo json_encode(['success' => false, 'message' => 'Session not found.']);
            return;
        }
        if (tfInterviewEnsureParticipant($pdo, $session, $userId, false) === null) {
            echo json_encode(['success' => false, 'message' => 'No access to session.']);
            return;
        }
        if (mb_strlen($snapshot) > 250000) {
            echo json_encode(['success' => false, 'message' => 'Snapshot is too large.']);
            return;
        }
        if (is_array($boardsSnapshot)) {
            $boardsJson = json_encode($boardsSnapshot, JSON_UNESCAPED_UNICODE);
            $upd = $pdo->prepare("UPDATE interview_sessions SET code_snapshot = ?, boards_snapshot = ? WHERE id = ?");
            $upd->execute([$snapshot, $boardsJson, (int) $session['id']]);
        } else {
            $upd = $pdo->prepare("UPDATE interview_sessions SET code_snapshot = ? WHERE id = ?");
            $upd->execute([$snapshot, (int) $session['id']]);
        }
        echo json_encode(['success' => true]);
    } catch (Throwable $e) {
        error_log('[INTERVIEW_SAVE] ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Не удалось сохранить код.']);
    }
}

function handleInterviewEnd()
{
    try {
        $user = getCurrentUser();
        $userId = (int) ($user['id'] ?? 0);
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || $userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }
        $code = trim((string) ($data['code'] ?? ''));
        if ($code === '') {
            echo json_encode(['success' => false, 'message' => 'Invalid code.']);
            return;
        }
        $pdo = getDBConnection();
        ensureInterviewsSchema($pdo);
        $session = tfInterviewFetchSession($pdo, $code);
        if (!$session) {
            echo json_encode(['success' => false, 'message' => 'Session not found.']);
            return;
        }
        if ((int) ($session['interview_user_id'] ?? 0) !== $userId) {
            echo json_encode(['success' => false, 'message' => 'No access to session.']);
            return;
        }
        $sessionId = (int) $session['id'];
        $interviewId = (int) $session['interview_id'];
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM interview_messages WHERE session_id = ?")->execute([$sessionId]);
        $pdo->prepare("DELETE FROM interview_participants WHERE session_id = ?")->execute([$sessionId]);
        $pdo->prepare("DELETE FROM interview_sessions WHERE id = ?")->execute([$sessionId]);
        $pdo->prepare("UPDATE interviews SET status = 'completed' WHERE id = ?")->execute([$interviewId]);
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('[INTERVIEW_END] ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Не удалось завершить собеседование.']);
    }
}

function handleInterviewDelete()
{
    try {
        $user = getCurrentUser();
        $userId = (int) ($user['id'] ?? 0);
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || $userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }
        $interviewId = (int) ($data['interview_id'] ?? 0);
        if ($interviewId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid interview.']);
            return;
        }
        $pdo = getDBConnection();
        ensureInterviewsSchema($pdo);
        $check = $pdo->prepare("SELECT id FROM interviews WHERE id = ? AND user_id = ? LIMIT 1");
        $check->execute([$interviewId, $userId]);
        if (!$check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'No access to interview.']);
            return;
        }
        $pdo->beginTransaction();
        $sessStmt = $pdo->prepare("SELECT id FROM interview_sessions WHERE interview_id = ?");
        $sessStmt->execute([$interviewId]);
        $sessionIds = $sessStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        foreach ($sessionIds as $sid) {
            $pdo->prepare("DELETE FROM interview_messages WHERE session_id = ?")->execute([(int) $sid]);
            $pdo->prepare("DELETE FROM interview_participants WHERE session_id = ?")->execute([(int) $sid]);
        }
        $pdo->prepare("DELETE FROM interview_sessions WHERE interview_id = ?")->execute([$interviewId]);
        $pdo->prepare("DELETE FROM interviews WHERE id = ?")->execute([$interviewId]);
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('[INTERVIEW_DELETE] ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Не удалось удалить собеседование.']);
    }
}

function handleInterviewTimerUpdate()
{
    try {
        $user = getCurrentUser();
        $userId = (int) ($user['id'] ?? 0);
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || $userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }
        $code = trim((string) ($data['code'] ?? ''));
        $remaining = max(0, (int) ($data['remaining'] ?? 0));
        $isRunning = !empty($data['is_running']) ? 1 : 0;
        if ($code === '') {
            echo json_encode(['success' => false, 'message' => 'Invalid code.']);
            return;
        }
        $pdo = getDBConnection();
        ensureInterviewsSchema($pdo);
        $session = tfInterviewFetchSession($pdo, $code);
        if (!$session) {
            echo json_encode(['success' => false, 'message' => 'Session not found.']);
            return;
        }
        $role = tfInterviewEnsureParticipant($pdo, $session, $userId, false);
        if ($role !== 'owner') {
            echo json_encode(['success' => false, 'message' => 'No access to timer.']);
            return;
        }
        $upd = $pdo->prepare("UPDATE interview_sessions SET remaining_seconds = ?, is_running = ? WHERE id = ?");
        $upd->execute([$remaining, $isRunning, (int) $session['id']]);
        echo json_encode(['success' => true]);
    } catch (Throwable $e) {
        error_log('[INTERVIEW_TIMER] ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Не удалось обновить таймер.']);
    }
}

function routeRequest()
{
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($isAjax) {
        ini_set('display_errors', 0);
        error_reporting(0);
        tfEnableJsonOutputNormalization();
    }

    $actionRaw = $_GET['action'] ?? $_POST['action'] ?? 'home';
    tfEnforceCsrf($isAjax);
    $action = is_string($actionRaw) ? trim($actionRaw) : '';
    if ($action === '') {
        $action = 'home';
    }
    if (!$isAjax && in_array($action, ['home-like-toggle', 'home-like-state', 'home-ai-insights'], true)) {
        $isAjax = true;
        ini_set('display_errors', 0);
        error_reporting(0);
        tfEnableJsonOutputNormalization();
    }
    if (!$isAjax) {
        tfEnableHtmlOutputNormalization();
    }
    $user = getCurrentUser();

    if ($user && !empty($user['is_blocked'])) {
        if ($isAjax) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'message' => 'Аккаунт заблокирован.']);
            return;
        }
        $_SESSION = [];
        if (session_id() !== '') {
            session_destroy();
        }
        header('Location: index.php?action=login');
        exit;
    }

    $protectedActions = [
        'dashboard',
        'courses',
        'courses-interview',
        'visualizations',
        'contest',
        'contests',
        'interview',
        'interview-ai',
        'interview-ai-history',
        'interview-ai-session',
        'interview-ai-chat',
        'interview-ai-score',
        'interview-room',
        'interview-create',
        'interview-join',
        'interview-get',
        'interview-message',
        'interview-code-save',
        'interview-timer-update',
        'interview-end',
        'interview-delete',
        'interview-ai-coach',
        'community',
        'vacancies',
        'ratings',
        'profile',
        'profile-view',
        'settings',
        'admin',
        'get-course',
        'complete-lesson',
        'submit-practice',
        'contest-submit',
        'interview-submit',
        'community-create-post',
        'community-create-comment',
        'community-update-post',
        'community-delete-post',
        'community-like-post',
        'community-view-post',
        'chat',
        'apply-vacancy',
        'roadmap',
        'roadmaps',
        'certificate',
        'vacancy-chat',
        'git-trainer',
        'it-events'
    ];
    if (in_array($action, $protectedActions) && !$user) {
        if ($isAjax) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'message' => 'Ошибка запроса.']);
            return;
        }
        header('Location: index.php?action=login');
        exit;
    }

    if ($action === 'logout') {
        handleLogout();
        return;
    }

    if (in_array($action, ['roadmap-data', 'roadmap-save-progress', 'roadmap-issue-certificate'], true)) {
        tfEnableJsonOutputNormalization();
        header('Content-Type: application/json; charset=UTF-8');
        if ($action === 'roadmap-data') {
            handleRoadmapGetData();
            return;
        }
        if ($action === 'roadmap-save-progress') {
            handleRoadmapSaveProgress();
            return;
        }
        if ($action === 'roadmap-issue-certificate') {
            handleRoadmapIssueCertificate();
            return;
        }
    }

    // AJAX обработчики
    if ($isAjax) {
        header('Content-Type: application/json; charset=UTF-8');
        switch ($action) {
            case 'register':
                handleRegistration();
                break;
            case 'login':
                handleLogin();
                break;
            case 'google-login':
                handleGoogleLogin();
                break;
            case 'session-status':
                handleSessionStatus();
                break;
            case 'logout':
                handleLogout();
                break;
            case 'get-course':
                handleGetCourse();
                break;
            case 'complete-lesson':
                handleCompleteLesson();
                break;
            case 'submit-practice':
                handleSubmitPractice();
                break;
            case 'admin-submission-detail':
                handleAdminSubmissionDetail();
                break;
            case 'admin-reset-contest-submission':
                handleAdminResetContestSubmission();
                break;
            case 'contest-submit':
                handleContestSubmit();
                break;
            case 'interview-submit':
                handleInterviewSubmit();
                break;
            case 'interview-ai-coach':
                handleInterviewAiCoach();
                break;
            case 'interview-ai-history':
                handleInterviewAiHistory();
                break;
            case 'interview-ai-session':
                handleInterviewAiSessionGet();
                break;
            case 'interview-ai-chat':
                handleInterviewAiChat();
                break;
            case 'interview-ai-score':
                handleInterviewAiScore();
                break;
            case 'community-create-post':
                handleCommunityCreatePost();
                break;
            case 'community-create-comment':
                handleCommunityCreateComment();
                break;
            case 'community-update-post':
                handleCommunityUpdatePost();
                break;
            case 'community-delete-post':
                handleCommunityDeletePost();
                break;
            case 'community-like-post':
                handleCommunityLikePost();
                break;
            case 'community-view-post':
                handleCommunityViewPost();
                break;
            case 'chat-message':
                handleChatMessage();
                break;
            case 'get-chat':
                handleGetChat();
                break;
            case 'clear-chat':
                handleClearChat();
                break;
            case 'create-vacancy':
                handleCreateVacancy();
                break;
            case 'get-vacancy':
                handleGetVacancy();
                break;
            case 'apply-vacancy':
                handleApplyToVacancy();
                break;
            case 'vacancy-chat-get':
                handleVacancyChatGet();
                break;
            case 'vacancy-chat-send':
                handleVacancyChatSend();
                break;
            case 'vacancy-doc-get':
                handleVacancyDocumentsGet();
                break;
            case 'vacancy-doc-upload':
                handleVacancyDocumentUpload();
                break;
            case 'vacancy-employment-status':
                handleVacancyEmploymentStatus();
                break;
            case 'platform-review':
                handlePlatformReview();
                break;
            case 'home-like-toggle':
                handleHomeLikeToggle();
                break;
            case 'home-like-state':
                handleHomeLikeState();
                break;
            case 'home-ai-insights':
                handleHomeAiInsights();
                break;
            case 'get-top-users':
                handleGetTopUsers();
                break;
            case 'add-experience':
                handleAddExperience();
                break;
            case 'add-education':
                handleAddEducation();
                break;
            case 'update-experience':
                handleUpdateExperience();
                break;
            case 'update-education':
                handleUpdateEducation();
                break;
            case 'add-portfolio':
                handleAddPortfolio();
                break;
            case 'update-portfolio':
                handleUpdatePortfolio();
                break;
            case 'delete-experience':
                handleDeleteExperience();
                break;
            case 'delete-education':
                handleDeleteEducation();
                break;
            case 'delete-portfolio':
                handleDeletePortfolio();
                break;
            case 'add-skill':
                handleAddSkill();
                break;
            case 'update-skill':
                handleUpdateSkill();
                break;
            case 'delete-skill':
                handleDeleteSkill();
                break;
            case 'verify-skill':
                handleVerifySkill();
                break;
            case 'skill-assessment-state':
                handleSkillAssessmentState();
                break;
            case 'skill-assessment-round-1':
                handleSkillAssessmentRound(1);
                break;
            case 'skill-assessment-round-2':
                handleSkillAssessmentRound(2);
                break;
            case 'skill-assessment-round-3':
                handleSkillAssessmentRound(3);
                break;
            case 'skill-assessment-surrender':
                handleSkillAssessmentSurrender();
                break;
            case 'skill-quiz':
                handleSkillQuiz();
                break;
            case 'update-profile':
                handleUpdateProfile();
                break;
            case 'save-cv-customization':
                handleSaveCvCustomization();
                break;
            case 'reset-cv-customization':
                handleResetCvCustomization();
                break;
            case 'change-password':
                handleChangePassword();
                break;
            case 'get-notifications':
                handleGetNotifications();
                break;
            case 'mark-notifications-read':
                handleMarkNotificationsRead();
                break;
            case 'create-custom-course':
                handleCreateCustomCourse();
                break;
            case 'interview-create':
                handleInterviewCreate();
                break;
            case 'interview-join':
                handleInterviewJoin();
                break;
            case 'interview-get':
                handleInterviewGet();
                break;
            case 'interview-message':
                handleInterviewMessage();
                break;
            case 'interview-code-save':
                handleInterviewCodeSave();
                break;
            case 'interview-timer-update':
                handleInterviewTimerUpdate();
                break;
            case 'interview-end':
                handleInterviewEnd();
                break;
            case 'interview-delete':
                handleInterviewDelete();
                break;
            case 'check-answer':
                handleCheckShortAnswer();
                break;
            case 'analyze-cheating':
                handleAnalyzeCheating();
                break;
            case 'generate-password':
                handleGeneratePassword();
                break;
            case 'upload-image':
                handleUploadImage();
                break;
            case 'admin-get-user':
                handleAdminGetUser();
                break;
            case 'admin-create-user':
                handleAdminCreateUser();
                break;
            case 'admin-update-user':
                handleAdminUpdateUser();
                break;
            case 'admin-delete-user':
                handleAdminDeleteUser();
                break;
            case 'admin-reset-user-contests':
                handleAdminResetUserContestProgress();
                break;
            case 'toggle-user-block':
                handleAdminToggleUserBlock();
                break;
            case 'admin-toggle-user-block':
                handleAdminToggleUserBlock();
                break;
            case 'update-user-role':
                handleAdminUpdateUserRole();
                break;
            case 'admin-update-user-role':
                handleAdminUpdateUserRole();
                break;
            case 'admin-get-course':
                handleAdminGetCourse();
                break;
            case 'admin-get-contest':
                handleAdminGetContest();
                break;
            case 'admin-create-contest':
                handleAdminCreateContest();
                break;
            case 'admin-update-contest':
                handleAdminUpdateContest();
                break;
            case 'admin-delete-contest':
                handleAdminDeleteContest();
                break;
            case 'admin-get-contest-task':
                handleAdminGetContestTask();
                break;
            case 'admin-import-contest-task-package':
                handleAdminImportContestTaskPackage();
                break;
            case 'admin-import-interview-prep-folders':
                handleAdminImportInterviewPrepFolders();
                break;
            case 'admin-ejudge-scan':
                handleAdminEjudgeScan();
                break;
            case 'admin-ejudge-import':
                handleAdminEjudgeImport();
                break;
            case 'admin-create-contest-task':
                handleAdminCreateContestTask();
                break;
            case 'admin-update-contest-task':
                handleAdminUpdateContestTask();
                break;
            case 'admin-delete-contest-task':
                handleAdminDeleteContestTask();
                break;
            case 'admin-get-interview-prep-task':
                handleAdminGetInterviewPrepTask();
                break;
            case 'admin-create-interview-prep-task':
                handleAdminCreateInterviewPrepTask();
                break;
            case 'admin-update-interview-prep-task':
                handleAdminUpdateInterviewPrepTask();
                break;
            case 'admin-delete-interview-prep-task':
                handleAdminDeleteInterviewPrepTask();
                break;
            case 'admin-create-course':
                handleAdminCreateCourse();
                break;
            case 'admin-update-course':
                handleAdminUpdateCourse();
                break;
            case 'admin-seed-learning-pack':
                handleAdminSeedLearningPack();
                break;
            case 'delete-course':
                handleAdminDeleteCourse();
                break;
            case 'admin-delete-course':
                handleAdminDeleteCourse();
                break;
            case 'admin-get-lesson':
                handleAdminGetLesson();
                break;
            case 'admin-create-lesson':
                handleAdminCreateLesson();
                break;
            case 'admin-update-lesson':
                handleAdminUpdateLesson();
                break;
            case 'admin-delete-lesson':
                handleAdminDeleteLesson();
                break;
            case 'admin-create-vacancy':
                handleAdminCreateVacancy();
                break;
            case 'admin-update-vacancy':
                handleAdminUpdateVacancy();
                break;
            case 'delete-vacancy':
                handleAdminDeleteVacancy();
                break;
            case 'admin-delete-vacancy':
                handleAdminDeleteVacancy();
                break;
            case 'admin-create-notification':
                handleAdminCreateNotification();
                break;
            case 'admin-delete-notification':
                handleAdminDeleteNotification();
                break;
            case 'roadmap-data':
                handleRoadmapGetData();
                break;
            case 'roadmap-save-progress':
                handleRoadmapSaveProgress();
                break;
            case 'roadmap-issue-certificate':
                handleRoadmapIssueCertificate();
                break;
            case 'admin-roadmap-get-node':
                handleAdminRoadmapGetNode();
                break;
            case 'admin-roadmap-get-roadmap':
                handleAdminRoadmapGetRoadmap();
                break;
            case 'admin-roadmap-create-node':
                handleAdminRoadmapCreateNode();
                break;
            case 'admin-roadmap-create-roadmap':
                handleAdminRoadmapCreateRoadmap();
                break;
            case 'admin-roadmap-update-node':
                handleAdminRoadmapUpdateNode();
                break;
            case 'admin-roadmap-update-roadmap':
                handleAdminRoadmapUpdateRoadmap();
                break;
            case 'admin-roadmap-delete-node':
                handleAdminRoadmapDeleteNode();
                break;
            case 'admin-roadmap-delete-roadmap':
                handleAdminRoadmapDeleteRoadmap();
                break;
            case 'admin-roadmap-get-lesson':
                handleAdminRoadmapGetLesson();
                break;
            case 'admin-roadmap-create-lesson':
                handleAdminRoadmapCreateLesson();
                break;
            case 'admin-roadmap-update-lesson':
                handleAdminRoadmapUpdateLesson();
                break;
            case 'admin-roadmap-delete-lesson':
                handleAdminRoadmapDeleteLesson();
                break;
            case 'admin-roadmap-get-quiz':
                handleAdminRoadmapGetQuiz();
                break;
            case 'admin-roadmap-create-quiz':
                handleAdminRoadmapCreateQuiz();
                break;
            case 'admin-roadmap-update-quiz':
                handleAdminRoadmapUpdateQuiz();
                break;
            case 'admin-roadmap-delete-quiz':
                handleAdminRoadmapDeleteQuiz();
                break;
            case 'admin-get-course-exam':
                handleAdminGetCourseExam();
                break;
            case 'admin-save-course-exam':
                handleAdminSaveCourseExam();
                break;
            case 'admin-delete-course-exam':
                handleAdminDeleteCourseExam();
                break;
            case 'course-issue-certificate':
                handleCourseIssueCertificate();
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Неизвестное действие']);
        }
        return;
    }

    // Параметры вкладок
    $tab = $_GET['tab'] ?? '';
    if ($action === 'admin' && $tab === '') {
        $tab = 'dashboard';
    }
    if (($action === 'profile' || $action === 'settings') && $tab === '') {
        $tab = 'overview';
    }
    $search = $_GET['search'] ?? '';
    $filter = $_GET['filter'] ?? '';
    $levelFilter = $_GET['level'] ?? '';

    switch ($action) {
        case 'login':
            if ($user) {
                header('Location: ?action=dashboard');
                exit;
            }
            require_once __DIR__ . '/templates/login.php';
            break;
        case 'blog':
        case 'about':
        case 'contacts':
        case 'partners':
        case 'support':
        case 'docs':
        case 'charity':
        case 'privacy':
        case 'terms':
            $pageKey = $action;
            $pageTitle = t('page_' . $pageKey . '_title', ucfirst($pageKey));
            $pageSubtitle = t('page_' . $pageKey . '_subtitle', '');
            $pageSections = [
                [
                    'title' => t('page_' . $pageKey . '_section_1_title', ''),
                    'body' => t('page_' . $pageKey . '_section_1_body', ''),
                ],
                [
                    'title' => t('page_' . $pageKey . '_section_2_title', ''),
                    'body' => t('page_' . $pageKey . '_section_2_body', ''),
                ],
                [
                    'title' => t('page_' . $pageKey . '_section_3_title', ''),
                    'body' => t('page_' . $pageKey . '_section_3_body', ''),
                ],
            ];
            $pageSections = array_values(array_filter($pageSections, function ($s) {
                return !empty($s['title']) || !empty($s['body']);
            }));
            $pageHighlights = [
                [
                    'title' => t('page_' . $pageKey . '_highlight_1_title', ''),
                    'body' => t('page_' . $pageKey . '_highlight_1_body', ''),
                ],
                [
                    'title' => t('page_' . $pageKey . '_highlight_2_title', ''),
                    'body' => t('page_' . $pageKey . '_highlight_2_body', ''),
                ],
                [
                    'title' => t('page_' . $pageKey . '_highlight_3_title', ''),
                    'body' => t('page_' . $pageKey . '_highlight_3_body', ''),
                ],
            ];
            $pageHighlights = array_values(array_filter($pageHighlights, function ($s) {
                return !empty($s['title']) || !empty($s['body']);
            }));
            $pageCta = [
                'title' => t('page_' . $pageKey . '_cta_title', ''),
                'body' => t('page_' . $pageKey . '_cta_body', ''),
                'button' => t('page_' . $pageKey . '_cta_button', ''),
            ];
            if (empty($pageCta['title']) && empty($pageCta['body']) && empty($pageCta['button'])) {
                $pageCta = [];
            }
            $supportForm = [];
            $supportFlash = null;
            if ($pageKey === 'support') {
                $supportForm = [
                    'name' => trim((string) ($user['name'] ?? '')),
                    'email' => trim((string) ($user['email'] ?? '')),
                    'subject' => '',
                    'message' => '',
                    'priority' => 'normal',
                ];
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $supportForm['name'] = trim((string) ($_POST['name'] ?? ''));
                    $supportForm['email'] = trim((string) ($_POST['email'] ?? ''));
                    $supportForm['subject'] = trim((string) ($_POST['subject'] ?? ''));
                    $supportForm['message'] = trim((string) ($_POST['message'] ?? ''));
                    $priority = trim((string) ($_POST['priority'] ?? 'normal'));
                    $supportForm['priority'] = in_array($priority, ['low', 'normal', 'high'], true) ? $priority : 'normal';

                    if ($supportForm['name'] === '' || $supportForm['email'] === '' || $supportForm['subject'] === '' || $supportForm['message'] === '') {
                        $supportFlash = ['type' => 'error', 'message' => t('support_form_error_required', 'Please fill in all required fields.')];
                    } elseif (!filter_var($supportForm['email'], FILTER_VALIDATE_EMAIL)) {
                        $supportFlash = ['type' => 'error', 'message' => t('support_form_error_email', 'Please enter a valid email.')];
                    } elseif (mb_strlen($supportForm['subject']) > 190 || mb_strlen($supportForm['message']) > 5000) {
                        $supportFlash = ['type' => 'error', 'message' => t('support_form_error_length', 'Message is too long.')];
                    } else {
                        try {
                            $pdo = getDBConnection();
                            ensureSupportRequestsTable($pdo);
                            $stmt = $pdo->prepare("
                                INSERT INTO support_requests (user_id, name, email, subject, message, priority)
                                VALUES (?, ?, ?, ?, ?, ?)
                            ");
                            $stmt->execute([
                                !empty($user['id']) ? (int) $user['id'] : null,
                                $supportForm['name'],
                                $supportForm['email'],
                                $supportForm['subject'],
                                $supportForm['message'],
                                $supportForm['priority'],
                            ]);
                            $supportFlash = ['type' => 'success', 'message' => t('support_form_success', 'Your request has been sent.')];
                            $supportForm['subject'] = '';
                            $supportForm['message'] = '';
                            $supportForm['priority'] = 'normal';
                        } catch (Throwable $e) {
                            $supportFlash = ['type' => 'error', 'message' => t('support_form_error', 'Failed to send request. Please try later.')];
                        }
                    }
                }
            }
            require_once __DIR__ . '/templates/static_page.php';
            break;
        case 'home':
            $pdo = getDBConnection();
            ensureVacancyChatTables($pdo);
            ensureHomeEngagementTables($pdo);
            $platformReviews = [];
            $homeCourses = [];
            $homeVacancies = [];
            $homeCompanies = [];
            $homeLikes = [
                'course' => ['counts' => [], 'liked' => []],
                'vacancy' => ['counts' => [], 'liked' => []],
                'review' => ['counts' => [], 'liked' => []]
            ];
            $homeStats = [
                'students_count' => 0,
                'courses_count' => 0,
                'vacancies_count' => 0,
                'completed_courses_count' => 0,
                'avg_rating' => 0
            ];

            try {
                $stmt = $pdo->query("
                    SELECT pr.id, pr.rating, pr.comment, pr.created_at, u.name, u.role, u.avatar
                    FROM platform_reviews pr
                    INNER JOIN users u ON u.id = pr.user_id
                    WHERE u.is_blocked = FALSE
                    ORDER BY pr.created_at DESC
                    LIMIT 6
                ");
                $platformReviews = $stmt->fetchAll();
            } catch (Throwable $e) {
                $platformReviews = [];
            }

            try {
                $stmt = $pdo->query("
                    SELECT c.*,
                           (SELECT COUNT(*) FROM user_course_progress ucp WHERE ucp.course_id = c.id) as students_count,
                           (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id) as lessons_count
                    FROM courses c
                    ORDER BY c.created_at DESC
                    LIMIT 3
                ");
                $homeCourses = $stmt->fetchAll();
            } catch (Throwable $e) {
                $homeCourses = [];
            }

            try {
                $stmt = $pdo->query("
                    SELECT id, title, company, location, type, salary_min, salary_max, salary_currency, created_at
                    FROM vacancies
                    ORDER BY created_at DESC, id DESC
                    LIMIT 4
                ");
                $homeVacancies = $stmt->fetchAll();
            } catch (Throwable $e) {
                $homeVacancies = [];
            }

            try {
                $stmt = $pdo->query("
                    SELECT DISTINCT company
                    FROM vacancies
                    WHERE company IS NOT NULL AND company <> ''
                    ORDER BY company ASC
                    LIMIT 8
                ");
                $homeCompanies = array_values(array_filter($stmt->fetchAll(PDO::FETCH_COLUMN)));
            } catch (Throwable $e) {
                $homeCompanies = [];
            }

            try {
                $homeStats['students_count'] = (int) ($pdo->query("SELECT COUNT(*) as total FROM users WHERE role IN ('seeker','recruiter') AND is_blocked = FALSE")->fetch()['total'] ?? 0);
                $homeStats['courses_count'] = (int) ($pdo->query("SELECT COUNT(*) as total FROM courses")->fetch()['total'] ?? 0);
                $homeStats['vacancies_count'] = (int) ($pdo->query("SELECT COUNT(*) as total FROM vacancies")->fetch()['total'] ?? 0);
                $homeStats['completed_courses_count'] = (int) ($pdo->query("SELECT COUNT(*) as total FROM user_course_progress WHERE completed = TRUE")->fetch()['total'] ?? 0);
                $homeStats['avg_rating'] = (float) ($pdo->query("SELECT AVG(rating) as avg_rating FROM platform_reviews")->fetch()['avg_rating'] ?? 0);
            } catch (Throwable $e) {
            }

            try {
                $viewer = getHomeLikeViewer();
                $courseIds = array_map(static fn($c) => (int) ($c['id'] ?? 0), $homeCourses);
                $vacancyIds = array_map(static fn($v) => (int) ($v['id'] ?? 0), $homeVacancies);
                $reviewIds = array_map(static fn($r) => (int) ($r['id'] ?? 0), $platformReviews);
                $homeLikes['course'] = getHomeLikesForEntities($pdo, 'course', $courseIds, $viewer['user_id'], $viewer['session_id']);
                $homeLikes['vacancy'] = getHomeLikesForEntities($pdo, 'vacancy', $vacancyIds, $viewer['user_id'], $viewer['session_id']);
                $homeLikes['review'] = getHomeLikesForEntities($pdo, 'review', $reviewIds, $viewer['user_id'], $viewer['session_id']);
            } catch (Throwable $e) {
                $homeLikes = [
                    'course' => ['counts' => [], 'liked' => []],
                    'vacancy' => ['counts' => [], 'liked' => []],
                    'review' => ['counts' => [], 'liked' => []]
                ];
            }
            require_once __DIR__ . '/templates/home.php';
            break;
        case 'dashboard':
            $pdo = getDBConnection();
            $chatMessages = [];
            if (!empty($user['id'])) {
                $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE user_id = ? ORDER BY sent_at DESC LIMIT 50");
                $stmt->execute([$user['id']]);
                $chatMessages = array_reverse($stmt->fetchAll());
            }
            $recommendedCourses = [];
            if (!empty($user['id'])) {
                $stmt = $pdo->prepare("
    SELECT c.*, ucp.progress, ucp.completed
    FROM courses c
    LEFT JOIN user_course_progress ucp ON c.id = ucp.course_id AND ucp.user_id = ?
    ORDER BY c.created_at DESC
    LIMIT 2
");
                $stmt->execute([$user['id']]);
                $recommendedCourses = $stmt->fetchAll();
            }
            $topUsers = getTopUsers(3);
            require_once __DIR__ . '/templates/dashboard.php';
            break;
        case 'courses':
            $pdo = getDBConnection();
            $filter = $_GET['filter'] ?? '';
            $levelFilter = $_GET['level'] ?? '';
            $search = $_GET['search'] ?? '';
            $userId = (int) ($user['id'] ?? 0);
            $page = (int) ($_GET['page'] ?? 1);
            if ($page < 1) {
                $page = 1;
            }
            $perPage = 3;
            $offset = ($page - 1) * $perPage;

            $allowedFilters = ['frontend', 'backend', 'design', 'devops', 'other'];
            if (!in_array($filter, $allowedFilters, true)) {
                $filter = '';
            }
            $allowedLevels = ['начальный', 'средний', 'продвинутый'];
            if (!in_array($levelFilter, $allowedLevels, true)) {
                $levelFilter = '';
            }

            $where = [];
            $params = [];
            if ($filter !== '') {
                $where[] = 'c.category = ?';
                $params[] = $filter;
            }
            if ($levelFilter !== '') {
                $where[] = 'c.level = ?';
                $params[] = $levelFilter;
            }
            if ($search !== '') {
                $where[] = '(c.title LIKE ? OR c.description LIKE ? OR c.instructor LIKE ?)';
                $like = '%' . $search . '%';
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
            }
            $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

            $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM courses c $whereSql");
            $countStmt->execute($params);
            $totalCourses = (int) ($countStmt->fetch()['total'] ?? 0);
            $totalPages = (int) ceil($totalCourses / $perPage);
            if ($totalPages > 0 && $page > $totalPages) {
                $page = $totalPages;
                $offset = ($page - 1) * $perPage;
            }

            $stmt = $pdo->prepare("
                SELECT c.*,
                       COALESCE(ROUND(
                           100.0 * (
                               SELECT COUNT(*)
                               FROM user_lesson_progress ulp
                               JOIN lessons l ON l.id = ulp.lesson_id
                               WHERE ulp.user_id = ? AND ulp.completed = TRUE AND l.course_id = c.id
                           ) / NULLIF((
                               SELECT COUNT(*)
                               FROM lessons l2
                               WHERE l2.course_id = c.id
                           ), 0)
                       ), 0) as progress
                FROM courses c
                $whereSql
                ORDER BY c.created_at DESC
                LIMIT $perPage OFFSET $offset
            ");
            $stmt->execute(array_merge([$userId], $params));
            $courses = $stmt->fetchAll();
            require_once __DIR__ . '/templates/courses.php';
            break;
        case 'courses-interview':
            $pdo = getDBConnection();
            ensureContestsSchema($pdo);
            $stmt = $pdo->query("
                SELECT *
                FROM interview_prep_tasks
                WHERE is_active = 1
                ORDER BY sort_order ASC, id ASC
            ");
            $interviewProblems = $stmt ? ($stmt->fetchAll() ?: []) : [];
            if (!empty($interviewProblems)) {
                foreach ($interviewProblems as &$problem) {
                    $problem['title'] = normalizeMojibakeText((string) ($problem['title'] ?? ''));
                    $problem['category'] = normalizeMojibakeText((string) ($problem['category'] ?? 'General'));
                    $problem['statement'] = normalizeMojibakeText((string) ($problem['statement'] ?? ''));
                    $problem['input'] = normalizeMojibakeText((string) ($problem['input_spec'] ?? ''));
                    $problem['output'] = normalizeMojibakeText((string) ($problem['output_spec'] ?? ''));
                    $problem['starter_cpp'] = (string) ($problem['starter_cpp'] ?? tfAdminDefaultStarterCode('cpp'));
                    $problem['starter_python'] = (string) ($problem['starter_python'] ?? tfAdminDefaultStarterCode('python'));
                    $problem['starter_c'] = tfAdminDefaultStarterCode('c');
                    $problem['starter_csharp'] = tfAdminDefaultStarterCode('csharp');
                    $problem['starter_java'] = tfAdminDefaultStarterCode('java');
                    $problem['tests'] = json_decode((string) ($problem['tests_json'] ?? '[]'), true) ?: [];
                    $problem['difficulty'] = ucfirst(tfAdminNormalizeDifficulty((string) ($problem['difficulty'] ?? 'easy')));
                }
                unset($problem);
            } else {
                $interviewProblems = function_exists('tfGetInterviewProblemsDataRich')
                    ? tfGetInterviewProblemsDataRich()
                    : tfGetInterviewProblemsData();
            }
            $trendingCompanies = tfGetTrendingCompaniesData();
            require_once __DIR__ . '/templates/courses_interview.php';
            break;
        case 'visualizations':
            require_once __DIR__ . '/templates/visualizations.php';
            break;
        case 'contests':
            $pdo = getDBConnection();
            ensureContestsSchema($pdo);
            tfSeedDefaultContests($pdo);
            tfAutoLockExpiredContests($pdo);
            $stmt = $pdo->query("
                SELECT c.*,
                       (SELECT COUNT(*) FROM contest_tasks ct WHERE ct.contest_id = c.id) as tasks_count
                FROM contests c
                WHERE c.is_active = 1
                ORDER BY c.created_at DESC, c.id DESC
            ");
            $contests = $stmt->fetchAll();
            foreach ($contests as &$contestRow) {
                $contestRow['title'] = normalizeMojibakeText((string) ($contestRow['title'] ?? ''));
                $contestRow['description'] = normalizeMojibakeText((string) ($contestRow['description'] ?? ''));
            }
            unset($contestRow);
            $contestLeaderboard = getContestLeaderboard($pdo, 20);
            require_once __DIR__ . '/templates/contests.php';
            break;
        case 'contest':
            $pdo = getDBConnection();
            ensureContestsSchema($pdo);
            tfSeedDefaultContests($pdo);
            tfAutoLockExpiredContests($pdo);
            $contestId = (int) ($_GET['id'] ?? 0);
            if ($contestId <= 0) {
                $fallback = $pdo->query("
                    SELECT c.id
                    FROM contests c
                    WHERE c.is_active = 1
                    ORDER BY
                        (SELECT COUNT(*) FROM contest_tasks ct WHERE ct.contest_id = c.id) DESC,
                        c.created_at DESC,
                        c.id DESC
                    LIMIT 1
                ")->fetch();
                if (!empty($fallback['id'])) {
                    header('Location: ?action=contest&id=' . (int) $fallback['id']);
                    exit;
                }
                header('Location: ?action=contests');
                exit;
            }
            $stmt = $pdo->prepare("SELECT * FROM contests WHERE id = ? LIMIT 1");
            $stmt->execute([$contestId]);
            $contest = $stmt->fetch();
            if (!$contest) {
                header('Location: ?action=contests');
                exit;
            }
            $contest['title'] = normalizeMojibakeText((string) ($contest['title'] ?? ''));
            $contest['description'] = normalizeMojibakeText((string) ($contest['description'] ?? ''));
            $stmt = $pdo->prepare("SELECT * FROM contest_tasks WHERE contest_id = ? ORDER BY order_num ASC, id ASC");
            $stmt->execute([$contestId]);
            $contestTasks = $stmt->fetchAll();
            $isBrokenText = static function ($text) {
                if (!is_string($text) || $text === '')
                    return false;
                if (strpos($text, '?') !== false)
                    return true;
                if (substr_count($text, '?') >= 3)
                    return true;
                if (preg_match('/[??][^\s]/u', $text))
                    return true;
                return false;
            };
            $baseTaskTemplates = [
                1 => [
                    'title' => 'A + B',
                    'statement' => 'Даны два целых числа A и B. Выведите их сумму.',
                    'input_spec' => 'Ввод: A B',
                    'output_spec' => 'Вывод: A + B',
                ],
                2 => [
                    'title' => 'Палиндром',
                    'statement' => 'Дана строка S. Выведите YES, если строка является палиндромом, иначе NO.',
                    'input_spec' => 'Ввод: S',
                    'output_spec' => 'Вывод: YES или NO',
                ],
                3 => [
                    'title' => 'Максимум массива',
                    'statement' => 'Даны N и массив из N целых чисел. Найдите максимальный элемент.',
                    'input_spec' => "Ввод: N\nмассив из N чисел",
                    'output_spec' => 'Вывод: максимальный элемент',
                ],
            ];
            foreach ($contestTasks as &$task) {
                $task['title'] = normalizeMojibakeText((string) ($task['title'] ?? ''));
                $task['difficulty'] = normalizeMojibakeText((string) ($task['difficulty'] ?? ''));
                $task['statement'] = normalizeMojibakeText((string) ($task['statement'] ?? ''));
                $task['input_spec'] = normalizeMojibakeText((string) ($task['input_spec'] ?? ''));
                $task['output_spec'] = normalizeMojibakeText((string) ($task['output_spec'] ?? ''));

                $orderNum = (int) ($task['order_num'] ?? 0);
                if (isset($baseTaskTemplates[$orderNum])) {
                    $tpl = $baseTaskTemplates[$orderNum];
                    if ($isBrokenText($task['statement']))
                        $task['statement'] = $tpl['statement'];
                    if ($isBrokenText($task['input_spec']))
                        $task['input_spec'] = $tpl['input_spec'];
                    if ($isBrokenText($task['output_spec']))
                        $task['output_spec'] = $tpl['output_spec'];
                    if ($isBrokenText($task['title']))
                        $task['title'] = $tpl['title'];
                }
            }
            unset($task);
            if (empty($contestTasks)) {
                $stmt = $pdo->prepare("
                    SELECT c.id
                    FROM contests c
                    WHERE c.is_active = 1
                      AND c.id <> ?
                    ORDER BY
                        (SELECT COUNT(*) FROM contest_tasks ct WHERE ct.contest_id = c.id) DESC,
                        c.created_at DESC,
                        c.id DESC
                    LIMIT 1
                ");
                $stmt->execute([$contestId]);
                $nextContest = $stmt->fetch();
                if (!empty($nextContest['id'])) {
                    header('Location: ?action=contest&id=' . (int) $nextContest['id']);
                    exit;
                }
            }
            $contestLeaderboard = getContestLeaderboardForContest($pdo, $contestId, 20);
            $contestSolvedTaskIds = [];
            $contestUserPoints = 0;
            $userId = (int) ($user['id'] ?? 0);
            if ($userId > 0) {
                $stmt = $pdo->prepare("SELECT task_id, points_awarded FROM contest_submissions WHERE user_id = ? AND contest_id = ? AND status = 'accepted'");
                $stmt->execute([$userId, $contestId]);
                $rows = $stmt->fetchAll();
                foreach ($rows as $row) {
                    $contestSolvedTaskIds[] = (int) ($row['task_id'] ?? 0);
                }
                foreach ($contestLeaderboard as $leaderRow) {
                    if ((int) ($leaderRow['id'] ?? 0) === $userId) {
                        $contestUserPoints = (int) ($leaderRow['contest_points'] ?? 0);
                        break;
                    }
                }
            }
            require_once __DIR__ . '/templates/contest.php';
            break;
        case 'it-events':
            require_once __DIR__ . '/templates/it_events.php';
            break;
        case 'community':
            $pdo = getDBConnection();
            ensureCommunitySchema($pdo);
            $posts = [];
            $commentsByPost = [];

            $stmt = $pdo->query("
                SELECT
                    cp.*,
                    u.name as author_name,
                    u.avatar as author_avatar,
                    (SELECT COUNT(*) FROM community_comments cc WHERE cc.post_id = cp.id) as comments_count
                FROM community_posts cp
                INNER JOIN users u ON u.id = cp.user_id
                WHERE u.is_blocked = FALSE
                ORDER BY cp.created_at DESC, cp.id DESC
                LIMIT 100
            ");
            $posts = $stmt->fetchAll() ?: [];

            if (!empty($posts)) {
                $postIds = array_values(array_filter(array_map(function ($row) {
                    return (int) ($row['id'] ?? 0);
                }, $posts)));
                if (!empty($postIds)) {
                    $in = implode(',', array_fill(0, count($postIds), '?'));
                    $stmt = $pdo->prepare("
                        SELECT
                            cc.*,
                            u.name as author_name,
                            u.avatar as author_avatar
                        FROM community_comments cc
                        INNER JOIN users u ON u.id = cc.user_id
                        WHERE cc.post_id IN ($in) AND u.is_blocked = FALSE
                        ORDER BY cc.created_at ASC, cc.id ASC
                    ");
                    $stmt->execute($postIds);
                    $allComments = $stmt->fetchAll() ?: [];
                    foreach ($allComments as $comment) {
                        $pid = (int) ($comment['post_id'] ?? 0);
                        if ($pid <= 0)
                            continue;
                        if (!isset($commentsByPost[$pid])) {
                            $commentsByPost[$pid] = [];
                        }
                        $commentsByPost[$pid][] = $comment;
                    }
                }
            }

            require_once __DIR__ . '/templates/community.php';
            break;
        case 'vacancies':
            $pdo = getDBConnection();
            $typeFilter = $_GET['type'] ?? '';
            $search = $_GET['search'] ?? '';
            $salaryMins = $_GET['salary_min'] ?? [];
            $skillsFilter = $_GET['skills'] ?? [];
            $page = (int) ($_GET['page'] ?? 1);
            if ($page < 1) {
                $page = 1;
            }
            $perPage = 9;
            $offset = ($page - 1) * $perPage;

            $allowedTypes = ['remote', 'office', 'hybrid'];
            if (!in_array($typeFilter, $allowedTypes, true)) {
                $typeFilter = '';
            }

            $where = [];
            $params = [];
            if ($typeFilter !== '') {
                $where[] = 'type = ?';
                $params[] = $typeFilter;
            }
            if ($search !== '') {
                $where[] = '(title LIKE ? OR company LIKE ? OR location LIKE ? OR description LIKE ?)';
                $like = '%' . $search . '%';
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
            }
            $salaryMin = 0;
            if (!is_array($salaryMins)) {
                $salaryMins = [$salaryMins];
            }
            $salaryMins = array_filter(array_map('intval', $salaryMins));
            if (!empty($salaryMins)) {
                $salaryMin = max($salaryMins);
                $where[] = '(salary_min >= ? OR salary_max >= ?)';
                $params[] = $salaryMin;
                $params[] = $salaryMin;
            }
            if (!is_array($skillsFilter)) {
                $skillsFilter = [$skillsFilter];
            }
            $skillsFilter = array_values(array_filter($skillsFilter, function ($v) {
                return $v !== '';
            }));
            if (!empty($skillsFilter)) {
                $placeholders = implode(',', array_fill(0, count($skillsFilter), '?'));
                $where[] = "EXISTS (SELECT 1 FROM vacancy_skills vs WHERE vs.vacancy_id = vacancies.id AND vs.skill_name IN ($placeholders))";
                $params = array_merge($params, $skillsFilter);
            }
            array_unshift($where, 'verified = TRUE');
            $whereSql = 'WHERE ' . implode(' AND ', $where);

            $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM vacancies $whereSql");
            $countStmt->execute($params);
            $totalVacancies = (int) ($countStmt->fetch()['total'] ?? 0);
            $totalPages = (int) ceil($totalVacancies / $perPage);
            if ($totalPages > 0 && $page > $totalPages) {
                $page = $totalPages;
                $offset = ($page - 1) * $perPage;
            }

            $stmt = $pdo->prepare("SELECT * FROM vacancies $whereSql ORDER BY created_at DESC, id DESC LIMIT $perPage OFFSET $offset");
            $stmt->execute($params);
            $vacancies = $stmt->fetchAll();

            $seenVacancyIds = [];
            $uniqueVacancies = [];
            foreach ($vacancies as $vacancyRow) {
                $rowId = (int) ($vacancyRow['id'] ?? 0);
                if ($rowId > 0 && isset($seenVacancyIds[$rowId])) {
                    continue;
                }
                if ($rowId > 0) {
                    $seenVacancyIds[$rowId] = true;
                }
                $uniqueVacancies[] = $vacancyRow;
            }
            $vacancies = $uniqueVacancies;

            foreach ($vacancies as &$vacancy) {
                $skillStmt = $pdo->prepare("SELECT skill_name FROM vacancy_skills WHERE vacancy_id = ?");
                $skillStmt->execute([$vacancy['id']]);
                $vacancy['skills'] = $skillStmt->fetchAll();
            }
            unset($vacancy);
            require_once __DIR__ . '/templates/vacancies.php';
            break;
        case 'interview':
            $pdo = getDBConnection();
            ensureInterviewsSchema($pdo);
            $interviews = [];
            if (!empty($user['id'])) {
                $stmt = $pdo->prepare("
                    SELECT i.*, s.code, s.created_at as session_created_at
                    FROM interviews i
                    LEFT JOIN interview_sessions s ON s.interview_id = i.id
                    WHERE i.user_id = ?
                    ORDER BY i.created_at DESC, i.id DESC
                ");
                $stmt->execute([(int) $user['id']]);
                $interviews = $stmt->fetchAll();
            }
            require_once __DIR__ . '/templates/interview.php';
            break;
        case 'interview-ai':
            require_once __DIR__ . '/templates/interview_ai.php';
            break;
        case 'interview-room':
            $code = trim((string) ($_GET['code'] ?? ''));
            $pdo = getDBConnection();
            ensureInterviewsSchema($pdo);
            $session = $code !== '' ? tfInterviewFetchSession($pdo, $code) : [];
            if (empty($session)) {
                header('Location: ?action=interview');
                exit;
            }
            if (!empty($user['id'])) {
                tfInterviewEnsureParticipant($pdo, $session, (int) $user['id'], true);
                $session = tfInterviewFetchSession($pdo, $code);
            }
            require_once __DIR__ . '/templates/interview_room.php';
            break;
        case 'ratings':
            $pdo = getDBConnection();
            ensureContestsSchema($pdo);
            $allUsers = buildRatingsUsers();
            $activeTab = (string) ($_GET['tab'] ?? 'general');
            if (!in_array($activeTab, ['general', 'contests'], true)) {
                $activeTab = 'general';
            }
            $search = trim($_GET['search'] ?? '');
            $skillsFilter = $_GET['skills'] ?? [];
            $experienceFilter = $_GET['experience'] ?? [];
            $educationFilter = $_GET['education'] ?? [];
            $page = (int) ($_GET['page'] ?? 1);
            if ($page < 1) {
                $page = 1;
            }
            $perPage = 10;

            if (!is_array($skillsFilter)) {
                $skillsFilter = [$skillsFilter];
            }
            if (!is_array($experienceFilter)) {
                $experienceFilter = [$experienceFilter];
            }
            if (!is_array($educationFilter)) {
                $educationFilter = [$educationFilter];
            }

            $skillsFilter = array_values(array_filter($skillsFilter, function ($v) {
                return $v !== '';
            }));
            $experienceFilter = array_values(array_filter($experienceFilter, function ($v) {
                return $v !== '';
            }));
            $educationFilter = array_values(array_filter($educationFilter, function ($v) {
                return $v !== '';
            }));

            $filteredAll = array_values(array_filter($allUsers, function ($user) use ($search, $skillsFilter, $experienceFilter, $educationFilter) {
                if ($search !== '') {
                    $hay = mb_strtolower(($user['name'] ?? '') . ' ' . ($user['title'] ?? '') . ' ' . ($user['location'] ?? ''));
                    if (mb_strpos($hay, mb_strtolower($search)) === false) {
                        return false;
                    }
                }

                if (!empty($skillsFilter)) {
                    $userSkills = array_map(function ($s) {
                        return $s['skill_name'] ?? '';
                    }, $user['skills'] ?? []);
                    $userSkillsLower = array_map('mb_strtolower', $userSkills);
                    $wanted = array_map('mb_strtolower', $skillsFilter);
                    $hasAny = false;
                    foreach ($wanted as $w) {
                        if (in_array($w, $userSkillsLower, true)) {
                            $hasAny = true;
                            break;
                        }
                    }
                    if (!$hasAny) {
                        return false;
                    }
                }

                if (!empty($experienceFilter)) {
                    $expMonths = (int) ($user['experience_months'] ?? 0);
                    $matches = false;
                    foreach ($experienceFilter as $range) {
                        if ($range === '1-3' && $expMonths >= 12 && $expMonths < 36) {
                            $matches = true;
                            break;
                        }
                        if ($range === '3-5' && $expMonths >= 36 && $expMonths < 60) {
                            $matches = true;
                            break;
                        }
                        if ($range === '5+' && $expMonths >= 60) {
                            $matches = true;
                            break;
                        }
                    }
                    if (!$matches) {
                        return false;
                    }
                }

                if (!empty($educationFilter)) {
                    $eduItems = $user['education'] ?? [];
                    $eduHay = [];
                    foreach ($eduItems as $e) {
                        $degree = mb_strtolower($e['degree'] ?? '');
                        $eduHay[] = $degree;
                    }
                    $matches = false;
                    foreach ($educationFilter as $f) {
                        $f = mb_strtolower($f);
                        foreach ($eduHay as $deg) {
                            if ($f === 'bachelor' && (mb_strpos($deg, 'бакалавр') !== false || mb_strpos($deg, 'bachelor') !== false)) {
                                $matches = true;
                                break 2;
                            }
                            if ($f === 'master' && (mb_strpos($deg, 'магистр') !== false || mb_strpos($deg, 'master') !== false)) {
                                $matches = true;
                                break 2;
                            }
                            if ($f === 'phd' && (mb_strpos($deg, 'кандидат') !== false || mb_strpos($deg, 'phd') !== false || mb_strpos($deg, 'доктор') !== false)) {
                                $matches = true;
                                break 2;
                            }
                        }
                    }
                    if (!$matches) {
                        return false;
                    }
                }

                return true;
            }));

            $totalUsers = count($allUsers);
            $totalFiltered = count($filteredAll);
            $totalPages = (int) ceil($totalFiltered / $perPage);
            if ($totalPages > 0 && $page > $totalPages) {
                $page = $totalPages;
            }
            $offset = ($page - 1) * $perPage;
            $filteredUsers = array_slice($filteredAll, $offset, $perPage);

            $topUsers = array_slice($filteredAll, 0, 3);
            foreach ($topUsers as $i => $u) {
                $topUsers[$i]['position'] = $i + 1;
            }
            $contestSearch = trim((string) ($_GET['contest_search'] ?? ''));
            $contestPointsMin = max(0, (int) ($_GET['contest_points_min'] ?? 0));
            $contestPointsMaxRaw = trim((string) ($_GET['contest_points_max'] ?? ''));
            $contestPointsMax = $contestPointsMaxRaw !== '' ? max(0, (int) $contestPointsMaxRaw) : null;
            if ($contestPointsMax !== null && $contestPointsMax < $contestPointsMin) {
                $contestPointsMax = $contestPointsMin;
            }
            $contestSolvedMin = max(0, (int) ($_GET['contest_solved_min'] ?? 0));
            $contestAttemptsMaxRaw = trim((string) ($_GET['contest_attempts_max'] ?? ''));
            $contestAttemptsMax = $contestAttemptsMaxRaw !== '' ? max(0, (int) $contestAttemptsMaxRaw) : null;
            $contestPage = max(1, (int) ($_GET['contest_page'] ?? 1));
            $contestPerPage = 10;
            $contestLeaderboard = getContestLeaderboard($pdo, 1000);
            $contestFilteredAll = array_values(array_filter($contestLeaderboard, function ($row) use ($contestSearch, $contestPointsMin, $contestPointsMax, $contestSolvedMin, $contestAttemptsMax) {
                if ($contestSearch === '') {
                    $matchesSearch = true;
                } else {
                    $hay = mb_strtolower((string) ($row['name'] ?? ''));
                    $matchesSearch = mb_strpos($hay, mb_strtolower($contestSearch)) !== false;
                }
                if (!$matchesSearch) {
                    return false;
                }

                $contestPoints = (int) ($row['contest_points'] ?? 0);
                if ($contestPoints < $contestPointsMin) {
                    return false;
                }
                if ($contestPointsMax !== null && $contestPoints > $contestPointsMax) {
                    return false;
                }

                if ((int) ($row['solved_count'] ?? 0) < $contestSolvedMin) {
                    return false;
                }
                if ($contestAttemptsMax !== null && (int) ($row['attempts_count'] ?? 0) > $contestAttemptsMax) {
                    return false;
                }

                return true;
            }));
            $contestTotalFiltered = count($contestFilteredAll);
            $contestTotalPages = max(1, (int) ceil($contestTotalFiltered / $contestPerPage));
            if ($contestPage > $contestTotalPages) {
                $contestPage = $contestTotalPages;
            }
            $contestOffset = ($contestPage - 1) * $contestPerPage;
            $contestLeaderboardPage = array_slice($contestFilteredAll, $contestOffset, $contestPerPage);
            require_once __DIR__ . '/templates/ratings.php';
            break;
        case 'profile-view':
            $profileId = (int) ($_GET['id'] ?? 0);
            if ($profileId <= 0) {
                header('Location: ?action=ratings');
                exit;
            }
            $profileUser = getUserProfileById($profileId);
            if (!$profileUser) {
                header('Location: ?action=ratings');
                exit;
            }
            require_once __DIR__ . '/templates/profile_view.php';
            break;
        case 'roadmap':
            require_once __DIR__ . '/templates/roadmap.php';
            break;
        case 'roadmaps':
            require_once __DIR__ . '/templates/roadmaps.php';
            break;
        case 'git-trainer':
            require_once __DIR__ . '/templates/git_trainer.php';
            break;
        case 'vacancy-chat':
            $appId = (int) ($_GET['app_id'] ?? 0);
            if ($appId <= 0) {
                header('Location: index.php?action=vacancies');
                exit;
            }
            $pdo = getDBConnection();
            ensureVacancyChatTables($pdo);
            $stmt = $pdo->prepare("
                SELECT ua.*, v.title as vacancy_title, v.company, v.owner_id, u.name as applicant_name
                FROM user_applications ua
                JOIN vacancies v ON ua.vacancy_id = v.id
                JOIN users u ON ua.user_id = u.id
                WHERE ua.id = ?
            ");
            $stmt->execute([$appId]);
            $chatApp = $stmt->fetch();
            if (!$chatApp) {
                header('Location: index.php?action=vacancies');
                exit;
            }
            if ((int) $user['id'] !== (int) $chatApp['user_id'] && (int) $user['id'] !== (int) ($chatApp['owner_id'] ?? 0)) {
                header('Location: index.php?action=vacancies');
                exit;
            }
            require_once __DIR__ . '/templates/vacancy_chat.php';
            break;
        case 'certificate':
            handleCertificateView();
            break;
        case 'certificate-public':
            handleCertificatePublicView();
            break;
        case 'get-course':
            $pdo = getDBConnection();
            $courseId = (int) ($_GET['id'] ?? 0);
            if ($courseId <= 0) {
                header('Location: ?action=courses');
                exit;
            }
            $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
            $stmt->execute([$courseId]);
            $course = $stmt->fetch();
            if (!$course) {
                header('Location: ?action=courses');
                exit;
            }
            $userId = $user['id'] ?? null;
            $lessons = [];
            if ($userId) {
                ensurePracticeSchema($pdo);
                ensureQuizSchema($pdo);
                tfEnsureCourseFallbackPracticeTasks($pdo, (int) $courseId);
                $stmt = $pdo->prepare("
                    SELECT DISTINCT l.*,
                           (SELECT completed FROM user_lesson_progress ulp
                            WHERE ulp.lesson_id = l.id AND ulp.user_id = ?) as completed,
                           lpt.id as practice_task_id,
                           lpt.language as practice_language,
                           lpt.title as practice_title,
                           lpt.prompt as practice_prompt,
                           lpt.starter_code as practice_starter_code,
                           lpt.tests_json as practice_tests_json,
                           lpt.is_required as practice_required,
                           (SELECT 1
                            FROM practice_submissions ps
                            WHERE ps.user_id = ? AND ps.task_id = lpt.id AND ps.passed = 1
                            LIMIT 1) as practice_passed
                    FROM lessons l
                    LEFT JOIN lesson_practice_tasks lpt
                           ON lpt.id = (
                                SELECT t.id
                                FROM lesson_practice_tasks t
                                WHERE t.lesson_id = l.id AND t.is_required = 1
                                ORDER BY t.id ASC
                                LIMIT 1
                           )
                    WHERE l.course_id = ?
                    ORDER BY l.order_num ASC
                ");
                $stmt->execute([$userId, $userId, $courseId]);
                $lessons = $stmt->fetchAll();
                foreach ($lessons as &$lesson) {
                    foreach (['title', 'description', 'practice_title', 'practice_prompt', 'practice_starter_code'] as $field) {
                        if (array_key_exists($field, $lesson) && $lesson[$field] !== null) {
                            $lesson[$field] = normalizeMojibakeText((string) $lesson[$field]);
                        }
                    }
                    if (($lesson['type'] ?? '') === 'quiz') {
                        $stmtQuiz = $pdo->prepare("
                            SELECT qq.*, 
                                   (SELECT GROUP_CONCAT(option_text ORDER BY option_order SEPARATOR '|||') 
                                    FROM quiz_options qo 
                                    WHERE qo.question_id = qq.id) as options_text
                            FROM quiz_questions qq
                            WHERE qq.lesson_id = ?
                        ");
                        $stmtQuiz->execute([$lesson['id']]);
                        $lesson['questions'] = $stmtQuiz->fetchAll();
                        foreach ($lesson['questions'] as &$question) {
                            foreach (['question', 'options_text'] as $qField) {
                                if (array_key_exists($qField, $question) && $question[$qField] !== null) {
                                    $question[$qField] = normalizeMojibakeText((string) $question[$qField]);
                                }
                            }
                        }
                        unset($question);
                    }
                }
                unset($lesson);
            }
            $stmt = $pdo->prepare("SELECT progress, completed FROM user_course_progress WHERE user_id = ? AND course_id = ?");
            $stmt->execute([$userId, $courseId]);
            $progress = $stmt->fetch();
            $stmt = $pdo->prepare("SELECT * FROM course_skills WHERE course_id = ?");
            $stmt->execute([$courseId]);
            $courseSkills = $stmt->fetchAll();
            $stmt = $pdo->prepare("SELECT * FROM course_exams WHERE course_id = ? LIMIT 1");
            $stmt->execute([$courseId]);
            $courseExam = $stmt->fetch();
            require_once __DIR__ . '/templates/course.php';
            break;
        case 'profile':
            require_once __DIR__ . '/templates/profile.php';
            break;
        case 'settings':
            require_once __DIR__ . '/templates/profile.php';
            break;
        case 'admin':
            if (($user['role'] ?? '') !== 'admin') {
                header('Location: index.php?action=dashboard');
                exit;
            }
            $pdo = getDBConnection();
            ensureRoadmapTables($pdo);
            ensureContestsSchema($pdo);
            $allowedAdminTabs = [
                'dashboard',
                'users',
                'courses',
                'course-lessons',
                'course-practice',
                'course-exams',
                'contests',
                'contest-tasks',
                'contest-solutions',
                'vacancy-prep-tasks',
                'vacancies',
                'roadmaps',
                'roadmap-nodes',
                'roadmap-tasks',
                'roadmap-exams',
                'lessons',
                'roadmap',
                'exams',
                'notifications',
                'settings',
                'ejudge-import'
            ];
            if (!in_array((string) $tab, $allowedAdminTabs, true)) {
                $tab = 'dashboard';
            }
            $normalizeRows = static function (&$rows, $fields) {
                if (!is_array($rows) || empty($rows)) {
                    return;
                }
                foreach ($rows as &$row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    foreach ($fields as $field) {
                        if (array_key_exists($field, $row) && $row[$field] !== null) {
                            $row[$field] = normalizeMojibakeText((string) $row[$field]);
                        }
                    }
                }
                unset($row);
            };
            $makePager = static function (PDO $pdo, string $countSql, array $countParams, string $queryParam, int $perPage = 25): array {
                $stmtCount = $pdo->prepare($countSql);
                $stmtCount->execute($countParams);
                $total = (int) ($stmtCount->fetchColumn() ?: 0);
                $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 1;
                $page = (int) ($_GET[$queryParam] ?? 1);
                if ($page < 1) {
                    $page = 1;
                }
                if ($page > $totalPages) {
                    $page = $totalPages;
                }
                $offset = ($page - 1) * $perPage;
                return [
                    'param' => $queryParam,
                    'page' => $page,
                    'per_page' => $perPage,
                    'offset' => $offset,
                    'total' => $total,
                    'total_pages' => $totalPages,
                ];
            };

            $totalUsers = (int) $pdo->query("SELECT COUNT(*) as total FROM users")->fetch()['total'];
            $totalCourses = (int) $pdo->query("SELECT COUNT(*) as total FROM courses")->fetch()['total'];
            $totalLessons = (int) $pdo->query("SELECT COUNT(*) as total FROM lessons")->fetch()['total'];
            $totalVacancies = (int) $pdo->query("SELECT COUNT(*) as total FROM vacancies")->fetch()['total'];

            $users = [];
            $courses = [];
            $allCoursesForAdmin = [];
            $lessons = [];
            $vacancies = [];
            $notifications = [];
            $courseExams = [];
            $roadmapNodes = [];
            $roadmapLessons = [];
            $roadmapQuizzes = [];
            $roadmapList = [];
            $roadmapListForSelect = [];
            $roadmapNodesForSelect = [];
            $roadmapCountsMap = [];
            $selectedRoadmapNodeIds = [];
            $adminContests = [];
            $adminContestsForSelect = [];
            $adminContestTasks = [];
            $adminInterviewPrepTasks = [];
            $practiceTasks = [];
            $recentPracticeSubmissions = [];
            $recentContestSubmissions = [];
            $recentUsers = [];
            $recentCourses = [];
            $adminRoleCounts = ['admin' => 0, 'recruiter' => 0, 'seeker' => 0];
            $adminTrendLabels = [];
            $adminTrendValues = [];
            $adminPagination = [];
            $isLessonsTab = in_array($tab, ['lessons', 'course-lessons'], true);
            $isPracticeTab = ($tab === 'course-practice');
            $isCourseExamsTab = in_array($tab, ['exams', 'course-exams'], true);
            $isContestOverviewTab = ($tab === 'contests');
            $isContestTasksTab = ($tab === 'contest-tasks');
            $isContestSolutionsTab = ($tab === 'contest-solutions');
            $isVacancyPrepTasksTab = ($tab === 'vacancy-prep-tasks');
            $isRoadmapOverviewTab = in_array($tab, ['roadmap', 'roadmaps'], true);
            $isRoadmapNodesTab = ($tab === 'roadmap-nodes');
            $isRoadmapTasksTab = ($tab === 'roadmap-tasks');
            $isRoadmapExamsTab = ($tab === 'roadmap-exams');
            $adminCourseId = max(0, (int) ($_GET['admin_course_id'] ?? 0));
            $adminContestId = max(0, (int) ($_GET['admin_contest_id'] ?? 0));
            $adminRoadmapTitle = trim((string) ($_GET['admin_roadmap_title'] ?? ''));

            $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
            $recentUsers = $stmt->fetchAll();
            $normalizeRows($recentUsers, ['name', 'title', 'email', 'location']);
            $stmt = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC LIMIT 5");
            $recentCourses = $stmt->fetchAll();
            $normalizeRows($recentCourses, ['title', 'instructor', 'category', 'level', 'roadmap_title', 'roadmap']);
            $stmt = $pdo->query("SELECT id, title FROM courses ORDER BY title ASC");
            $allCoursesForAdmin = $stmt ? ($stmt->fetchAll() ?: []) : [];
            $normalizeRows($allCoursesForAdmin, ['title']);

            if ($tab === 'dashboard') {
                $stmt = $pdo->query("SELECT role, COUNT(*) AS total FROM users GROUP BY role");
                foreach (($stmt ? $stmt->fetchAll() : []) as $row) {
                    $role = (string) ($row['role'] ?? 'seeker');
                    $count = (int) ($row['total'] ?? 0);
                    if (!isset($adminRoleCounts[$role])) {
                        $adminRoleCounts['seeker'] += $count;
                    } else {
                        $adminRoleCounts[$role] = $count;
                    }
                }
                $trendWeeks = 8;
                $trendStart = strtotime('monday this week');
                if ($trendStart === false) {
                    $trendStart = time();
                }
                $trendStart = strtotime('-' . ($trendWeeks - 1) . ' weeks', $trendStart);
                $trendBuckets = [];
                for ($i = 0; $i < $trendWeeks; $i++) {
                    $stamp = strtotime('+' . $i . ' weeks', $trendStart);
                    $key = date('Y-m-d', $stamp);
                    $adminTrendLabels[] = date('d.m', $stamp);
                    $trendBuckets[$key] = 0;
                }
                $stmt = $pdo->query("
                    SELECT DATE_FORMAT(DATE_SUB(DATE(created_at), INTERVAL WEEKDAY(created_at) DAY), '%Y-%m-%d') AS week_start, COUNT(*) AS total
                    FROM users
                    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 WEEK)
                    GROUP BY week_start
                    ORDER BY week_start ASC
                ");
                foreach (($stmt ? $stmt->fetchAll() : []) as $row) {
                    $weekStart = (string) ($row['week_start'] ?? '');
                    if (isset($trendBuckets[$weekStart])) {
                        $trendBuckets[$weekStart] = (int) ($row['total'] ?? 0);
                    }
                }
                $adminTrendValues = array_values($trendBuckets);
            } elseif ($tab === 'users') {
                $adminPagination['users'] = $makePager($pdo, "SELECT COUNT(*) FROM users", [], 'users_page', 30);
                $p = $adminPagination['users'];
                $stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
                $stmt->bindValue(1, (int) $p['per_page'], PDO::PARAM_INT);
                $stmt->bindValue(2, (int) $p['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $users = $stmt->fetchAll();
                $normalizeRows($users, ['name', 'title', 'email', 'location']);
            } elseif ($tab === 'courses') {
                $adminPagination['courses'] = $makePager($pdo, "SELECT COUNT(*) FROM courses", [], 'courses_page', 25);
                $p = $adminPagination['courses'];
                $stmt = $pdo->prepare("SELECT * FROM courses ORDER BY created_at DESC LIMIT ? OFFSET ?");
                $stmt->bindValue(1, (int) $p['per_page'], PDO::PARAM_INT);
                $stmt->bindValue(2, (int) $p['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $courses = $stmt->fetchAll();
                $normalizeRows($courses, ['title', 'instructor', 'category', 'level', 'roadmap_title', 'roadmap']);
            } elseif ($isLessonsTab) {
                $lessonWhere = [];
                $lessonParams = [];
                if ($adminCourseId > 0) {
                    $lessonWhere[] = 'l.course_id = ?';
                    $lessonParams[] = $adminCourseId;
                }
                $lessonWhereSql = !empty($lessonWhere) ? ('WHERE ' . implode(' AND ', $lessonWhere)) : '';
                $adminPagination['lessons'] = $makePager($pdo, "SELECT COUNT(*) FROM lessons l {$lessonWhereSql}", $lessonParams, 'lessons_page', 30);
                $p = $adminPagination['lessons'];
                $stmt = $pdo->prepare("
                    SELECT l.*, c.title as course_title
                    FROM lessons l
                    LEFT JOIN courses c ON c.id = l.course_id
                    {$lessonWhereSql}
                    ORDER BY l.course_id ASC, l.order_num ASC, l.created_at ASC
                    LIMIT ? OFFSET ?
                ");
                $bindIndex = 1;
                foreach ($lessonParams as $param) {
                    $stmt->bindValue($bindIndex++, $param, PDO::PARAM_INT);
                }
                $stmt->bindValue($bindIndex++, (int) $p['per_page'], PDO::PARAM_INT);
                $stmt->bindValue($bindIndex++, (int) $p['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $lessons = $stmt->fetchAll();
                $normalizeRows($lessons, ['title', 'course_title', 'type']);
                $courses = $allCoursesForAdmin;
            } elseif ($isPracticeTab) {
                $practiceWhere = [];
                $practiceParams = [];
                if ($adminCourseId > 0) {
                    $practiceWhere[] = 'l.course_id = ?';
                    $practiceParams[] = $adminCourseId;
                }
                $practiceWhereSql = !empty($practiceWhere) ? ('WHERE ' . implode(' AND ', $practiceWhere)) : '';
                $adminPagination['practice_tasks'] = $makePager(
                    $pdo,
                    "
                        SELECT COUNT(*)
                        FROM lesson_practice_tasks lpt
                        LEFT JOIN lessons l ON l.id = lpt.lesson_id
                        {$practiceWhereSql}
                    ",
                    $practiceParams,
                    'practice_tasks_page',
                    30
                );
                $p = $adminPagination['practice_tasks'];
                $stmt = $pdo->prepare("
                    SELECT lpt.*, l.title AS lesson_title, l.course_id, c.title AS course_title
                    FROM lesson_practice_tasks lpt
                    LEFT JOIN lessons l ON l.id = lpt.lesson_id
                    LEFT JOIN courses c ON c.id = l.course_id
                    {$practiceWhereSql}
                    ORDER BY c.title ASC, l.order_num ASC, lpt.id ASC
                    LIMIT ? OFFSET ?
                ");
                $bindIndex = 1;
                foreach ($practiceParams as $param) {
                    $stmt->bindValue($bindIndex++, $param, PDO::PARAM_INT);
                }
                $stmt->bindValue($bindIndex++, (int) $p['per_page'], PDO::PARAM_INT);
                $stmt->bindValue($bindIndex++, (int) $p['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $practiceTasks = $stmt->fetchAll();
                $normalizeRows($practiceTasks, ['title', 'prompt', 'language', 'lesson_title', 'course_title']);
                $courses = $allCoursesForAdmin;

                $practiceSubmissionWhere = [];
                $practiceSubmissionParams = [];
                if ($adminCourseId > 0) {
                    $practiceSubmissionWhere[] = 'c.id = ?';
                    $practiceSubmissionParams[] = $adminCourseId;
                }
                $practiceSubmissionWhereSql = !empty($practiceSubmissionWhere) ? ('WHERE ' . implode(' AND ', $practiceSubmissionWhere)) : '';
                $stmt = $pdo->prepare("
                    SELECT ps.id, ps.user_id, ps.task_id, ps.passed, ps.created_at, ps.code,
                           u.name AS user_name,
                           lpt.title AS task_title,
                           l.title AS lesson_title,
                           c.title AS course_title
                    FROM practice_submissions ps
                    LEFT JOIN users u ON u.id = ps.user_id
                    LEFT JOIN lesson_practice_tasks lpt ON lpt.id = ps.task_id
                    LEFT JOIN lessons l ON l.id = lpt.lesson_id
                    LEFT JOIN courses c ON c.id = l.course_id
                    {$practiceSubmissionWhereSql}
                    ORDER BY ps.created_at DESC, ps.id DESC
                    LIMIT 50
                ");
                $bindIndex = 1;
                foreach ($practiceSubmissionParams as $param) {
                    $stmt->bindValue($bindIndex++, $param, PDO::PARAM_INT);
                }
                $stmt->execute();
                $recentPracticeSubmissions = $stmt ? ($stmt->fetchAll() ?: []) : [];
                $normalizeRows($recentPracticeSubmissions, ['user_name', 'task_title', 'lesson_title', 'course_title']);
            } elseif ($tab === 'vacancies') {
                $adminPagination['vacancies'] = $makePager($pdo, "SELECT COUNT(*) FROM vacancies", [], 'vacancies_page', 25);
                $p = $adminPagination['vacancies'];
                $stmt = $pdo->prepare("SELECT * FROM vacancies ORDER BY created_at DESC, id DESC LIMIT ? OFFSET ?");
                $stmt->bindValue(1, (int) $p['per_page'], PDO::PARAM_INT);
                $stmt->bindValue(2, (int) $p['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $vacancies = $stmt->fetchAll();
                $normalizeRows($vacancies, ['title', 'company', 'location', 'type']);
            } elseif ($tab === 'notifications') {
                $adminPagination['notifications'] = $makePager($pdo, "SELECT COUNT(*) FROM notifications", [], 'notifications_page', 50);
                $p = $adminPagination['notifications'];
                $stmt = $pdo->prepare("
                    SELECT n.*, u.name as user_name
                    FROM notifications n
                    LEFT JOIN users u ON u.id = n.user_id
                    ORDER BY n.notification_time DESC
                    LIMIT ? OFFSET ?
                ");
                $stmt->bindValue(1, (int) $p['per_page'], PDO::PARAM_INT);
                $stmt->bindValue(2, (int) $p['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $notifications = $stmt->fetchAll();
                $normalizeRows($notifications, ['message', 'user_name']);
            } elseif ($isCourseExamsTab) {
                $examWhere = [];
                $examParams = [];
                if ($adminCourseId > 0) {
                    $examWhere[] = 'c.id = ?';
                    $examParams[] = $adminCourseId;
                }
                $examWhereSql = !empty($examWhere) ? ('WHERE ' . implode(' AND ', $examWhere)) : '';
                $adminPagination['exams'] = $makePager($pdo, "SELECT COUNT(*) FROM courses c {$examWhereSql}", $examParams, 'exams_page', 25);
                $p = $adminPagination['exams'];
                $stmt = $pdo->prepare("
                    SELECT c.id as course_id, c.title as course_title,
                           ce.exam_json, ce.time_limit_minutes, ce.pass_percent, ce.shuffle_questions, ce.shuffle_options
                    FROM courses c
                    LEFT JOIN course_exams ce ON ce.course_id = c.id
                    {$examWhereSql}
                    ORDER BY c.title ASC
                    LIMIT ? OFFSET ?
                ");
                $bindIndex = 1;
                foreach ($examParams as $param) {
                    $stmt->bindValue($bindIndex++, $param, PDO::PARAM_INT);
                }
                $stmt->bindValue($bindIndex++, (int) $p['per_page'], PDO::PARAM_INT);
                $stmt->bindValue($bindIndex++, (int) $p['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $courseExams = $stmt->fetchAll();
                $normalizeRows($courseExams, ['course_title']);
            } elseif ($isContestOverviewTab || $isContestTasksTab || $isContestSolutionsTab || $isVacancyPrepTasksTab) {
                $adminPagination['contests'] = $makePager($pdo, "SELECT COUNT(*) FROM contests", [], 'contests_page', 20);
                $p1 = $adminPagination['contests'];
                $stmt = $pdo->prepare("SELECT * FROM contests ORDER BY created_at DESC, id DESC LIMIT ? OFFSET ?");
                $stmt->bindValue(1, (int) $p1['per_page'], PDO::PARAM_INT);
                $stmt->bindValue(2, (int) $p1['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $adminContests = $stmt->fetchAll();
                $normalizeRows($adminContests, ['title', 'slug', 'description']);

                $contestTaskWhere = [];
                $contestTaskParams = [];
                if ($adminContestId > 0) {
                    $contestTaskWhere[] = 'ct.contest_id = ?';
                    $contestTaskParams[] = $adminContestId;
                }
                $contestTaskWhereSql = !empty($contestTaskWhere) ? ('WHERE ' . implode(' AND ', $contestTaskWhere)) : '';
                $adminPagination['contest_tasks'] = $makePager($pdo, "SELECT COUNT(*) FROM contest_tasks ct {$contestTaskWhereSql}", $contestTaskParams, 'contest_tasks_page', 25);
                $p2 = $adminPagination['contest_tasks'];
                $stmt = $pdo->prepare("
                    SELECT ct.*, c.title as contest_title
                    FROM contest_tasks ct
                    LEFT JOIN contests c ON c.id = ct.contest_id
                    {$contestTaskWhereSql}
                    ORDER BY ct.contest_id ASC, ct.order_num ASC, ct.id ASC
                    LIMIT ? OFFSET ?
                ");
                $bindIndex = 1;
                foreach ($contestTaskParams as $param) {
                    $stmt->bindValue($bindIndex++, $param, PDO::PARAM_INT);
                }
                $stmt->bindValue($bindIndex++, (int) $p2['per_page'], PDO::PARAM_INT);
                $stmt->bindValue($bindIndex++, (int) $p2['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $adminContestTasks = $stmt->fetchAll();
                $normalizeRows($adminContestTasks, ['title', 'contest_title', 'difficulty']);

                $stmt = $pdo->query("SELECT id, title FROM contests ORDER BY created_at DESC, id DESC");
                $adminContestsForSelect = $stmt ? ($stmt->fetchAll() ?: []) : [];
                $normalizeRows($adminContestsForSelect, ['title']);

                $stmt = $pdo->query("
                    SELECT ct.id, ct.contest_id, ct.title, c.title AS contest_title
                    FROM contest_tasks ct
                    LEFT JOIN contests c ON c.id = ct.contest_id
                    ORDER BY c.title ASC, ct.order_num ASC, ct.id ASC
                ");
                $adminContestSubmissionTasksForSelect = $stmt ? ($stmt->fetchAll() ?: []) : [];
                $normalizeRows($adminContestSubmissionTasksForSelect, ['title', 'contest_title']);

                if ($isContestSolutionsTab) {
                    $contestSubmissionContestId = (int) ($_GET['contest_solution_contest_id'] ?? $_GET['contest_id'] ?? $adminContestId ?? 0);
                    $contestSubmissionTaskId = (int) ($_GET['contest_solution_task_id'] ?? $_GET['task_id'] ?? 0);
                    $submissionWhere = [];
                    $submissionParams = [];
                    if ($contestSubmissionContestId > 0) {
                        $submissionWhere[] = 'cs.contest_id = ?';
                        $submissionParams[] = $contestSubmissionContestId;
                    }
                    if ($contestSubmissionTaskId > 0) {
                        $submissionWhere[] = 'cs.task_id = ?';
                        $submissionParams[] = $contestSubmissionTaskId;
                    }

                    $submissionWhereSql = !empty($submissionWhere) ? ('WHERE ' . implode(' AND ', $submissionWhere)) : '';
                    $adminPagination['contest_submissions'] = $makePager(
                        $pdo,
                        "
                            SELECT COUNT(*)
                            FROM contest_submissions cs
                            LEFT JOIN users u ON u.id = cs.user_id
                            LEFT JOIN contests c ON c.id = cs.contest_id
                            LEFT JOIN contest_tasks ct ON ct.id = cs.task_id
                            {$submissionWhereSql}
                        ",
                        $submissionParams,
                        'contest_submissions_page',
                        30
                    );
                    $ps = $adminPagination['contest_submissions'];
                    $stmt = $pdo->prepare("
                        SELECT cs.id, cs.user_id, cs.contest_id, cs.task_id, cs.language, cs.status, cs.points_awarded,
                               cs.checks_passed, cs.checks_total, cs.attempts, cs.wrong_attempts, cs.created_at, cs.updated_at,
                               u.name AS user_name,
                               c.title AS contest_title,
                               ct.title AS task_title
                        FROM contest_submissions cs
                        LEFT JOIN users u ON u.id = cs.user_id
                        LEFT JOIN contests c ON c.id = cs.contest_id
                        LEFT JOIN contest_tasks ct ON ct.id = cs.task_id
                        {$submissionWhereSql}
                        ORDER BY cs.updated_at DESC, cs.id DESC
                        LIMIT ? OFFSET ?
                    ");
                    $bindIndex = 1;
                    foreach ($submissionParams as $param) {
                        $stmt->bindValue($bindIndex++, $param, PDO::PARAM_INT);
                    }
                    $stmt->bindValue($bindIndex++, (int) $ps['per_page'], PDO::PARAM_INT);
                    $stmt->bindValue($bindIndex++, (int) $ps['offset'], PDO::PARAM_INT);
                    $stmt->execute();
                    $recentContestSubmissions = $stmt->fetchAll();
                    $normalizeRows($recentContestSubmissions, ['user_name', 'contest_title', 'task_title', 'language', 'status']);
                }

                $adminPagination['interview_prep_tasks'] = $makePager($pdo, "SELECT COUNT(*) FROM interview_prep_tasks", [], 'interview_prep_tasks_page', 25);
                $p3 = $adminPagination['interview_prep_tasks'];
                $stmt = $pdo->prepare("
                    SELECT ipt.*, ct.title AS source_title
                    FROM interview_prep_tasks ipt
                    LEFT JOIN contest_tasks ct ON ct.id = ipt.source_task_id
                    ORDER BY ipt.sort_order ASC, ipt.id ASC
                    LIMIT ? OFFSET ?
                ");
                $stmt->bindValue(1, (int) $p3['per_page'], PDO::PARAM_INT);
                $stmt->bindValue(2, (int) $p3['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $adminInterviewPrepTasks = $stmt->fetchAll();
                $normalizeRows($adminInterviewPrepTasks, ['title', 'category', 'source_title']);
            } elseif ($tab === 'ejudge-import') {
                $stmt = $pdo->query("SELECT * FROM contests ORDER BY created_at DESC, id DESC");
                $adminContests = $stmt ? ($stmt->fetchAll() ?: []) : [];
                $normalizeRows($adminContests, ['title', 'slug', 'description']);
                $adminContestsForSelect = $adminContests;
            } elseif ($isRoadmapOverviewTab || $isRoadmapNodesTab || $isRoadmapTasksTab || $isRoadmapExamsTab) {
                $stmt = $pdo->query("SELECT * FROM roadmap_list ORDER BY id ASC");
                $roadmapListForSelect = $stmt ? ($stmt->fetchAll() ?: []) : [];
                $normalizeRows($roadmapListForSelect, ['title']);
                if (empty($roadmapListForSelect)) {
                    $stmtTitles = $pdo->query("SELECT DISTINCT roadmap_title FROM roadmap_nodes WHERE roadmap_title IS NOT NULL AND roadmap_title <> ''");
                    $titles = $stmtTitles ? ($stmtTitles->fetchAll(PDO::FETCH_COLUMN) ?: []) : [];
                    foreach ($titles as $title) {
                        $insert = $pdo->prepare("INSERT IGNORE INTO roadmap_list (title) VALUES (?)");
                        $insert->execute([$title]);
                    }
                    $stmt = $pdo->query("SELECT * FROM roadmap_list ORDER BY id ASC");
                    $roadmapListForSelect = $stmt ? ($stmt->fetchAll() ?: []) : [];
                    $normalizeRows($roadmapListForSelect, ['title']);
                }

                $adminPagination['roadmap_list'] = $makePager($pdo, "SELECT COUNT(*) FROM roadmap_list", [], 'roadmap_list_page', 20);
                $pr0 = $adminPagination['roadmap_list'];
                $stmt = $pdo->prepare("SELECT * FROM roadmap_list ORDER BY id ASC LIMIT ? OFFSET ?");
                $stmt->bindValue(1, (int) $pr0['per_page'], PDO::PARAM_INT);
                $stmt->bindValue(2, (int) $pr0['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $roadmapList = $stmt->fetchAll();
                $normalizeRows($roadmapList, ['title']);

                $roadmapNodeWhere = [];
                $roadmapNodeParams = [];
                if ($adminRoadmapTitle !== '') {
                    $roadmapNodeWhere[] = "COALESCE(NULLIF(TRIM(roadmap_title), ''), 'Основной') = ?";
                    $roadmapNodeParams[] = $adminRoadmapTitle;
                }
                $roadmapNodeWhereSql = !empty($roadmapNodeWhere) ? ('WHERE ' . implode(' AND ', $roadmapNodeWhere)) : '';
                $adminPagination['roadmap_nodes'] = $makePager($pdo, "SELECT COUNT(*) FROM roadmap_nodes {$roadmapNodeWhereSql}", $roadmapNodeParams, 'roadmap_nodes_page', 30);
                $pr1 = $adminPagination['roadmap_nodes'];
                $stmt = $pdo->prepare("SELECT * FROM roadmap_nodes {$roadmapNodeWhereSql} ORDER BY id ASC LIMIT ? OFFSET ?");
                $bindIndex = 1;
                foreach ($roadmapNodeParams as $param) {
                    $stmt->bindValue($bindIndex++, $param, PDO::PARAM_STR);
                }
                $stmt->bindValue($bindIndex++, (int) $pr1['per_page'], PDO::PARAM_INT);
                $stmt->bindValue($bindIndex++, (int) $pr1['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $roadmapNodes = $stmt->fetchAll();
                $normalizeRows($roadmapNodes, ['title', 'description', 'roadmap_title']);
                $selectedRoadmapNodeIds = array_values(array_filter(array_map(static function ($row) {
                    return (int) ($row['id'] ?? 0);
                }, $roadmapNodes)));

                $roadmapLessonWhere = [];
                $roadmapLessonParams = [];
                if ($adminRoadmapTitle !== '') {
                    $roadmapLessonWhere[] = "COALESCE(NULLIF(TRIM(rn.roadmap_title), ''), 'Основной') = ?";
                    $roadmapLessonParams[] = $adminRoadmapTitle;
                }
                $roadmapLessonWhereSql = !empty($roadmapLessonWhere) ? ('WHERE ' . implode(' AND ', $roadmapLessonWhere)) : '';
                $adminPagination['roadmap_lessons'] = $makePager(
                    $pdo,
                    "SELECT COUNT(*) FROM roadmap_lessons rl LEFT JOIN roadmap_nodes rn ON rn.id = rl.node_id {$roadmapLessonWhereSql}",
                    $roadmapLessonParams,
                    'roadmap_lessons_page',
                    30
                );
                $pr2 = $adminPagination['roadmap_lessons'];
                $stmt = $pdo->prepare("
                    SELECT rl.*, rn.title as node_title
                    FROM roadmap_lessons rl
                    LEFT JOIN roadmap_nodes rn ON rn.id = rl.node_id
                    {$roadmapLessonWhereSql}
                    ORDER BY rl.node_id ASC, rl.order_index ASC
                    LIMIT ? OFFSET ?
                ");
                $bindIndex = 1;
                foreach ($roadmapLessonParams as $param) {
                    $stmt->bindValue($bindIndex++, $param, PDO::PARAM_STR);
                }
                $stmt->bindValue($bindIndex++, (int) $pr2['per_page'], PDO::PARAM_INT);
                $stmt->bindValue($bindIndex++, (int) $pr2['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $roadmapLessons = $stmt->fetchAll();
                $normalizeRows($roadmapLessons, ['title', 'description', 'node_title']);

                $roadmapQuizWhere = [];
                $roadmapQuizParams = [];
                if ($adminRoadmapTitle !== '') {
                    $roadmapQuizWhere[] = "COALESCE(NULLIF(TRIM(rn.roadmap_title), ''), 'Основной') = ?";
                    $roadmapQuizParams[] = $adminRoadmapTitle;
                }
                $roadmapQuizWhereSql = !empty($roadmapQuizWhere) ? ('WHERE ' . implode(' AND ', $roadmapQuizWhere)) : '';
                $adminPagination['roadmap_quizzes'] = $makePager(
                    $pdo,
                    "SELECT COUNT(*) FROM roadmap_quiz_questions rq LEFT JOIN roadmap_nodes rn ON rn.id = rq.node_id {$roadmapQuizWhereSql}",
                    $roadmapQuizParams,
                    'roadmap_quizzes_page',
                    30
                );
                $pr3 = $adminPagination['roadmap_quizzes'];
                $stmt = $pdo->prepare("
                    SELECT rq.*, rn.title as node_title
                    FROM roadmap_quiz_questions rq
                    LEFT JOIN roadmap_nodes rn ON rn.id = rq.node_id
                    {$roadmapQuizWhereSql}
                    ORDER BY rq.node_id ASC, rq.id ASC
                    LIMIT ? OFFSET ?
                ");
                $bindIndex = 1;
                foreach ($roadmapQuizParams as $param) {
                    $stmt->bindValue($bindIndex++, $param, PDO::PARAM_STR);
                }
                $stmt->bindValue($bindIndex++, (int) $pr3['per_page'], PDO::PARAM_INT);
                $stmt->bindValue($bindIndex++, (int) $pr3['offset'], PDO::PARAM_INT);
                $stmt->execute();
                $roadmapQuizzes = $stmt->fetchAll();
                $normalizeRows($roadmapQuizzes, ['question', 'correct_answer', 'node_title']);

                if ($adminRoadmapTitle !== '') {
                    $stmt = $pdo->prepare("SELECT id, title FROM roadmap_nodes WHERE COALESCE(NULLIF(TRIM(roadmap_title), ''), 'Основной') = ? ORDER BY id ASC");
                    $stmt->execute([$adminRoadmapTitle]);
                    $roadmapNodesForSelect = $stmt ? ($stmt->fetchAll() ?: []) : [];
                } else {
                    $stmt = $pdo->query("SELECT id, title FROM roadmap_nodes ORDER BY id ASC");
                    $roadmapNodesForSelect = $stmt ? ($stmt->fetchAll() ?: []) : [];
                }
                $normalizeRows($roadmapNodesForSelect, ['title']);

                $stmt = $pdo->query("
                    SELECT COALESCE(NULLIF(TRIM(roadmap_title), ''), 'Основной') AS roadmap_title, COUNT(*) AS total
                    FROM roadmap_nodes
                    GROUP BY COALESCE(NULLIF(TRIM(roadmap_title), ''), 'Основной')
                ");
                foreach (($stmt ? $stmt->fetchAll() : []) as $row) {
                    $title = normalizeMojibakeText((string) ($row['roadmap_title'] ?? ''));
                    if ($title === '') {
                        $title = 'Основной';
                    }
                    $roadmapCountsMap[$title] = (int) ($row['total'] ?? 0);
                }
            }

            if (empty($adminContestsForSelect)) {
                $adminContestsForSelect = $adminContests;
            }
            if (empty($roadmapListForSelect)) {
                $roadmapListForSelect = $roadmapList;
            }
            if (empty($roadmapNodesForSelect)) {
                $roadmapNodesForSelect = $roadmapNodes;
            }
            require_once __DIR__ . '/templates/admin.php';
            break;
        default:
            http_response_code(404);
            require_once __DIR__ . '/templates/404.php';
    }
}
