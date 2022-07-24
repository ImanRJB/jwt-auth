<?php

namespace ImanRjb\JwtAuth\Commands;

use App\Facades\ExchangeMongoRepositoryFacade;
use App\Models\Market;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use ImanRjb\JwtAuth\Models\Token;

class PurgeRevokedTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt-auth:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge revoked or expired tokens';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Token::whereRevoked(1)->orWhere('refresh_token_expires_at', '<', Carbon::now())->delete();
    }
}
