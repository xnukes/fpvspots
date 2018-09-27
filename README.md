Project
-
FPVSpots.info is opensource project for pilots and teams with race drones.
This project has running on website **www.fpvspots.info** .

Database
-
Database is versioned using cakephp/phinx migrations.
To initialize the database use `vendor/bin/phinx migrate`
command followed by `vendor/bin/phinx seed:run` command. <br />
Creating new migration is done by invoking `vendor/bin/phinx create MigrationName`, 
which creates a file in db/migrations. Use either the `change` 
method or `up` and `down` methods to implement the migration.<br />
Migrations can be reverted using `vendor/bin/phinx rollback`<br />
More info @ `https://github.com/cakephp/phinx`

Special Thanks
-
- Petr Čada (google maps implements)