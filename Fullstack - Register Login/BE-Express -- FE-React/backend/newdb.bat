@echo off
setlocal
:PROMPT
SET /P AREYOUSURE=Create new DB, Are you sure (Y/[N])?
IF /I "%AREYOUSURE%" NEQ "Y" GOTO END

if exist db.sqlite3 del db.sqlite3
npx knex migrate:latest


:END
endlocal