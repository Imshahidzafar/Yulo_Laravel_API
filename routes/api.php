<?php

use App\Http\Controllers\API\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: user,key,token,Content-Type, x-xsrf-token");
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');


Route::group(['prefix' => 'v1','namespace'=>'API'],function(){
    Route::post('install-url', 'InstallerController@storeUrl')->name('storeUrl');

	Route::post('register', 'RegisterController@index')->name('user_register');
	Route::post('is-email-exist', 'RegisterController@isEmailExist')->name('is_email_exist');
	Route::post('is-mobile-exist', 'RegisterController@isMobileExist')->name('is_mobile_exist');
	Route::post('login', 'RegisterController@login')->name('login');
	Route::post('social-register','RegisterController@socialRegister')->name('social-register');
	Route::post('firebaseregister','RegisterController@firebaseregister')->name('firebaseregister');
	Route::post('refresh', 'RegisterController@refresh')->name('refresh');
	Route::post('resend-otp', 'RegisterController@resendOtp')->name('resend-otp');
	Route::post('logout', 'UserController@logout')->name('logout');
	Route::post('register-social', 'RegisterController@socialLogin')->name('user_register_social');
	Route::post('verify-otp', 'RegisterController@verifyOtp')->name('verify_otp');
	Route::get('get-sounds', 'SoundController@index')->name('get_sounds');
	Route::get('fav-sounds', 'SoundController@favSounds')->name('get_fav_sounds');
	Route::post('set-fav-sound', 'SoundController@setFavSound')->name('set_fav_sound');
	Route::get('get-videos', 'VideoController@index')->name('get_videos');
	Route::get('user_information', 'RegisterController@loginProfileInformation')->name('user_information');
	Route::post('update_user_information', 'RegisterController@updateUserInformation')->name('update_user_information');
	Route::post('update_profile_pic', 'UserController@updateUserProfilePic')->name('update_profile_pic');
	Route::post('upload-video', 'VideoController@uploadVideo')->name('upload-video');
	Route::post('fetch-user-info', 'UserController@fetchUserInformation')->name('fetch-user-info');
	Route::post('fetch-login-user-info', 'UserController@fetchLoginUserInformation')->name('fetch-login-user-info');
	Route::post('fetch-login-user-fav-videos', 'UserController@fetchLoginUserFavVideos')->name('fetch-login-user-fav-videos');
	Route::post('video-like', 'VideoController@videoLikes')->name('video-like');
	Route::post('fetch-video-comments', 'VideoController@fetchVideoComments')->name('fetch-video-comments');
	Route::post('add-comment', 'VideoController@addComment')->name('add-comment');
	Route::post('follow-unfollow-user', 'UserController@followUnfollowUser')->name('follow-unfollow-user');
	Route::post('remove-follower', 'UserController@removeFollower')->name('remove-follower');

	Route::post('video-upload-2', 'VideoController@uploadVideo2')->name('video-upload-2');
	Route::post('filter-video-upload', 'VideoController@filterUploadVideo')->name('filter-video-upload');
	Route::post('hash-tag-videos', 'VideoController@hashTagVideos')->name('hash-tag-videos');
	Route::post('video-views', 'VideoController@video_views')->name('video-views');
	Route::post('video-enabled', 'VideoController@video_enabled')->name('video-enabled');
	Route::post('delete-video', 'VideoController@deleteVideo')->name('delete-video');
	Route::post('most-viewed-video-users', 'VideoController@mostViewedVideoUsers')->name('most-viewed-video-users');
	Route::post('following-users-list', 'UserController@FollowingUsersList')->name('following-users-list');
	Route::post('followers-list', 'UserController@FollowersList')->name('followers-list');
	Route::get('blocked-users-list','UserController@blockedUsersList')->name('blocked-users-list');
	Route::post('get-unique-id', 'UserController@unique_user_id')->name('get-unique-id');
	Route::post('get-sound', 'SoundController@getSound')->name('get-sound');
	Route::get('get-cat-sounds', 'SoundController@getCategorySounds')->name('get-cat-sounds');
	Route::post('submit-report', 'UserController@submitReport')->name('submit-report');
	Route::post('delete-comment', 'UserController@deleteComment')->name('delete-comment');
	Route::post('edit-comment', 'UserController@editComment')->name('edit-comment');
	Route::post('block-user', 'UserController@blockUser')->name('block-user');
	Route::get('get-ads', 'adController@index')->name('get-ads');
	Route::get('get-watermark', 'VideoController@getWatermark')->name('get-watermark');
	Route::post('user-verify', 'UserController@userVerify')->name('user-verify');
	Route::get('verify-status', 'UserController@verifyStatusDetail')->name('verify-status');

	Route::get('app-configration', 'AppController@appConfig')->name('app-configration');
	Route::get('app-login', 'AppController@index')->name('app-login');
	Route::get('end-user-license-agreement','AppController@endUserLicenseAgreement')->name('end-user-license-agreement');
	Route::post('change-password','UserController@changePassword')->name('change-password');

	Route::get('get-eula-agree','UserController@getEulaAgree')->name('get-eula-agree');
	Route::post('update-eula-agree','UserController@updateEulaAgree')->name('update-eula-agree');
	Route::post('forgot-password','UserController@forgotPassword')->name('forgot-password');
	Route::post('update-forgot-password','UserController@updateForgotPassword')->name('update-forgot-password');
	Route::post('update-video-description','VideoController@updateVideoDescription')->name('update-video-description');

	Route::get('search','AppController@search')->name('search');
	Route::get('user-search','AppController@searchUsers')->name('user-search');
	Route::get('video-search','AppController@searchVideos')->name('video-search');
	Route::get('tag-search','AppController@searchTags')->name('tag-search');
	Route::get('hash-videos','AppController@hashTagVideos')->name('hash-videos');

	Route::post('add-guest-user','UserController@addGuestUser')->name('add-guest-user');
	Route::post('update-fcm-token', 'UserController@updateFcmToken')->name('update-fcm-token');

	Route::post('update-notification-setting', 'SettingController@updateNotificationSetting')->name('update-notification-setting');
	Route::post('user-notification-setting', 'SettingController@userNotification')->name('user-notification-setting');

	Route::post('notifications-list', 'UserController@notificationsList')->name('notifications-list');

	Route::post('/chat-users', 'ConversationController@chatUsers')->name('chat-users');
	Route::post('/conversation/store', 'ConversationController@store')->name('conversation.store');
    Route::post('/conversation/get', 'ConversationController@getConversation')->name('conversation.get');
    Route::post('get-online-users', 'ConversationController@getOnlineUsers')->name('get-online-users');

    Route::post('/message/{conversation}/store', 'ChatController@storeMessage')->name('chats.storeMessage');
    Route::post('/message/{conversation}/read', 'ChatController@readMessage')->name('chats.readMessage');
    Route::post('/message/{conversation}/delete', 'ChatController@deleteMessage')->name('chats.deleteMessage');
    Route::post('/message/{conversation}/typing', 'ChatController@typingMessage')->name('chats.typingMessage');
    Route::post('/message/{conversation}/get-messages', 'ChatController@getMessage')->name('chats.getMessage');

    Route::post('get-chat-with','UserController@getChatWith')->name('get-chat-with');

    Route::post('views_counter', 'CustomController@views_counter')->name('views_counter');
    Route::post('update_privacy', 'CustomController@update_privacy')->name('update_privacy');
    Route::post('get_users', 'CustomController@get_users')->name('get_users');
    Route::post('register_resend_otp', 'CustomController@register_resend_otp')->name('register_resend_otp');
    Route::post('get_all_users_data', 'CustomController@get_all_users_data')->name('get_all_users_data');
    Route::post('search_user', 'CustomController@search_user')->name('search_user');
    Route::post('get_email_exists', 'CustomController@get_email_exists')->name('get_email_exists');
    Route::post('get_username_exists', 'CustomController@get_username_exists')->name('get_username_exists');
    Route::post('get_user_profile', 'CustomController@get_user_profile')->name('get_user_profile');
    Route::post('update_user_pref', 'CustomController@update_user_pref')->name('update_user_pref');
    Route::post('get_user_one_signal_id', 'CustomController@get_user_one_signal_id')->name('get_user_one_signal_id');
    Route::post('update_user_one_signal_id', 'CustomController@update_user_one_signal_id')->name('update_user_one_signal_id');

    Route::post('get_user_requests', 'CustomController@get_user_requests')->name('get_user_requests');
    Route::post('get_user_requests_status', 'CustomController@get_user_requests_status')->name('get_user_requests_status');
    Route::post('add_user_requests', 'CustomController@add_user_requests')->name('add_user_requests');

    Route::post('get_users_videos', 'CustomController@get_users_videos')->name('get_users_videos');
    Route::post('get_users_achieved_videos', 'CustomController@get_users_achieved_videos')->name('get_users_achieved_videos');
    Route::post('delete_user_requests', 'CustomController@delete_user_requests')->name('delete_user_requests');

    Route::post('update_privacy_videos', 'CustomController@update_privacy_videos')->name('update_privacy_videos');
    Route::post('get_videos_data', 'CustomController@get_videos_data')->name('get_videos_data');

    Route::post('test_uploadVideo', 'VideoController@test_uploadVideo')->name('test_uploadVideo');
    Route::post('shahid_test_uploadVideo', 'VideoController@shahid_test_uploadVideo')->name('shahid_test_uploadVideo');
});
