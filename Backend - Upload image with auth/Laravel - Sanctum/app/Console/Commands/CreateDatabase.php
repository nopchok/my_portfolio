<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new MySQL database';

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
     * @return int
     */
    public function handle()
    {
        $schemaName = config("database.connections.mysql.database");
        $charset = config("database.connections.mysql.charset",'utf8');
        $collation = config("database.connections.mysql.collation",'utf8_unicode_ci');

        config(["database.connections.mysql.database" => null]);
        $query = "CREATE DATABASE IF NOT EXISTS $schemaName CHARACTER SET $charset COLLATE $collation;";

        DB::statement($query);
        config(["database.connections.mysql.database" => $schemaName]);

        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $out->writeln("<info>make:database successfully.</info>");
    }
}
