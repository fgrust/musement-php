# Backend PHP tech homework

## Step 1 | Development

### Project Overview
The project is Docker based application running Nginx, PHP-FPM, and MySQL.<br>
Web application has been designed with MVC pattern and is able to migrate and test via `composer` scripts.<br>
The main feature to fetch the cities' list is performed by a one-time running script(`make service`). The next plan is to run this service as a cronjob for every day under the discussion.
### Prerequisites

- PHP: 7.x
- Composer
- Docker

### Run with Docker

- Build and Run containers with:
```
make docker-start.
```
After running, you will be ready to play with the application.
- Migrate with:
```
make migrate
```
This command performs to migrate necessary tables to mysql database on `db` container.<br>
You can rollback the last migration by running `make migrate-rollback`.
- Test
```
make test
```
- Run to get cities' list with weather information.
```
make service 
```
This command will fetch cities' listt with weather information and store in database. If you run it again, it will update information in database.<br>
And it will output the formatted strings for every city as you mentioned<br>
```
.i.e
... ... ...
Processed city Milan | Heavy rain - Partly cloudy
Processed city Naples | Sunny - Sunny
... ... ...
```

<b>! - Next plan is to run this service as a cronjob.</b>

- Stop running containers
```
make docker-stop
```

Refer to `make help` command.
### API endpoint
The API service runs on `localhost:8000` after running `make docker-start` and `make composer-update`.
```
GET /api/cities
```
Retrives the list of all cities with weather information.
```
GET /api/cities/{id}
```
Retrives a city with weather information for all the dates (from date when you ran `make service` to tomorrow).
## Step 2 | API design
### Set the weather for a specific date in a city.
```
POST /api/v3/cities/{id}/weather
```
Create or update the weather condition for a `date` in the city identified by `id`.
<h4>Parameters</h4>

- date: optional<br>
A string formatted as `yyyy-mm-dd` or deserved with 'today' or 'tomorrow'. default value is 'today'.
- condition: required<br>
A string presenting the weather.

<h4>Response</h4>

- success<br>
Returns 200 status code with message 'Set the weather for %%date%% in %%city%% successfully'.
- error
    - Input validation failure: Returns 400 status code with message 'date or condition are incorrect or not provided'.
    - Server internal error: Returns 500 status code with message 'Cannot find the city for id %%id%%'.

### Set the forecast in a city
```
POST /api/v3/cities/{id}/forecast
```
Create or update massive weather forecast in the city identified by `id`.
<h4>Parameters</h4>

- forecast: required<br>
An array of weather objects that have `date` and `condition`.

<h4>Response</h4>

- success<br>
Returns 200 status code with message 'Set the forecast from %%date%% to %%date%% in %%city%% successfully.'
- error
    - Input validation failure: Returns 400 status code with message 'forecast data are invalid or not provided'.
    - Server internal error: Returns 500 status code with message 'Cannot find the city for id %%id%%'.

### Get the weather for a specific date in a city
```
GET /api/v3/cities/{id}/weather
```
Retrives the weather for the `date` in the city identified by `id`.
<h4>Parameters</h4>

- date: optional<br>
A string formatted as `yyyy-mm-dd` or deserved with 'today' or 'tomorrow'. default value is 'today'.
<h4>Response</h4>

A string for the weather condition.

- success<br>
Returns 200 status code with message 'Get the weather for %%date%% in %%city%% successfully' and condition data.
- error
    - Input validation failure: Returns 400 status code with message 'date are invalid'.
    - Unprocessable entity: Returns 422 status code with message 'No weather data for %%date%%'.
    - Server internal error: Returns 500 status code with message 'Cannot find the city for id %%id%%'.

### Get the forecast in a city
```
GET /api/v3/cities/{id}/forecast
```
Retrives upto next 10 day weather forecast in the city identified by `id`.
<h4>Parameters</h4>

- days: optional<br>
A number of days from today. default value is 10 and max/min is 10/1

<h4>Response</h4>

An array of weather objects
- success<br>
Returns 200 status code with message 'Get next %%days%% weather forecast'.
- error<br>
    - Input validation faliure: Returns 400 status code with message 'days should be an integer in range 1 to 10'.
    - Server internal error: Returns 500 status code with message 'Cannot find the city for id %%id%%'.
