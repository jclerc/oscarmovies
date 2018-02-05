<h1 align="center">
  <img alt="oscarmovies" width="652" src="https://jclerc.github.io/assets/repos/banner/oscarmovies.jpg">
  <br>
</h1>

<p align="center">
  <img alt="made for: school" src="https://jclerc.github.io/assets/static/badges/made-for/school.svg">
  <img alt="language: php" src="https://jclerc.github.io/assets/static/badges/language/php.svg">
  <img alt="made in: 2016" src="https://jclerc.github.io/assets/static/badges/made-in/2016.svg">
  <br>
  <sub>Talk with an AI, and it will suggest you movies.</sub>
</p>
<br>

## Features

- [x] Chatbot using [**Wit**](https://wit.ai)
- [x] Find movies using [**TMDb**](https://www.themoviedb.org)
- [x] Check movie availability using [**Can I Stream it ?**](http://canistream.it)
- [x] User's location using [**IP-API**](http://ip-api.com)
- [x] Current weather using [**OpenWeatherMap**](http://openweathermap.org)
- [x] Authentication using [**Facebook SDK**](https://developers.facebook.com/)
- [x] GIFs using [**Giphy**](http://giphy.com)

## Stack used

- Twig `1.24`
- PHP `7.0`
- MySQL `5.5`

## Getting started

#### Requirements

- Apache server with PHP 7+
- A recent MySQL server

#### Installation

```sh
git clone https://github.com/jclerc/oscarmovies.git
cd oscarmovies
composer install -d htdocs
```

Then create a MySQL database named `oscar`, and import data from `sql/mysql/oscar.sql` file.
You may need to change credentials in the config file at `/htdocs/app/core/config.json`.

Once it's done, start the webserver in directory `/htdocs/www/`.

## Notes

- It doesn't work anymore, due to breaking changes of APIs used
