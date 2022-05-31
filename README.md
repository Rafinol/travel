# Kazan`s Router
Used to find routes between Kazan and wish destination.
You can use the application for any cities. To do this, edit the `config/travelways.php` file.

###How it works
There are 2 arrays in the `travelways.php` file: `additional_routes` and `exclusive_bus_pairs`.
For example, a traveler from Kazan can look for flights not only from his own city, but also take a plane/train/bus to a neighboring city and check routes from them. For these purposes, we use the array `additional_routes`.

```
'additional_routes' => [
        ['Moscow'], // From the point of departure to Moscow, from Moscow to the point of destination
        ['Saint Petersburg'],
        ['Kaliningrad', 'Gdansk'],// From the point of departure to Kaliningrad, from Kaliningrad to Gdyansk, from Gdyansk to the destination point
        ['Minsk', 'Riga',],
        ['Moscow','Saint Petersburg','Tallinn'],
    ],
```
To determine which routes will be operated exclusively by the bus, it is necessary to fill in the array `exclusive_bus_pairs`. This can be useful if there is no air service between the current cities or for other reasons.

```
'exclusive_bus_pairs' => [
    ['Saint Petersburg'=>'Tallinn'],
    ['Minsk'=>'Riga'],
    ['Kaliningrad'=>'Gdansk'],
]
```

###Proxy
To speed up the search for routes, you need to use a proxy to run parallel jobs.
Paste them into the `.env `file. 

```
PROXY_1 = "login:password@test.com:1252"
PROXY_2 = "login:password@test.com:1253"
PROXY_3 = "login:password@test.com:1254"
```

###Jobs
Routes are searched in the background. So, you need to dispatch jobs anytime.
Start the supervisor by specifying the number of channels equal to the number of your proxies and set up the cron every minute.

