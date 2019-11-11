# What is khatovar-web?

[![Build Status](https://travis-ci.org/damien-carcel/khatovar-web.svg?branch=master)](https://travis-ci.org/damien-carcel/khatovar-web)

This repository contains the source code of the CMS handling the web site of the medieval association :
[“La compagnie franche du Khatovar”](http://www.compagniefranchedukhatovar.fr/)

## Installation

The following part assume the use of Docker and Docker Compose controlled by a Makefile.
However, the same commands (without the Docker part) can be used on a local environment.

### Development server

You can use the internal Symfony server (dev and testing purpose only)
```bash
$ make server-run PHP_OUTPUT_PORT=8000
```

You should be able to access the application through [localhost:8000](http://localhost:8000).

### Production like server

First install Traefik as a reverse proxy by following [these instructions](https://github.com/damien-carcel/traefik-as-local-reverse-proxy).

Then serve the application:
```bash
$ make install
```

You can now access the it through [khatovar.docker.localhost](http://khatovar.docker.localhost).

## License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program.  If not, see <http://www.gnu.org/licenses/>.

The Symfony framework is licensed under The MIT License. For full copyright and license information, please see the [MIT License](http://www.opensource.org/licenses/mit-license.php).
