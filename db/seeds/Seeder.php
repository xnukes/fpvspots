<?php


use Phinx\Seed\AbstractSeed;

class Seeder extends AbstractSeed
{
    const SQL_FILE = __DIR__.'/../db_data.sql';

    public function run()
    {
        if (file_exists(self::SQL_FILE)) {
            $this->execute(file_get_contents(self::SQL_FILE));
        } else {
            echo(self::SQL_FILE." not found");
        }
    }
}
