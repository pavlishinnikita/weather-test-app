The app has been created as test app for some vacancy.

Requirements:
 - docker should be installed and running
 - have time to fun

How to run:
1. **Clone the repository:**
  ```git clone https://github.com/pavlishinnikita/weather-test-app.git```
2. **Setup project in docker:**
   run ```make build```
3. **Setup config:**
  create ```.env.local``` file or put "real" values into .env
  optionally clear cache using ```make cache``` command
4. **See the app:**
  go to http://localhost/api/weather/{city} to get weather for the {city} passed in request
