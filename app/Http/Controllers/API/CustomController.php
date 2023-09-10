<?php

namespace App\Http\Controllers\API;

use Auth;
use App\User;
use DateTime;
use Exception;
use JWTAuth;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use App\Helpers\Common\Functions;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomController extends Controller {
    public function __construct(){
        $this->middleware('auth:api', ['except' => ['views_counter', 'update_privacy', 'get_users', 'register_resend_otp', 'get_all_users_data', 'get_email_exists', 'get_username_exists', 'get_user_profile', 'update_user_pref', 'get_user_requests', 'add_user_requests', 'get_user_requests_status','get_users_videos', 'get_users_achieved_videos', 'delete_user_requests', 'update_privacy_videos', 'get_videos_data','search_user','get_user_one_signal_id','update_user_one_signal_id']]);
    }
    
    /** TOTAL VIEWS **/
    public function views_counter(Request $data){
        if(!empty($data->user_id)){
            $users_id           = $data->user_id;
            
            $my_videos_ids      = DB::table('videos')->select('video_id')->where('user_id', $users_id)->get();
            $my_videos_array    = json_decode(json_encode($my_videos_ids), true);
            $total_views        = number_format(DB::table('video_views')->whereIn('video_id', $my_videos_array)->count());
            
            $response = array("status" => "success", "data" => ['views' => $total_views]);
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User ID is required.");
            return response()->json($response);
        }
    }
    /** TOTAL VIEWS **/
    
    /** UPDATE PRIVACY **/
    public function update_privacy(Request $data){
        if(!empty($data->user_id)){
            $users_id           = $data->user_id;
            
            if(!empty($data->privacy_profile)){
                DB::table('users')->where('user_id', $users_id)->update(array('privacy_profile' => $data->privacy_profile));
            }
            
            if(!empty($data->post_view)){
                DB::table('users')->where('user_id', $users_id)->update(array('post_view' => $data->post_view));
            }
            
            if(!empty($data->social_links)){
                DB::table('users')->where('user_id', $users_id)->update(array('social_links' => $data->social_links));
            }
            
            $response = array("status" => "success", "msg" => "User privacy updated successfully.");
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User ID is required.");
            return response()->json($response);
        }
    }
    /** UPDATE PRIVACY **/

    /** TOTAL VIEWS **/
    public function get_users(Request $data){
        if(!empty($data->user_id)){
            $users_id           = $data->user_id;
            
            $my_follow_ids      = DB::table('follow')->select('follow_to')->where('follow_by', $users_id)->get();
            $my_follow_array    = json_decode(json_encode($my_follow_ids), true);
            $all_users          = DB::table('users')->whereNotIn('user_id', $my_follow_array)->where('user_id', '!=', $users_id)->where('privacy_profile', '=', 'Public')->get();
            
            $response = array("status" => "success", "data" => $all_users);
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User ID is required.");
            return response()->json($response);
        }
    }
    /** TOTAL VIEWS **/

    /** REGISTER RESEND OTP **/
    public function register_resend_otp(Request $data){
       if (isset($data->email)) {
            $mail_setting = DB::table('mail_types')->where('active', 1)->first();
            if ((config('app.sendgrid_api_key') != "" || config('app.mail_host') != "") && isset($mail_setting)) {
                $site_title = Functions::getSiteTitle();
                
                $data_exists = DB::table('users')->select(DB::raw("user_id, username, email, app_token"))->where('email', $data->email)->count();
                if($data_exists > 0){
                    $data = DB::table('users')->select(DB::raw("user_id, username, email, app_token"))->where('email', $data->email)->first();

                    $user_token = $data->app_token;
                    $username = $data->username;
                    $user_id = $data->user_id;

                    $otp = mt_rand(100000, 999999);
                    DB::table('users')->where('user_id', $user_id)->update(['verification_code' => $otp]);

                    $mailBody = '
                    <p>Dear <b>' .  $data->email . '</b>,</p>
                    <p style="font-size:16px;color:#333333;line-height:24px;margin:0">Use the OTP to verify your email address.</p>
                    <h3 style="color:#333333;font-size:24px;line-height:32px;margin:0;padding-bottom:23px;margin-top:20px;text-align:center">'
                        . $otp . '</h3>
                    <br/><br/>
                    <p style="color:#333333;font-size:16px;line-height:24px;margin:0;padding-bottom:23px">Thank you<br /><br/>' . $site_title . '</p>
                    ';
                    // dd($mailBody);
                    // $ref_id
                    $array = array('subject' => 'OTP Email Verification - ' . $site_title, 'view' => 'emails.site.company_panel', 'body' => $mailBody);
                    if (strpos($_SERVER['SERVER_NAME'], "localhost") === false && strpos($_SERVER['SERVER_NAME'], "leukewebpanel.local") === false) {
                        Mail::to($data->email)->send(new SendMail($array));
                    }
                    $msg = "An OTP has been sent to your Email";
                    // $id = $user_id;
                    $data  = array('user_id' => $user_id, 'app_token' => $user_token, 'username' => strtolower(strip_tags($username)));
                    $msg = "An OTP has been sent to your Email";
                    DB::table('notification_settings')->insert(['user_id' => $user_id]);
                    $response = array("status" => "success", 'msg' => $msg, 'content' => $data);
                    return response()->json($response);
                } else {
                    return response()->json(['status' => 'error', 'msg' => "User does not exists."]);
                }
            } else {
                return response()->json(['status' => 'error', 'msg' => "Registration failed. Please Contact to administrator."]);
            }
        } else{ 
            return response()->json(['status' => 'error', 'msg' => "All data required."]);
        }
    }
    /** REGISTER RESEND OTP **/

    /** GET ALL USERS **/
    public function get_all_users_data(Request $data){
        if(!empty($data->user_id)){
            $users_id           = $data->user_id;
            $all_users          = DB::table('users')->where('user_id', '!=', $users_id)->get();
            
            $response = array("status" => "success", "data" => $all_users);
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User ID is required.");
            return response()->json($response);
        }
    }
    /** GET ALL USERS **/

    /** EMAIL CHECK **/
    public function get_email_exists(Request $data){
        if(!empty($data->email)){
            $all_users          = DB::table('users')->where('email', '=', $data->email)->count();
            if($all_users == 0){
                $message = 'Available';
            } else {
                $message = 'Unavailable';
            }
            
            $response = array("status" => "success", "data" => $message);
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "Email is required.");
            return response()->json($response);
        }
    }
    /** EMAIL CHECK **/

    /** USERNAME CHECK **/
    public function get_username_exists(Request $data){
        if(!empty($data->username)){
            $all_users          = DB::table('users')->where('username', '=', $data->username)->count();
            if($all_users == 0){
                $message = 'Available';
            } else {
                $message = 'Unavailable';
            }
            
            $response = array("status" => "success", "data" => $message);
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "Email is required.");
            return response()->json($response);
        }
    }
    /** USERNAME CHECK **/

    /** GET USERS PROFILE **/
    public function get_user_profile(Request $data){
        if(!empty($data->user_id)){
            $users_id           = $data->user_id;
            $users_data          = DB::table('users')->where('user_id', '=', $users_id)->first();
            
            $response = array("status" => "success", "data" => $users_data);
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User ID is required.");
            return response()->json($response);
        }
    }
    /** GET USERS PROFILE **/

    /** UPDATE USERS PROFILE **/
    public function update_user_pref(Request $data){
        if(!empty($data->user_id)){
            $users_id           = $data->user_id;
            if(!empty($data->video_pref)){
                DB::table('users')->where('user_id', $users_id)->update(array('video_pref' => $data->video_pref));
            }
            
            $response = array("status" => "success", "msg" => "User preferences updated successfully.");            
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User ID is required.");
            return response()->json($response);
        }
    }
    /** UPDATE USERS PROFILE **/

    /** GET USERS REQUESTS **/
    public function get_user_requests(Request $data){
        if(!empty($data->user_id)){
            $users_id           = $data->user_id;
            $users_data          = DB::table('users_requests')->where('status', '=', 'Pending')->where('to_user_id', '=', $users_id)->get();
            $final_data = [];
            foreach($users_data as $row){
                $row->users_data = DB::table('users')->where('user_id', '=', $row->from_user_id)->first();
                $final_data[] = $row;
            }

            if(!empty($final_data)){
                $response = array("status" => "success", "data" => $final_data);
            } else {
                $response = array("status" => "error", "msg" => "No Requests Found.");
            }
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User ID is required.");
            return response()->json($response);
        }
    }
    /** GET USERS REQUESTS **/

    /** GET USERS REQUESTS STATUS **/
    public function get_user_requests_status(Request $data){
        if(!empty($data->from_user_id) && !empty($data->to_user_id)){
            $users_data             = DB::table('users_requests')->where('from_user_id', '=', $data->from_user_id)->where('to_user_id', '=', $data->to_user_id)->orderBy('users_requests_id', 'DESC')->first();
            
            $response = array("status" => "success", "data" => $users_data);
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User ID is required.");
            return response()->json($response);
        }
    }
    /** GET USERS REQUESTS STATUS **/

    /** UPDATE USERS PROFILE **/
    public function add_user_requests(Request $data){
        if(!empty($data->from_user_id) && !empty($data->to_user_id)){
            $from_user_id           = $data->from_user_id;
            $to_user_id             = $data->to_user_id;

            if(!empty($data->status)){
                DB::table('users_requests')->where('from_user_id', $from_user_id)->update(array('status' => $data->status));
                $response = array("status" => "success", "msg" => "User Request " . $data->status. " Successfully.");            
            } else {
                $users_requests         = DB::table('users_requests')->where('from_user_id', '=', $from_user_id)->where('to_user_id', '=', $to_user_id)->where('status', '=', 'Pending')->count();
                if($users_requests == 0){
                    DB::table('users_requests')->insert([
                        'from_user_id' => $from_user_id,
                        'to_user_id' => $to_user_id,
                        'date_added' => date('Y-m-d H:i:s'),
                        'date_modified' => date('Y-m-d H:i:s'),
                        'status' => 'Pending'
                    ]);

                    $response = array("status" => "success", "msg" => "User request sent successfully.");            
                } else {
                    $response = array("status" => "success", "msg" => "Request Already Sent.");            
                }
            }
    
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User ID is required.");
            return response()->json($response);
        }
    }
    /** UPDATE USERS PROFILE **/

    /** DELETE USERS PROFILE **/
    public function delete_user_requests(Request $data){
        if(!empty($data->from_user_id) && !empty($data->to_user_id)){
            $from_user_id           = $data->from_user_id;
            $to_user_id             = $data->to_user_id;

            $deleted = DB::table('users_requests')->where('from_user_id', $from_user_id)->delete();
            if($deleted){
                $response = array("status" => "success", "msg" => "User Request Deleted Successfully.");            
            } else {
                $response = array("status" => "error", "msg" => "Oops! Something went wrong.");            
            }
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User IDs is required.");
            return response()->json($response);
        }
    }
    /** DELETE USERS PROFILE **/

    /** GET ALL USERS VIDEOS **/
    public function get_users_videos(Request $data){
        if(!empty($data->user_id) && !empty($data->viewer_user_id)){
            $users_id        = $data->user_id;
            $users_data      = DB::table('users')->where('user_id', '=', $users_id)->first();

            if($users_data->video_pref == '24 Hours'){
                $date_ending = (new \DateTime())->modify('-1 day');
                $date_final  = $date_ending->format('Y-m-d H:i:s'); // 2021-09-12 13:01:55
                
                $all_videos  = DB::table('videos')->where('user_id', '=', $users_id)->where('created_at', '>', $date_final)->get();
            } else if($users_data->video_pref == 'Open Once'){
                $all_videos  = [];
                $videos      = DB::table('videos')->where('user_id', '=', $users_id)->get();
                foreach($videos as $video){
                    $view_videos = DB::table('video_views')->where('user_id', '=', $data->viewer_user_id)->where('video_id', '=', $video->video_id)->count();
                    if($view_videos == 0){
                        $all_videos[] = $video;
                    }
                }
            } else {
                $all_videos          = DB::table('videos')->where('user_id', '=', $users_id)->get();
            }
            
            
            $response = array("status" => "success", "data" => $all_videos);
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User ID is required.");
            return response()->json($response);
        }
    }
    /** GET ALL USERS VIDEOS **/

    /** GET ALL USERS ARCHIEVED VIDEOS **/
    public function get_users_achieved_videos(Request $data){
        if(!empty($data->user_id)){
            $users_id        = $data->user_id;
            $users_data      = DB::table('users')->where('user_id', '=', $users_id)->first();

            $date_ending = (new \DateTime())->modify('-1 day');
            $date_final  = $date_ending->format('Y-m-d H:i:s'); // 2021-09-12 13:01:55
            
            $view_pref = 'Open Once';

            $all_videos  = DB::table('videos')->where('user_id', '=', $users_id)
            ->where(function($query) use ($view_pref, $date_final)
            {
                $query->where("view_pref", $view_pref)->orWhere('created_at', '<', $date_final);
            })
            ->where('view_pref', '!=', 'Forever')->get();
        
            // print_r($all_videos); exit;
            
            $response = array("status" => "success", "data" => $all_videos);
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User ID is required.");
            return response()->json($response);
        }
    }
    /** GET ALL USERS ARCHIEVED VIDEOS **/

    /** UPDATE PRIVACY **/
    public function update_privacy_videos(Request $data){
        if(!empty($data->video_id)){
            $video_id           = $data->video_id;
            
            if(!empty($data->view_pref)){
                DB::table('videos')->where('video_id', $video_id)->update(array('view_pref' => $data->view_pref));
            }
            
            if(!empty($data->privacy_profile)){
                DB::table('videos')->where('video_id', $video_id)->update(array('privacy_profile' => $data->privacy_profile));
            }
            
            if(!empty($data->pinned)){
                DB::table('videos')->where('video_id', $video_id)->update(array('pinned' => $data->pinned));
            }
            
            $response = array("status" => "success", "msg" => "Video privacy updated successfully.");
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "Video ID is required.");
            return response()->json($response);
        }
    }
    /** UPDATE PRIVACY **/

    /** VIDEOS DATA **/
    public function get_videos_data(Request $data){
        if(!empty($data->video_id)){
            $video_id           = $data->video_id;
            $all_videos         = DB::table('videos')->where('video_id', '=', $video_id)->first();
            
            $response = array("status" => "success", "data" => $all_videos);
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "Video ID is required.");
            return response()->json($response);
        }
    }
    /** VIDEOS DATA **/

    /* SEARCH USER*/
    public function search_user(Request $req){
        if(isset($req->search_user )) {
            $searchQuery=$req->search_user;
            if(!empty($req->user_id)){
                $users_id           = $req->user_id;
                $all_users          = DB::table('users')->where(function ($query) use ($searchQuery, $users_id) {
                                            $query->where('username', 'like', '%' . $searchQuery . '%')
                                                ->orWhere('fname', 'like', '%' . $searchQuery . '%')
                                                ->orWhere('lname', 'like', '%' . $searchQuery . '%');
                                        })
                                        ->where('user_id', '!=', $users_id)
                                        ->get();
                
                
                $response = array("status" => "success", "data" => $all_users);
                return response()->json($response);
            } else {
                $response = array("status" => "error", "msg" => "User ID is required.");
                return response()->json($response);
            }
        } else {
            $response = array("status" => "error", "msg" => "Search Value is required.");
            return response()->json($response);
        }
	  }
	  /* SEARCH USER*/	

      /** GET USERS ONE SIGNAL ID **/
    public function get_user_one_signal_id(Request $data){
        if(!empty($data->user_id)){
            $users_id           = $data->user_id;
            $users_data          = DB::table('users')->select('user_id','username','fname','lname','email','one_signal_id')->where('user_id', $users_id)->first();
            
            $response = array("status" => "success", "data" => $users_data);
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User ID is required.");
            return response()->json($response);
        }
    }
    /** GET USERS ONE SIGNAL ID **/

      /** UPDATE USERS ONE SIGNAL ID **/
    public function update_user_one_signal_id(Request $data){
        if(!empty($data->user_id)){
            $users_id           = $data->user_id;
            if($data->one_signal_id){
                $update=DB::table('users')->where('user_id', $users_id)->update(['one_signal_id'=>$data->one_signal_id]);
            }
            $response = array("status" => "success", "msg" => "User data updated successfully.");
            return response()->json($response);
        } else {
            $response = array("status" => "error", "msg" => "User ID is required.");
            return response()->json($response);
        }
    }
    /** UPDATE USERS ONE SIGNAL ID **/
}