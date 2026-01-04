<?php
/**
 * Google OAuth 2.0 Login Helper
 * Simple implementation without deprecated libraries
 */

// Google API configuration
define('GOOGLE_CLIENT_ID', '1084564570697-k6it48e7f356ki1leoier2jo59eoi247.apps.googleusercontent.com');  
define('GOOGLE_CLIENT_SECRET', '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'); 
define('GOOGLE_REDIRECT_URL', 'index.php?page=login&action=google_callback'); 

// Start session
if(!session_id()){
    session_start();
}

/**
 * Generate Google OAuth URL
 */
function getGoogleAuthUrl() {
    $params = [
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URL,
        'response_type' => 'code',
        'scope' => 'email profile',
        'access_type' => 'online',
        'prompt' => 'select_account'
    ];
    
    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}

/**
 * Exchange authorization code for access token
 */
function getGoogleAccessToken($code) {
    $url = 'https://oauth2.googleapis.com/token';
    
    $postData = [
        'code' => $code,
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URL,
        'grant_type' => 'authorization_code'
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For localhost development
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return null;
    }
    
    $data = json_decode($response, true);
    return $data['access_token'] ?? null;
}

/**
 * Get user info from Google
 */
function getGoogleUserInfo($accessToken) {
    $url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . urlencode($accessToken);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For localhost development
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return null;
    }
    
    return json_decode($response, true);
}

?>
