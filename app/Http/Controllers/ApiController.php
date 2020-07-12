<?php

namespace App\Http\Controllers;


use Auth;
use Illuminate\Http\Request;
use Response;
use App\Database;
use Carbon\Carbon;

class ApiController extends Controller
{
	public function Index(Request $request)
	{
		return view('welcome');
	}
	
	public function GetGraphData($label,$start,$end)
	{
		$c =[ 'created_this_week'=>1,'total_created'=>2,'resolved_this_week'=>3,'total_resolved'=>4,'defects_resolved_this_week'=>5,'total_resolved_defects'=>6,
		'defects_created_this_week'=>7,'total_defects_created'=>8];
		
		$db =  new Database();
		$start = $start->getTimeStamp();
		$end = $end->getTimeStamp();
		$query = [
			'project'=>$label,
			'$or'=>[
			  ['created'=>['$gte'=> $start,'$lte'=>$end]],
			  ['resolutiondate'=>['$gte'=> $start,'$lte'=>$end]]
		       ]
		    ];
		//$query = 
		//	['$or'=>[ ['created'=>$start->getTimeStamp()],['resolutiondate'=> $start->getTimeStamp()]]];
		
		$records=$db->Read($query)->toArray();
		
		$graphdata = [];
		foreach($records as $record)
		{
			$record = $record->jsonSerialize();
			
			$created = $record->created;
			//dump("Created=".$created."#####".$start."--".$end);
			
			if($created >= $start && $created <= $end)
			{
				$created = CDate($created);
				$week = (Sunday($created)->format('Y-m-d'));
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
			}
			else
			{
				if($record->resolutiondate == '')
					dd("Created date violation");
			}
			//////////////////////////////////////////////////////////////////
			$resolutiondate = $record->resolutiondate;
			if(($resolutiondate  >= $start && $resolutiondate <= $end)&&($resolutiondate != ''))
			{
				$resolutiondate = CDate($resolutiondate);
				$week = (Sunday($resolutiondate)->format('Y-m-d'));
				$week = $week." 00:00";
				if(isset($graphdata[$week]))
					$graphdata[$week];
				else
					$graphdata[$week] = [$week,0,0,0,0,0,0,0,0];
				$graphdata[$week][$c['resolved_this_week']]++;
				if(($record->reason_for_closure == "Defect/Bug")||($record->issuetype=='Issue'))
					$graphdata[$week][$c['defects_resolved_this_week']]++;
				//dump($record->reason_for_closure);//": "Defect/Bug",
			}
		}
		ksort($graphdata);
		$created = 0;
		$resolved = 0;
		$defects_resolved = 0;
		$defects_created = 0;
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
		}
		
		/*$graphdata = [];
		$graphdata['2020-01-01']=['2020-01-01',10];
		$graphdata['2020-02-01']=['2020-02-01',15];
		$graphdata['2020-03-01']=['2020-03-01',20];
		$graphdata['2020-04-01']=['2020-04-01',25];
		$graphdata['2020-05-01']=['2020-05-01',30];
		$graphdata['2020-06-01']=['2020-06-01',35];
		$graphdata['2020-07-01']=['2020-07-01',40];*/
		
		$graphdata = (array_values($graphdata));
		
		return $graphdata;
	}
	public function BurnUp(Request $request)
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
		//dump($end->format('Y-m-d H:i:s'));
		$graphdata1 = $this->GetGraphData('VSTARMOD',$start,$end);
		$graphdata2 = $this->GetGraphData('VOLSUP',$start,$end);
		$start = $start->format('Y-m-d');
		$end = $end->format('Y-m-d');
		return view('welcome',compact('graphdata1','graphdata2','start','end'));
	}
}