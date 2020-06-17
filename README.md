### How to install

0. Install `Apache2`, `PHP`, and all extensions Laravel needs
0. Install `Redis` and its extension for `PHP`
0. Execute `composer install` in the root directory
0. Create `.env` file with correct values
0. Execute `php artisan migrate` to create tables in the given database.

### How to run
0. Execute `php artisan snapp:call` in the root directory. You can use `php artisan snapp:call --random` to assign calls to operators with same priority randomly (This algorithm is fair, but consumes more resources).
0. Use the below API to interact with the application (There are more APIs but the question just asks these APIs). You should see the output in `stdout` of `php artisan snapp:call
` command. 

    0. Create new call with given priority (1 means low, 2 means high)
        ```
        curl -X POST -H 'Content-Type: application/json' -H 'Accept: application/json' -i http://localhost/snappfood/public/api/calls --data '{
          "priority" : 2
        }'
        ```
       
   0. Create a new operator with given priority (you can use any positive integer value):
       ```
       curl -X POST -H 'Accept: application/json' -H 'Content-Type: application/json' -i http://localhost/snappfood/public/api/operators --data '{
         "priority" : 3
       }'
       ```
      
   0. Hang Up the phone (5 is the call's ID in the database):
       ```
       curl -X PATCH -H 'Accept: application/json' -H 'Content-Type: application/json' -i http://localhost/snappfood/public/api/calls/5 --data '{
        "isOpen" : false
       }'
       ```
      
   0. Pick a low priority call (4 is the operator's ID in the database): 
       ```
       curl -X GET -H 'Accept: application/json' -H 'Content-Type: application/json' -i http://localhost/snappfood/public/api/operators/4/pick
       ```

### Information

**Start Time:** 11:15

**End Time:** 15:00
