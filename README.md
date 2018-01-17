# Vessels Reports API 

Some notes on the requested functionality:

**The API must support the following:**
* filterable by: **imo** (one or more), **condition**, **datetime frame** as well as **fuel_consumption** (greater,lower either for meCons or auxCons). The output must contain the reports data along with the vessel name and email from vessels.csv file.

All filters are implemented as described above, trying to keep it as simple as possible:

    - url: /api/vrep

    - params: imo (comma seperated numbers), format(json or csv), fuel_consumption(gt or lt,number,me or aux), condition (steaming of anchor), from (datetime), to (datetime)



* Create a rate limiter to limit requests per user to **5/hour**. Use the request ip address to define the user. 

Using the default throtle

    more details with notes in App/Http/Kernel
 

* Create a log for the incoming requests (e.g database table, plain text etc.)

Loggin info or error messages in custom log file

    Implemented in Http/Controllers/REportsController

* Return the output based on an input parameter that defines the format (JSON, CSV)

returning json data or streamed csv

    Implemented in Http/Controllers/REportsController

* Make the necessary validation for the incoming request values.

Using laravel and some custom validation rules

    Implemented in Http/Controllers/REportsController

**Examples of valid requests:**

[a link](/api/vrep?imo=9224570&format=json&condition=steaming&from=2016-02-17%2012:15:38&to=2016-02-21%2012:15:19)


[a link](/api/vrep?imo=9224570,9327475&format=csv&condition=anchor)