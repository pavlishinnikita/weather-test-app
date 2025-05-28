The app has been created as test app for some vacancy.

Requirements:
 - docker should be installed and running
 - have time to fun

How to run:
1. ```git clone [https://github.com/pavlishinnikita/weather-test-app.git](https://github.com/pavlishinnikita/weather-test-app.git)```
2. ```make build```
3. create ```.env.local``` file or put "real" values into .env
4. optionally clear cache using ```make cache``` command
5. go to http://localhost/api/weather/{city} to get weather for the {city} passed in request