# 3D Follow

## About the project

3D Follow is an application for 3D printers and makers. It offers some features like:
- an history of all your prints
- knowing the quantity of filament it remains for each of your spool
- estimate the material cost for each print
- allow your family, friends or customers to send you their print wishes

## Running the application locally

### Requirements

A Docker environment is provided and requires you to have these tools available:

* Docker
* Bash
* PHP >= 8.1
* [Castor](https://github.com/jolicode/castor#installation)

#### Castor

Once castor is installed, in order to improve your usage of castor scripts, you
can install console autocompletion script.

If you are using bash:

```bash
castor completion | sudo tee /etc/bash_completion.d/castor
```

If you are using something else, please refer to your shell documentation. You
may need to use `castor completion > /to/somewhere`.

Castor supports completion for `bash`, `zsh` & `fish` shells.

### Docker environment

The Docker infrastructure provides a web stack with:
- NGINX
- PostgreSQL
- PHP
- Traefik
- A container with some tooling:
   - Composer
   - Node
   - Yarn / NPM

### Domain configuration (first time only)

Before running the application for the first time, ensure your domain names
point the IP of your Docker daemon by editing your `/etc/hosts` file.

This IP is probably `127.0.0.1` unless you run Docker in a special VM (docker-machine, dinghy, etc).

Note: The router binds port 80 and 443, that's why it will work with `127.0.0.1`

```
echo '127.0.0.1 3dfollow.test' | sudo tee -a /etc/hosts
```

Using dinghy? Run `dinghy ip` to get the IP of the VM.

### Starting the stack

Launch the stack by running this command:

```bash
castor start
```

> Note: the first start of the stack should take a few minutes.

The site is now accessible at the hostnames your have configured over HTTPS
(you may need to accept self-signed SSL certificate if you don't have [`mkcert`](https://github.com/FiloSottile/mkcert#installation)).

### Builder

Having some composer, yarn or other modifications to make on the project?
Start the builder which will give you access to a container with all these
tools available:

```bash
castor builder
```

### Other tasks

Checkout `castor` to have the list of available tasks.
