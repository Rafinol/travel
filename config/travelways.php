<?php
/* Waypoints for finding routes from neighboring cities.
    For example, a traveler from Kazan can look for flights not only from his own city, but also take a plane/train/bus to a neighboring city and check routes from them.
*/
return [
    'additional_routes' => [
        ['Moscow'], // From the point of departure to Moscow, from Moscow to the point of destination
        ['Saint Petersburg'],

        ['Kaliningrad','Gdansk'],// From the point of departure to Kaliningrad, from Kaliningrad to Gdyansk, from Gdyansk to the destination point
        ['Moscow','Kaliningrad','Gdansk'],// From the point of departure to Moscow, from Moscow to Kaliningrad, from Kaliningrad to Gdyansk, from Gdyansk to the destination point

        ['Minsk','Vilnius',],
        ['Minsk', 'Riga',],
        ['Moscow','Minsk','Vilnius'],
        ['Moscow','Minsk','Riga'],

        ['Saint Petersburg','Tallinn'],
        ['Saint Petersburg','Helsinki'],
        ['Moscow','Saint Petersburg','Tallinn'],
        ['Moscow','Saint Petersburg','Helsinki'],
    ],
    'exclusive_bus_pairs' => [ // For these routes, it is better to search for tickets only by bus
        ['Saint Petersburg'=>'Tallinn'],
        ['Saint Petersburg'=>'Helsinki'],
        ['Minsk'=>'Vilnius'],
        ['Minsk'=>'Riga'],
        ['Kaliningrad'=>'Gdansk'],
    ]
];
