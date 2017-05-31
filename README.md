# HashKraken
Demostration of the [Gishiki](https://github.com/NeroReflex/Gishiki) framework througth hash craking!

## Usage
This tool is just a demonstration and is not meant to be used in __ANY__ way!

## Demo
You can find a *probably* live demo at this address: [https://hashkraken.herokuapp.com](https://hashkraken.herokuapp.com).

## I want to be an hacker
Open a terminal and issue:
```sh
git clone https://github.com/NeroReflex/HashKraken.git
cd HashKraken
wget https://getcomposer.org/composer.phar
php composer.phar install --no-dev
php -S localhost:8080 -t ./
```

Open another terminal and...
```sh
cd HashKraken
php dictionary.php localhost:8080 dictionary.txt
```

Enjoy colorful text spreading all over your terminals!

## License
Released under the MIT License. Read the LICENSE file.
