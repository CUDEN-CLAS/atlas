.. Atlas documentation master file, created by
   sphinx-quickstart on Thu Aug 25 18:26:44 2016.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

Welcome to Atlas's documentation!
=================================

Contents:

.. toctree::
   :maxdepth: 2

   installation
   sample_requests



Indices and tables
==================

* :ref:`genindex`
* :ref:`modindex`
* :ref:`search`


Installation
------------

See [Express_local](https://github.com/CuBoulder/express_local) for setting up a local development environment.

Getting started
---------------

Code items should be created first. Required fields are: git URL, commit hash, Name, Version, and Type (core, profile, module, theme, library). Optional fields are: is_current (allows you to indicate the preferred version of a code item) and a tagging field.

Site items are created with a 'pending' status and can be assigned a specific core and/or profile when created. If a core or profile is not specified, the 'current' version of the default is used.

Features
--------
- Chronological tasks run to keep a small number of instances available for assignment to end users.
- POST to create additional instances on demand.
- Available instances are replaced every night.
- Code, Site, and Command items are versioned.

API
----
- Prefers to receive JSON encoded POST request.
- CRUD Web Express instances

Configuration
-------------

Configuration is split between various files `config_*.py`. You need to create a `config_local.py` file and will most likely want to replace `config_servers.py`.

Deploying Atlas
---------------

Currently we use a `git pull` deployment. When code is changed, you need to restart Celery, Celerybeat and Apache.

Contributing
------------

Pull requests are always welcome. Project is under active development. We want to make sure that Express doesn't become dependant on Atlas.
