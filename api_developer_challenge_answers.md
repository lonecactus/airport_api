# The Wonderful Business Logic + API Developer Challenge


### Problem 1

#### Requirements

Provide a detailed description of the full stack that you would choose to build this API, complete with descriptive strategies for the following:

- Hosting
- Language
- Framework (if applicable)
- Storage
- Performance 
- Misc (anything not covered above)

Also, provide estimates on the scalability and monthly costs of this environment.

#### Discussion

For this app I recommend a PHP/MySQL stack. MySQL is good for read-heavy applications, and since ours will be mostly a public API for reading data, it should perform better than something like Postgres would.

Additionally, a combo of NGINX and PHP-FPM is well-suited for a resource-heavy application like ours (thanks, Dijkstra formula). If we're expecting ~500 requests per minute and possibly more than that, we'll want something with relatively low CPU and memory usage that runs well if we are doing asynchronous operations. 

Since this is starting out as a small app with just a few endpoints, we don't necessarily need a framework with a lot of bells and whistles and overhead. I recommend Slim PHP as a simple, lightweight framework where we can just quickly build a model for retrieving data and a few controller actions to handle our requests/responses. 

(Also I've never actually used Slim PHP before, and I want to.)

(NARRATOR VOICEOVER: "He should not have wanted to. Ask him why in your next meeting.")

As for hosting, I have used LiquidWeb a lot and know them to be a very easy to work with and reliable host for PHP-based apps. They offer 99.99% uptime and have very quick support responses if we choose a hosting plan with managed support. (We can also go the self-management route and save $39 a month, but if this is a critical application that we need to be up 24/7, then hell, I'll pay the $39 myself so I can sleep through the night.)

I talked to a sales rep and gave them the general idea of the resources we'd need and the traffic volume we're expecting, and their recommendation was one of these two plans:

Cloud VPS, single tenant, generally $145/month after initial discounts
https://www.liquidweb.com/configuration/cloud-vps/?location=1%3Alan&hardware=1%3A1783&htab=gp&software=1%3AAlmaLinux&softwareTab=os&softwareVersion=9&mgmt=1%3Afull&controlPanel=1%3AInterworx&controlPanelTier=&collection=255fccf7-c294-48e5-8068-846df585fe1e&cycle=monthly

Cloud Metal VPS, single tenant but we'd get the whole server, generally $229/month after initial discounts
https://www.liquidweb.com/configuration/cloud-metal/?location=1%3Alan&hardware=1%3A1816&htab=cloud-metal&software=1%3AAlmaLinux&softwareTab=os&softwareVersion=9&mgmt=1%3Afull&controlPanel=1%3AInterworx&controlPanelTier=&collection=255fccf7-c294-48e5-8068-846df585fe1e&cycle=monthly

There's some flexibility in the price depending on the particular options we choose in those plans. It's also a matter of doing a push-button upgrade to convert from the Cloud VPS to the Cloud Metal VPS instance, so we could start with the cheaper one and immediately go bigger if we found that we needed more horsepower to handle our volume. There's also a certain degree of on-demand upscaling within either plan that we can do as-needed if we experience spikes in traffic for some reason or another.



### Problem 2

#### Requirement

Write and document an endpoint that is able to efficiently return a JSON-formatted response of airports within a given radius of a specific coordinate. The iOS-supplied information should be:

- Latitude
- Longitude
- Radius

#### Solution

This can be tested using the endpoint: `/api/distance/latitude/{latitude}/longitude/{longitude}/radius/{radius}/unit/{unit}`

It leverages an endpoint created to return a single airport's information, including latitude and longitude, which can be tested at `/api/airports/{id}` 

Distance is calculated with the Haversine formula, which accounts for as-the-crow-flies distances that take into account the curvature of the Earth. (Because despite what a lot of people are tweeting on X, the Earth is NOT flat.) 

The Haversine formula we're using here has the ability to return distances in miles, kilometers, or nautical miles, so the endpoint takes a `{unit}` parameter to identify the desired result.

If any airports are found within the specified radius, the API returns them in a list. If no airports are found, it returns a 200 response with the message "No airports found within the provided radius."


### Problem 3

#### Requirement

Write and document an endpoint that is able to return a JSON-formatted response with the distance between two supplied airport id’s. The iOS-supplied information should be:

- Airport 1 ID
- Airport 2 ID

#### Solution

This can be tested using the endpoint: `/api/distance/airport/{airport1_id}/airport/{airport2_id}/unit/{unit}`

It leverages an endpoint created to return a single airport's information, including latitude and longitude, which can be tested at `/api/airports/{id}` 

Distances are calculated with the Haversine formula.

The endpoint will return an error if the two airport IDs are identical (because why would someone do that?). It will also return an error if a provided airport ID does not exist in the system (8675309).



### Problem 4

#### Requirement

Write and document an endpoint that is able to return a JSON-formatted response with the geographically closest airports between two countries. For example, if tasked to compare the airports in the United States and Mexico, the endpoint would find the 1 airport in each country that is the shortest distance from the airport in the opposite country. The iOS supplied information should be:

- Country 1 Name
- Country 2 Name

#### Solution

This can be tested using the endpoint: `/api/distance/country/{country1_name}/country/{country2_name}/unit/{unit}`

It leverages an endpoint created to return all of the airports in a given country (`/api/airports/country/{country}`). 

Distance is calculated with the Haversine formula. The logic here takes a list of airports from country 1 and iterates each airport against the airports in country 2, measuring the distance between each one's latitude and longitude. The overall shortest distance, along with its corresponding airports, is continually updated as the loop iterates, until the final result is returned.

If an invalid country name is passed as a parameter (maybe "Westeros" or "Narnia" or something), the endpoint returns an error response of "Country with name {whatever} not found".


### Problem 5
 
#### Requirement

Write and document an endpoint that is able to return a JSON-formatted list of instructions as to how to fly the shortest possible route (in terms of airport stops) from one airport to another. When generating these instructions, assume that an airplane can only travel 500 miles before requiring a stop to refuel. Therefore, the returned instructions should read as a list of airport stops, and the distance between each stop. The iOS supplied information should be:

- Airport 1 ID
- Airport 2 ID

#### Solution

This can be tested using the endpoint: `/api/routefinder/airport/{airport1_id}/airport/{airport2_id}/range/{range}/unit/{unit}`

It leverages an endpoint created to return all of the airports in the system: `{/api/airports}`

Distance is calculated using the Haversine formula, as always... but wrapped in another layer, the Dijkstra formula. The Dijkstra formula is some complicated math doohickey that I vaguely understand but that I'm mostly taking on faith when the internet says it "finds the shortest path between a starting node and all other nodes in a weighted graph by iteratively selecting the unvisited node with the smallest tentative distance. It uses a priority queue to efficiently pick the next closest node, updating neighbor distances (relaxation) until all nodes are visited or the target is reached."

Effectively what that means is we feed it a list of all of the airports/latitudes/longitudes that we have in the system. It is going to start at airport 1 and map out the distances between all of the other airports using Haversine, weeding out ones that exceed our {range} parameter. Then it will take all of those airports and repeat the process with each of them, and then repeat the process with those airports, and so on and so on, until some combination of airports is found that gets you from airport 1 to airport 2 without exceeding your {range} and running out of fuel and making you plummet into the ocean.

The returned result is a list of stops (waypoints) on the route, including the full airport information, the airport ID of the next waypoint on the journey, and distance to that waypoint in the specified measurement unit (miles/km/nautical miles). If a waypoint is the last one on the route, it is denoted with 'distance_to_next_waypoint: 0'. 

It's a very resource-intensive calculation and it can take a lot of time to run. The implementation I've done here does work, but I don't believe it will run at scale without the right infrastructure improvements (i.e. caching; possibly a background process that calculates and stores all possible route permutations in the db and updates them whenever an airport is added/modified in the system; definitely a server with more horsepower than my 2019 Macbook Pro; stuff like that). 
