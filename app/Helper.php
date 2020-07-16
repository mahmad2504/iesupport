<?php
use App\Database;
use Carbon\Carbon;

function CDate($timestamp=null)
{
	if($timestamp== null)
		return Carbon::now();
	$carbon = new Carbon();
	$carbon->setTimeStamp($timestamp);
	return $carbon;
}
function Sunday($date)
{
	$date->next(Carbon::SUNDAY);
	return $date;
}

function IsSyncRequested()
{
	$db= new Database();
	$sync_request = $db->GetVar('sync_request');
	if($sync_request == 1)
		return 1;
	return 0;
}
function RequestSync()
{
	$db= new Database();
	$db->SaveVar(['sync_request'=>1]);
}
function ResetSyncRequest()
{
	$db= new Database();
	$db->SaveVar(['sync_request'=>0]);
}
function ProcessTickets($tickets)
{
	$db= new Database();
	for($i=0;$i<count($tickets);$i++)
	{
		$ticket = $tickets[$i];
		//dump($ticket);
		$db->Update(['key'=>$ticket->key],$ticket);
	}
}