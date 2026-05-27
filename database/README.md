# NewLove Database

This folder contains the SQL export for the NewLove project.

## File

```text
newLove.sql
```

The SQL file includes:

- database creation
- table creation
- existing local data

## Import

Run this command from the project root:

```bash
mysql -u newLove -p < database/newLove.sql
```

If the database user does not exist on another computer, create the database and user first, then update `config/app_local.php`.

Recommended local database settings:

```text
host: 127.0.0.1
database: newLove
username: newLove
password: password
```
