<?php

namespace App\Http\Controllers;


use Auth;
use Illuminate\Http\Request;
use Response;
use App\Database;
use Carbon\Carbon;

class ApiController extends Controller
{
	public function __construct()
    {
		date_default_timezone_set("Asia/Karachi");
    }
	
	public function GetGraphData($project,$components,$issuetypes,$start,$end)
	{	
		$c =[ 'created_this_week'=>1,'total_created'=>2,'resolved_this_week'=>3,'total_resolved'=>4,'defects_resolved_this_week'=>5,'total_resolved_defects'=>6,
		'defects_created_this_week'=>7,'total_defects_created'=>8];
		$today = new Carbon('now');
		
		$db =  new Database();
		$startts = $start->getTimeStamp();
		$endts = $end->getTimeStamp();
		$query = [
			'project'=>$project,
			'$or'=>[
					['created'=>['$gte'=> $startts,'$lte'=>$endts]],
					['resolutiondate'=>['$gte'=> $startts,'$lte'=>$endts]],
		       ]
		    ];
		if(count($components)>0)
		{
			$query['components']= ['$in'=>$components];
		}
		
		if(count($issuetypes)>0)
		{
			$query['issuetype']= ['$in'=>$issuetypes];
		}
		//dump($query);
		//$query = 
		//	['$or'=>[ ['created'=>$startts->getTimeStamp()],['resolutiondate'=> $startts->getTimeStamp()]]];
		
		$records=$db->Read($query)->toArray();
		//dd(count($records));
		$graphdata = [];
		$first_contact = [['<1',0,0,0,0],['1-5',0,0,0,0],['5-10',0,0,0,0],['>10',0,0,0,0]];	
		$backlogdata = [];
		foreach($records as $record)
		{
			$record = $record->jsonSerialize();
			$record->components = $record->components->jsonSerialize();
			
			$created = $record->created;
			
			//dump($record->key);
			//dump($record->priority);
			$record->firsresponse = get_working_minutes(CDate($record->created),CDate($record->first_contact));
			
			//if($record->key == 'VOLSUP-3807');
			//{
				//dump(CDate($record->created)->format('Y-m-d H:i'));
				//dump(CDate($record->first_contact)->format('Y-m-d H:i'));
				//dd($record->firsresponse);
			//}	
			
			if($record->firsresponse <= 8*60)
				$first_contact[0][$record->priority]++;
			else if(($record->firsresponse > 8*60) < ($record->firsresponse <= 8*5*60))
			{
				$first_contact[1][$record->priority]++;
			}
			else if(($record->firsresponse > 8*5*60) < ($record->firsresponse <= 8*10*60))
			{
				$first_contact[2][$record->priority]++;
			}
			else
			{
				$first_contact[3][$record->priority]++;
			}
			$created = CDate($created);
			$week = (Sunday($created)->format('Y-m-d'));
			if($created->getTimeStamp() > $today->getTimeStamp())
			{
				$week = $today->format('Y-m-d');
					
			}
			if($created->getTimeStamp() < $start->getTimeStamp())
			{
				$week = $start->format('Y-m-d');
			}
			$week = $week." 00:00";	
				
			if(isset($graphdata[$week]))
				$graphdata[$week];
			else
				$graphdata[$week] = [$week,0,0,0,0,0,0,0,0];
	
			$graphdata[$week][$c['created_this_week']]++;
			if($record->issuetype=='Issue')
			{
				$graphdata[$week][$c['defects_created_this_week']]++;

			}
			//////////////////////////////////////////////////////////////////
			$resolutiondate = $record->resolutiondate;
			if($resolutiondate != -1)
			{
				$resolutiondate = CDate($resolutiondate);
				$week = (Sunday($resolutiondate)->format('Y-m-d'));
				
				if($resolutiondate->getTimeStamp() > $end->getTimeStamp())
				{
					$week = $end->format('Y-m-d');
				}
				
				if($resolutiondate->getTimeStamp() > $today->getTimeStamp())
				{
					$week = $today->format('Y-m-d');
				}
				
				$week = $week." 00:00";
					
				if(isset($graphdata[$week]))
					$graphdata[$week];
				else
					$graphdata[$week] = [$week,0,0,0,0,0,0,0,0];
				$graphdata[$week][$c['resolved_this_week']]++;
				if(($record->reason_for_closure == "Defect/Bug")||($record->issuetype=='Issue'))
					$graphdata[$week][$c['defects_resolved_this_week']]++;
			}
		}
		
		ksort($graphdata);
		//dd($graphdata);
		$created = 0;
		$resolved = 0;
		$defects_resolved = 0;
		$defects_created = 0;
		$backlogdata = [];
		$bl = 0;
		$dbl = 0;
		foreach($graphdata as $week=>&$record)
		{
			$resolved = $resolved + $record[$c['resolved_this_week']];
			$created = $created + $record[$c['created_this_week']];
			$defects_resolved = $defects_resolved + $record[$c['defects_resolved_this_week']];
			$defects_created = $defects_created + $record[$c['defects_created_this_week']];
			
			$record[$c['total_created']] = $created;
			$record[$c['total_resolved']] = $resolved;
			$record[$c['total_resolved_defects']] = $defects_resolved;
			$record[$c['total_defects_created']] = $defects_created;
			$bl = $record[$c['created_this_week']]-$record[$c['resolved_this_week']]+$bl;
			$dbl = $record[$c['defects_created_this_week']]-$record[$c['defects_resolved_this_week']]+$dbl;
			
			$backlogdata[$week] = [$week,$record[$c['created_this_week']],$record[$c['resolved_this_week']],$bl,$dbl];
			
		}
		$out['gd'] = (array_values($graphdata));
		$out['fr'] = $first_contact;
		$out['bl'] = (array_values($backlogdata));;
		
		return $out;
	}
	public function Index(Request $request)
	{
		$end  = CDate();
		$start  = CDate();
		$start = $start->addDays(-90);
		if($request->start != null)
		{
			$start = new Carbon($request->start);
			$start->hour = 0;
			$start->minute = 0;
		}
		if($request->end != null)
		{
			$end =  new Carbon($request->end);
			$end->hour = 23;
			$end->minute = 59;
			
		}
		$graphdata1 = $this->GetGraphData('VOLSUP',[],[],$start,$end);
		$graphdata2 = $this->GetGraphData('VSTARMOD',[],[],$start,$end);
		$start = $start->format('Y-m-d');
		$end = $end->format('Y-m-d');
		return view('welcome',compact('graphdata1','graphdata2','start','end'));
	}
	public function Data(Request $request,$project)
	{
		$end  = CDate();
		$start  = CDate();
		$start = $start->addDays(-90);
		if($request->start != null)
		{
			$start = new Carbon($request->start);
			$start->hour = 0;
			$start->minute = 0;
		}
		if($request->end != null)
		{
			$end =  new Carbon($request->end);
			$end->hour = 23;
			$end->minute = 59;
			
		}
		$issuetypes = [];
		$components = [];
		
		if($request->components != null)
			$components = explode(",",$request->components);
		
		if($request->issuetypes != null)
			$issuetypes = explode(",",$request->issuetypes);
		
	
		$graphdata = $this->GetGraphData($project,$components,$issuetypes,$start,$end);
		return $graphdata;
	}
}