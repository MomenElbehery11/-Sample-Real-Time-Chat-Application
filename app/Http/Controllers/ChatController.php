<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Channel;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function loadDashboard(){
        //fetch them here except the logged in user as user should not see him or herself in a list
        $all_users=User::where('id','!=',auth()->user()->id)->get();
        return view('dashboard',compact('all_users'));
    }

    public function CheckChannel(Request $request){
        $recipientId=$request->recipientId;
        $loggedInUserId=auth()->user()->id;
        //check if channel exists in database
        $channel=Channel::where(function($query)use($recipientId,$loggedInUserId){
        $query->where('user1_id',$loggedInUserId)
        ->where('user2_id',$recipientId);
        })->first();
        if($channel){
            return response()->json([
                'channelExists'=>true,
                'channelName'=>$channel->name,
            ]);
        }else{
            return response()->json([
                'channelExists'=>false,
            ]);
        }
    }

    public function CreateChannel(Request $request){
        $recipientId=$request->recipientId;
        $loggedInUserId=auth()->user()->id;
        try{
            //Generate the Channel name
            $ChannelName='chat-'.min($recipientId,$loggedInUserId). '-'.max($recipientId,$loggedInUserId);
            //this will produce something like chat-2-3 as our private channel just for user with ids used

            //create channel in database
            $channel =Channel ::create([
                'user1_id'=>$loggedInUserId,
                'user2_id'=>$recipientId,
                'name'=>$ChannelName,
            ]);
            //so let's  update the migration file to have the data above
            return response()->json([
                'success'=>false,
                'error'=>$ChannelName(),
            ]);
        } catch (\Exception $e){
            return response()->json([
                'success'=>false,
                'error'=>$e->getMessage(),
            ]);
        }

    }


}
