<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LtiResult;
use App\LoginModule\LTI\LTIHelper;

class SendLTIResultsCommand extends Command
{

    protected $signature = 'lti:send';
    protected $description = 'Sent LTI results';


    public function __construct(LTIHelper $lti)
    {
        parent::__construct();
        $this->lti = $lti;
    }


    public function handle()
    {
        $time = new \DateTime();
        $period = new \DateInterval(config('lti.send_result.period'));
        $time->sub($period);

        LtiResult::where('last_attempt', '<=', $time)->chunk(50, function($results) {
            $attempts_max = config('lti.send_result.attempts_max');
            foreach($results as $result) {
                $res = $this->lti->sendResult($result->lti_connection_id, $result->score);
                if($res) {
                    $result->delete();
                    continue;
                }
                $result->attempts++;
                if($attempts_max && $result->attempts > $attempts_max) {
                    $result->delete();
                    continue;
                }
                $result->last_attempt = new \DateTime;
                $result->save();
            }
        });
        return 0;
    }
}
