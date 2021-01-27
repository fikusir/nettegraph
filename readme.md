# Nette Microsoft Graph project

This is a simple, skeleton application using the [Nette](https://nette.org) and [Microsoft Graph API](https://docs.microsoft.com/en-us/graph/use-the-api). This is meant to
be used as a starting point for your new projects.

The basic Nette skeleton show you how to use Microsft Graph with Nette.

Here are two scenarios:

- Using Microsoft graph as daemon (without user login). See Presenter **app/Presenters/HomepagePresenter**. Detailed [Instructions](graphasdaemon.md)
- Using Microsoft graph for user login (oAuth). See Presenter **app/Presenters/Signo365Presenter** (COMMING SOON)

## Requirements

- Web Project for Nette 3.1 requires PHP 7.2
- For Microsoft Graph API you need several libraries, all installed with microsoft/microsoft-graph

## Installation

The best way to install Web Project is using Composer. If you don't have Composer yet, download it following [the instructions](https://doc.nette.org/composer). Then use command:

	composer create-project nette/web-project path/to/install
	cd path/to/install
	composer require microsoft/microsoft-graph


Make directories `temp/` and `log/` writable.

## Web Server Setup

The simplest way to get started is to start the built-in PHP server in the root directory of your project:

	php -S localhost:8000 -t www

For Apache or Nginx, setup a virtual host to point to the `www/` directory of the project and you
should be ready to go.

**It is CRITICAL that whole `app/`, `config/`, `log/` and `temp/` directories are not accessible directly
via a web browser. See [security warning](https://nette.org/security-warning).**
