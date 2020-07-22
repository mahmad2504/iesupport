<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CustomFields;
use App\Jira;
use App\Database;
class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'sync {--rebuild=0} {--beat=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
		date_default_timezone_set("Asia/Karachi");
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$db= new Database();
		$rebuild = $this->option('rebuild');
		$beat = $this->option('beat');
		if((($beat%10)!=0)&& (!IsSyncRequested()))//Every ten minutes
			return;
		if($beat%10 == 0)
			file_get_contents("https://script.google.com/macros/s/AKfycbwCNrLh0BxlYtR3I9iW2Z-4RQK88Hryd4DEC03lIYLoLCce80A/exec?func=alive&device=iesd_support");
		
		$jql = env('SEED_QUERY');
		$jira=new Jira();
		$updated = null;
		if($rebuild == 0)
			$updated = date('Y-m-d H:i',strtotime(' -2 day'));
		
		$tickets = $jira->Sync($jql,$updated);
		ProcessTickets($tickets);
        return 0;
    }
}
