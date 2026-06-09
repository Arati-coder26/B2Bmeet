<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Virtualevent;
use App\Models\Event;

use Illuminate\Support\Facades\DB;

class Meetings extends Controller
{
    //
    function virtualmeeting($virutalmeetingid)
    {
    	$meetingInfo = DB::table('virtual_event_meetings')->where('meeting_id',$virutalmeetingid)->get()->toArray();
    	if(count($meetingInfo)==0){
    		echo "Page Expired";
    	}else{
    		
    	$data['pagetitle'] = "Meeting Info";
    	$data['pageId'] = 'meetingInfo';
    	$data['meetingInfo'] = $meetingInfo;
    	$data['virtualmeetingid'] = $virutalmeetingid;
    	$eventId = $meetingInfo[0]->event_id;
    	$eventInfo = DB::table('virtual_events')->where('event_id',$eventId)->get()->toArray();
    	$data['eventInfo'] = $eventInfo;
    	return view("meetingsandchat.virtualmeetinginfo",$data);
    	}
    }
    function updatevirtualmeeting(Request $req){
    	
    	if($req->ajax())
    	{
    	if($req->input('meetingNote')!=NULL )
    	{
    		DB::table('virtual_event_meetings')->where('meeting_id',$req->input('meetingid'))->update(
        array('notes' => $req->input('meetingNote'),
    'updated_at' => (date("Y-m-d H:i:s")),
    'updated_by' => $userId = loggedInUserId()
           
        )		);
    		$response['validation'] = true;
    		$response['message'] = "Meeting Note has been saved successfully";

    		return $response;
    	}

    	if($req->input('meetingLink')!=NULL )
    	{

            $errors = array();

                if ($req->input('meetingLink')==NULL || ($req->input('meetingLink')=="")) { 
                    $errors[] = "Please specify the meeting link";
                }

                if(count($errors)!=0)
                {
                        $response['validation'] = false;
                    $response['message'] = "WE found some Errors";
                    $response['errors'] = $errors;

                }else{
                    DB::table('virtual_event_meetings')->where('meeting_id',$req->input('meetingid'))->update(
        array('meeting_link' => $req->input('meetingLink'),
    'updated_at' => (date("Y-m-d H:i:s")),
    'updated_by' => $userId = loggedInUserId()
           
        )       );
            $response['validation'] = true;
            $response['message'] = "Meeting Link has been saved successfully";

                }
    		
    		return $response;
    	}

        if($req->input('savechatmessage'))
        {
            $message = $req->input('messageBeingSent');
            $meetingId = $req->input('virtualmeetingid');
            $sender = loggedInUserId();

            $meetingInfo = DB::table('virtual_event_meetings')->where('meeting_id',$req->input('virtualmeetingid'))->get()->toArray();    
            if(count($meetingInfo)!=0)
            {
                $receiverId = ($meetingInfo[0]->participant1_userid == $sender ) ? ($meetingInfo[0]->participant2_userid) : ($meetingInfo[0]->participant1_userid);

            DB::table('virtual_meeting_chat')->insert(
                    array(
                        'meeting_id' => $meetingId,
                        'sender_id' => $sender,
                        'receiver_id' => $receiverId,
                        'message' => $message,
                        'message_read_status' => 'Unread',
                        'updated_at' => (date("Y-m-d H:i:s")),
                        'created_at' => (date("Y-m-d H:i:s"))
                    )
            );

            $response['validation'] = true;
            $response['message'] = "Meeting Link has been saved successfully";

            return $response;
            }
        }


    		}


    }
    public function checkAvailabilityForUser(Request $req){
        // if($req->ajax())
        {
            $eventId = $req->input('time_slots_for_event');
            $participantId = $req->input('slot_for_user_id');
            if($req->input('reschedule_slot')=='true'){
                $reschedule = true;
            }else { $reschedule = false; }
            $userId = loggedInUserId();
            
                $event = new Virtualevent();

        $eventInfo = $event->eventDetails($eventId);
$participantAlreadyAssignedCHeck = DB::table('virtual_event_requests')->select('*')->whereRaw('((participant1_userid =\''.$userId.'\' AND participant2_userid=\''.$participantId.'\' ) OR (participant2_userid =\''.$userId.'\' AND participant1_userid=\''.$participantId.'\' )) AND (event_id = \''.$eventId.'\') AND (approval_status=\'Pending\')')->get()->toArray();
            
            
            if(count($participantAlreadyAssignedCHeck)>0 && $reschedule==false){
                ?>
                    <div class="alert alert-warning">
                            Your request is pending with this user.
                    </div>
            <?php 

                exit();
            }        
            $slotDuration = intval($eventInfo[0]['slot_duration']); 
                              if(strtotime($eventInfo[0]['from_date_time']) < strtotime($eventInfo[0]['to_date_time']))
                              {
                                $lowerTime = $eventInfo[0]['from_date_time'];
                                $counter =0;
                                ?>
                                    <div class="col-12">
                                        Current Time as per Europe Helsinki-<?= date("M d, h:i A") ?>
                                    </div>  
                                <?php 
                                  do{
                                    $currentTime = time();

                                      $upperTime = date("Y-m-d H:i:s",strtotime($lowerTime) + ($slotDuration * 60))  ;
                                      
                                      ?>
                                        <div class="col-3">

                                            <?php 
                                            $participant1SlotCheck = DB::table('virtual_event_meetings')->whereRaw('(participant1_userid=\''.$userId.'\' OR participant2_userid=\''.$userId.'\') AND  ( (from_date_time <= \''.$lowerTime.'\' AND to_date_time > \''.$lowerTime.'\' ) OR (from_date_time <= \''.$upperTime.'\' AND to_date_time >= \''.$upperTime.'\' ) ) ')->get()->toArray();

                                            // echo '(participant1_userid=\''.$userId.'\' OR participant2_userid=\''.$userId.'\') AND  ( (from_date_time <= \''.$lowerTime.'\' AND to_date_time >= \''.$lowerTime.'\' ) OR (from_date_time <= \''.$upperTime.'\' AND to_date_time >= \''.$upperTime.'\' ) ) ';

                                            $participant2SlotCheck = DB::table('virtual_event_meetings')->whereRaw('(participant1_userid=\''.$participantId.'\' OR participant2_userid=\''.$participantId.'\')  AND        ( (from_date_time <= \''.$lowerTime.'\' AND to_date_time > \''.$lowerTime.'\' ) OR (from_date_time <= \''.$upperTime.'\' AND to_date_time >= \''.$upperTime.'\' ) ) ')->get()->toArray();
//
                                            // echo '(participant1_userid=\''.$userId.'\' OR participant2_userid=\''.$userId.'\')  AND        ( (from_date_time >= \''.$lowerTime.'\' AND to_date_time <= \''.$lowerTime.'\' ) OR (from_date_time >= \''.$upperTime.'\' AND to_date_time <= \''.$upperTime.'\' ) )  '."<br />";
                                            // echo '(participant1_userid=\''.$participantId.'\' OR participant2_userid=\''.$participantId.'\')  AND        ( (from_date_time <= \''.$lowerTime.'\' AND to_date_time >= \''.$lowerTime.'\' ) OR (from_date_time <= \''.$upperTime.'\' AND to_date_time >= \''.$upperTime.'\' ) ) '."<br />";

                                             $buttonClass = "btn-success";
                                             $buttonDisabled = "";   
                                             $tooltipContent = "Slot Available";
                                             if(count($participant1SlotCheck)!=0){
                                                $buttonClass = "btn-secondary disabled";
                                                // $buttonDisabled = "disabled=\"disabled\"";   
                                                $tooltipContent ="Meeting slot booked with ".userNameForID($participant1SlotCheck[0]->participant2_userid);
                                             }
                                             else if(count($participant2SlotCheck)!=0){
                                                $buttonClass = "btn-warning disabled";
                                                // $buttonDisabled = "disabled=\"disabled\"";   
                                                $tooltipContent = "Meeting Slot booked for ".userNameForID($participantId);
                                             }
                                             else if($currentTime > strtotime($lowerTime))
                                             {
                                                    $buttonClass = "btn-secondary disabled";
                                                $buttonDisabled = "disabled=\"disabled\"";   
                                                $tooltipContent = "Meeting Time Elapsed";  
                                             }
                                              if(count($participant2SlotCheck)!=0 && count($participant1SlotCheck)!=0){
                                                        if(($participant1SlotCheck[0]->participant1_userid==$userId && $participant1SlotCheck[0]->participant2_userid==$participantId ) || ($participant1SlotCheck[0]->participant2_userid==$userId && $participant1SlotCheck[0]->participant1_userid==$participantId ) )
                                                        {
                                                            $buttonClass = "btn-danger disabled";
                                                // $buttonDisabled = "disabled=\"disabled\"";   
                                                        $tooltipContent = "Slot Booked with ".userNameForID($req->input('slot_for_user_id'));
                                                            }
                                             }



                                            ?>
                                            <button type="button" class="btn <?= $buttonClass ?> slotBookingBtn" style="margin-bottom: 20px;" data-fromtime="<?= date("Y-m-d H:i",strtotime($lowerTime)) ?>" slotindex="<?= $counter ?>" data-totime="<?= date("Y-m-d H:i",strtotime($upperTime)) ?>" <?= $buttonDisabled ?> 
 data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?= $tooltipContent ?>"
                                            ><?= date("D h:i A",strtotime($lowerTime)) ?> to <?= date("D h:i A",strtotime($upperTime)) ?></button>
                                        </div>
                                      <?php 
                                      $lowerTime = $upperTime;
                                      $counter++;
                                  }while(strtotime($upperTime) < strtotime($eventInfo[0]['to_date_time']));
                                  
                              }else{
                                echo "Invalid from time and date time";
                              }
                              
        }
    }
    function pendingRequests(){

        $data['pagetitle'] = "Pending Requests";
        $data['pageId'] = 'myrequests';
        return view("meetingsandchat.pendingrequests",$data);
    }
}
