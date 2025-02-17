# Design
There are two users: postgres and carving
The 'postgres' user is the default user for postgres. It is tied to the user 
account on the machine of the same name. You can only log in with this account
from the local machine. The 'postgres' user owns the carving database and all
the entities inside (tables, sequences, etc). Permissions are granted to the
'carving' user to update the information, but not the database schema itself.

## Installation
On Ubuntu 18/Debian:
```
sudo apt install apache2 postgresql zip

sudo pg_createcluster 10 default
sudo systemctl restart postgresql
sudo apt install php7.2-pgsql
```

## Configuration
Modify - change local authentication to 'trust'
/etc/postgresql/10/main/pg_hba.conf
Restore the tables from a dump

Log in as 'postgres' to grant permissions to 'carving' user:
sudo psql -U postgres -d carving
grant select,insert,update,delete ON all tables in schema public to carving;
grant select,update ON all sequences in schema public to carving;

## Increase Security
Modify - change local authentication to 'md5'
/etc/postgresql/10/main/pg_hba.conf

Login to Postgres with:
psql -U carving

## Database credentials
The username and password are kept in a separate file so they will not be
checked into source control (git). The file must appear one level higher
than the directory containing the sources, and must be named 'db-credentials.php'.
It should have a single variable defined like this:

<?php
$conn_string = "host=127.0.0.1 port=5432 user=*** dbname=*** password=***";
?>
