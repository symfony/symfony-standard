UPGRADE FROM 2.1 to 2.2
=======================

Functional Tests
----------------

 * The profiler has been disabled by default in the test environment. You can
   enable it again by modifying the ``config_test.yml`` configuration file or
   even better, you can just enable it for the very next request by calling
   ``$client->enableProfiler()`` when you need the profiler in a test (that
   speeds up functional tests quite a bit).

Routing
----------------
 * The default place for Acme DemoBundle routes has been changed to Acme
   DemoBundle directory. You can find them in
   ```Acme\DemoBundle\Resources\config\routing.yml```

Command
----------------
 * A new demo command has been added to the AcmeDemoBundle that will help you
   remove the bundle once you want to start working on a clean copy of
   Symfony2 Standard. You can call it like this:
   ```app/console demo:self-remove``` and type ```y``` when prompted to remove
   the bundle in order to confirm the action. Else the command will display a
   list of actions that it will do when running in ```live``` mode.
   Make sure that the user executing the command has the needed privilages to
   delete the files and directories mentioned while in dry-run mode.

